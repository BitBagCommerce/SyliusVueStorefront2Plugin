@wishlist_as_quest
Feature: Managing wishlists
    In order add a product to the wishlist
    As a quest
    I need to receive access denied

    Background:
        Given the store operates on a single channel in "United States"

    @graphql
    Scenario: Get wishlists
        Given There is query to fetch all wishlists
        When I send that GraphQL request
        Then I should receive a JSON response
        And I should receive access denied

    @graphql
    Scenario: Creating a wishlist
        Given There is operation to create wishlist
        And I set name field to "Wishlist"
        And I set channelCode field to "WEB-US"
        When I send that GraphQL request
        Then I should receive a JSON response
        And I should receive access denied

    @graphql
    Scenario: Updating a wishlist
        Given anonymous user has a wishlist named "Wishlist"
        And There is operation to update wishlist
        And this operation has "id" variable with iri value of object "Wishlist"
        And I set name field to "Wishlist"
        When I send that GraphQL request
        Then I should receive a JSON response
        And I should receive access denied

    @graphql
    Scenario: Clearing a wishlist
        Given anonymous user has a wishlist named "Wishlist"
        And There is operation to clear wishlist
        And this operation has "id" variable with iri value of object "Wishlist"
        When I send that GraphQL request
        Then I should receive a JSON response
        And I should receive access denied

    @graphql
    Scenario: Removing a wishlist
        Given anonymous user has a wishlist named "Wishlist"
        And There is operation to remove wishlist
        And this operation has "id" variable with iri value of object "Wishlist"
        When I send that GraphQL request
        Then I should receive a JSON response
        And I should receive access denied

    @graphql
    Scenario: Adding a product to a wishlist
        Given anonymous user has a wishlist named "Wishlist"
        And the store has a product "Climbing shoes" priced at "$10.00"
        And There is operation to add product to wishlist
        And this operation has "id" variable with iri value of object "Wishlist"
        And this operation has "productVariant" variable with iri value of object "product"
        When I send that GraphQL request
        Then I should receive a JSON response
        And I should receive access denied

    @graphql
    Scenario: Removing a product from a wishlist
        Given anonymous user has a wishlist named "Wishlist"
        And the store has a product "Climbing shoes" priced at "$10.00"
        And There is operation to remove product from wishlist
        And this operation has "id" variable with iri value of object "Wishlist"
        And this operation has "productVariant" variable with iri value of object "product"
        When I send that GraphQL request
        Then I should receive a JSON response
        And I should receive access denied

