## Installation


1. Require plugin with composer:

    ```bash
    composer require bitbag/vue-storefront2-plugin
    ```

2. Add plugin dependencies to your `config/bundles.php` file:

    ```php
        return [
         ...
        
            BitBag\SyliusVueStorefront2Plugin\BitBagSyliusVueStorefront2Plugin::class => ['all' => true],
        ];
    ```
   
3. Enable API
    In `config/packages/_sylius.yaml`
    ```yaml
        sylius_api:
            enabled: true
    ```

4. Add plugin mapping path to your `config/packages/api_platform.yaml` file as a last element:

    ```yaml
        api_platform:
            mapping:
                paths:
                    - '%kernel.project_dir%/vendor/bitbag/vue-storefront2-plugin/src/Resources/api_resources'
    ```
    

5. Add plugin serialization files path to your `config/packages/framework.yaml` file (Remeber to include here Your own serialization files path, without it - fields using serialization groups wont be visible in GraphQL Schema):

    ```yaml
        framework:    
            serializer:
                mapping:
                    paths:
                        - '%kernel.project_dir%/vendor/bitbag/vue-storefront2-plugin/src/Resources/serialization'
    ```

6. Import required config by adding  `config/packages/bitbag_sylius_vue_storefront2_plugin.yaml` file:

    ```yaml
    # config/packages/bitbag_sylius_vue_storefront2_plugin.yaml
    
    imports:
        - { resource: "@BitBagSyliusVueStorefront2Plugin/Resources/config/services.xml" }
    ```    
   
    There are 2 plugin parameters that You can adjust:
   
    ```yml
    bitbag_sylius_vue_storefront2:
        refresh_token_lifespan: 2592000 #that its default value
        test_endpoint: 'http://127.0.0.1:8080/api/v2/graphql' #that its default value
    ```
7. Add doctrine mapping to your `config/packages/doctrine.yaml` file:

    ```yml
    doctrine:
        orm:
            mappings:
                VueStorefront2:
                    is_bundle: false
                    type: xml
                    dir: '%kernel.project_dir%/vendor/bitbag/vue-storefront2-plugin/src/Resources/doctrine/model'
                    prefix: 'BitBag\SyliusVueStorefront2Plugin\Model'
                    alias: BitBag\SyliusVueStorefront2Plugin
    ```
   
8. In _sylius.yaml add mappings for product attribute and taxonomy repository so graphql can see them properly

    ```yml
    sylius_taxonomy:
       resources:
          taxon:
             classes:
                repository: BitBag\SyliusVueStorefront2Plugin\Doctrine\Repository\TaxonRepository
    ```
    
9. If you're already extending Sylius' `ProductAttributeValue` entity, please use our trait - `BitBag\SyliusVueStorefront2Plugin\Model\ProductAttributeValueTrait`, inside your own `ProductAttributeValue` entity. If you're not extending `ProductAttributeValue`, please create an entity, which uses the trait and setup the Sylius resource in _sylius.yaml:

```yml
    sylius_attribute:
        driver: doctrine/orm
        resources:
            product:
                subject: Sylius\Component\Core\Model\Product
                attribute_value:
                    classes:
                        model: App\Entity\ProductAttributeValue
```

10. Please add the Doctrine mapping configuration into your project:

If you are using xml mapping:

```xml
<?xml version="1.0" encoding="UTF-8"?>

<doctrine-mapping
    xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping
                            http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd"
>
    <entity name="App\Entity\ProductAttributeValue" table="sylius_product_attribute_value">
        <indexes>
            <index name="locale_code" columns="locale_code" />
        </indexes>
    </entity>
</doctrine-mapping>
```

**Please change the `name` attribute to fit your entity name. If you've already the `ProductAttributeValue` mapping in your project, just add there the `<index>` part of mapping above.**

If you are using annotations:

```
/**
 * @ORM\Entity
 * @ORM\Table(name="sylius_product_attribute_value",indexes={@Index(name="locale_code", columns={"locale_code"})})
 */
 ```

11. Import routing in `config/routes.yaml`

```yml
bitbag_sylius_vue_storefront2_plugin:
    resource: "@BitBagSyliusVueStorefront2Plugin/Resources/config/routing.yml"
   
```
12. After all steps, run this commends in your project directory:

```
yarn install
yarn build
```

