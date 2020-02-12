<?php
/* @var $this yii\web\View */
/* @var $model yii\data\ActiveDataProvider */
/* @var $text */

use yii\helpers\Html;
$this->title = false;
echo Html::a('清除日志',['clear','id'=>$model->id],['class' => 'btn btn-danger', 'data-method' => 'post']);
echo "<pre style='margin-top: 10px'>";
echo $text;
echo "</pre>";
?>


