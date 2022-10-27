# [![](https://bitbag.io/wp-content/uploads/2022/10/SyliusVueStorefront2Plugin-1.png )](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_graphql)

# BitBag SyliusVueStorefront2Plugin

[![](https://img.shields.io/packagist/l/bitbag/vue-storefront2-plugin.svg) ](https://packagist.org/packages/bitbag/vue-storefront2-plugin "License") [ ![](https://img.shields.io/packagist/v/bitbag/vue-storefront2-plugin.svg) ](https://packagist.org/packages/bitbag/vue-storefront2-plugin "Version") [ ![](https://img.shields.io/github/workflow/status/BitBagCommerce/SyliusVueStorefront2Plugin/Build) ](https://github.com/BitBagCommerce/SyliusVueStorefront2Plugin/actions "Build status") [![](https://poser.pugx.org/bitbag/vue-storefront2-plugin/downloads)](https://packagist.org/packages/bitbag/vue-storefront2-plugin "Total Downloads") [![Slack](https://img.shields.io/badge/community%20chat-slack-FF1493.svg)](http://sylius-devs.slack.com) [![Support](https://img.shields.io/badge/support-contact%20author-blue])](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_graphql)

Sylius Vue Storefront 2 back-end integration enabling PWA and mobile-first experience in Sylius-based stores.

The plugin was developed by BitBag, the leading and biggest Sylius partner. We breath open-source in and out. If you like what we do, feel free to [contact us](https://bitbag.io/contact-us). We are opened for partnership realtions and if you look for an experienced, open-source team that will be able to deliver an outstanding eCommerce solution, feel free to reach us too!

Like the package? Do not forget to leave us a star! ⭐

## Table of Content

* [Overview](#overview)
* [Support](#we-are-here-to-help)
* [About us](#about-us)
    * [Community](#community)
* [Demo](https://vsf2-demo.bitbag.io/)
* [License](#license)
* [Contact](#contact)
* [Installation](doc/installation.md)
* [Customization & Testing](doc/customization.md)

## Overview

This plugin allows you to integrate Sylius backend with [Vue Storefront 2 integration](https://github.com/BitBagCommerce/SyliusVueStorefront2Frontend).

This plugin allows you to expose all critical models and operations as graphql queries and mutations<br />
It unlocks graphql-based API on top of ApiPlatform and JWT.

To check automatically generated GraphQL API Doc for your Vue Storefront 2 application - visit one of the following endpoints.

```
  api_graphql_entrypoint              /api/v2/graphql                                                                   
  api_graphql_graphiql                /api/v2/graphql/graphiql                                                          
  api_graphql_graphql_playground      /api/v2/graphql/graphql_playground
```

Please note that many of used functions and approaches both on Sylius and API Platform with Graphql support are still marked as experimental.

### Requirements

The plugin uses following 3rd party packages: 


- `"sylius/sylius": "~1.11.0"`<br />
- `"webonyx/graphql-php": "^14.9"`<br />
- `"gesdinet/jwt-refresh-token-bundle": "^0.12.0"`


## Community

For online communication, we invite you to chat with us & other users on [Sylius Slack](https://sylius-devs.slack.com/).

## Additional resources for developers

To learn more about our contribution workflow and more, we encourage you to use the following resources:
* [Sylius Documentation](https://docs.sylius.com/en/latest/)
* [Sylius Contribution Guide](https://docs.sylius.com/en/latest/contributing/)
* [Sylius Online Course](https://sylius.com/online-course/)

## License

This plugin's source code is completely free and released under the terms of the MIT license.


## Contact

If you want to contact us, the best way to do it is over [our contact form](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_graphql) or by sending us a direct e-mail at hello@bitbag.io.

[![](https://bitbag.io/wp-content/uploads/2021/08/badges-bitbag.png)](https://bitbag.io/contact-us/?utm_source=github&utm_medium=referral&utm_campaign=plugins_graphql)
