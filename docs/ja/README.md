# Imagine ドキュメント

Imagine プラグインは、[Imagine 画像処理ライブラリ](https://github.com/php-imagine/Imagine) を CakePHP
で使うためのラッパーです。リサイズ、サムネイルの作成、切り抜きや、フォトコラージュの作成のような
より複雑な処理など、画像データを簡単に加工できます。また、PHP で画像データを扱うさまざまな方法
(GD、Imagick、Gmagick) の違いを吸収します。

- [インストール](#installation)
- [Imagine ビヘイビア](#imagine-behavior)
- [Imagine コンポーネント](#imagine-component)
- [Imagine ヘルパー](#imagine-helper)
- [キャッシュと保存](#caching-and-storage)
- [パフォーマンスに関する注意](#performance-notes)
- [基本的な例](#basic-example)

<a id="installation"></a>

## インストール

プロジェクトの `composer.json` があるディレクトリで、[Composer](https://getcomposer.org/) を使って
プラグインをインストールします:

```bash
composer require pieceofcake2/imagine
```

これにより、[Imagine ライブラリ](https://github.com/php-imagine/Imagine) (`imagine/imagine`) も依存
パッケージとしてインストールされます。このライブラリは Composer のオートローダーで読み込まれるので、
アプリケーションで `vendor/autoload.php` を読み込むようにしてください。

このプラグインは `cakephp-plugin` タイプなので、`composer/installers` によってプラグインディレクトリに
インストールされます。正しい場所にインストールされるように、アプリケーションの `composer.json` で
`installer-paths` を設定してください:

| レイアウト | `installer-paths` |
| --- | --- |
| モダン (`config/`, `src/`, `templates/`, `plugins/`) | `"plugins/{$name}/": ["type:cakephp-plugin"]` |
| レガシー (`app/Config/`, `app/Plugin/`) | `"app/Plugin/{$name}/": ["type:cakephp-plugin"]` |

[pieceofcake2/app](https://github.com/pieceofcake2/app) スケルトンには、この設定がすでに含まれています。

使いたい Imagine のエンジンに応じて、GD (`ext-gd`) または Imagick (`ext-imagick`) の PHP
エクステンションも必要です。

<a id="loading-the-plugin"></a>

### プラグインのロード

`config/bootstrap.php` (レガシーレイアウトでは `app/Config/bootstrap.php`) でプラグインをロードします:

```php
CakePlugin::load('Imagine');
```

<a id="configure-the-salt"></a>

### ソルトの設定

画像の URL のハッシュに使うソルトを設定する必要があります ([Imagine ヘルパー](#imagine-helper) を
参照してください):

```php
Configure::write('Imagine.salt', 'your-salt-string-here');
```

ソルトを他のセキュリティ機能と共有しないように、`Security.salt` はあえて使っていません。

<a id="imagine-behavior"></a>

## Imagine ビヘイビア

このビヘイビアは、一連の操作で画像ファイルを処理します。`ImagineBehavior::processImage()` を
参照してください。

```php
class Image extends AppModel
{
    public $actsAs = ['Imagine.Imagine'];
}
```

`engine` 設定で Imagine のエンジンを選択します: `Gd` (デフォルト)、`Imagick`、`Gmagick` のいずれかです。

```php
public $actsAs = ['Imagine.Imagine' => ['engine' => 'Imagick']];
```

<a id="processing-images"></a>

### 画像の処理

`processImage($image, $output = null, $imagineOptions = [], $operations = [])` は、画像 (ファイル名
または Imagine の画像オブジェクト) を開き、指定した順番で操作を適用して、Imagine の保存オプション
`$imagineOptions` で結果を `$output` に保存します。`$output` を指定しない場合は、画像オブジェクトを
返します。

`$operations` は、操作の名前とそのオプションの配列です。操作はビヘイビアのメソッド、またはモデルの
同じ名前のメソッド (画像とオプションを渡して呼び出されます) なので、独自の操作を追加できます。

<a id="image-operations"></a>

### 画像の操作

| 操作 | オプション |
| --- | --- |
| `crop` | `width`、`height` (必須)、`cropX`、`cropY` (開始位置、デフォルトは `0`) |
| `squareCenterCrop` | `size` (必須): 中央の正方形を切り抜いて `size` にリサイズします。`preventUpscale` |
| `widen` | `size` (必須): 縦横比を保って幅に合わせてリサイズします。`preventUpscale` |
| `heighten` | `size` (必須): 縦横比を保って高さに合わせてリサイズします。`preventUpscale` |
| `widenAndHeighten` | `width`、`height`、`size` のいずれか。`noUpScale`、`noDownScale` |
| `scale` | `factor` (必須): 指定した倍率で拡大縮小します。`preventUpscale` |
| `flip` | `direction`: `vertically` (デフォルト) または `horizontally` (`vertical` と `horizontal` も使えます) |
| `rotate` | `degree` |
| `thumbnail` | `width`、`height` (必須)、`mode`: `inset` (デフォルト) または `outbound`。`preventUpscale` |
| `resize` | `width`、`height` (必須) |

`preventUpscale` を `true` にすると、要求したサイズより小さい画像はそのままになります。
`noUpScale` と `noDownScale` の場合は、代わりに `InvalidArgumentException` がスローされます。

<a id="utility-methods"></a>

### ユーティリティメソッド

* `getImageSize($image)`: 画像オブジェクトまたはファイルのサイズ
* `operationsToString($operations, $separators = [], $hash = null)`: 操作をファイル名用の文字列に
  変換します ([パフォーマンスに関する注意](#performance-notes) を参照)
* `hashImageOperations($imageSizes, $hashLength = 8)`: `Model.configName => operations` の配列の
  ハッシュ
* `imagineObject()`: Imagine のインスタンス

素の Imagine のインスタンスは、モデルから取得できます:

```php
$this->Image->imagineObject();
```

または、ビヘイビアから直接取得できます:

```php
$this->Image->Behaviors->Imagine->Imagine;
```

<a id="imagine-component"></a>

## Imagine コンポーネント

Imagine コンポーネントは次の処理を行います:

* `actions` 設定に指定したアクションで、[Imagine ヘルパー](#imagine-helper) が生成した正しいハッシュが
  URL の名前付きパラメータとして渡されたことを検証し、操作を `$this->Imagine->operations` に展開します
* URL のハッシュを取得します: `getHash()`
* 名前付きパラメータをもとにハッシュを検証します: `checkHash()`。ハッシュがない場合や正しくない場合は
  `NotFoundException` をスローします
* まとめられた名前付きパラメータを展開します: `unpackParams()`

```php
public $components = [
    'Imagine.Imagine' => [
        'actions' => ['display'],
    ],
];
```

設定:

* `actions` (デフォルト `[]`): ハッシュを検証し、パラメータを展開するアクション
* `checkHash` (デフォルト `true`): これらのアクションでハッシュを検証するかどうか
* `hashField` (デフォルト `hash`): ハッシュを渡す名前付きパラメータの名前

<a id="imagine-helper"></a>

## Imagine ヘルパー

このヘルパーは、サムネイルなどの操作のための名前付きパラメータを含む画像の URL を生成し、ハッシュで
署名します。

ハッシュは [Imagine コンポーネント](#imagine-component) で検証できます。これにより、要求する
サムネイルのサイズを少しずつ変えてサーバー上に何千もの画像を生成させ、サイトをダウンさせるといった
ことを防げます。

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

2 番目の引数は `$full` です。ベース URL を含む完全な URL にするには `true` を指定します。

<a id="caching-and-storage"></a>

## キャッシュと保存

このプラグインは、画像をどのように保存し、どのようにキャッシュするかは扱い*ません*。ただし、ハッシュや
一意な文字列をもとに画像をキャッシュするためのヘルパーメソッドをいくつか提供しています。

これは設計上の判断です。ファイルの保存の実装は人によって少しずつ異なるので、生成した画像をどのように
保存するかはあなた次第です。

<a id="storing-images-with-filestorage"></a>

### FileStorage による画像の保存

アプリケーションで画像やファイルを保存するには、[FileStorage](https://github.com/burzum/cakephp-file-storage)
プラグインを参照してください。FileStorage にはアップロード後に画像を自動で処理する機能があり、
その処理に Imagine を使っています。

<a id="performance-notes"></a>

## パフォーマンスに関する注意

**画像をその場で生成するべきではありません**。特にトラフィックの多いサイトではそうです。同時に
大量のリクエストが来ると、サーバーが応答しなくなるおそれがあります。最初のリクエストがサーバーに届いて
画像の生成を始めている間に、他のリクエストも同時に同じことをしようとして、最悪の場合はサイトが
止まってしまいます。リクエストのたびに画像を生成する*必要はありません*。実際、サーバーの CPU に負荷を
かけ、メモリを消費するので、悪い習慣です。

**画像のアップロード直後に必要なバージョンを生成し**、後から必要になる他のバージョンはシェルスクリプトで
生成するほうがよいでしょう。そのために、`ImagineBehavior::operationsToString()` は画像の操作の配列を
文字列に変換します。

キャッシュされたバージョンを持つファイルをまとめて削除できるように、このメソッドで生成した文字列を
画像のファイル名の末尾に付けてください。想定している使い方は、次のようにファイルを保存することです

```
my_horse.thumbnail+width-100+height-100.jpg
```

ファイル名に画像の操作をすべて含めるとかなり長くなることがあるので、文字列をハッシュ化して (例えば
`$hash` に `'md5'` を渡して) 次のようなファイル名を生成するほうがよいでしょう:

```
my_horse.e18c820f1c3da390e3d01b4fb91b0f68.jpg
```

つまり、アップロード後に画像のメタデータをデータベースに保存し、ファイル名にはそのレコードの id を付け、
その末尾にこの文字列を付けて、その文字列もデータベースに保存します。ビューでは、画像へのアクセスを
それ以上制御する必要がなければ、画像に直接リンクするだけで済みます:

```php
echo $this->Html->image('/images/05/04/61/my_horse.thumbnail+width-100+height-100.jpg');
```

<a id="basic-example"></a>

## 基本的な例

モデルにビヘイビアを追加します:

```php
class Image extends AppModel
{
    public $actsAs = ['Imagine.Imagine'];
}
```

これで画像を加工できるようになります:

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

<a id="origin"></a>

## 由来

このパッケージは、Florian Krämer 氏が作成した
[burzum/cakephp-imagine-plugin](https://github.com/burzum/cakephp-imagine-plugin)
(Packagist では `burzum/cakephp-imagine-plugin`、アップストリームの最終バージョンは 1.0.6) をフォークして
メンテナンスしているものです。[pieceofcake2](https://github.com/pieceofcake2) が、モダンな PHP で動く
CakePHP 2 向けにメンテナンスしています。オリジナルからの変更点は
[フォーク以降のコミット履歴](https://github.com/pieceofcake2/imagine/compare/a15b2f9...1.x)
を参照してください。
