<?php

/* @var $this yii\web\View */
/* @var $model common\models\BgTask */

$this->title = '创建任务';
?>
<div class="bg-task-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
