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
        And I authorize as "aondra@climb.com"

    @graphql
    Scenario: Cleaning a wishlist
        Given user has a product "ROPE" in my wishlist "For me"
        And user has a product "HARNESS_CLIMBING" in my wishlist "For me"
        And There is operation to clear wishlist
        And this operation has "id" variable with iri value of object "For me"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This wishlist should have 0 products


    @graphql
    Scenario: Cleaning an empty wishlist
        Given There is operation to clear wishlist
        And this operation has "id" variable with iri value of object "For me"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This wishlist should have 0 products

    @graphql
    Scenario: Cleaning another user's wishlist
        Given There is operation to clear wishlist
        And this operation has "id" variable with iri value of object "For Alex"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I should receive access denied
