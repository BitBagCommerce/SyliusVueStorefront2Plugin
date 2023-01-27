@add_products_to_cart
Feature: Add multiple products to cart
    In order buy a products
    As a Customer
    I need to be able to add multiple prodcuts to cart

    Background:
        Given the store operates on a single channel in "United States"
        And there is a customer "Adam Ondra" identified by an email "aondra@climb.com" and a password "ardno1"
        And I create a JWT Token for customer identified by an email "aondra@climb.com"
        And the store has a product "Harness climbing" priced at "$30.00"
        And the store has a product "Rope" priced at "$40.00"

    @graphql
    Scenario: Placing order
        When I prepare query to fetch all products
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I save key 'collection.0.variants.collection.0.id' of this response as "firstProductVariantIri"
        And I save key 'collection.1.variants.collection.0.id' of this response as "secondProductVariantIri"

        When I prepare create cart operation
        Then I send that GraphQL request as authorised user
        And I save key 'order.tokenValue' of this response as "orderToken"

        When I prepare add products to cart operation
        And I set 'orderTokenValue' field to value "orderToken"
        And I set 'cartItemFirst' object "quantity" property to 2 as 'int'
        And I set 'cartItemFirst' object "productVariant" property to previously saved value "firstProductVariantIri"
        And I set 'cartItemSecond' object "quantity" property to 3 as 'int'
        And I set 'cartItemSecond' object "productVariant" property to previously saved value "secondProductVariantIri"
        And I set 'cartItems' object "0" property to previously saved value "cartItemFirst"
        And I set 'cartItems' object "1" property to previously saved value "cartItemSecond"
        And I set cartItems field to value "cartItems"
        Then I send that GraphQL request as authorised user
        And This response should contain "order.items.edges.0.node.productName" equal to "Harness climbing"
        And This response should contain "order.items.edges.0.node.quantity" equal to 2 as 'int'
        And This response should contain "order.items.edges.1.node.productName" equal to "Rope"
        And This response should contain "order.items.edges.1.node.quantity" equal to 3 as 'int'
        And total price for items should equal to "18000"
