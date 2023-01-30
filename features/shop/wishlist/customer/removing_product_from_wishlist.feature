@wishlist_as_customer
Feature: Adding a product to a wishlist
    In order to add a product to a wishlist
    As a customer
    I want to be able to add products to wishlists

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Harness climbing" priced at "$30.00"
        And the store has a product "Rope" priced at "$40.00"
        And there is a customer "Alex Honnold" identified by an email "ahonnold@climb.com" and a password "dlonnoh1"
        And user "ahonnold@climb.com" has a wishlist named "For Alex"
        And user have a product "ROPE" in my wishlist "For Alex"
        And there is a customer "Adam Ondra" identified by an email "aondra@climb.com" and a password "ardno1"
        And user "aondra@climb.com" has a wishlist named "For me"
        And user have a product "ROPE" in my wishlist "For me"
        And I create a JWT Token for customer identified by an email "aondra@climb.com"

    @graphql
    Scenario: Removing products from a wishlist
        And user have a product "HARNESS_CLIMBING" in my wishlist "For me"

        When I prepare query to fetch all wishlists
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I should receive 1 wishlists
        And This response should contain "collection.0.wishlistProducts.totalCount" equal to 2 as "int"
        And This response should contain "collection.0.wishlistProducts.edges.0.node.variant.name" equal to "Rope"
        And This response should contain "collection.0.wishlistProducts.edges.1.node.variant.name" equal to "Harness climbing"
        And I save key 'collection.0.wishlistProducts.edges.0.node.variant.id' of this response as "firstProductVariantIri"
        And I save key 'collection.0.wishlistProducts.edges.1.node.variant.id' of this response as "secondProductVariantIri"

        When I prepare remove product from wishlist operation
        And I set id field to iri object "For me"
        And I set productVariant field to value "firstProductVariantIri"
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response should contain "wishlist.wishlistProducts.totalCount" equal to 1 as "int"
        And This response should contain "wishlist.wishlistProducts.edges.0.node.variant.name" equal to "Harness climbing"

        When I prepare remove product from wishlist operation
        And I set id field to iri object "For me"
        And I set productVariant field to value "secondProductVariantIri"
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And This response should contain "wishlist.wishlistProducts.totalCount" equal to 0 as "int"

    @graphql
    Scenario: Removing a product from another user's wishlist
        When I prepare query to fetch all products
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I save key 'collection.0.variants.collection.0.id' of this response as "productVariantIri"

        When I prepare remove product from wishlist operation
        And I set id field to iri object "For Alex"
        And I set productVariant field to value "productVariantIri"
        And I send that GraphQL request as authorised user
        Then I should receive a JSON response
        And I should receive access denied
