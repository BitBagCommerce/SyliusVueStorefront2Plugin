@managing_addresses
Feature: Managing customer addresses
    In order to manage my addresses
    As a Customer
    I need to be able to create, edit and delete them

    Background:
        Given the store operates on a single channel in "United States"

    @graphql
    Scenario: Changing password
        When I have the following GraphQL request:
        """
        mutation shop_postCustomer ($input: shop_postCustomerInput!) {
            shop_postCustomer(input: $input){
		        customer{
                    _id
                }
            }
        }
        """
        And I prepare the variables for GraphQL request with saved data:
        """
        {
            "input":{
                "firstName": "John",
                "lastName": "Doe",
                "email": "john.doe@example.org",
                "phoneNumber": "+44 123 123 789",
                "subscribedToNewsletter": false,
                "password": "S3cret"
            }
        }
        """
        Then I save value at key "customer._id" from last response as "customerId".

        When I have the following GraphQL request:
        """
        mutation shop_password_updateCustomer ($input: shop_password_updateCustomerInput!) {
            shop_password_updateCustomer(input: $input){
                customer{
                    email
                }
            }
        }
        """
        And I prepare the variables for GraphQL request with saved data:
        """
        {
            "input": {
                "id": "{customerId}",
                "currentPassword": "S3cret",
                "newPassword": "SuperS4cret",
                "confirmNewPassword": "SuperS4cret",
            }
        }
        """
        Then I should see following response:
        """
        {
            "data": {
                "shop_password_updateCustomer": {
                    "customer": {
                        "email": "john.doe@example.org",
                    }
                }
            }
        }
        """
