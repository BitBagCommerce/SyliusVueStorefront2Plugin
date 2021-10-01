@writing_review
Feature: Writing product review
    In order to review a product
    As a Customer who bought some product
    I need to be able to submit, edit and delete its review

    Background:
        Given the store operates on a single channel in "United States"

    @graphql
    Scenario: Writing review
