<?php

use dsj\bgtask\models\BgTask;
use dsj\components\helpers\Html;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\BgTaskSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '后台任务管理';
$this->params['breadcrumbs'][] = $this->title;
$mainPidInfo = !empty($pid) ? '主进程ID:' . $pid : '主进程未运行';
$mainClass = !empty($pid) ? 'btn btn-primary col-md-offset-4 col-lg-offset-4col-xl-offset-4' : 'btn btn-danger col-md-offset-4 col-lg-offset-4col-xl-offset-4';
?>
<div class="bg-task-index">

    <p>
        <?= Html::a('创建任务', ['create'], [
                'class' => 'btn btn-success data-create',
                'data-toggle' => 'modal',
                'data-target' => '#create-modal',
        ]) ?>
        <?= Html::button($mainPidInfo , [
            'class' => $mainClass,
        ]) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pager'=>[
            'firstPageLabel'=>"首页",
            'prevPageLabel'=>'上一页',
            'nextPageLabel'=>'下一页',
            'lastPageLabel'=>'尾页',
        ],
        'columns' => [
            'name',
            'program',
            'rule',
            [
                'attribute' => 'status',
                'headerOptions' => ['class' => 'col-md-1'],
                'filter' => array_merge(['' => '所有状态'],BgTask::$statusMap),
                'contentOptions' => function($dataProvider){
                    return BgTask::$statusColorMap[$dataProvider->status];
                },
                'value' => function($dataProvider){
                    return BgTask::$statusMap[$dataProvider->status];
                },

            ],
            'pid',
            [
                    'attribute' => 'start_time',
                    'value' => function($dataProvider){
                        return $dataProvider->start_time == 0 ? null : date('Y-m-d H:i:s',$dataProvider->start_time);
                    }
            ],
            [
                'attribute' => 'end_time',
                'value' => function($dataProvider){
                    return $dataProvider->end_time == 0 ? null : date('Y-m-d H:i:s',$dataProvider->end_time);
                }
            ],
            [
                'attribute' => 'run_time',
                'value' => function($dataProvider){
                    return $dataProvider->run_time == 0 ? '0秒' : $dataProvider->run_time . '秒';
                }
            ],
            [
                'attribute' => 'memory',
                'value' => function($dataProvider){
                    return  empty($dataProvider->memory) ? null : round($dataProvider->memory/(1024*1024),4) . 'MB';
                }
            ],

            [
                    'class' => 'yii\grid\ActionColumn',
                    'header' => '操作',
                    'template'=> '{view} {update} {log} {switch} {replace} {delete}',
                    'buttons' => [
                        'view' => function ($url, $model, $key) {
                            return Html::a('查看', ['view','id' => $model->id], [
                                'data-toggle' => 'modal',
                                'data-target' => '#view-modal',
                                'class' => 'btn btn-primary btn-sm data-view',
                                'data-id' => $key,
                            ]);
                        },
                        'update' => function ($url, $model, $key) {
                            return Html::a('修改', ['update','id' => $model->id], [
                                'data-toggle' => 'modal',
                                'data-target' => '#view-modal',
                                'class' => 'btn btn-warning btn-sm data-update',
                                'data-id' => $key,
                            ]);
                        },
                        'log' => function ($url, $model, $key) {
                            return Html::a('日志', ['log','id' => $model->id], [
                                'data-toggle' => 'modal',
                                'data-target' => '#log-modal',
                                'class' => 'btn btn-info btn-sm data-log',
                                'data-id' => $key,
                            ]);
                        },
                        'switch' => function ($url, $model, $key) {
                            $showName = $model->is_open == 1 ? '禁用' : '启用';
                            $confirmName = $model->is_open == 1 ? '确定要禁用吗？' : '确定要启用吗？';
                            $className = $model->is_open == 1 ? 'btn btn-default btn-sm' : 'btn btn-success btn-sm';
                            return Html::a("{$showName}",['switch','id' => $model->id,'is_open' => $model->is_open],[
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('yii',"{$confirmName}"),
                                'class' => $className
                            ]);
                        },
                        'replace' => function ($url, $model, $key) {
                            return Html::a('重置',['replace', 'id' => $model->id],[
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('yii','你确定要重置吗？'),
                                'class' => 'btn btn-success btn-sm',
                            ]);
                        },
                        'delete' => function ($url, $model, $key) {
                            return Html::a('删除',['delete', 'id' => $model->id],[
                                'data-method' => 'post',
                                'data-confirm' => Yii::t('yii','你确定要删除吗？'),
                                'class' => 'btn btn-danger btn-sm',
                            ]);
                        },
                    ],
            ],
        ],
    ]); ?>
    <?php
    //创建
    Modal::begin([
        'id' => 'create-modal',
        'header' => '<h4 class="modal-title" style="color: #0d6aad">创建</h4>',
        'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">关闭</a>',
        'size' => 'modal-lg',
    ]);
    Modal::end();
    $createUrl = Url::toRoute(['create']);
    $createJs = <<<JS
    $('.data-create').on('click', function () {
		var contentHeight = document.body.scrollHeight - 150;
	
        var url = "{$createUrl}";
        
      $('.modal-body').html('<iframe id="iframe_name_top"  style="width: 100%;' + 'height:' + contentHeight +'px;"' + 'src="' + url + '" frameborder="0"></iframe>');
    
    });
JS;
    $this->registerJs($createJs);
    ?>
    <?php
    //查看
    Modal::begin([
        'id' => 'view-modal',
        'header' => '<h4 class="modal-title" style="color: #0d6aad">查看</h4>',
        'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">关闭</a>',
        'size' => 'modal-lg',
    ]);
    Modal::end();
    $viewUrl = Url::toRoute(['view']);
    $viewJs = <<<JS
    $('.data-view').on('click', function () {
		var contentHeight = document.body.scrollHeight;
	
        var url = "{$viewUrl}" + "&id=" + $(this).closest('tr').data('key');
        
      $('.modal-body').html('<iframe id="iframe_name_top"  style="width: 100%;' + 'height:' + contentHeight +'px;"' + 'src="' + url + '" frameborder="0"></iframe>');
    
    });
JS;
    $this->registerJs($viewJs);
    ?>
    <?php
    //修改
    Modal::begin([
        'id' => 'update-modal',
        'header' => '<h4 class="modal-title" style="color: #0d6aad">修改</h4>',
        'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">关闭</a>',
        'size' => 'modal-lg',
    ]);
    Modal::end();
    $updateUrl = Url::toRoute(['update']);
    $updateJs = <<<JS
    $('.data-update').on('click', function () {
		var contentHeight = document.body.scrollHeight - 150;
	
        var url = "{$updateUrl}" + "&id=" + $(this).closest('tr').data('key');
        
      $('.modal-body').html('<iframe id="iframe_name_top"  style="width: 100%;' + 'height:' + contentHeight +'px;"' + 'src="' + url + '" frameborder="0"></iframe>');
    
    });
JS;
    $this->registerJs($updateJs);
    ?>
    <?php
    //日志
    Modal::begin([
        'id' => 'log-modal',
        'header' => '<h4 class="modal-title" style="color: #0d6aad">日志</h4>',
        'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">关闭</a>',
        'size' => 'modal-lg',
    ]);
    Modal::end();
    $logUrl = Url::toRoute(['log']);
    $logJs = <<<JS
    $('.data-log').on('click', function () {
		var contentHeight = document.body.scrollHeight - 150;
	
        var url = "{$logUrl}" + "&id=" + $(this).closest('tr').data('key');
        
      $('.modal-body').html('<iframe id="iframe_name_top"  style="width: 100%;' + 'height:' + contentHeight +'px;"' + 'src="' + url + '" frameborder="0"></iframe>');
    
    });
JS;
    $this->registerJs($logJs);
    ?>
</div>
