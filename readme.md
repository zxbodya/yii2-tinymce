TinyMCE integration for Yii2
===========================
Yii2 extension to simplify tinymce wyiwyg editor usage in your application.

Extension is based on Yii 1.1 version: https://github.com/zxbodya/yii-tinymce

Provides:

* widget
* compressor action
* stub for integration with file managers like elFinder


##Installation
The preferred way to install this extension is through [composer](https://getcomposer.org/).

Either run

`php composer.phar require --prefer-dist zxbodya/yii2-tinymce "*@dev"`

or add

`"zxbodya/yii2-tinymce": "*@dev"`

to the require section of your `composer.json` file.
## Usage

### Widget basic usage

```php
$form->field($model, 'content')->widget(TinyMce::className())
```

### Scripts Compressor Action

This can be used to optimize widget loading time.

At fist setup compressor action:

```php
public function actions()
{
    return [
        'tinyMceCompressor' => [
            'class' => TinyMceCompressorAction::className(),
        ],
    ];
}
```

Next add route to configured action to widget ooptions:

```php
$form->field($model, 'content')->widget(
    TinyMce::className(),
    ['compressorRoute' => 'test/tinyMceCompressor']
)
```

### ElFinder Fille manager 
At fisrt install `zxbodya/yii2-elfinder` extesion.

[https://github.com/zxbodya/yii2-elfinder](https://github.com/zxbodya/yii2-elfinder)

And configure connector action for it.

Next add file manager settings to widget:

```php
$form->field($model, 'content')->widget(
    TinyMce::className(),
    [
        'fileManager' => [
            'class' => TinyMceElFinder::className(),
            'connectorRoute' => 'el-finder/connector',
        ],
    ]
)
```

### Spellchecker 
TinyMce has bundled plugin for spellchecking but it requires backed to work...

You can use yandex spellchecker service.

```php
$form->field($model, 'content')->widget(
    TinyMce::className(),
    ['spellcheckerUrl'=>'http://speller.yandex.net/services/tinyspell']
)
```

More info about it here: 

[http://api.yandex.ru/speller/doc/dg/tasks/how-to-spellcheck-tinymce.xml](http://api.yandex.ru/speller/doc/dg/tasks/how-to-spellcheck-tinymce.xml)

Or you can build own spellcheking service using code provided by moxicode:
[http://www.tinymce.com/download/download.php](http://www.tinymce.com/download/download.php)