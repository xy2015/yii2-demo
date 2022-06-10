<?php


namespace app\models;


use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Supplier extends ActiveRecord
{
    //模糊查询的字段，写法：表名.列名
    public $likeSearch = [
        'name',
        'code'
    ];

    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['name','code','t_status'],'string'],
        ];
    }

    public function search($params=null,$defaultParams=false)
    {
        $query = $this::find();

        // 过滤条件
        $this->load($params);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $with = [];

        if($with){
            $query->with(array_values($with));
        }

        //默认搜索条件
        if(!isset($params['sort'])) {
            $query->orderby("id DESC");
        }else{
            unset($params['sort']);
        }
        if (isset($params['page'])){
            unset($params['page']);
        }
        $this->formatQueryParams($query, $params,$defaultParams);
        return $dataProvider;
    }

    /**
     * 格式化查询参数
     * @param $query
     * @param array $params
     * @param bool $defaultParams
     * @return bool
     */
    protected function formatQueryParams(&$query, $params=[],$defaultParams=false){
        if(empty($params) && $defaultParams==false){
            return false;
        }
        $modelName = ucfirst(basename(str_replace('\\','/',static::class)));
        $params = $params[$modelName];
        foreach($params as $key=>$value) {
            if ($value){
                if (in_array($key, $this->likeSearch)) {
                    $query->andWhere(['like', $key, $value]);
                } else {
                    $query->andWhere([$key=>$value]);
                }
            }
        }
    }

    public static function getStatus(){
        $options = [
            'ok' =>'OK',
            'hold' => 'HOLD',
        ];
        return $options;
    }
}