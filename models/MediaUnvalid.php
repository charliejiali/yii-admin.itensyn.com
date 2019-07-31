<?php
namespace app\models;

use Yii;
use yii\db\Query;
use yii\base\Model;

class MediaUnvalid extends Model{
    /**
     * 添加一条非法字段
     * @param $data
     * @throws \yii\db\Exception
     */
    public static function add($data){
        Yii::$app->db->createCommand()->insert('media_unvalid', $data)->execute();
    }

    /**
     * 获取所有非法字段
     * @param $media_id
     * @return array
     */
    public function get_all($media_id){
        return (new Query)
            ->select('*')
            ->from('media_unvalid')
            ->where(array('=','media_id',$media_id))
            ->all();
    }

    /**
     * 删除所有非法字段
     * @param $media_id
     * @throws \yii\db\Exception
     */
    public function delete($media_id){
        Yii::$app->db->createCommand()->delete('media_unvalid',array("media_id"=>$media_id))->execute();
    }
}
