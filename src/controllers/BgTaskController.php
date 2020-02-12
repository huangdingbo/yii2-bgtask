<?php


namespace dsj\bgtask\controllers;

use dsj\bgtask\models\BgTask;
use dsj\bgtask\models\BgTaskSearch;
use dsj\components\controllers\WebController;
use dsj\components\helpers\CommandHelper;
use Yii;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;

class BgTaskController extends WebController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all BgTask models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BgTaskSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        CommandHelper::getInstance()->setProgram('main/index')->searchPidByProgram();
        $pid = CommandHelper::getInstance()->getPid();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'pid' => $pid
        ]);
    }

    /**
     * Displays a single BgTask model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new BgTask model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BgTask();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->save()){
                Yii::$app->session->setFlash('success','任务创建成功');
                $this->redirectParent(['index']);
            }
            Yii::$app->session->setFlash('danger','任务创建失败，错误信息：' . Json::encode($model->getErrors()));
            $this->redirectParent(['index']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing BgTask model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->save()){
                Yii::$app->session->setFlash('success','任务修改成功');
                $this->redirectParent(['index']);
            }
            Yii::$app->session->setFlash('danger','任务创建失败，错误信息：' . Json::encode($model->getErrors()));
            $this->redirectParent(['index']);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing BgTask model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the BgTask model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BgTask the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BgTask::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @return \yii\web\Response
     * @throws NotFoundHttpException
     * 开关
     */
    public function actionSwitch(){
        $getData = Yii::$app->request->get();
        $value = $getData['is_open'] == 1 ? 0 : 1;
        $model = $this->findModel($getData['id']);
        $model->is_open = $value;
        if ($model->save()){
            return $this->redirect(['index']);
        }
        Yii::$app->session->setFlash('danger','操作失败，错误信息：' . Json::encode($model->getErrors()));
        return $this->redirect(['index']);
    }

    public function actionLog($id){
        $model = BgTask::findOne(['id' => $id]);
        $filePath =  \Yii::getAlias('@console') . '/runtime/' .date('Ymd') . '/' . str_replace('/','_',$model->program) . '.txt';

        if (!file_exists($filePath)){
            $text = '任务：' . $model->name . '还未运行，没有日志文件产生!!!';
        }else{
            $text = file_get_contents($filePath);
        }

        return $this->render('log',[
            'model' => $model,
            'text' => $text,
        ]);
    }

    public function actionClear($id){
        $model = BgTask::findOne(['id' => $id]);
        $filePath =  \Yii::getAlias('@console') . '/runtime/' .date('Ymd') . '/' . str_replace('/','_',$model->program) . '.txt';
        file_put_contents($filePath,'');
        Yii::$app->session->setFlash('success','日志清除成功');
        $this->redirectParent(['index']);
    }

    public function actionReplace($id){
        $model = $this->findModel($id);
        $model->pid = null;
        $model->memory = $model->status = $model->start_time = $model->end_time = $model->run_time = 0;
        if ($model->save()){
            Yii::$app->session->setFlash('success','重置成功');
            return $this->redirect(['index']);
        }

        Yii::$app->session->setFlash('danger','重置出错：'.Json::encode($model->getErrors()));
        return $this->redirect(['index']);
    }
}