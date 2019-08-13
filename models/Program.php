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

    /**
     * 删除线上剧目和得分
     * @param $program_default_name string 剧目原名
     * @param $platform string 媒体平台
     * @throws \yii\db\Exception
     */
    public function delete($program_default_name,$platform){
        $program=$this->get($program_default_name,$platform);
        if($program){
            $program_id=$program["program_id"];
            Yii::$app->db->createCommand()->delete('program',array('program_default_name'=>$program_default_name,'platform_name'=>$platform))->execute();
            Yii::$app->db->createCommand()->delete('score',array("program_id"=>$program_id))->execute();
            // 更新历史剧目数据
            $class_programHistory=new ProgramHistory;
            $class_programHistory->delete($program_default_name,$platform);
        }
    }
}
