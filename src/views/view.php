<?php

use backend\models\Adminuser;
use common\models\BgTask;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\BgTask */

$this->title = '任务：' . $model->name;
?>
<div class="bg-task-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                    'attribute' => 'created_at',
                    'value' => function($model){
                        return date('Y-m-d H:i:s',$model->created_at);
                    }
            ],
            [
                'attribute' => 'created_by',
                'value' => function($model){
                    return Adminuser::findOne(['id' => $model->created_by])->nickname;
                }
            ],
            [
                'attribute' => 'updated_at',
                'value' => function($model){
                    return date('Y-m-d H:i:s',$model->updated_at);
                }
            ],
            [
                'attribute' => 'updated_by',
                'value' => function($model){
                    return Adminuser::findOne(['id' => $model->updated_by])->nickname;
                }
            ],
            'name',
            'program',
            'rule',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return BgTask::$statusMap[$model->status];
                },
            ],
            'pid',
            [
                'attribute' => 'start_time',
                'value' => function($model){
                    return $model->start_time == 0 ? null : date('Y-m-d H:i:s',$model->start_time);
                }
            ],
            [
                'attribute' => 'run_time',
                'value' => function($model){
                    return $model->run_time == 0 ? null : $model->run_time . '秒';
                }
            ],
            [
                'attribute' => 'memory',
                'value' => function($model){
                    return empty($model->memory) ? null : round($model->memory/(1024*1024),4) . 'MB';
                }
            ],
            'info:ntext',
        ],
    ]) ?>

</div>
