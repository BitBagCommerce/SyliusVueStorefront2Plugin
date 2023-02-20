@wishlist_as_customer
Feature: Updating a wishlist
    In order to update a wishlist
    As a customer
    I want to be able to update wishlists

    Background:
        Given the store operates on a single channel in "United States"
        And there is a customer "Alex Honnold" identified by an email "ahonnold@climb.com" and a password "dlonnoh1"
        And user "ahonnold@climb.com" has a wishlist named "For Alex"
        And there is a customer "Adam Ondra" identified by an email "aondra@climb.com" and a password "ardno1"
        And user "aondra@climb.com" has a wishlist named "For me"
        And I authorize as "aondra@climb.com"

    @graphql
    Scenario: Updating a wishlist
        Given There is operation to update wishlist
        And this operation has "id" variable with iri value of object "For me"
        And I set name field to "For wife"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This wishlist should have name "For wife"

    @graphql
    Scenario: Updating a wishlist to a name that already exists
        Given user "aondra@climb.com" has a wishlist named "For wife"
        And There is operation to update wishlist
        And this operation has "id" variable with iri value of object "For me"
        And I set name field to "For wife"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response should contain message why the name cannot be changed

    @graphql
    Scenario: Updating another user's wishlist
        Given There is operation to update wishlist
        And this operation has "id" variable with iri value of object "For Alex"
        And I set name field to "For me"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I should receive access denied

