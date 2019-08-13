<?php
/**
 * Created by PhpStorm.
 * User: zhangjiali
 * Date: 2019-08-12
 * Time: 17:01
 */

namespace app\models;

use Yii;
use yii\db\Query;
use yii\base\Model;

class TensynAttach extends Model{
    /**
     * 获取线上剧目所有附件
     * @param $program_default_name string 剧目原名
     * @param $platform string 媒体平台
     * @return array
     */
    public function get_all($program_default_name,$platform){
        return (new Query)
            ->select('*')
            ->from('tensyn_attach')
            ->where(array('=','program_default_name',$program_default_name))
            ->andWhere(array('=','platform',$platform))
            ->all();
    }
    /**
     * 删除线上附件
     * @param $program_default_name string 剧目原名
     * @param $platform string 媒体平台
     * @throws \yii\db\Exception
     */
    public function delete($program_default_name,$platform){
        $attachs=$this->get_all($program_default_name,$platform);
        if(count($attachs)){
            foreach($attachs as $attach){
                Yii::$app->db->createCommand()->delete('tensyn_attach',array("program_default_name"=>$program_default_name,"platform"=>$platform))->execute();
                unlink(Yii::$app->params['UPLOAD_DIR'].$attach["url"]);
            }
        }
    }
}