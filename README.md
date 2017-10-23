# Facebook Instant Articles PHP SDK #

[![Build Status](https://travis-ci.org/facebook/facebook-instant-articles-sdk-php.svg?branch=master)](https://travis-ci.org/facebook/facebook-instant-articles-sdk-php)
[![Latest Stable Version](https://poser.pugx.org/facebook/facebook-instant-articles-sdk-php/v/stable)](https://packagist.org/packages/facebook/facebook-instant-articles-sdk-php)

The Facebook Instant Articles SDK for PHP provides a native interface for creating and publishing Instant Articles. The SDK enables developers to more easily integrate Instant Articles into content management systems and in turn enables journalist and publishers to easily publish Instant Articles.

The SDK consists of three components:
- **Elements**: A domain-specific language for creating an Instant Articles structure that strictly follows the specification and can be automatically serialized into the subset of HTML5 markup used in the [Instant Articles format](https://developers.facebook.com/docs/instant-articles/reference). This language allows users to programmatically create Instant Articles that are guaranteed to be in compliance with the format.
- **Transformer**: An engine for transforming any markup into an Instant Article structure in the DSL. The engine runs a set of rules on the markup that will specify the selection and transformation of elements output by the CMS into their Instant Articles counterparts. The transformer ships with a base set of rules for common elements (such as a basic paragraph or an image) that can be extended and customized by developers utilizing the SDK.
- **Client**: A simple wrapper around the [Instant Articles API](https://developers.facebook.com/docs/instant-articles/api), which can be used for publishing Instant Articles on Facebook. The client provides a CRUD interface for Instant Articles as well as a helper for authentication. The client depends on the main [Facebook SDK for PHP](https://github.com/facebook/facebook-php-sdk-v4) as an interface to the Graph API and Facebook Login.

## Quick Start

The Facebook Instant Articles PHP SDK can be installed with the [Composer](https://getcomposer.org/) dependency manager by running this command on your project's root folder:

```sh
$ composer require facebook/facebook-instant-articles-sdk-php
```

After the installation, you can include the auto loader script in your source with:

```PHP
require_once('vendor/autoload.php');
```

## Official Documentation
You can find examples on how to use the different components of this SDK to integrate it with your CMS in the [Getting Started](https://developers.facebook.com/docs/instant-articles/sdk/#getting-started) section of the documentation.

## Contributing

Clone the repository
```sh
$ git clone https://github.com/facebook/facebook-instant-articles-sdk-php.git
```

[Composer](https://getcomposer.org/) is a prerequisite for testing and developing. [Install composer globally](https://getcomposer.org/doc/00-intro.md#globally), then install project dependencies by running this command in the project's root directory:

```sh
$ hhvm -d hhvm.php7.all=1 `which composer` install
```

To run the tests:

```sh
$ hhvm -d hhvm.php7.all=1 vendor/bin/phpunit tests
```

To fix and check for coding style issues:

```sh
$ hhvm -d hhvm.php7.all=1 `which composer` cs
```

Extra lazy? Run

```sh
$ hhvm -d hhvm.php7.all=1 `which composer` all
```

to fix and check for coding style issues, and run the tests.

If you change structure, paths, namespaces, etc., make sure you run the [autoload generator](https://getcomposer.org/doc/03-cli.md#dump-autoload):
```sh
$ hhvm -d hhvm.php7.all=1 `which composer` dump-autoload
```

___
**For us to accept contributions you will have to first sign the [Contributor License Agreement](https://code.facebook.com/cla). Please see [CONTRIBUTING](https://github.com/facebook/facebook-instant-articles-sdk-php/blob/master/CONTRIBUTING.md) for details.**
___
## Testing with Docker

We have provide a Docker environment to ensure a consistent testing & development environment.

Use `docker-compose run composer composer install` to get started.

## Troubleshooting

If you are encountering problems, the following tips may help in troubleshooting issues:

- Warnings from the Transformer can be seen with `$transformer->getWarnings()` method.

- If content is missing from your transformed article, more likely than not there isn't a **Transformer Rule** matching an element in your source markup. See how to configure appropriate rules for your content in the [Transformer Rules documentation](https://developers.facebook.com/docs/instant-articles/sdk/transformer-rules).

- Set the `threshold` in the [configuration of the Logger](https://logging.apache.org/log4php/docs/configuration.html#PHP) to `DEBUG` to expose more details about the items processed by the Transformer.

- Refer to the existing [tests of the `Elements`](https://github.com/facebook/facebook-instant-articles-sdk-php/tree/master/tests/Facebook/InstantArticles/Elements) for examples of what is required of each and to potentially create your own tests (which can be run with `$ composer test`).

## License

Please see the [license file](https://github.com/facebook/facebook-instant-articles-sdk-php/blob/master/LICENSE) for more information.
