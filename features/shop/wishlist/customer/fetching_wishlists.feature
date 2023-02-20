@wishlist_as_customer
Feature: Fetching wishlists
    In order to fetch all available wishlists
    As a customer
    I want to be able to fetch list of my wishlists

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Harness climbing" priced at "$30.00"
        And the store has a product "Rope" priced at "$40.00"
        And there is a customer "Alex Honnold" identified by an email "ahonnold@climb.com" and a password "dlonnoh1"
        And user "ahonnold@climb.com" has a wishlist named "For me"
        And there is a customer "Adam Ondra" identified by an email "aondra@climb.com" and a password "ardno1"
        And I create a JWT Token for customer identified by an email "aondra@climb.com"

    @graphql
    Scenario: Fetching wishlists
        Given There is query to fetch all wishlists
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response body should contain:
            | key                       | value | type  |
            | paginationInfo.totalCount | 0     | int   |

    @graphql
    Scenario: Fetching wishlists with two existing
        Given user "aondra@climb.com" has a wishlist named "For me"
        And user "aondra@climb.com" has a wishlist named "For wife"
        And user have a product "ROPE" in my wishlist "For me"
        And user have a product "HARNESS_CLIMBING" in my wishlist "For me"
        And There is query to fetch all wishlists
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response body should contain:
            | key                                       | value     | type      |
            | paginationInfo.totalCount                 | 2         | int       |
            | collection.0.name                         | For me    | string    |
            | collection.0.wishlistProducts.totalCount  | 2         | int       |
            | collection.1.name                         | For wife  | string    |
            | collection.1.wishlistProducts.totalCount  | 0         | int       |
