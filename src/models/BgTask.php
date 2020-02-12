<?php

namespace dsj\bgtask\models;

use dsj\components\server\ParseCrontab;
use Yii;

/**
 * This is the model class for table "bg_task".
 *
 * @property int $id
 * @property int $created_at 创建时间
 * @property string $created_by 创建人
 * @property int $updated_at 修改时间
 * @property string $updated_by 修改人
 * @property string $name 任务名称
 * @property string $program 执行程序
 * @property string $rule crontab规则
 * @property string $pid pid
 *  * @property string $memory 消耗内存
 * @property string $info 任务信息
 * @property int $status 任务状态(0未开始1正在执行2执行成功3执行失败)
 * @property int $is_open 是否启用。默认启用
 * @property int $start_time 开始执行时间
 * @property int $end_time 执行结束时间
 * @property int $run_time 任务运行时间
 */
class BgTask extends \yii\db\ActiveRecord
{
    //正在执行
    const STATUS_ON = 1;
    //执行成功
    const STATUS_SUCCESS = 2;
    //执行失败
    const STATUS_OFF = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bg_task';
    }

    public static function getDb()
    {
        return Yii::$app->manager_db;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at', 'updated_at', 'start_time', 'end_time', 'run_time','status','is_open','created_by','updated_by','memory'], 'integer'],
            [['name', 'program', 'rule'], 'required'],
            [['name', 'program', 'rule'], 'string', 'max' => 200],
            [['pid'], 'string', 'max' => 20],
            [['info'], 'string', 'max' => 500],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'created_at' => '创建时间',
            'created_by' => '创建人',
            'updated_at' => '更新时间',
            'updated_by' => '更新人',
            'name' => '任务名称',
            'program' => '执行路由',
            'rule' => '规则',
            'pid' => 'Pid',
            'info' => '备注',
            'status' => '任务状态',
            'is_open' => '是否启用',
            'start_time' => '开始时间',
            'end_time' => '结束时间',
            'run_time' => '运行时长',
            'memory' => '消耗内存',
        ];
    }

    public function getNeedExecList(){
        $list = self::find()->where(['!=','status','1'])->andWhere(['is_open' => 1])->asArray()->all();
        foreach ($list as $key => $item){
            //检查当前时间是否符合crontab规则
            $res = ParseCrontab::check(time(),$item['rule']);
            if (!$res){
                unset($list[$key]);
            }
            //保证每分钟只运行一次
            if (date('Y-m-d H:i',$item['start_time']) == date('Y-m-d H:i',time())){
                unset($list[$key]);
            }
        }

        return $list;
    }

    public function findOneByProgram($program){
        return self::findOne(['program' => $program]);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)){
            if ($insert){
                $this->created_at = time();
                $this->updated_at = time();
                $this->created_by = Yii::$app->user->id;
                $this->updated_by = Yii::$app->user->id;
            }else{
                $this->updated_at = time();
                $this->updated_by = Yii::$app->user->id;
            }
            return true;
        }
        return false;
    }

    public static $statusMap = [
        '0' => '未开始',
        '1' => '正在执行',
        '2' => '执行成功',
        '3' => '执行失败',
    ];

    public static $statusColorMap = [
        '0' => ['class' => 'bg-default'],
        '1' => ['class' => 'bg-primary'],
        '2' => ['class' => 'bg-success'],
        '3' => ['class' => 'bg-danger'],
    ];
}
