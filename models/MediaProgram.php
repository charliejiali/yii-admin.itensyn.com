<?php
namespace app\models;

use Yii;
use yii\db\Query;
use yii\base\Model;

class MediaProgram extends Model{
    public function get_by_id($media_id){
        return (new Query)
            ->select('*')
            ->from('media_program')
            ->where(array('=','media_id',$media_id))
            ->one();
    }
    /**
     * 获取线上媒体剧目信息
     * @param $program_default_name string 剧目原名
     * @param $platform string 媒体平台
     * @return array|bool
     */
    public function get($program_default_name,$platform){
        return (new Query)
            ->select('*')
            ->from('media_program')
            ->where(array('=','program_default_name',$program_default_name))
            ->andWhere(array('=','platform',$platform))
            ->one();
    }

    /**
     * 删除线上媒体剧目
     * @param $media_id string 媒体id
     * @param $program_default_name string 剧目原名
     * @param $platform string 媒体平台
     * @return int
     * @throws \yii\db\Exception
     */
    public function delete($media_id,$program_default_name,$platform){
        Yii::$app->db->createCommand()->delete('media_program',array("program_default_name"=>$program_default_name,"platform"=>$platform))->execute();
        Yii::$app->db->createCommand()->update('media_program_log',array("delete_status"=>1,"update_time"=>date("Y-m-d H:i:s")),array("media_id"=>$media_id))->execute();
    }
    /**
     * 增加线上媒体剧目
     * @param $media array 剧目信息
     * @return int
     * @throws \yii\db\Exception
     */
    public function add($media){
        return Yii::$app->db->createCommand()->insert('media_program',$media)->execute();
    }

    /**
     * 获取媒体线上剧目列表
     * @param $options array 过滤条件
     * @param $offset
     * @param $pagecount
     * @return array
     */
    public function get_list($options,$offset,$pagecount){
        $query=(new Query)
            ->select('*')
            ->from('media_program as m')
            ->leftJoin('tensyn_program_name as t', 'm.program_default_name=t.program_default_name and m.platform=t.platform')
            ->where(['>','m.media_id',0]);

        if(count($options)>0){
            foreach($options as $k=>$v){
                if($v==""){continue;}
                switch($k){
                    case "start_date":
                        $start_date=$v." 00:00:00 ";
                        $query->andWhere(array('>=','m.pass_time',$start_date));
                        break;
                    case "end_date":
                        $end_date=$v." 23:59:59 ";
                        $query->andWhere(array('<=','m.pass_time',$end_date));
                        break;
                    case "status":
                        $query->andWhere(array('=','m.status',$v));
                        break;
                    case "type":
                        $query->andWhere(array('like','m.type',$v));
                        break;
                    case "program_name":
                        $query->andWhere(array('like','m.program_name',$v));
                        break;
                    case "year":
                        $query->andWhere(array('like','m.play_time',$v));
                        break;
                    case "season":
                        $query->andWhere(array('like','m.play_time',$v));
                        break;
                }
            }
        }

        if($offset!==false&&$pagecount!==false){
            $query->limit($pagecount)->offset($offset);
        }

        $data=$query->orderBy([
            'media_id' => SORT_DESC,
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

    /**
     * 待删除剧目
     * @param $media_id string
     * @return int
     * @throws \yii\db\Exception
     */
    public function pre_delete($media_id){
        return Yii::$app->db->createCommand()->update('media_program',
            array("status"=>3),
            array("media_id"=>$media_id)
        )->execute();
    }

    public function delete_pass($media_id){
        $program=$this->get_by_id($media_id);
        $program_default_name=$program["program_default_name"];
        $platform=$program["platform"];

        // 删除媒体数据
        $this->delete($media_id,$program_default_name,$platform);

        // 删除媒体附件
        $class_mediaAttach=new MediaAttach;
        $class_mediaAttach->delete($program_default_name,$platform);

        // 删除腾信数据
        $class_tensynProgram=new TensynProgram;
        $class_tensynProgram->delete($program_default_name,$platform);

        // 删除腾信附件
        $class_tensynAttach=new TensynAttach;
        $class_tensynAttach->delete($program_default_name,$platform);

        // 删除腾信名称
        $class_tensynName=new TensynName;
        $class_tensynName->delete($program_default_name,$platform);

        // 删除线上剧目和得分
        $class_program=new Program;
        $class_program->delete($program_default_name,$platform);
    }

    /**
     * 待删除审批——拒绝
     * @param $media_id string 媒体剧目id
     * @return int
     * @throws \yii\db\Exception
     */
    public function delete_reject($media_id){
        Yii::$app->db->createCommand()->update('media_program',
            array("status"=>2),
            array("media_id"=>$media_id)
        )->execute();
    }
}
