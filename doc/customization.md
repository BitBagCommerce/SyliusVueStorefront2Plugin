## Customization
***

### Available services you can [decorate](https://symfony.com/doc/current/service_container/service_decoration.html) and forms you can [extend](http://symfony.com/doc/current/form/create_form_type_extension.html)
```bash
$ bin/console debug:container | grep bitbag_sylius_graphql
```

### Parameters you can override in your parameters.yml(.dist) file
```bash
$ bin/console debug:container --parameters | grep bitbag
```

##Hints

1. While adding some resource to GraphQL - be sure you are using proper model, the safest way would be to use  %sylius.model.<model>.class% notation. 
   
    They are defined in _sylius.yaml like
    ```yml
    sylius_promotion:
        resources:
            promotion_coupon:
                classes:
                    model: Sylius\Component\Core\Model\PromotionCoupon
            promotion:
                classes:
                    model: Sylius\Component\Core\Model\Promotion
    ```

    That can be sometimes tricky as GraphQL is not following inheritance and sometimes default models point to specific components e.g. 
    
    Some components like Promotion have defined their models (`%sylius.model.promotion.class%`) as class from specific Component by default (check promotion component extension)

    Serializer would then expect `Sylius\Component\Promotion\Model\Promotion` on output on every mutation when You could expect `Sylius\Component\Core\Model\Promotion` as the models from Core extends the models from distinct Components. 

    For GraphQL at the moment `Sylius\Component\Core\Model\Promotion` =/= `Sylius\Component\Promotion\Model\Promotion`


2. Why my properties are not visible as available to query ?
   
    There might be few options:

    * Forgot to add property to api_resources/YourModel.xml

    * Forgot to add property to serialization in serialization/YourModel.xml with proper groups

    In Your api_resources/YourModel.xml  You have something like:

    ```xml
    <attribute name="normalization_context">
        <attribute name="groups">
            <attribute>admin:order:read</attribute>
            <attribute>shop:cart:read</attribute>
        </attribute>
    </attribute>
    ```
    
    That’s changing available properties and if Your application does not need to have strict serialization groups (e.g. security wise) 
    for the sake of GraphQL we would suggest dropping it as the whole point is to allow client to browse what he needs.


3.  Serializing methods
    
    Add normalization context in api_resources part for resource description
    
    ```xml
    <attribute name="normalization_context">
        <attribute name="groups">some:group:read</attribute>
    </attribute>
    ```
    
    Add property in same file
    
    ```xml
    <property name="value" readable="true" />
    ```
    
    Your model has to have getter with return type specified at least by @return tag
    
    ```php
    /**
    * @return string|null
    */
    public function getValue(): ?string
    {
    //logic...
    }
    ```

    Add serializer config for that model
    ```xml
    <?xml version="1.0" ?>
    
    <serializer xmlns="http://symfony.com/schema/dic/serializer-mapping"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://symfony.com/schema/dic/serializer-mapping https://symfony.com/schema/dic/serializer-mapping/serializer-mapping-1.0.xsd"
    >
        <class name="Your\Class\Here">
            <attribute name="value">
                <group>some:group:read</group>
            </attribute>
        </class>
    </serializer>
    ```


## Testing
***
```bash
$ composer install
$ cd tests/Application
$ yarn install
$ yarn run gulp
$ bin/console assets:install public -e test
$ bin/console doctrine:schema:create -e test

$ bin/console server:run 127.0.0.1:8000 -d public -e test
OR
$ symfony server:start -d --dir=public

$ open http://127.0.0.1:8000
$ vendor/bin/behat
$ vendor/bin/phpspec run
$ vendor/bin/phpstan analyse -c phpstan.neon -l max src/
```
