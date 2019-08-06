<?php
namespace app\models;

use PHPUnit\Framework\Exception;
use Yii;
use yii\base\Model;
use yii\db\Query;

class MediaInput extends Model{
    /**
     * 获取媒体待审核录入单列表
     * @param $filters array 过滤条件
     * @param bool $offset
     * @param bool $pagecount
     * @return array
     */
    public function get_list($filters,$offset=false,$pagecount=false){
        $query=(new Query)
            ->select('*')
            ->from('media_input')
            ->where(array('=', 'status',0))
            ->orderBy(array('input_id'=>SORT_DESC));

        if(count($filters)>0){
            foreach($filters as $k=>$v){
                if($v==""){continue;}
                switch($k){
                    case "start_date":
                        $query->andWhere(array('>=', 'create_date', $v));
                        break;
                    case "end_date":
                        $query->andWhere(array('<=', 'create_date', $v));
                        break;
                    case "supplier":
                        $query->andWhere(array('=', 'supplier', $v));
                        break;
                }
            }
        }
        $data=$query->limit($pagecount) ->offset($offset)->all();

        $total_count=$query->count();
        $page_count=ceil($total_count/$pagecount);

        return array(
            "data"=>$data,
            "total_count"=>$total_count,
            "page_count"=>$page_count
        );
    }
//    public function add($data){
//        $r=Yii::$app->db->createCommand()->insert('user', $data)->execute();
//        if($r){
//            $r=Yii::$app->db->getLastInsertID();
//        }
//        return $r;
//    }

    /**
     * 生成录入单名称
     * @param $user_id string
     * @return string
     */
    public function make_name($user_id){
        $input=(new Query)
            ->select('*')
            ->from('media_input')
            ->where(array('=','create_date',date("Y-m-d")))
            ->andWhere(array('=','user_id',$user_id))
            ->orderBy(array('input_id'=>SORT_DESC))
            ->one();
        if(!$input){
            $input_name="MT".date("Ymd")."0001";
        }else{
            $input_name="MT".date("Ymd").str_pad((intval(substr($input["name"],-4))+1),4,"0",STR_PAD_LEFT);
        }
        return $input_name;
    }

    /**
     * 创建录入单
     * @param $user_id string
     * @param $input_name string 录入单名称
     * @param $platform string 媒体平台
     * @param $remark string 备注
     * @param $medias array 剧目数据
     * @throws \yii\db\Exception
     */
    public function create($user_id,$input_name,$platform,$remark,$medias){
        $class_mediaUnvalid=new MediaUnvalid;
        $class_mediaProgramLog=new MediaProgramLog;

        Yii::$app->db->createCommand()->insert('media_input',array(
            "user_id"=>$user_id,
            "name"=>$input_name,
            "supplier"=>$platform,
            "remark"=>$remark,
            "status"=>0,
            "create_time"=>date("Y-m-d H:i:s"),
            "create_date"=>date("Y-m-d"),
            "total"=>count($medias)
        ))->execute();
        $input_id=Yii::$app->db->getLastInsertID();

        foreach($medias as $media){
            $media_id=$media["media_id"];
            $unvalid=$class_mediaUnvalid->get_all($media_id);
            if(count($unvalid)>0){continue;}
            $type_status=$class_mediaProgramLog->get_type_status($media);
            Yii::$app->db->createCommand()->update('media_program_log',array(
                "submit_time"=>date("Y-m-d H:i:s"),
                "status"=>1,
                "input_id"=>$input_id,
                "user_id"=>$user_id,
                "update_date"=>date("Y-m-d"),
                "type_status"=>$type_status
            ),array("media_id"=>$media_id))->execute();
        }
    }

    /**
     * 获取指定录入单的剧目
     * @param $input_id string
     * @param $offset string
     * @param $pagecount string
     * @return array
     */
    public function get_programs($input_id,$offset,$pagecount){
        $query=(new Query)
            ->select('*')
            ->from('media_program_log')
            ->where(array('=', 'input_id',$input_id))
            ->orderBy(array('input_id'=>SORT_DESC));

        $data=$query->limit($pagecount) ->offset($offset)->all();

        $total_count=$query->count();
        $page_count=ceil($total_count/$pagecount);

        return array(
            "data"=>$data,
            "total_count"=>$total_count,
            "page_count"=>$page_count
        );
    }
    public function get_by_id($input_id){
        return (new Query)
            ->select('*')
            ->from('media_input')
            ->where(array('=','input_id',$input_id))
            ->one();
    }
    public function audit(){

    }
}
