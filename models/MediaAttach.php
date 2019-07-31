<?php
namespace app\models;

use PHPUnit\Framework\Exception;
use Yii;
use yii\db\Query;
use yii\base\Model;

class MediaAttach extends Model{
    public static function get_log($media_id){
        return (new Query)
            ->select('*')
            ->from('media_attach_log')
            ->where(['=','media_id',$media_id])
            ->all();
    }
    public static function get_all($media_id){
        return (new Query)
            ->select('*')
            ->from('media_attach_log')
            ->where(['=','media_id',$media_id])
            ->all();
    }
    public function delete($media_id,$path){
        Yii::$app->db->createCommand()->delete('media_attach_log',array("media_id"=>$media_id))->execute();
        unlink($path);
    }

    /**
     * 保存文件信息
     * @param $media_id 剧目id
     * @param $type 类型(剧照、视频、招商资源包)
     * @param $name 名称
     * @param $file_path 路径
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
                "url"=>$file_path,
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
    public function set_online($media_id,$program_default_name,$platform){
        $transaction=Yii::$app->db->beginTransaction();
        try{
            $attachs=$this->get_log($media_id);
            if(count($attachs)>0){
                foreach($attachs as $attach){
                    $type=$attach["type"];
                    $current_url=$attach["url"];
                    $target_url="/".$type."/".$attach["name"];
                    $old_attach=(new Query)
                        ->select('*')
                        ->from('media_attach')
                        ->where(array('=','type',$type))
                        ->andWhere(array('=','program_default_name',$program_default_name))
                        ->andWhere(array('=','platform',$platform))
                        ->one();
                    if(!$old_attach){
                        $attach["url"]=$target_url;
                        Yii::$app->db->createCommand()->insert('media_attach',$attach)->execute();
                    }else{
                        Yii::$app->db->createCommand()->update('media_attach',array(
                            "media_id"=>$media_id,"name"=>$attach["name"],"url"=>$target_url
                        ),array(
                            "platform"=>$platform,"program_default_name"=>$program_default_name,"type"=>$type
                        ))->execute();
                    }
                    if(!copy(Yii::$app->params['UPLOAD_DIR'].$current_url,Yii::$app->params['UPLOAD_DIR'].$target_url)){
                        throw new Exception('复制文件失败');
                    }
//                unlink(Yii::$app->params['UPLOAD_DIR'].$current_url);
                }

            }else{
                $old_attachs=(new Query)
                    ->select('*')
                    ->from('media_attach')
                    ->where(array('=','program_default_name',$program_default_name))
                    ->andWhere(array('=','platform',$platform))
                    ->all();
                if(count($old_attachs)>0){
                    Yii::$app->db->createCommand()->update('media_attach',array(
                        "media_id"=>$media_id
                    ),array(
                        "platform"=>$platform,"program_default_name"=>$program_default_name
                    ))->execute();
                }
            }
            $transaction->commit();
        }catch(Exception $e){
            $transaction->rollback();
        }
    }
}
