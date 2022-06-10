<?php
namespace common\helpers;

use yii\helpers\BaseHtml;
use yii\helpers\Url;

class Html extends BaseHtml
{
    /**
     * 生成提交按钮
     * @param $text
     * @param null $url
     * @param array $options
     * @return string
     */
    public static function authSubmitButton($text, $url = null, $options = [],$extraData=[]){
        $formClass = isset($options['form-class'])?$options['form-class']:'';
        $output = Html::beginForm($url,'post',['class'=>'inline-block '.$formClass]);
        $output .= Html::hiddenInput('ids','',['id'=>'ids']);
        foreach($extraData as $name=>$value){
            $output .= Html::hiddenInput($name,$value);
        }
        $output .= Html::submitButton($text, $options);
        $output .= Html::endForm();
        return $output;
    }
}