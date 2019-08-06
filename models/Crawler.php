<?php
namespace app\models;

use PHPUnit\Framework\Exception;
use Yii;
use yii\base\Model;
use yii\db\Query;

class Crawler extends Model{
    private $masterpiece_type=array("male"=>"男主演","female"=>"女主演","host"=>"主持人","team"=>"制作团队");
    private $play_status=array("待播出","播出中","已播完");
    private $select_status=array(
        "-1"=>"异常",
        "0"=>"未采集",
        "1"=>"已采集"
    );

    // 采集状态select选项
    public function get_select_status(){
        return $this->select_status;
    }
    // 代表作类型
    public function get_masterpiece_type(){
        return $this->masterpiece_type;
    }
    // 播放状态
    public function get_play_status(){
        return $this->play_status;
    }
    // 获取爬虫更新信息
    public function get_update($name){
        return (new Query)
            ->select('*')
            ->from('crawler_update')
            ->where(array('=','name',$name))
            ->one();
    }
    // 生成状态
    public function make_status($error,$crawled){
        if($error===1){
            $status=-1;
        }else{
            if($crawled===1){
                $status=1;
            }else{
                $status=0;
            }
        }
        return $status;
    }

    // 视频

    // 获取视频
    public function get_videos($filters,$offset=false,$pagecount=false){
        $query=(new Query)
            ->select('*')
            ->from('crawler_video')
            ->where(['>','id',0]);

        if(count($filters)>0){
            foreach($filters as $k=>$v){
                if($v==""){continue;}
                switch($k){
                    case "ps":
                        $query->andWhere(array('=', 'play_status', $v));
                        break;
                    case "s":
                        switch($v){
                            case "-1":
                                $query->andWhere(array('=','error','1'));
                                $query->andWhere(array('=','crawled','1'));
                                break;
                            case "0":
                                $query->andWhere(array('=','error','0'));
                                $query->andWhere(array('=','crawled','0'));
                                break;
                            case "1":
                                $query->andWhere(array('=','error','0'));
                                $query->andWhere(array('=','crawled','1'));
                                break;
                        }
                        break;
                    case "q":
                        $query->andWhere("(program_name like '%{$v}%' or ex_program_name like '%{$v}%'  or male like '%{$v}%' or female like '%{$v}%' or host like '%{$v}%' or team like '%{$v}%' or guest like '%{$v}%' )");
                        break;
                }
            }
        }

        if($offset!==false&&$pagecount!==false){
            $query->limit($pagecount)->offset($offset);
        }

        $data=$query->orderBy([
            'id' => SORT_DESC,
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
     * 通过剧目名称获取视频设置
     * @param $name string 剧目名称
     * @return array|bool
     */
    public function get_video_by_name($name){
        return (new Query)
            ->select('*')
            ->from('crawler_video')
            ->where(array('=','program_name',$name))
            ->one();
    }
    /**
     * 创建视频爬虫基本信息
     * @param $name string 剧目原名
     * @param $start_type string 播放状态
     * @throws \yii\db\Exception
     */
    public function add_video($name,$start_type){
        Yii::$app->db->createCommand()->insert('crawler_video',array(
            "program_name"=>$name,
            "create_time"=>date("Y-m-d H:i:s"),
            "play_status"=>$start_type,
            "crawler_status"=>"未完成",
            "error"=>0,
            "crawled"=>0
        ))->execute();
    }

    // 代表作
    // 获取代表作
    public function get_masterpiece($name,$identity){
        $data=array(
            "program_name"=>"",
            "url"=>"",
            "pv_avg"=>""
        );

        $result=(new Query)
            ->select('*')
            ->from('crawler_video_masterpiece')
            ->where(array('=','name',$name))
            ->andWhere(array('=','identity',$identity))
            ->one();

        if($result!==false){
            $program_name=$result["program_name"];
            $data["program_name"]=$program_name;
            $program=(new Query)
                ->select('*')
                ->from('crawler_video')
                ->where(array('=','program_name',$program_name))
                ->one();
            if($program!==false){
                $data["url"]=$program["url"];
                $data["pv_avg"]=$program["pv_avg"];
            }
        }

        return $data;
    }

    // 微博
    // 获取微博
    public function get_weibos($filters,$offset=false,$pagecount=false){
        $query=(new Query)
            ->select('*')
            ->from('crawler_weibo')
            ->where(['!=','name','']);

        if(count($filters)>0){
            foreach($filters as $k=>$v){
                if($v==""){continue;}
                switch($k){
                    case "q":
                        $query->andWhere(array('like', 'name', $v));
                        break;
                    case "s":
                        switch($v){
                            case "-1":
                                $query->andWhere(array('=','error','1'));
                                $query->andWhere(array('=','crawled','1'));
                                break;
                            case "0":
                                $query->andWhere(array('=','error','0'));
                                $query->andWhere(array('=','crawled','0'));
                                break;
                            case "1":
                                $query->andWhere(array('=','error','0'));
                                $query->andWhere(array('=','crawled','1'));
                                break;
                        }
                        break;
                }
            }
        }

        if($offset!==false&&$pagecount!==false){
            $query->limit($pagecount)->offset($offset);
        }

        $data=$query->orderBy([
            'name' => SORT_ASC,
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

    // 贴吧
    // 获取贴吧列表
    public function get_tiebas($filters,$offset=false,$pagecount=false){
        $query=(new Query)
            ->select('*')
            ->from('crawler_tieba')
            ->where(['!=','name','']);

        if(count($filters)>0){
            foreach($filters as $k=>$v){
                if($v==""){continue;}
                switch($k){
                    case "q":
                        $query->andWhere(array('like', 'name', $v));
                        break;
                    case "s":
                        switch($v){
                            case "-1":
                                $query->andWhere(array('=','error','1'));
                                $query->andWhere(array('=','crawled','1'));
                                break;
                            case "0":
                                $query->andWhere(array('=','error','0'));
                                $query->andWhere(array('=','crawled','0'));
                                break;
                            case "1":
                                $query->andWhere(array('=','error','0'));
                                $query->andWhere(array('=','crawled','1'));
                                break;
                        }
                        break;
                }
            }
        }

        if($offset!==false&&$pagecount!==false){
            $query->limit($pagecount)->offset($offset);
        }

        $data=$query->orderBy([
            'name' => SORT_ASC,
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
    // 获取贴吧信息
    public function get_tieba($name){
        return (new Query)
            ->select('*')
            ->from('crawler_tieba')
            ->where(array('=','name',$name))
            ->one();
    }
}
