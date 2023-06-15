@resetting_password
Feature: Resetting one customer password
    In order to reset my password
    As a Customer
    I need to be able to reset it by reset query


    Background:
        Given the store operates on a single channel in "United States"
        And the store has locale "en_US"
        And there is a customer account "shop@example.com"

    @graphql
    Scenario: Setting token for password reset
        Given I prepare password reset request operation for user "shop@example.com"
        And I send that GraphQL request
        Then I should receive a JSON response
        And This response should contain pattern '/^\{"data":\{"shop_send_reset_password_emailCustomer":\{"customer":\{"id":"\/api\/v2\/shop\/customers\/(\d+)"\}\}\}\}$/'
        And user "shop@example.com" should have reset password token set

    @graphql
    Scenario: Checking for valid reset password token availability
        Given I prepare password reset request operation for user "shop@example.com"
        And I send that GraphQL request
        And I prepare check reset password token operation for user's "shop@example.com" token
        And I send that GraphQL request
        Then I should receive a JSON response
        And This response should contain pattern '/^\{"data":\{"password_reset_tokenUser":\{"username":"([^"]+)"\}\}\}$/'

    @graphql
    Scenario: Checking for invalid reset password token availability
        Given I prepare password reset request operation for user "shop@example.com"
        And I send that GraphQL request
        And I prepare check reset password token operation for invalid token
        And I send that GraphQL request
        Then I should receive a JSON response
        And This response should contain pattern '/^\{"data":\{"password_reset_tokenUser":null\}\}$/'
