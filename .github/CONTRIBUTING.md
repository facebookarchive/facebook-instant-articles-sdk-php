# Contributing to Facebook Instant Articles PHP SDK
We want to make contributing to this project as easy and transparent as possible.

We accept contributions via pull requests on [GitHub](https://github.com/facebook/facebook-instant-articles-sdk-php).

## Pull Requests
- **Sign the CLA** - In order to accept your pull request, we need you to submit a [Contributor License Agreement](https://code.facebook.com/cla). You only need to do this once to work on any of Facebook's open source projects.

- **[PSR-2 Coding Standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md)** - The easiest way to apply the conventions is to run [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer) as you code.

- **Add tests** - If you've added code that can be tested, add tests.

- **Document any change in behaviour** - Make sure the README and the [documentation](https://github.com/facebook/facebook-instant-articles-sdk-php/tree/master/docs) are kept up-to-date.

- **Consider our release cycle** - We try to follow [SemVer](http://semver.org/). Randomly breaking public APIs is not an option.

- **Create topic branches** - Don't ask us to pull from your master branch.

- **One pull request per feature** - If you want to do more than one thing, send multiple pull requests.

- **Send coherent history** - Make sure each individual commit in your pull request is meaningful. If you had to make multiple intermediate commits while developing, please squash them before submitting.

- **Ensure tests pass!** - Please [run the tests](https://github.com/facebook/facebook-instant-articles-sdk-php#testing-and-developing) before submitting your pull request, and make sure they pass. We won't accept a patch until all tests pass.

- **Ensure no coding standards violations** - Please run [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer) using the PSR-2 standard before submitting your pull request. A violation will cause the build to fail, so please make sure there are no violations. We can't accept a patch if the build fails.


## Issues
We use GitHub issues to track public bugs. Please ensure your description is clear and has sufficient instructions to be able to reproduce the issue.


## Running Tests

``` bash
$ ./vendor/bin/phpunit
```

When doing a pull request, consider if this diff has a testcase that was covered in a wrong way or if it needs a new test case.


## Running PHP Code Sniffer

You can install [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer) globally with composer.

``` bash
$ composer global require squizlabs/php_codesniffer
```

Then you can `cd` into the Facebook Instant Articles PHP SDK folder and run Code Sniffer against the `src/` directory.

``` bash
$ ~/.composer/vendor/bin/phpcs src --standard=psr2 -sp
$ ~/.composer/vendor/bin/phpcs tests --standard=psr2 -sp
```

Give a try for the autofixer for code style

``` bash
$ ~/.composer/vendor/bin/phpcbf src --standard=psr2 -sp
$ ~/.composer/vendor/bin/phpcbf tests --standard=psr2 -sp
```
**Happy coding**!
