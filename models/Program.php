<?php
namespace app\models;

use Yii;
use yii\db\Query;
use yii\base\Model;

class Program extends Model{
    public function get($program_default_name,$platform){
        return (new Query)
            ->select('*')
            ->from('program')
            ->where(array('=','program_default_name',$program_default_name))
            ->andWhere(array('=','platform_name',$platform))
            ->one();
    }
    public function update($data,$where){
        return Yii::$app->db->createCommand()->update('program',$data,$where)->execute();
    }
    public function delete($program_default_name,$platform){
        return Yii::$app->db->createCommand()->delete('program',array('program_default_name'=>$program_default_name,'platform_name'=>$platform))->execute();
    }
}
