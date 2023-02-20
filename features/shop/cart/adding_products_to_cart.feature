@add_products_to_cart
Feature: Adding multiple products to cart
    In order buy a products
    I need to be able to add multiple products to cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Harness climbing" priced at "$30.00"
        And the store has a product "Rope" priced at "$40.00"

    @graphql
    Scenario: Placing order as quest
        Given There is operation to add products to cart
        And this operation has orderTokenValue
        And this operation has cartItems:
            | productVariant    | quantity  |
            | Harness_climbing  | 2         |
            | Rope              | 3         |

        When I send that GraphQL request

        Then I should receive a JSON response
        And This response body should contain:
            | key                                   | value             | type      |
            | order.items.edges.0.node.productName  | Harness climbing  | string    |
            | order.items.edges.0.node.quantity     | 2                 | int       |
            | order.items.edges.1.node.productName  | Rope              | string    |
            | order.items.edges.1.node.quantity     | 3                 | int       |
            | order.total                           | 18000             | int       |

    @graphql
    Scenario: Placing order as customer
        Given there is a customer "Adam Ondra" identified by an email "aondra@climb.com" and a password "ardno1"
        And I create a JWT Token for customer identified by an email "aondra@climb.com"
        And There is operation to add products to cart
        And this operation has orderTokenValue for customer identified by an email "aondra@climb.com"
        And this operation has cartItems:
            | productVariant    | quantity  |
            | Harness_climbing  | 1         |
            | Rope              | 4         |

        When I send that GraphQL request

        Then I should receive a JSON response
        And This response body should contain:
            | key                                   | value             | type      |
            | order.items.edges.0.node.productName  | Harness climbing  | string    |
            | order.items.edges.0.node.quantity     | 1                 | int       |
            | order.items.edges.1.node.productName  | Rope              | string    |
            | order.items.edges.1.node.quantity     | 4                 | int       |
            | order.total                           | 19000             | int       |
