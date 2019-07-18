<?php
namespace app\controllers;

use Yii;
use app\models\Crawler;

class CrawlerController extends UserController{
    public $pageTitle="爬虫数据";
    public $pageNavId="3";

    // 视频
    // 视频列表
    public function actionVideoList(){
        $pageNavSub="31";
        $class_crawler=new Crawler;

        $get = Yii::$app->request->get();

        $page=isset($get["p"])?intval($get["p"]):1; // 当前页数
        $pagecount=isset($get["c"])?intval($get["c"]):5; // 每页显示数量
        $offset=($page-1)*$pagecount;

        $result=$class_crawler->get_videos($get,$offset,$pagecount);

        $list=$result["data"];
        $list_count=$result["total_count"];
        $page_count=$result["page_count"];

        $crawler_status=$class_crawler->get_select_status();
        $crawler_update=$class_crawler->get_update('video');

        $masterpiece_type=$class_crawler->get_masterpiece_type();
        $play_status=$class_crawler->get_play_status();

        $list_status=array();
        $list_masterpiece=array();
        foreach($list as $l){
            $list_status[$l["id"]]=$class_crawler->make_status(intval($l["error"]),intval($l["crawled"]));
            foreach($masterpiece_type as $k=>$v){
                $list_masterpiece[$l["id"]][$k]=$class_crawler->get_masterpiece($l[$k],$k);
            }
        }

        return $this->render('video',array(
            "pageNavSub"=>$pageNavSub,
            "crawler_status"=>$crawler_status,
            "crawler_update"=>$crawler_update,
            "masterpiece_type"=>$masterpiece_type,
            "play_status"=>$play_status,
            "filters"=>$get,
            "list"=>$list,
            "list_status"=>$list_status,
            "list_masterpiece"=>$list_masterpiece,
            "page_info"=>array(
                "page"=>$page,
                "pagecount"=>$pagecount,
                "list_count"=>$list_count,
                "page_count"=>$page_count
            )
        ));
    }

    // 微博
    // 微博列表
    public function actionWeiboList(){
        $pageNavSub="32";
        $class_crawler=new Crawler;

        $get = Yii::$app->request->get();

        $page=isset($get["p"])?intval($get["p"]):1; // 当前页数
        $pagecount=isset($get["c"])?intval($get["c"]):5; // 每页显示数量
        $offset=($page-1)*$pagecount;

        $result=$class_crawler->get_weibos($get,$offset,$pagecount);

        $list=$result["data"];
        $list_count=$result["total_count"];
        $page_count=$result["page_count"];

        $crawler_status=$class_crawler->get_select_status();
        $crawler_update=$class_crawler->get_update('weibo');

        $list_status=array();
        foreach($list as $l){
            $list_status[$l["name"]]=$class_crawler->make_status(intval($l["error"]),intval($l["crawled"]));
        }

        return $this->render('weibo',array(
            "pageNavSub"=>$pageNavSub,
            "crawler_status"=>$crawler_status,
            "crawler_update"=>$crawler_update,
            "filters"=>$get,
            "list"=>$list,
            "list_status"=>$list_status,
            "page_info"=>array(
                "page"=>$page,
                "pagecount"=>$pagecount,
                "list_count"=>$list_count,
                "page_count"=>$page_count
            )
        ));
    }


    // 贴吧
    // 贴吧列表
    public function actionTiebaList(){
        $pageNavSub="33";
        $class_crawler=new Crawler;

        $get = Yii::$app->request->get();

        $page=isset($get["p"])?intval($get["p"]):1; // 当前页数
        $pagecount=isset($get["c"])?intval($get["c"]):5; // 每页显示数量
        $offset=($page-1)*$pagecount;

        $result=$class_crawler->get_tiebas($get,$offset,$pagecount);

        $list=$result["data"];
        $list_count=$result["total_count"];
        $page_count=$result["page_count"];

        $crawler_status=$class_crawler->get_select_status();
        $crawler_update=$class_crawler->get_update('tieba');

        $list_status=array();
        foreach($list as $l){
            $list_status[$l["name"]]=$class_crawler->make_status(intval($l["error"]),intval($l["crawled"]));
        }

        return $this->render('tieba',array(
            "pageNavSub"=>$pageNavSub,
            "crawler_status"=>$crawler_status,
            "crawler_update"=>$crawler_update,
            "filters"=>$get,
            "list"=>$list,
            "list_status"=>$list_status,
            "page_info"=>array(
                "page"=>$page,
                "pagecount"=>$pagecount,
                "list_count"=>$list_count,
                "page_count"=>$page_count
            )
        ));
    }
    // 贴吧编辑页面
    public function actionTiebaEdit(){
        $pageNavSub = 33;
        $old=array();
        $class_crawler=new Crawler;
        $status=0;

        $get=Yii::$app->request->get();
        $act=array_key_exists("act",$get)&&trim($get["act"])==="edit"?"edit":"add";

        if($act==="edit"){
            $name=$get["name"];
            $old=$class_crawler->get_tieba($name);
            $status=$class_crawler->make_status(intval($old["error"]),intval($old["crawled"]));
        }

        $crawler_status=$class_crawler->get_select_status();

        return $this->render('tieba-edit',array(
            "pageNavSub"=>$pageNavSub,
            "crawler_status"=>$crawler_status,
            "filters"=>$get,
            "old"=>$old,
            "status"=>$status,
            "act"=>$act
        ));
    }
    public function actionEditTieba(){
        $r=0;
        $data=array();
        $input=Yii::$app->request->post();
        $class_crawler=new Crawler;

        do{
            foreach($input as $k=>$v){
                $data[$k]=trim($v);
            }

            if($data["name"]===""){
                $msg="请填写名称";
                break;
            }

            $error_crawled=$this->_make_error_crawled($data["status"]);

            $d=array(
                "follow"=>$data["follow"],
                "post"=>$data["post"],
                "per"=>$data["per"],
                "crawled"=>$error_crawled["crawled"],
                "error"=>$error_crawled["error"]
            );
            $name=$data["name"];
            $old=$class_crawler->get_tieba($name);

            if($data["act"]=="add"){
                if($old!==false){
                    $msg="当前名称已存在";
                    break;
                }

                $d["name"]=$name;
                $d["create_time"]=date("Y-m-d H:i:s");
                $d["status"]=0;
                $r=Yii::$app->db->createCommand()->insert('crawler_tieba',$d)->execute();
                if(!$r){
                    $msg="创建失败";
                    break;
                }
            }else{
                if($old===false){
                    $msg="未能找到当前数据";
                    break;
                }
                $update=array_diff_assoc($d,$old);
                if(count($update)>0){
                    $update["update_time"]=date("Y-m-d H:i:s");
                    $r=Yii::$app->db->createCommand()->update('crawler_tieba',$update,array("name"=>$name))->execute();
                    if(!$r){
                        $msg="更新失败";
                        break;
                    }
                }
            }
            $r=1;
            $msg="success";
        }while(false);

        echo json_encode(array(
            "r"=>$r,
            "msg"=>$msg
        ));
    }


    private function _make_error_crawled($status){
        switch($status){
            case "-1":
                $error=1;
                $crawled=1;
                break;
            case "0":
                $error=0;
                $crawled=0;
                break;
            case "1":
                $error=0;
                $crawled=1;
                break;
        }
        return array(
            "error"=>$error,
            "crawled"=>$crawled
        );
    }
}
