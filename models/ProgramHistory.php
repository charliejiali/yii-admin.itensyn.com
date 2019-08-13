<?php
/**
 * Created by PhpStorm.
 * User: zhangjiali
 * Date: 2019-08-12
 * Time: 17:17
 */
namespace app\models;


use Yii;
use yii\db\Query;
use yii\base\Model;

class ProgramHistory extends Model{
    /**
     * 获取指定历史剧目
     * @param $program_default_name string 剧目原名
     * @param $platform string 平台
     * @return array|bool
     */
    public function get($program_default_name,$platform){
        return (new Query)
            ->select('*')
            ->from('program_history')
            ->where(array('=','program_default_name',$program_default_name))
            ->andWhere(array('=','platform_name',$platform))
            ->one();
    }

    /**
     * 创建历史剧目数据
     * @param $program array 剧目数据
     * @return int
     * @throws \yii\db\Exception
     */
    public function add($program){
        return Yii::$app->db->createCommand()->insert('program_history',$program)->execute();
    }

    /**
     * 更新历史剧目数据
     * @param $program_default_name string 剧目原名
     * @param $platform string 媒体平台
     * @throws \yii\db\Exception
     */
    public function delete($program_default_name,$platform){
        $program=$this->get($program_default_name,$platform);
        if($program){
            Yii::$app->db->createCommand()->delete(
                'program_history'
                ,array("program_default_name"=>$program_default_name,"platform"=>$platform)
            )->execute();
        }
        $this->add($program);
    }
}