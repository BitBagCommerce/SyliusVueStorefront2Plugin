@wishlist_as_quest
Feature: Managing wishlists
    In order add a product to the wishlist
    As a quest
    I need to receive access denied

    Background:
        Given the store operates on a single channel in "United States"

    @graphql
    Scenario: Get wishlists
        When I prepare query to fetch all wishlists
        And I send that GraphQL request
        Then I should receive a JSON response
        And I should receive access denied

    @graphql
    Scenario: Creating a wishlist
        When I prepare create wishlist operation
        And I set name field to "Wishlist"
        And I set channelCode field to "WEB-US"
        And I send that GraphQL request
        Then I should receive a JSON response
        And I should receive access denied

    @graphql
    Scenario: Updating a wishlist
        And anonymous user has a wishlist named "Wishlist"
        When I prepare update wishlist operation
        And I set id field to iri object "Wishlist"
        And I set name field to "Wishlist"
        And I send that GraphQL request
        Then I should receive a JSON response
        And I should receive access denied

    @graphql
    Scenario: Removing a wishlist
        And anonymous user has a wishlist named "Wishlist"
        When I prepare remove wishlist operation
        And I set id field to iri object "Wishlist"
        And I send that GraphQL request
        Then I should receive a JSON response
        And I should receive access denied

    @graphql
    Scenario: Adding a product to a wishlist
        And anonymous user has a wishlist named "Wishlist"
        And the store has a product "Climbing shoes" priced at "$10.00"
        When I prepare add product to wishlist operation
        And I set id field to iri object "Wishlist"
        And I set productVariant field to iri object "product"
        And I send that GraphQL request
        Then I should receive a JSON response
        And I should receive access denied

    @graphql
    Scenario: Removing a product from a wishlist
        And anonymous user has a wishlist named "Wishlist"
        And the store has a product "Climbing shoes" priced at "$10.00"
        When I prepare remove product from wishlist operation
        And I set id field to iri object "Wishlist"
        And I set productVariant field to iri object "product"
        And I send that GraphQL request
        Then I should receive a JSON response
        And I should receive access denied

