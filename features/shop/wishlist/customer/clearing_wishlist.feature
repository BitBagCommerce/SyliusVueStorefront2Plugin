@wishlist_as_customer
Feature: Clearing a wishlist
    In order to clear a wishlist
    As a customer
    I want to be able to clear wishlists

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
    Scenario: Cleaning a wishlist
        And user have a product "ROPE" in my wishlist "For me"
        And user have a product "HARNESS_CLIMBING" in my wishlist "For me"

        When I prepare query to fetch all wishlists
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I should receive 1 wishlists
        And This response should contain "collection.0.wishlistProducts.totalCount" equal to 2 as "int"

        When I prepare clear wishlist operation
        And I set id field to iri object "For me"
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response should contain "wishlist.wishlistProducts.totalCount" equal to 0 as "int"

        When I prepare query to fetch all wishlists
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I should receive 1 wishlists
        And This response should contain "collection.0.wishlistProducts.totalCount" equal to 0 as "int"

    @graphql
    Scenario: Cleaning another user's wishlist
        When I prepare clear wishlist operation
        And I set id field to iri object "For Alex"
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I should receive access denied
