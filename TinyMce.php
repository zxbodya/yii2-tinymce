<?php

namespace zxbodya\yii2\tinymce;

use Yii;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * This is just an example.
 */
class TinyMce extends InputWidget
{

    /** @var bool|string Route to compressor action */
    public $compressorRoute = false;

    /**
     * For example here could be url to yandex spellchecker service.
     * http://speller.yandex.net/services/tinyspell
     * More info about it here: http://api.yandex.ru/speller/doc/dg/tasks/how-to-spellcheck-tinymce.xml
     *
     * Or you can build own spellcheking service using code provided by moxicode:
     * http://www.tinymce.com/download/download.php
     *
     * @var bool|string|array URL or an action route that can be used to create a URL or false if no url
     */
    public $spellcheckerUrl = false;


    /** @var bool|string Must be set to force widget language */
    public $language = false; // editor language, if false app language is used
    /**
     * @var bool|array FileManager configuration.
     * For example:
     * 'fileManager' => array(
     *      'class' => 'TinyMceElFinder',
     *      'connectorRoute'=>'admin/elfinder/connector',
     * )
     */
    public $fileManager = false;

    /** @var array Supported languages */
    private static $languages = array(
        'ar',
        'ar_SA',
        'bg_BG',
        'bn_BD',
        'bs',
        'ca',
        'cs',
        'cs_CZ',
        'cy',
        'da',
        'de',
        'de_AT',
        'el',
        'eo',
        'es',
        'es_MX',
        'et',
        'eu',
        'fa',
        'fa_IR',
        'fi',
        'fo',
        'fr_FR',
        'ga',
        'gl',
        'he_IL',
        'hi_IN',
        'hr',
        'hu_HU',
        'hy',
        'id',
        'it',
        'ja',
        'ka_GE',
        'kab',
        'ko',
        'ko_KR',
        'ku',
        'ku_IQ',
        'lb',
        'lt',
        'lv',
        'ml',
        'mn_MN',
        'nb_NO',
        'nl',
        'pl',
        'pt_BR',
        'pt_PT',
        'ro',
        'ru',
        'si_LK',
        'sk',
        'sl_SI',
        'sr',
        'sv_SE',
        'ta',
        'ta_IN',
        'th_TH',
        'tr_TR',
        'tt',
        'ug',
        'uk',
        'uk_UA',
        'vi',
        'vi_VN',
        'zh_CN',
        'zh_TW',
        'en_GB',
        'km_KH',
        'tg',
        'tr',
        'az',
        'en_CA',
        'is_IS',
        'be',
        'dv',
        'kk',
        'ml_IN',
        'gd',
    ); // widget supported languages


    private static $defaultSettings = array(
        'language' => 'ru',
        'plugins' => array(
            "advlist autolink lists link image charmap print preview hr anchor pagebreak",
            "searchreplace visualblocks visualchars code fullscreen",
            "insertdatetime media nonbreaking save table contextmenu directionality",
            "template paste textcolor"
        ),
        'toolbar' => "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor",
        'toolbar_items_size' => 'small',
        'image_advtab' => true,
        'relative_urls' => false,
        'spellchecker_languages' => "+Русский=ru",
    );
    /** @var array Widget settings will override defaultSettings */
    public $settings = array();

    public function init()
    {

        $this->settings = array_merge(self::$defaultSettings, $this->settings);
        if ($this->language === false) {
            $this->settings['language'] = Yii::$app->language;
        } else {
            $this->settings['language'] = $this->language;
        }
        if (!in_array($this->settings['language'], self::$languages)) {
            $lang = false;
            foreach (self::$languages as $i) {
                if (strpos($this->settings['language'], $i)) {
                    $lang = $i;
                }
            }
            if ($lang !== false) {
                $this->settings['language'] = $lang;
            } else {
                $this->settings['language'] = 'en';
            }
        }

        $assetsDir = $this->getView()->getAssetManager()->getBundle(TinyMceAsset::className())->baseUrl;
        $this->settings['script_url'] = "{$assetsDir}/tiny_mce.js";


        if ($this->spellcheckerUrl !== false) {
            $this->settings['plugins'][] = 'spellchecker';
            if (is_array($this->spellcheckerUrl)) {
                $this->settings['spellchecker_rpc_url'] = Url::toRoute($this->spellcheckerUrl);
            } else {
                $this->settings['spellchecker_rpc_url'] = $this->spellcheckerUrl;
            }
        }
    }

    public function run()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = $this->getId();
        }

        if (isset($this->model)) {
            echo Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textArea($this->name, $this->value, $this->options);
        }

        $this->registerScripts();
    }

    private function registerScripts()
    {
        $id = $this->options['id'];
        $view = $this->getView();

        if ($this->compressorRoute === false) {
            TinyMceAsset::register($view);
        } else {
            $opts = array(
                'files' => 'jquery.tinymce',
                'source' => defined('YII_DEBUG') && YII_DEBUG,
            );
            $opts["plugins"] = strtr(implode(',', $this->settings['plugins']), array(' ' => ','));
            if (isset($this->settings['theme'])) {
                $opts["themes"] = $this->settings['theme'];
            }
            $opts["languages"] = $this->settings['language'];

            $view->registerJsFile(
                TinyMceCompressorAction::scripUrl($this->compressorRoute, $opts),
                [
                    'depends' => [
                        'yii\web\JqueryAsset'
                    ]
                ]
            );
        }


        if ($this->fileManager !== false) {
            /** @var $fm FileManager */
            $fm = Yii::createObject($this->fileManager);
            $fm->init();
            $fm->registerAsset($view);
            $this->settings['file_browser_callback'] = $fm->getFileBrowserCallback();
        }

        $settings = Json::encode($this->settings);

        $this->getView()->registerJs("$('#{$id}').tinymce({$settings});");
    }
}
