<?php
/**
 * Created by PhpStorm.
 * User: z_bodya
 * Date: 9/30/14
 * Time: 12:41 AM
 */

namespace zxbodya\yii2\tinymce;


use yii\web\JsExpression;
use yii\web\View;

class DummyFileManager extends FileManager
{

    /**
     * @return string JavaScript callback function, starts with "js:"
     */
    public function getFileBrowserCallback()
    {
        $script = <<<JS
        function (field_name, url, type, win) {
            alert('It is not working');
        }
JS;

        return new JsExpression($script);
    }

    /**
     * @param View $view
     */
    public function registerAsset($view)
    {

    }
}