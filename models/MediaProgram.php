<?php
namespace app\models;

use Yii;
use yii\db\Query;
use yii\base\Model;

class MediaProgram extends Model{
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
     * @param $program_default_name string 剧目原名
     * @param $platform string 媒体平台
     * @return int
     * @throws \yii\db\Exception
     */
    public function delete($program_default_name,$platform){
        return Yii::$app->db->createCommand()->delete('media_program',array("program_default_name"=>$program_default_name,"platform"=>$platform))->execute();
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
}
