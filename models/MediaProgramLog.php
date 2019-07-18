<?php
namespace app\models;

use PHPUnit\Framework\Exception;
use Yii;
use yii\db\Query;
use yii\base\Model;

class MediaProgramLog extends Model{
    // 添加一条剧目信息
    public function add($input){
        $class_mediaRegex=new MediaRegex;
        $class_mediaUnvalid=new MediaUnvalid;
        $r=0;
        $msg="";

        do{
            $program_default_name=$input["program_default_name"];
            $platform=$input["platform"];

            if(!MediaRegex::check_program_default_name($program_default_name)){
                $msg="剧目原名不正确";
                break;
            }

            // 本季预估单集播放量=“本季预估播放量”*10000/(集数/期数)
            if(intval($input["play3"])!==0){
                $input["play6"]=round(doubleval($input["play1"])*10000/intval($input["play3"]),2);
            }else{
                $input["play6"]="";
            }
            if(trim($input["start_time"])===""){$input["start_time"]="时间待定";}

            $media_old=(new Query)
                ->select('*')
                ->from('media_program_log')
                ->where(['=','status','0'])
                ->andWhere(['=','program_default_name',$program_default_name])
                ->andWhere(['=','platform',$platform])
                ->one();

            $transaction=Yii::$app->db->beginTransaction();
            try{
                if($media_old===false){
                    $input["user_id"]=0;
                    Yii::$app->db->createCommand()->insert('media_program_log',$input)->execute();
                    $media_id=Yii::$app->db->getLastInsertID();
                }else{
                    $media_id=$media_old["media_id"];

                    if(count($class_mediaUnvalid->get_all($media_id))){
                        $class_mediaUnvalid->delete($media_id);
                    }
                    $diff=array_diff_assoc($input,$media_old);
                    if(count($diff)>0){
                        Yii::$app->db->createCommand()->update('media_program_log',$diff,array("media_id"=>$media_id))->execute();
                    }
                }
                $data=array();
                $data["media_id"]=$media_id;
                foreach($input as $k=>$v){
                    $function_name="check_".$k;
                    if(trim($v)!==""&&method_exists($class_mediaRegex,$function_name)&&!MediaRegex::$function_name($v)){
                        $data["field"]=$k;
                        $class_mediaUnvalid->add($data);
                    }
                }
                $transaction->commit();
                $r=1;
                $msg="操作成功";
            }catch(Exception $e){
                $transaction->rollback();
                $r=0;
                $msg="操作失败";
            }
        }while(false);

        return array("r"=>$r,"msg"=>$msg);
    }

    public function get_by_id($media_id){
        return (new Query)
            ->select('*')
            ->from('media_program_log')
            ->where(['=','media_id',$media_id])
            ->one();
    }
    public function get_list($filters,$offset=false,$pagecount=false){
        $query=(new Query)
            ->select('*')
            ->from('media_program_log')
            ->where(['>','media_id',0]);

        if(count($filters)>0){
            foreach($filters as $k=>$v){
                if($v==""){continue;}
                switch($k){
                    case "status":
                        $query->andWhere(array('=', 'status', $v));
                        break;
                    case "user_id":
                        $query->andWhere(array('=','user_id',$v));
                        break;
                    case "input_id":
                        $query->andWhere(array('=','input_id',$v));
                        break;
                }
            }
        }

        if($offset!==false&&$pagecount!==false){
            $query->limit($pagecount)->offset($offset);
        }

        $data=$query->orderBy([
            'platform' => SORT_ASC,
        ])->all();

        $total_count=$query->count();
        if($pagecount===false){
            $page_count=0;
        }else{
            $page_count=ceil($total_count/$pagecount);
        }

        return array(
            "data"=>$data,
            "total_count"=>$total_count,
            "page_count"=>$page_count
        );
    }


    public function update($data,$where){
        return Yii::$app->db->createCommand()->update('media_program_log',$data,$where)->execute();
    }
    public function delete($media_id){
        Yii::$app->db->createCommand()->delete('media_program_log',array("media_id"=>$media_id))->execute();
    }
    public function audit($id,$type){
        $ids=explode(",",$id);
        switch($type){
            case "yes":
                $status=2;
                break;
            case "no":
                $status=-2;
                break;
        }
        $transaction=Yii::$app->db->beginTransaction();
        try{
            foreach($ids as $media_id){
                Yii::$app->db->createCommand()->update('media_program_log',array("pass_time"=>date("Y-m-d H:i:s"),"status"=>$status,"update_date"=>date("Y-m-d")),array("media_id"=>$media_id))->execute();
                Yii::$app->db->createCommand()->update('media_attach_log',array("status"=>$status),array("media_id"=>$media_id))->execute();
            }
            $transaction->commit();
            $r=1;
            $msg="操作成功";
        }catch(Exception $e){
            $transaction->rollback();
            $r=0;
            $msg="操作失败";
        }
        return array(
            "r"=>$r,
            "msg"=>$msg
        );
    }
    public function set_online($media){
        $class_crawler=new Crawler;
        $class_tensynName=new TensynName;
        $class_mediaAttach=new MediaAttach;
        $class_mediaProgram=new MediaProgram;
        $class_program=new Program;

        $media["update_date"]=date("Y-m-d");
        $media_id=$media["media_id"];
        $program_default_name=$media["program_default_name"];
        $platform=$media["platform"];

        // 添加线上媒体数据
        $old=$class_mediaProgram->get($program_default_name,$platform);
        if(!$old){
            $class_mediaProgram->delete($program_default_name,$platform);
        }
        $class_mediaProgram->add($media);

        // 添加爬虫视频设置
        $crawler_video=$class_crawler->get_video_by_name($program_default_name);
        if(!$crawler_video){
            $class_crawler->add_video($program_default_name,$media["start_type"]);
        }
        // 添加腾信名称
        $tensyn_name=$class_tensynName->get($program_default_name,$platform);
        if(!$tensyn_name){
            $class_tensynName->add($program_default_name,$platform);
        }
        $class_mediaAttach->set_online($media_id,$program_default_name,$platform);
        // 判断剧目状态,是否下架剧目
        $start_type=trim($media["start_type"]);
        if($start_type==="播出中"||$start_type==="已播完"){
            $class_tensynProgram=new TensynProgram;
            $class_tensynProgram->delete($program_default_name,$platform);
            $class_program->delete($program_default_name,$platform);
        }else{
            // 线上有剧目则更新数据
            $old_program=$class_program->get($program_default_name,$platform);
            if(is_array($old_program)&&count($old_program)>0){
                // 组合数据

                // 字段转换
                $media_switch=array(
                    "type"=>"property_name", // 资源类型->内容属性
                    "satellite"=>"fanshuchu", // 播出卫视->反输出电电视
                    "platform"=>"platform_name", // 媒体平台
                    "play_time"=>"start_play", // 播出时间->开播时间
                    "content_type"=>"type_name", // 内容类型
                    "play3"=>"episode", // 集数期数
                    "play6"=>"play1", // 本季预估单集播放量
                );

                $data=array();
                foreach($media as $k=>$v){
                    if(array_key_exists($k,$media_switch)){
                        $data[$media_switch[$k]]=$v;
                    }
                }

                $diff=array_diff($data,$old_program);
                if(count($diff)>0){ // 有更新数据
                    $diff["media_id"]=$media_id;
                    $old_program_id=$old_program["program_id"];
                    $class_program->update($diff,array("program_id"=>$old_program_id));
                }
            }
        }
    }

    // 获取状态(新增,未更新,更新,删除)
    public function get_type_status($program){
        $class_system=new System;
        $class_mediaProgram=new MediaProgram;
        $media_fields=$class_system->get_media_fields();
        foreach($media_fields as $m){
            $valid_field[]=$m["field"];
        }
//        $valid_field=array(
//            "program_name","program_default_name","type","play_time",
//            "platform","start_time","copyright","start_type","satellite","creator",
//            "content_type","team","intro","play1","play3","play6"
//        );
        $media_id=$program["media_id"];
        $program_default_name=$program["program_default_name"];
        $platform=$program["platform"];
        $valid_program=$class_mediaProgram->get($program_default_name,$platform);

        if($valid_program===false){
            $old=(new Query)
                ->select('*')
                ->from('media_program_log')
                ->where(array('=','program_default_name',$program_default_name))
                ->andWhere(array('=','platform',$platform))
                ->andWhere(array('=','delete_status',1))
                ->all();
            if(count($old)>0){
                $status=-1;
            }else{
                $status=0;
            }
        }else{
            $diff=false;
            foreach($valid_field as $v){
                if(trim($program[$v])!=trim($valid_program[$v])){
                    $diff=true;
                    break;
                }
            }
            if($diff){
                $status=2;
            }else{
                $status=1;
            }
        }
        return $status;
    }
}

