## Installation


1. Require plugin with composer:

    ```bash
    composer require bitbag/graphql-plugin
    ```

2. Add plugin dependencies to your `config/bundles.php` file:

    ```php
        return [
         ...
        
            BitBag\SyliusGraphqlPlugin\BitBagSyliusGraphqlPlugin::class => ['all' => true],
        ];
    ```
   
3. Enable API
    In `config/services.yaml`
    ```yaml
        sylius_api:
            enabled: true
    ```

4. Add plugin mapping path to your `config/packages/api_platform.yaml` file as a last element:

    ```yaml
        api_platform:
            mapping:
                paths:
                    - '%kernel.project_dir%/vendor/bitbag/graphql-plugin/src/Resources/api_resources'
    ```
    

5. Add plugin serialization files path to your `config/packages/framework.yaml` file (Remeber to include here Your own serialization files path, without it - fields using serialization groups wont be visible in GraphQL Schema):

    ```yaml
        framework:    
            serializer:
                mapping:
                    paths:
                        - '%kernel.project_dir%/vendor/bitbag/graphql-plugin/src/Resources/serialization'
    ```

6. Import required config by adding  `config/packages/bitbag_sylius_graphql_plugin.yaml` file:

    ```yaml
    # config/packages/bitbag_sylius_graphql_plugin.yaml
    
    imports:
        - { resource: "@BitBagSyliusGraphqlPlugin/Resources/config/services.xml" }
    ```    
   
    There are 2 plugin parameters that You can adjsut:
   
    ```yml
    bitbag_sylius_graphql:
        refresh_token_lifespan: 2592000 #that its default value
        test_endpoint: 'http://127.0.0.1:8080/api/v2/graphql' #that its default value
    ```
7. Add doctrine mapping:

    ```yml
    doctrine:
        orm:
            mappings:
                GraphqQL:
                    is_bundle: false
                    type: xml
                    dir: '%kernel.project_dir%/vendor/bitbag/graphql-plugin/src/Resources/doctrine/model'
                    prefix: 'BitBag\SyliusGraphqlPlugin\Model'
                    alias: BitBag\SyliusGraphqlPlugin
    ```
   
8. In _sylius.yaml add mappings for product attribute and taxonomy repository so graphql can see them properly

    ```yml
    sylius_attribute:
        driver: doctrine/orm
        resources:
            product:
                subject: Sylius\Component\Core\Model\Product
                attribute_value:
                    classes:
                        model: BitBag\SyliusGraphqlPlugin\Model\ProductAttributeValue
    
    sylius_taxonomy:
       resources:
          taxon:
             classes:
                repository: BitBag\SyliusGraphqlPlugin\Doctrine\Repository\TaxonRepository
    ```

9. Import routing in routes.yaml

    ```yml
    bitbag_sylius_graphql_plugin:
        resource: "@BitBagSyliusGraphqlPlugin/Resources/config/routing.yml"
   ```
