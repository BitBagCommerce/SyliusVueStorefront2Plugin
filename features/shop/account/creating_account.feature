@creating_account
Feature: Creating base customer account
    In order to persist data related to my purchases
    As a Customer
    I want to be able to create an account

    Background:
        Given the store operates on a single channel in "United States"

    @graphql
    Scenario: Creating account
        When I create a GraphQL request
        And That request is for creating account
        And I supply 'firstName' field with ""
        And I supply 'lastName' field with ""
        And I supply 'email' field with ""
        And I provide 'password' field with ""
        And I set 'subscribedToNewsletter' field to true
        And I provide 'phoneNumber' field with ""
        Then I send that GraphQL request
        Then I should receive a JSON response
        And This response should contain 'user.username'
        And This response should contain 'user.customer.email'


