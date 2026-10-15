# Imagine Documentation

The Imagine plugin is a CakePHP wrapper around the
[Imagine image manipulation library](https://github.com/php-imagine/Imagine). It makes it easy
to manipulate image data, like resizing, creating thumbnails, cropping, or more complex tasks
like creating a photo collage. It also abstracts the different ways to work with image data in
PHP: GD, Imagick and Gmagick.

- [Installation](#installation)
- [Imagine Behavior](#imagine-behavior)
- [Imagine Component](#imagine-component)
- [Imagine Helper](#imagine-helper)
- [Caching and Storage](#caching-and-storage)
- [Performance Notes](#performance-notes)
- [Basic Example](#basic-example)

## Installation

Install the plugin with [Composer](https://getcomposer.org/) from the directory where your
project's `composer.json` is located:

```bash
composer require pieceofcake2/imagine
```

This also installs the [Imagine library](https://github.com/php-imagine/Imagine)
(`imagine/imagine`) as a dependency. It is loaded by Composer's autoloader, so make sure your
application includes `vendor/autoload.php`.

The plugin has the `cakephp-plugin` type, so it is installed into your plugins directory by
`composer/installers`. Set `installer-paths` in your application's `composer.json` so it is
installed into the right place:

| Layout | `installer-paths` |
| --- | --- |
| Modern (`config/`, `src/`, `templates/`, `plugins/`) | `"plugins/{$name}/": ["type:cakephp-plugin"]` |
| Legacy (`app/Config/`, `app/Plugin/`) | `"app/Plugin/{$name}/": ["type:cakephp-plugin"]` |

The [pieceofcake2/app](https://github.com/pieceofcake2/app) skeleton already contains this
setting.

You also need the GD (`ext-gd`) or Imagick (`ext-imagick`) PHP extension, depending on the
Imagine engine you want to use.

### Loading the plugin

Load the plugin in `config/bootstrap.php` (legacy layout: `app/Config/bootstrap.php`):

```php
CakePlugin::load('Imagine');
```

### Configure the salt

You need to configure a salt for the hashes of the image URLs (see
[Imagine Helper](#imagine-helper)):

```php
Configure::write('Imagine.salt', 'your-salt-string-here');
```

`Security.salt` is not used on purpose, so the salt is not shared with other security
features.

## Imagine Behavior

The behavior processes an image file with a set of operations, see
`ImagineBehavior::processImage()`.

```php
class Image extends AppModel
{
    public $actsAs = ['Imagine.Imagine'];
}
```

The `engine` setting selects the Imagine engine: `Gd` (default), `Imagick` or `Gmagick`.

```php
public $actsAs = ['Imagine.Imagine' => ['engine' => 'Imagick']];
```

### Processing images

`processImage($image, $output = null, $imagineOptions = [], $operations = [])` opens the image
(a file name or an Imagine image object), applies the operations in the given order and saves
the result to `$output` with the Imagine save options `$imagineOptions`. Without `$output`, the
image object is returned.

`$operations` is an array of operation names and their options. An operation is a method of
the behavior, or a method of the model with the same name (called with the image and the
options), so you can add your own operations.

### Image operations

| Operation | Options |
| --- | --- |
| `crop` | `width`, `height` (required), `cropX`, `cropY` (start point, default `0`) |
| `squareCenterCrop` | `size` (required): crops the center square and resizes it to `size`; `preventUpscale` |
| `widen` | `size` (required): resizes to the width, keeping the aspect ratio; `preventUpscale` |
| `heighten` | `size` (required): resizes to the height, keeping the aspect ratio; `preventUpscale` |
| `widenAndHeighten` | one of `width`, `height` or `size`; `noUpScale`, `noDownScale` |
| `scale` | `factor` (required): scales by the factor; `preventUpscale` |
| `flip` | `direction`: `vertically` (default) or `horizontally` (`vertical` and `horizontal` also work) |
| `rotate` | `degree` |
| `thumbnail` | `width`, `height` (required), `mode`: `inset` (default) or `outbound`; `preventUpscale` |
| `resize` | `width`, `height` (required) |

With `preventUpscale` set to `true`, an image smaller than the requested size is left as is.
`noUpScale` and `noDownScale` throw an `InvalidArgumentException` instead.

### Utility methods

* `getImageSize($image)`: the size of an image object or file
* `operationsToString($operations, $separators = [], $hash = null)`: turns the operations into
  a string for a file name (see [Performance Notes](#performance-notes))
* `hashImageOperations($imageSizes, $hashLength = 8)`: hashes of `Model.configName =>
  operations` arrays
* `imagineObject()`: the Imagine instance

The plain Imagine instance is available through the model:

```php
$this->Image->imagineObject();
```

or directly through the behavior:

```php
$this->Image->Behaviors->Imagine->Imagine;
```

## Imagine Component

The Imagine component does the following:

* For the actions listed in the `actions` setting, it validates that a valid hash from the
  [Imagine Helper](#imagine-helper) was passed as a named parameter in the URL, and unpacks the
  operations into `$this->Imagine->operations`
* Gets the hash of the URL: `getHash()`
* Validates the hash based on the named parameters: `checkHash()`; throws a `NotFoundException`
  when the hash is missing or wrong
* Unpacks the packed named parameters: `unpackParams()`

```php
public $components = [
    'Imagine.Imagine' => [
        'actions' => ['display'],
    ],
];
```

Settings:

* `actions` (default `[]`): actions in which the hash is checked and the parameters are unpacked
* `checkHash` (default `true`): check the hash in these actions
* `hashField` (default `hash`): name of the named parameter with the hash

## Imagine Helper

The helper generates image URLs with named parameters for thumbnails or other operations, and
signs them with a hash.

The hash can be checked with the [Imagine Component](#imagine-component), so that people can
not bring your site down by incrementing the size of a requested thumbnail to generate
thousands of images on your server.

```php
$url = $this->Imagine->url(
    [
        'controller' => 'images',
        'action' => 'display',
        1,
    ],
    false,
    [
        'thumbnail' => [
            'width' => 200,
            'height' => 150,
        ],
    ],
);

echo $this->Html->image($url);
```

The second argument is `$full`: set it to `true` for a full URL with the base URL.

## Caching and Storage

This plugin *does not* take care of how you store the images or how you cache them, but it
offers some helper methods for caching images based on a hash or a unique string.

This is a design decision: everyone implements file storage a little differently, so it is up
to you how you store the generated images.

### Storing images with FileStorage

See the [FileStorage](https://github.com/burzum/cakephp-file-storage) plugin for storing images
and files in your application. FileStorage features automated image processing after upload and
uses Imagine for that.

## Performance Notes

**You should not generate images on the fly**, especially not on high traffic sites: the
amount of simultaneous requests might lock up your server. The first request hits your server
and starts generating the image while others try to do the same at the same time, locking up
the site in the worst case. There is *no need* to generate images on each request; in fact it
is bad practice, because it puts load on your server's CPU and consumes memory.

**It's better to generate the needed versions directly after an image was uploaded**, and
generate other versions that are needed later with a shell script. For this purpose,
`ImagineBehavior::operationsToString()` turns the image operations array into a string.

Suffix your image with the string generated by this method to be able to batch delete a file
that has cached versions. The intended usage is to store the files like

```
my_horse.thumbnail+width-100+height-100.jpg
```

Instead of having all the image operations in the file name, which could become pretty long,
it is better to hash the string (pass e.g. `'md5'` as `$hash`) to generate a file name like
this:

```
my_horse.e18c820f1c3da390e3d01b4fb91b0f68.jpg
```

So after the upload, store your image metadata in a database, give the file name the id of the
record, suffix it with this string and store the string in the database too. In the views, if
no further control over the image access is needed, you can simply link the image directly:

```php
echo $this->Html->image('/images/05/04/61/my_horse.thumbnail+width-100+height-100.jpg');
```

## Basic Example

Attach the behavior to the model:

```php
class Image extends AppModel
{
    public $actsAs = ['Imagine.Imagine'];
}
```

Now you can start manipulating images:

```php
$imageOperations = [
    'flip' => [
        'direction' => 'vertically',
    ],
    'crop' => [
        'height' => 100,
        'width' => 100,
    ],
];

$this->Image->processImage(
    APP . 'files' . DS . 'image.jpg',
    APP . 'files' . DS . 'modifiedImage.jpg',
    [],
    $imageOperations,
);
```
