# Imagine Plugin for CakePHP 2

[![GitHub License](https://img.shields.io/github/license/pieceofcake2/imagine?label=License)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/pieceofcake2/imagine?label=Packagist)](https://packagist.org/packages/pieceofcake2/imagine)
![PHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/imagine/php?logo=php&logoColor=%23FFFFFF&label=PHP&labelColor=%23777BB4&color=%23FFFFFF)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%205-brightgreen?label=PHPStan)](https://github.com/pieceofcake2//Users/tsuyoshi/dev/pieceofcake2/imagine/actions/workflows/CI.yml)
![CakePHP](https://img.shields.io/packagist/dependency-v/pieceofcake2/imagine/pieceofcake2/cakephp?logo=cakephp&logoColor=%23FFFFFF&label=CakePHP&labelColor=%23D33C43&color=%23FFFFFF)
[![CI](https://img.shields.io/github/actions/workflow/status/pieceofcake2/imagine/CI.yml?label=CI)](https://github.com/pieceofcake2/imagine/actions/workflows/CI.yml)
[![Codecov](https://img.shields.io/codecov/c/gh/pieceofcake2/imagine?label=Coverage)](https://codecov.io/gh/pieceofcake2/imagine)

The Imagine plugin is a CakePHP wrapper around the
[Imagine image manipulation library](https://github.com/php-imagine/Imagine). It makes it easy
to resize, crop and create thumbnails of images with a model behavior, and to serve signed image
URLs with a helper and a component. This is a fork of
[burzum/cakephp-imagine-plugin](https://github.com/burzum/cakephp-imagine-plugin) for
[pieceofcake2/cakephp](https://github.com/pieceofcake2/cakephp).

## Documentation

- Website: https://pieceofcake2.github.io/imagine/
- Markdown: [docs/README.md](docs/README.md) (for AI tools and offline reading)

The Markdown documentation is also included in the Composer package, in the plugin's directory: `plugins/Imagine/docs/README.md` (the location depends on your `installer-paths`). Install the package with `--dev` if you only need it for development.

## Requirements

* PHP 8.0+
* pieceofcake2/cakephp 2.12+
* GD (`ext-gd`) or Imagick (`ext-imagick`) PHP extension

## Installation

```
composer require pieceofcake2/imagine
```

## Reporting Issues

Please open an issue on [GitHub](https://github.com/pieceofcake2/imagine/issues).

## Contributing

See the [contribution guidelines](https://github.com/pieceofcake2/.github/blob/master/CONTRIBUTING.md).

## License

Copyright 2012 - 2014, Florian Krämer

The MIT License. See [LICENSE](LICENSE).
