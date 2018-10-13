<?php
/**
 * Created by PhpStorm.
 * User: huijiewei
 * Date: 6/14/15
 * Time: 15:31
 */

namespace huijiewei\jquerydatetimepicker;

use yii\web\AssetBundle;

class JqueryDateTimePickerAssets extends AssetBundle
{
    public $sourcePath = '@huijiewei/jquerydatetimepicker/assets';

    public $js = [
        'datepicker/jquery-ui.min.js',
        'timepicker/jquery.ui.timepicker.js',
    ];

    public $css = [
        'datepicker/jquery-ui.min.css',
        'datepicker/jquery-ui.structure.min.css',
        'datepicker/jquery-ui.theme.min.css',
        'timepicker/jquery.ui.timepicker.css',
        'datetimepicker.css',
    ];

    public $depends = [
        'yii\web\JqueryAsset',
    ];
}
