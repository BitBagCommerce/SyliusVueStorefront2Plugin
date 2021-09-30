@editing_account
Feature: Editing customer account
    In order to change my data
    As a Customer
    I want to be able to edit my account

    Background:
        Given the store operates on a single channel in "United States"
        And there is a customer "Francis Underwood" identified by an email "francis@underwood.com" and a password "whitehouse"
        And I create a JWT Token for customer identified by an email "francis@underwood.com"

    @graphql
    Scenario: Editing account
        When I prepare edit customer account operation
        And I set IRI of this customer request to match "francis@underwood.com"
        And I set 'lastName' field to "Johnny"
        And I want to be subscribed to newsletter
        And I set 'birthday' field to "2013-09-29T17:46:19-0700"
        And I set 'phoneNumber' field to "111222333"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response should contain 'customer.user.username'
        And This response should contain 'customer.phoneNumber' equal to "111222333"
        And This response should contain 'customer.birthday' equal to "2013-09-29"
        And This response should contain 'customer.subscribedToNewsletter' equal to false
        And This response should contain 'customer.firstName' equal to "Johnny"
        And This response should contain 'customer.lastName' equal to "Underwood"


