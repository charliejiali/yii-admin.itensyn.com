<?php
namespace app\models;

use PHPUnit\Framework\Exception;
use Yii;
use yii\db\Query;
use yii\base\Model;

class MediaAttach extends Model{
    /**
     * 获取线上剧目所有附件
     * @param $program_default_name string 剧目原名
     * @param $platform string 媒体平台
     * @return array
     */
    public function get_all($program_default_name,$platform){
        return (new Query)
            ->select('*')
            ->from('media_attach')
            ->where(array('=','program_default_name',$program_default_name))
            ->andWhere(array('=','platform',$platform))
            ->all();
    }
    /**
     * 获取一个附件
     * @param $program_default_name string 剧目原名
     * @param $platform string 媒体平台
     * @param $type string 附件类型
     * @return array|bool
     */
    public function get_one($program_default_name,$platform,$type){
        return (new Query)
            ->select('*')
            ->from('media_attach')
            ->where(array('=','program_default_name',$program_default_name))
            ->andWhere(array('=','platform',$platform))
            ->andWhere(array('=','type',$type))
            ->one();
    }
    /**
     * 更新附件id
     * @param $media_id string 剧目id
     * @param $program_default_name string 剧目原名
     * @param $platform string 媒体平台
     * @throws \yii\db\Exception
     */
    public function update_id($media_id,$program_default_name,$platform){
        Yii::$app->db->createCommand()->update('media_attach',array(
            "media_id"=>$media_id
        ),array(
            "platform"=>$platform,"program_default_name"=>$program_default_name
        ))->execute();
    }
    /**
     * 上线附件
     * @param $attach array 附件数据
     * @throws \yii\db\Exception
     */
    public function add($attach){
        Yii::$app->db->createCommand()->insert('media_attach',$attach)->execute();
    }
    /**
     * 更新附件数据
     * @param $media_id int 剧目id
     * @param $target_url string 附件路径
     * @param $program_default_name string 剧目原名
     * @param $platform string 媒体平台
     * @param $type string 附件类型
     * @param $attach array 附件数据
     * @throws \yii\db\Exception
     */
    public function update($media_id,$target_url,$program_default_name,$platform,$type,$attach){
        Yii::$app->db->createCommand()->update('media_attach',array(
            "media_id"=>$media_id,"name"=>$attach["name"],"url"=>$target_url
        ),array(
            "platform"=>$platform,"program_default_name"=>$program_default_name,"type"=>$type
        ))->execute();
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
                Yii::$app->db->createCommand()->delete('media_attach',array("program_default_name"=>$program_default_name,"platform"=>$platform))->execute();
                if(file_exists(Yii::$app->params['UPLOAD_DIR'].$attach["url"])){
                    unlink(Yii::$app->params['UPLOAD_DIR'].$attach["url"]);
                }
            }
        }
    }
}
