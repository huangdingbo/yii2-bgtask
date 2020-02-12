<?php
/* @var $this yii\web\View */
/* @var $model common\models\BgTask */

$this->title = '修改任务：' . $model->name;
?>
<div class="bg-task-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
