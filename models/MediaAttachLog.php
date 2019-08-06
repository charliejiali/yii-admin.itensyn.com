<?php
namespace app\models;

use PHPUnit\Framework\Exception;
use Yii;
use yii\db\Query;
use yii\base\Model;

class MediaAttachLog extends Model{
    /**
     * 获取某个剧目所有附件
     * @param $media_id
     * @return array
     */
    public static function get_all($media_id){
        return (new Query)
            ->select('*')
            ->from('media_attach_log')
            ->where(['=','media_id',$media_id])
            ->all();
    }
    /**
     * 删除某个剧目所有附件
     * @param $media_id
     * @throws \yii\db\Exception
     */
    public function delete($media_id){
        Yii::$app->db->createCommand()->delete('media_attach_log',array("media_id"=>$media_id))->execute();
    }
    /**
     * 上传附件
     * @param $media_id
     * @param $type
     * @param $name
     * @param $file_path
     * @return bool|string
     * @throws \yii\db\Exception
     */
    public function add($media_id,$type,$name,$file_path){
        $old=(new Query)
            ->select('*')
            ->from('media_attach_log')
            ->where(['=','media_id',$media_id])
            ->andWhere(['=','type',$type])
            ->one();

        if($old==false){
            $class_mediaprogramlog=new MediaProgramLog;
            $media=$class_mediaprogramlog->get_by_id($media_id);

            $data=array(
                "media_id"=>$media_id,
                "program_default_name"=>$media["program_default_name"],
                "platform"=>$media["platform"],
                "type"=>$type,
                "url"=>"/temp/".$name,
                "name"=>$name,
                "status"=>"0"
            );
            $re=Yii::$app->db->createCommand()->insert('media_attach_log',$data)->execute();
            if(!$re){
                return "上传失败";
            }
        }else{
            if($old["url"]!=$file_path&&$old["name"]!=$name){
                $re=Yii::$app->db->createCommand()->update('media_attach_log',array('url'=>$file_path,"name"=>$name),array('media_id'=>$media_id,"type"=>$type))->execute();
                if(!$re){
                    return "上传失败";
                }
                unlink($old["url"]);
            }
        }
        return true;
    }
    /**
     * 上线剧目附件
     * @param $media_id string id
     * @param $program_default_name string 剧目原名
     * @param $platform string 媒体平台
     * @throws \yii\db\Exception
     */
    public function set_online($media_id,$program_default_name,$platform){
        $class_mediaAttach=new MediaAttach;
        $transaction=Yii::$app->db->beginTransaction();
        try{
            $attachs=$this->get_all($media_id);
            if(count($attachs)>0){
                // 有新上传附件
                foreach($attachs as $attach){
                    $type=$attach["type"];
                    $current_url=$attach["url"];
                    $target_url="/".$type."/".$attach["name"];
                    $old_attach=$class_mediaAttach->get_one($program_default_name,$platform,$type);
                    if(!$old_attach){
                        // 线上无当前类型附件，直接上传
                        $attach["url"]=$target_url;
                        $class_mediaAttach->add($attach);
                    }else{
                        // 线上有当前类型附件，更新数据
                       $class_mediaAttach->update($media_id,$target_url,$program_default_name,$platform,$type,$attach);
                    }
                    if(!copy(Yii::$app->params['UPLOAD_DIR'].$current_url,Yii::$app->params['UPLOAD_DIR'].$target_url)){
                        throw new Exception('复制文件失败');
                    }
//                unlink(Yii::$app->params['UPLOAD_DIR'].$current_url);
                }

            }else{
                //没有新上传附件，保留以前的附件
                $old_attachs=$class_mediaAttach->get_all($program_default_name,$platform);
                if(count($old_attachs)>0){
                   $class_mediaAttach->update_id($media_id,$program_default_name,$platform);
                }
            }
            $transaction->commit();
        }catch(Exception $e){
            $transaction->rollback();
        }
    }
}
