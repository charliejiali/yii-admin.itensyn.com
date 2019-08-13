<?php
namespace app\models;

use Yii;
use yii\db\Query;
use yii\base\Model;

class TensynProgram extends Model{
    /**
     * 删除腾信数据
     * @param $program_default_name string 剧目原名
     * @param $platform string 媒体平台
     * @return int
     * @throws \yii\db\Exception
     */
    public function delete($program_default_name,$platform){
        return Yii::$app->db->createCommand()->delete('tensyn_program',array('program_default_name'=>$program_default_name,'platform'=>$platform))->execute();
    }
}
