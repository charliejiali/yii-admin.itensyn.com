<?php
namespace app\models;

use Yii;
use yii\db\Query;
use yii\base\Model;

class MediaUser extends Model{
    public function get($options){
        $query=(new Query)
            ->select('*')
            ->from('media_user')
            ->where(['>','user_id',0]);
        if(count($options)>0){
            foreach($options as $k=>$v){
                switch($k){
                    case 'platform':
                        $query->andWhere(['=','platform',$v]);
                }
            }
        }
        return $query->one();
    }
}
