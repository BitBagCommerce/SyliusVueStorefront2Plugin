@managing_addresses
Feature: Managing customer addresses
    In order to manage my addresses
    As a Customer
    I need to be able to create, edit and delete them

    Background:
        Given the store operates on a single channel in "United States"
        And there is a customer "Robert Random" identified by an email "rr@somemail.com" and a password "justarandompassword"
        And customer identified by an email "rr@somemail.com" has an address
        And I create a JWT Token for customer identified by an email "rr@somemail.com"
# commented until functionality is fixed / implemented
#    @graphql
#    Scenario: Adding new address
#        When I prepare add address operation
#        And I set 'firstName' field to "Robert"
#        And I set 'lastName' field to "Random"
#        And I set 'phoneNumber' field to "330-583-2807"
#        And I set 'company' field to "Berg"
#        And I set 'countryCode' field to "US"
#        And I set 'provinceName' field to "Ohio"
#        And I set 'street' field to "Robert"
#        And I set 'city' field to "Greene"
#        And I set 'postcode' field to "44450"
#        When I send that GraphQL request as authorised user
#        Then I should receive a JSON response
#        And I save key 'address.id' of this response as "firstAddressIdentifier"
#        When I prepare get address collection operation

    @graphql
    Scenario: Editing address book
        When I prepare edit address operation
        And I set 'id' field to value "customerAddressIri"
        And I set 'company' field to "Bergen"
        And I set 'street' field to "Some Street"
        And I set 'city' field to "Yellow"
        And I set 'firstName' field to "Bobby"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I save key 'address.id' of this response as "firstAddressIdentifier"
        And This response should contain "address.company" equal to "Bergen"
        And This response should contain "address.city" equal to "Yellow"
        And This response should contain "address.firstName" equal to "Bobby"
        And This response should contain "address.lastName" equal to "Random"

    @graphql
    Scenario: Fetching address list
        When I prepare operation to fetch collection of user addresses
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response should contain "collection.0.firstName" equal to "Robert"
        And This response should contain "collection.0.lastName" equal to "Random"

    @graphql
    Scenario: Deleting address from list
        When I prepare delete address operation
        And I set 'id' field to value "customerAddressIri"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response should contain "clientMutationId"
        When I prepare operation to fetch collection of user addresses
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response should contain empty "collection"
