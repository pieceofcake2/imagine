Installation
============

Using Composer
--------------

Install the plugin with [Composer](https://getcomposer.org/) from the directory where your project's `composer.json` is located:

```
composer require pieceofcake2/imagine
```

This also installs the [Imagine library](https://github.com/php-imagine/Imagine) (`imagine/imagine`) as a dependency. Because the plugin has the type `cakephp-plugin`, Composer installs it into your `Plugin/Imagine` directory.

The Imagine library is loaded by Composer's autoloader, so make sure your application includes `vendor/autoload.php`.

You also need the GD (`ext-gd`) or Imagick (`ext-imagick`) PHP extension, depending on the Imagine engine you want to use.

Loading the plugin
------------------

Load the plugin in your app's `Config/bootstrap.php`:

```php
CakePlugin::load('Imagine');
```

Configure Salt for Imagine
--------------------------

You need to configure a salt for Imagine security functions.

```php
Configure::write('Imagine.salt', 'your-salt-string-here');
```

We do not use Security.salt on purpose because we do not want to use the same salt here for security reasons.
