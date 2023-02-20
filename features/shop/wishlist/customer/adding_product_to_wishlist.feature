@wishlist_as_customer
Feature: Adding a product to a wishlist
    In order to add a product to a wishlist
    As a customer
    I want to be able to add products to wishlists

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Harness climbing" priced at "$30.00"
        And the store has a product "Rope" priced at "$40.00"
        And there is a customer "Alex Honnold" identified by an email "ahonnold@climb.com" and a password "dlonnoh1"
        And user "ahonnold@climb.com" has a wishlist named "For Alex"
        And there is a customer "Adam Ondra" identified by an email "aondra@climb.com" and a password "ardno1"
        And user "aondra@climb.com" has a wishlist named "For me"
        And I create a JWT Token for customer identified by an email "aondra@climb.com"

    @graphql
    Scenario: Adding first product to a wishlist
        Given There is operation to add product to wishlist
        And this operation has "id" variable with iri value of object "For me"
        And this operation has "productVariant" variable with iri product variant "HARNESS_CLIMBING"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response body should contain:
            | key                                                   | value             | type      |
            | wishlist.name                                         | For me            | string    |
            | wishlist.wishlistProducts.totalCount                  | 1                 | int       |
            | wishlist.wishlistProducts.edges.0.node.variant.name   | Harness climbing  | string    |

    @graphql
    Scenario: Adding second product to a wishlist
        Given user have a product "HARNESS_CLIMBING" in my wishlist "For me"
        And There is operation to add product to wishlist
        And this operation has "id" variable with iri value of object "For me"
        And this operation has "productVariant" variable with iri product variant "Rope"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response body should contain:
            | key                                                   | value             | type      |
            | wishlist.name                                         | For me            | string    |
            | wishlist.wishlistProducts.totalCount                  | 2                 | int       |
            | wishlist.wishlistProducts.edges.0.node.variant.name   | Harness climbing  | string    |
            | wishlist.wishlistProducts.edges.1.node.variant.name   | Rope              | string    |

    @graphql
    Scenario: Adding existing product to a wishlist
        Given user have a product "HARNESS_CLIMBING" in my wishlist "For me"
        And There is operation to add product to wishlist
        And this operation has "id" variable with iri value of object "For me"
        And this operation has "productVariant" variable with iri product variant "HARNESS_CLIMBING"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response body should contain:
            | key                                                   | value             | type      |
            | wishlist.name                                         | For me            | string    |
            | wishlist.wishlistProducts.totalCount                  | 1                 | int       |
            | wishlist.wishlistProducts.edges.0.node.variant.name   | Harness climbing  | string    |


    @graphql
    Scenario: Adding a product to another user's wishlist
        Given There is operation to add product to wishlist
        And this operation has "id" variable with iri value of object "For Alex"
        And this operation has "productVariant" variable with iri product variant "HARNESS_CLIMBING"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I should receive access denied
