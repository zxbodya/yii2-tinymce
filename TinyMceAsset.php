<?php

namespace zxbodya\yii2\tinymce;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\AssetBundle;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * This is just an example.
 */
class TinyMceAsset extends AssetBundle
{
    public $sourcePath = '@zxbodya/yii2/tinymce/vendors/tinymce';
    public $js = [
        'js/tinymce/tinymce.min.js',
        'js/tinymce/jquery.tinymce.min.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset'
    ];
}