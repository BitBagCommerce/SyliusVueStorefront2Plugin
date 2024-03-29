@wishlist_as_customer
Feature: Removing a wishlist
    In order to remove a wishlist
    As a customer
    I want to be able to remove wishlists

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Harness climbing" priced at "$30.00"
        And the store has a product "Rope" priced at "$40.00"
        And there is a customer "Alex Honnold" identified by an email "ahonnold@climb.com" and a password "dlonnoh1"
        And user "ahonnold@climb.com" has a wishlist named "For Alex"
        And there is a customer "Adam Ondra" identified by an email "aondra@climb.com" and a password "ardno1"
        And user "aondra@climb.com" has a wishlist named "For me"
        And I authorize as "aondra@climb.com"

    @graphql
    Scenario: Removing a wishlist when I have one wishlist
        Given There is operation to remove wishlist
        And this operation has "id" variable with iri value of object "For me"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And user "aondra@climb.com" should have 0 wishlists

    @graphql
    Scenario: Removing wishlist when I have two wishlists
        Given user "aondra@climb.com" has a wishlist named "For wife"
        And There is operation to remove wishlist
        And this operation has "id" variable with iri value of object "For me"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And user "aondra@climb.com" should have 1 wishlists

    @graphql
    Scenario: Removing a wishlist with products
        Given user has a product "HARNESS_CLIMBING" in my wishlist "For me"
        Given user has a product "ROPE" in my wishlist "For me"
        And There is operation to remove wishlist
        And this operation has "id" variable with iri value of object "For me"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And user "aondra@climb.com" should have 0 wishlists

    @graphql
    Scenario: Removing another user's wishlist
        Given There is operation to remove wishlist
        And this operation has "id" variable with iri value of object "For Alex"
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I should receive access denied
