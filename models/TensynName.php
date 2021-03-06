<?php
namespace app\models;

use PHPUnit\Framework\Exception;
use Yii;
use yii\base\Model;
use yii\db\Query;

class TensynName extends Model{
    /**
     * 获取剧目的腾信名称
     * @param $name string 剧目原名
     * @param $platform string 媒体平台
     * @return array|bool
     */
    public function get($name,$platform){
        return (new Query)
            ->select('*')
            ->from('tensyn_program_name')
            ->where(array('=','program_default_name',$name))
            ->andWhere(array('=','platform',$platform))
            ->one();
    }
    /**
     * 创建剧目腾信名称
     * @param $name string 剧目原名
     * @param $platform string 媒体平台
     * @return int
     * @throws \yii\db\Exception
     */
    public function add($name,$platform){
        return Yii::$app->db->createCommand()->insert('tensyn_program_name',array(
            "program_default_name"=>$name,
            "platform"=>$platform,
            "tensyn_name"=>$name
        ))->execute();
    }

    /**
     * 更新腾信名称
     * @param $program_default_name string 剧目原名
     * @param $platform string 媒体平台
     * @param $tensyn_name string 腾信名称
     * @return int
     * @throws \yii\db\Exception
     */
    public function update($program_default_name,$platform,$tensyn_name){
        return Yii::$app->db->createCommand()->update('tensyn_program_name',
            array("tensyn_name"=>$tensyn_name),
            array("program_default_name"=>$program_default_name,"platform"=>$platform
            ))->execute();
    }

    /**
     * 删除腾信名称
     * @param $program_default_name string 剧目原名
     * @param $platform string 媒体平台
     * @return int
     * @throws \yii\db\Exception
     */
    public function delete($program_default_name,$platform){
        return Yii::$app->db->createCommand()->delete('tensyn_program_name',array(
            "program_default_name"=>$program_default_name,
            "platform"=>$platform
        ))->execute();
    }
}
