<?php

namespace dsj\bgtask\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * BgTaskSearch represents the model behind the search form of `common\models\BgTask`.
 */
class BgTaskSearch extends BgTask
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'created_at', 'updated_at', 'start_time', 'end_time', 'run_time'], 'integer'],
            [['created_by', 'updated_by', 'name', 'program', 'rule', 'pid', 'info', 'status','is_open','memory'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = BgTask::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'run_time' => $this->run_time,
            'is_open' => $this->is_open,
            'status' => $this->status,
            'pid' => $this->pid,
            'memory' => $this->memory,
        ]);

        $query->andFilterWhere(['>=', 'created_by', $this->created_by])
            ->andFilterWhere(['>=', 'updated_by', $this->updated_by])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'program', $this->program])
            ->andFilterWhere(['like', 'rule', $this->rule])
            ->andFilterWhere(['like', 'info', $this->info]);

        return $dataProvider;
    }
}
