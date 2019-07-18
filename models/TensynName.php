<?php
namespace app\models;

use PHPUnit\Framework\Exception;
use Yii;
use yii\base\Model;
use yii\db\Query;

class TensynName extends Model{
    public function get($name,$platform){
        return (new Query)
            ->select('*')
            ->from('tensyn_program_name')
            ->where(array('=','program_default_name',$name))
            ->andWhere(array('=','platform',$platform))
            ->one();
    }
    public function add($name,$platform){
        return Yii::$app->db->createCommand()->insert('tensyn_program_name',array(
            "program_default_name"=>$name,
            "platform"=>$platform,
            "tensyn_name"=>$name
        ))->execute();
    }

}
