<?php
namespace app\models;

use Yii;
use yii\db\Query;
use yii\base\Model;

class TensynProgram extends Model{
    public function delete($program_default_name,$platform){
        return Yii::$app->db->createCommand()->delete('tensyn_program',array('program_default_name'=>$program_default_name,'platform'=>$platform))->execute();
    }
}
