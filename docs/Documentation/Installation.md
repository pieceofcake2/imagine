Installation
============

Using Composer
--------------

Install the plugin with [Composer](https://getcomposer.org/) from the directory where your project's `composer.json` is located:

```
composer require pieceofcake2/imagine
```

This also installs the [Imagine library](https://github.com/php-imagine/Imagine) (`imagine/imagine`) as a dependency. Because the plugin has the type `cakephp-plugin`, it is installed by [composer/installers](https://github.com/composer/installers). Your application's `composer.json` needs the `installer-paths` setting so that the plugin is installed where CakePHP looks for plugins:

```json
"config": {
    "allow-plugins": {
        "composer/installers": true
    }
},
"extra": {
    "installer-paths": {
        "plugins/{$name}/": ["type:cakephp-plugin"]
    }
}
```

With this setting the plugin is installed into `plugins/Imagine`. In an application that uses the legacy `app/` layout, use `"app/Plugin/{$name}/"` instead. The [pieceofcake2/app](https://github.com/pieceofcake2/app) skeleton already contains this setting.

The Imagine library is loaded by Composer's autoloader, so make sure your application includes `vendor/autoload.php`.

You also need the GD (`ext-gd`) or Imagick (`ext-imagick`) PHP extension, depending on the Imagine engine you want to use.

Loading the plugin
------------------

Load the plugin in your app's `config/bootstrap.php` (`app/Config/bootstrap.php` in the legacy layout):

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
