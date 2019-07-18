<?php
namespace app\models;

use Yii;
use yii\db\Query;
use yii\base\Model;

class MediaUnvalid extends Model{
//    public static function get_by_id($media_id){
//        return (new Query)
//            ->select('*')
//            ->from('media_unvalid')
//            ->where(['=','media_id',$media_id])
//            ->all();
//    }

    // 添加某个剧目的一条非法字段
    public static function add($data){
        Yii::$app->db->createCommand()->insert('media_unvalid', $data)->execute();
    }
    // 获取某个剧目的所有非法字段
    public function get_all($media_id){
        return (new Query)
            ->select('*')
            ->from('media_unvalid')
            ->where(array('=','media_id',$media_id))
            ->all();
    }
    // 删除某个剧目的所有非法字段
    public function delete($media_id){
        Yii::$app->db->createCommand()->delete('media_unvalid',array("media_id"=>$media_id))->execute();
    }
}
