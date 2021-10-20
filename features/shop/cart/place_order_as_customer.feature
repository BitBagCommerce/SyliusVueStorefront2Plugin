@place_order_customer
Feature: Submitting order
    In order buy a product
    As a Customer
    I need to be able to place an order

    Background:
        Given the store operates on a single channel in "United States"
        And the store allows shipping with "UPS" identified by "ups"
        And the store has a payment method "Cash On Delivery" with a code "cash"
        And the store has promotion "Sale" with coupon "Crystal"
        And this promotion gives "10%" discount to every order
        And there is a customer "Gordon Freeman" identified by an email "gfreeman@resistance.com" and a password "gg3883"
        And I create a JWT Token for customer identified by an email "gfreeman@resistance.com"
        And customer identified by an email "gfreeman@resistance.com" has an address
        And the store has a product "HEV Suit" priced at "$1000.00"
        And the store has a product "Glasses" priced at "$10.00"

    @graphql
    Scenario: Placing order
        When I prepare query to fetch all products
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I save key 'collection.0.variants.collection.0.id' of this response as "firstProductVariantIri"
        And I save key 'collection.1.variants.collection.0.id' of this response as "secondProductVariantIri"

        When I prepare create cart operation
        Then I send that GraphQL request as authorised user
        And I save key 'order.id' of this response as "orderId"
        And I save key 'order.tokenValue' of this response as "orderToken"

        When I prepare add product to cart operation
        And I set 'quantity' field to integer 2
        And I set 'productVariant' field to value "firstProductVariantIri"
        And I set 'orderTokenValue' field to value "orderToken"
        Then I send that GraphQL request as authorised user
        And I save key 'order.items.edges.0.node._id' of this response as "firstOrderItemId"

        When I prepare add product to cart operation
        And I set 'quantity' field to integer 1
        And I set 'productVariant' field to value "secondProductVariantIri"
        And I set 'orderTokenValue' field to value "orderToken"
        Then I send that GraphQL request as authorised user
        And I save key 'order.items.edges.1.node._id' of this response as "secondOrderItemId" as "string"
        And total price for items should equal to "102000"

        When I prepare remove product from cart operation
        And I set 'id' field to value "orderId"
        And I set 'orderItemId' field to previously saved "string" value "firstOrderItemId"
        Then I send that GraphQL request as authorised user
        And total price for items should equal to "100000"

        When I prepare operation to fetch collection of user addresses
        And I send that GraphQL request as authorised user
        Then This response should contain "collection.0.id"
        And I save key 'collection.0.id' of this response as "userAddressShipping"

        When I prepare operation choose address saved as "userAddressShipping" as shipping
        And I set 'email' field to "gfreeman@resistance.com"
        And I set 'orderTokenValue' field to value "orderToken"
        And I send that GraphQL request as authorised user
        And This response should contain "order.shippingAddress.firstName" equal to "Gordon"
        And I save key 'order.shipments.edges.0.node._id' of this response as "orderShipmentId"

        When I prepare operation to add order billing address
        And I set 'email' field to "gfreeman@resistance.com"
        And I set 'orderTokenValue' field to value "orderToken"
        And I set 'billingAddress' object "firstName" property to "Alyx"
        And I set 'billingAddress' object "lastName" property to "Vance"
        And I set 'billingAddress' object "countryCode" property to "RU"
        And I set 'billingAddress' object "city" property to "City 17"
        And I set 'billingAddress' object "street" property to "Citadel Street"
        And I set 'billingAddress' object "postcode" property to "11222"
        And I set billingAddress field to value "billingAddress"
        And I send that GraphQL request as authorised user
        And This response should contain "order.billingAddress.firstName" equal to "Alyx"
        And I save key 'order.payments.edges.0.node._id' of this response as "orderPaymentId"

        When I prepare operation to select shipping method
        And I set 'orderTokenValue' field to value "orderToken"
        And I set 'shipmentId' field to previously saved 'string' value "orderShipmentId"
        And I set 'shippingMethodCode' field to "ups"
        And I send that GraphQL request as authorised user
        And This response should contain "order.shipments.edges.0.node.method.code" equal to "ups"

        When I prepare operation to select payment method
        And I set 'orderTokenValue' field to value "orderToken"
        And I set 'paymentId' field to previously saved 'string' value "orderPaymentId"
        And I set 'paymentMethodCode' field to "cash"
        And I send that GraphQL request as authorised user

        When I prepare operation to add promotion coupon "Crystal"
        And I set 'orderTokenValue' field to value "orderToken"
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response should contain "order.orderPromotionTotal"
        And This response should contain "order.promotionCoupon.code" equal to "Crystal"

        When I prepare operation to submit order with note "This is some note"
        And I set 'orderTokenValue' field to value "orderToken"
        And I set 'id' field to value "orderId"
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response should contain "order.id"
        And This response should contain "order._id"
        And This response should contain "order.billingAddress.firstName" equal to "Alyx"
        And This response should contain "order.shippingAddress.firstName" equal to "Gordon"
        And This response should contain "order.currencyCode" equal to "USD"
        And This response should contain "order.checkoutState" equal to "completed"
        And This response should contain "order.paymentState" equal to "awaiting_payment"
        And This response should contain "order.shippingState" equal to "ready"
        And This response should contain "order.shippingTotal"
        And This response should contain "order.total"
