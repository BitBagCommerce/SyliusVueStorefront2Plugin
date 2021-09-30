@changing_password
Feature: Changing one customer password
    In order to change my password
    As a Customer
    I need to be able to confirm new one and provide old one

    Background:
        Given the store operates on a single channel in "United States"
        And there is a customer "Frank Horrigan" identified by an email "frankh@enclave.com" and a password "semperfi"
        And I create a JWT Token for customer identified by an email "frankh@enclave.com"

    @graphql
    Scenario: Changing password
        When I prepare change customer password operation
        And I set id of this user request to match "frankh@enclave.com"
        And I set 'currentPassword' field to "semperfi"
        And I set 'newPassword' field to "oilrig"
        And I set 'confirmNewPassword' field to "oilrig"
        When I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I prepare refresh JWT Token operation
        When I send that GraphQL request as authorised user
        Then This response should contain 'shopUserToken.token'
        When I prepare login operation with username "frankh@enclave.com" and password "oilrig"
        And I send that GraphQL request
        Then I should receive a JSON response
        And This response should contain 'shopUserToken.user.username' equal to "frankh@enclave.com"
