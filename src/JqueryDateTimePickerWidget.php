<?php
/**
 * Created by PhpStorm.
 * User: huijiewei
 * Date: 6/14/15
 * Time: 15:34
 */

namespace huijiewei\jquerydatetimepicker;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\AssetBundle;
use yii\web\JsExpression;
use yii\widgets\InputWidget;

class JqueryDateTimePickerWidget extends InputWidget
{
    public $options = [];

    public $clientOptions = [];

    private $_assetBundle;
    private $_dateInputId;

    public function init()
    {
        parent::init();

        $this->clientOptions = ArrayHelper::merge([
            'dateOptions' => [
                'dateFormat' => 'yy-mm-dd',
                'gotoCurrentType' => false,
                'hideIfNoPrevNext' => true,
                'showOtherMonths' => true,
            ],
            'timeOptions' => [
                'minutes' => [
                    'interval' => 15,
                ],
                'defaultTime' => '',
            ],
        ], $this->clientOptions);

        $this->_dateInputId = $this->id . 'Date';

        $this->registerAssetBundle();

        $this->registerJavascript();
    }

    public function registerAssetBundle()
    {
        $this->_assetBundle = JqueryDateTimePickerAssets::register($this->getView());
    }

    public function registerJavascript()
    {
        if (isset($this->clientOptions['dateOptions']['beforeShowDay']) &&
            !empty($this->clientOptions['dateOptions']['beforeShowDay'])) {
            $this->clientOptions['dateOptions']['beforeShowDay'] =
                new JsExpression('function(day) { ' .
                    $this->clientOptions['dateOptions']['beforeShowDay'] .
                    ' }');
        }

        $onDateSelectJsAddon = '';

        if (isset($this->clientOptions['dateOptions']['onSelect']) &&
            !empty($this->clientOptions['dateOptions']['onSelect'])) {
            $onDateSelectJsAddon = $this->clientOptions['dateOptions']['onSelect'];
        }

        $onDateSelectJs = <<<EOD
function(dateText, inst) {
    $('#{$this->_dateInputId}').val(dateText);

    var timePicker = $('#{$this->id}').closest('.jquery-datetimepicker-wrap').find('.time');

    timePicker.timepicker('enable');

    {$onDateSelectJsAddon}

    timePicker.timepicker('refresh');
    timePicker.find('.ui-state-active').removeClass('ui-state-active');
    $('#{$this->id}').val('').trigger('datetime.change');
}
EOD;

        $this->clientOptions['dateOptions']['onSelect'] = new JsExpression($onDateSelectJs);

        $onTimeSelectJsAddon = '';

        if (isset($this->clientOptions['timeOptions']['onSelect']) &&
            !empty($this->clientOptions['timeOptions']['onSelect'])) {
            $onTimeSelectJsAddon = $this->clientOptions['dateOptions']['onSelect'];
        }

        $onTimeSelectJs = <<<EOD
function(timeText, inst) {
    $('#{$this->id}').val($('#{$this->_dateInputId}').val() + ' ' + timeText +':00').trigger('datetime.change');

    {$onTimeSelectJsAddon}
}
EOD;

        $this->clientOptions['timeOptions']['onSelect'] = new JsExpression($onTimeSelectJs);

        $dateClientOptions = Json::encode($this->clientOptions['dateOptions']);
        $timeClientOptions = Json::encode($this->clientOptions['timeOptions']);

        $js = <<<EOD
        var wrap = $('#{$this->id}').closest('.jquery-datetimepicker-wrap');
        wrap.find('.date').datepicker({$dateClientOptions});
        wrap.find('.time').timepicker({$timeClientOptions});
        wrap.find('.time').timepicker('disable');
EOD;

        if (isset($this->clientOptions['onChange']) && strlen($this->clientOptions['onChange'])) {
            $js .= '$("#' . $this->id . '").on("datetime.change",function() { ' .
                $this->clientOptions['onChange'] .
                ' });';
        }

        $this->getView()->registerJs($js);
    }

    public function run()
    {
        $html = '<div class="jquery-datetimepicker-wrap clearfix">';
        $html .= '<div class="pull-left mr10 date"></div>';
        $html .= '<div class="pull-left mr10 time"></div>';

        if ($this->hasModel()) {
            $html .= Html::activeHiddenInput($this->model, $this->attribute, $this->options);
        } else {
            $html .= Html::hiddenInput($this->name, $this->value, $this->options);
        }

        $dateValue = '';

        if (strlen($this->value)) {
            $dateTime = new \DateTime($this->value);
            $dateValue = $dateTime->format('y-m-d');

            $this->clientOptions['dateOptions']['defaultDate'] = $dateValue;
            $this->clientOptions['timeOptions']['defaultTime'] = $dateTime->format('H:i');
        }

        $html .= '<input type="hidden" id="' . $this->_dateInputId . '" value="' . $dateValue . '">';

        $html .= '</div>';

        return $html;
    }

    public function getAssetBundle()
    {
        if (!($this->_assetBundle instanceof AssetBundle)) {
            $this->registerAssetBundle();
        }

        return $this->_assetBundle;
    }
}
