@creating_account
Feature: Creating base customer account
    In order to persist data related to my purchases
    As a Customer
    I want to be able to create an account

    Background:
        Given the store operates on a single channel in "United States"

    @graphql
    Scenario: Creating account and logging in
        When I prepare register user operation
        And I provide first name "Adam"
        And I provide last name "Monroe"
        And I provide email "adam.monroe@mail.com"
        And I provide phone number "999666333"
        And I provide password "S3cretp@ssword"
        And I want to be subscribed to newsletter
        And I send that GraphQL request
        Then I should receive a JSON response
        And This response should contain 'user.username' equal to "adam.monroe@mail.com"
        And This response should contain 'user.customer.email' equal to "adam.monroe@mail.com"
        When I prepare login operation with username "adam.monroe@mail.com" and password "S3cretp@ssword"
        And I send that GraphQL request
        Then I should receive a JSON response
        And This response should contain 'shopUserToken.user.username' equal to "adam.monroe@mail.com"
        And This response should contain 'shopUserToken.token'
        And This response should contain 'shopUserToken.refreshToken'
