@place_order_guest
Feature: Submitting order
    In order buy a product
    As a guest
    I need to be able to place an order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Jack Daniels Gentleman" priced at "$30.00"
        And the store has a product "Jim Beam" priced at "$20.00"
        And the store allows shipping with "Pickup" identified by "pickup"
        And the store has a payment method "Cash" with a code "cash"
        And the store has promotion "Black Friday" with coupon "bfriday"
        And this promotion gives "50%" discount to every order

    @graphql
    Scenario: Placing order
        When I prepare query to fetch all products
        And I send that GraphQL request
        Then I should receive a JSON response
        And I save key 'collection.0.variants.collection.0.id' of this response as "firstProductVariantIri"
        And I save key 'collection.1.variants.collection.0.id' of this response as "secondProductVariantIri"

        When I prepare create cart operation
        Then I send that GraphQL request
        And I save key 'order.id' of this response as "orderId"
        And I save key 'order.tokenValue' of this response as "orderToken"

        When I prepare add product to cart operation
        And I set 'quantity' field to integer 2
        And I set 'productVariant' field to value "firstProductVariantIri"
        And I set 'id' field to value "orderId"
        Then I send that GraphQL request
        And I save key 'order.items.edges.0.node._id' of this response as "firstOrderItemId" as "string"

        When I prepare add product to cart operation
        And I set 'quantity' field to integer 1
        And I set 'productVariant' field to value "secondProductVariantIri"
        And I set 'id' field to value "orderId"
        Then I send that GraphQL request
        And I save key 'order.items.edges.1.node._id' of this response as "secondOrderItemId" as "string"
        And total price for items should equal to "8000"

        When I prepare remove product from cart operation
        And I set 'id' field to value "orderId"
        And I set 'orderItemId' field to value "firstOrderItemId"
        Then I send that GraphQL request
        And total price for items should equal to "2000"

        When I prepare operation to add order shipping address
        And I set 'email' field to "john.doe@mail.com"
        And I set 'orderTokenValue' field to value "orderToken"
        And I set 'shippingAddress' object "firstName" property to "John"
        And I set 'shippingAddress' object "lastName" property to "Doe"
        And I set 'shippingAddress' object "countryCode" property to "US"
        And I set 'shippingAddress' object "city" property to "Chicago"
        And I set 'shippingAddress' object "street" property to "Sunny Street"
        And I set 'shippingAddress' object "postcode" property to "45222"
        And I set shippingAddress field to value "shippingAddress"
        And I send that GraphQL request
        And This response should contain "order.shippingAddress.firstName" equal to "John"
        And I save key 'order.shipments.edges.0.node._id' of this response as "orderShipmentId"

        When I prepare operation to add order billing address
        And I set 'email' field to "jane.doe@mail.com"
        And I set 'orderTokenValue' field to value "orderToken"
        And I set 'billingAddress' object "firstName" property to "Jane"
        And I set 'billingAddress' object "lastName" property to "Doe"
        And I set 'billingAddress' object "countryCode" property to "US"
        And I set 'billingAddress' object "city" property to "Chicago"
        And I set 'billingAddress' object "street" property to "Sunny Street"
        And I set 'billingAddress' object "postcode" property to "45222"
        And I set billingAddress field to value "billingAddress"
        And I send that GraphQL request
        And This response should contain "order.billingAddress.firstName" equal to "Jane"
        And I save key 'order.payments.edges.0.node._id' of this response as "orderPaymentId"

        When I prepare operation to select shipping method
        And I set 'orderTokenValue' field to value "orderToken"
        And I set 'shipmentId' field to previously saved 'string' value "orderShipmentId"
        And I set 'shippingMethodCode' field to "pickup"
        And I send that GraphQL request
        And This response should contain "order.shipments.edges.0.node.method.code" equal to "pickup"

        When I prepare operation to select payment method
        And I set 'orderTokenValue' field to value "orderToken"
        And I set 'paymentId' field to previously saved 'string' value "orderPaymentId"
        And I set 'paymentMethodCode' field to "cash"
        And I send that GraphQL request
        And This response should contain "order.payments.edges.0.node.method.code" equal to "cash"

        When I prepare operation to add promotion coupon "bfriday"
        And I set 'orderTokenValue' field to value "orderToken"
        And I send that GraphQL request
        Then I should receive a JSON response
        And This response should contain "order.orderPromotionTotal"
        And This response should contain "order.promotionCoupon.code" equal to "bfriday"

        When I prepare operation to submit order with note "This is some note"
        And I set 'orderTokenValue' field to value "orderToken"
        And I set 'id' field to value "orderId"
        And I send that GraphQL request
        Then I should receive a JSON response
        And This response should contain "order.id"
        And This response should contain "order._id"
        And This response should contain "order.billingAddress.firstName" equal to "Jane"
        And This response should contain "order.shippingAddress.firstName" equal to "John"
        And This response should contain "order.currencyCode" equal to "USD"
        And This response should contain "order.checkoutState" equal to "completed"
        And This response should contain "order.paymentState" equal to "awaiting_payment"
        And This response should contain "order.shippingState" equal to "ready"
        And This response should contain "order.shippingTotal"
        And This response should contain "order.total"
