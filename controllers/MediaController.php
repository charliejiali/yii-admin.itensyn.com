<?php
namespace app\controllers;

use app\models\old;
use PHPUnit\Framework\Exception;
use Yii;
use app\models\MediaInput;
use app\models\MediaProgramLog;
use app\models\MediaProgram;
use app\models\MediaUnvalid;
use app\models\MediaAttachLog;
use app\models\MediaAttach;
use app\models\MediaUser;
use app\models\Upload;

class MediaController extends UserController{
    public $pageTitle="媒体数据";
    public $pageNavId="2";

    // 待审核录入单
    // 待审核录入单页面
    public function actionInputList(){
        $pageNavSub="21";

        $get = Yii::$app->request->get();

        $page=isset($get["p"])?intval($get["p"]):1; // 当前页数
        $pagecount=isset($get["c"])?intval($get["c"]):5; // 每页显示数量
        $offset=($page-1)*$pagecount;

        $class_mediainput=new MediaInput;
        $result=$class_mediainput->get_list($get,$offset,$pagecount);

        $list=$result["data"];
        $list_count=$result["total_count"];
        $page_count=$result["page_count"];

        return $this->render('input',array(
            "pageNavSub"=>$pageNavSub,
            "platforms"=>array("爱奇艺"),
            "filters"=>$get,
            "list"=>$list,
            "page_info"=>array(
                "page"=>$page,
                "pagecount"=>$pagecount,
                "list_count"=>$list_count,
                "page_count"=>$page_count
            )
        ));
    }
    // 录入单审批页面
    public function actionInputAuditList(){
        $pageNavSub="22";

        $get=Yii::$app->request->get();
        $input_id=$get["id"];

        $page=isset($get["p"])?intval($get["p"]):1; // 当前页数
        $pagecount=isset($get["c"])?intval($get["c"]):5; // 每页显示数量
        $offset=($page-1)*$pagecount;

        $class_mediaInput=new MediaInput;

        $result=$class_mediaInput->get_programs($input_id,$offset,$pagecount);
        $list=$result["data"];
        $list_count=$result["total_count"];
        $page_count=$result["page_count"];

        $attachs_temp=array(
            "poster"=>"未上传",
            "resource"=>"未上传",
            "video"=>"未上传"
        );
        $attachs=array();
        if(count($list)>0){
            foreach($list as $l){
                $at=array();
                $media_id=$l["media_id"];
                $results=MediaAttach::get_log($media_id);
                if(count($results)>0){
                    foreach($results as $r){
                        $at[]=$r["type"];
                    }
                }
                foreach($attachs_temp as $k=>$v){
                    if(in_array($k,$at)){
                        $attachs[$media_id][$k]="已上传";
                    }else{
                        $attachs[$media_id][$k]=$v;
                    }
                }
            }
        }

        $input=$class_mediaInput->get_by_id($input_id);
        $type_status=array(
            -1=>"delete",
            0=>"new",
            1=>"same",
            2=>"update"
        );
        $status=array(
            -2=>"审批未通过",
            -1=>"删除",
            0=>"草稿箱",
            1=>"未审批",
            2=>"审批通过",
            3=>"待删除"
        );

        return $this->render('audit',array(
            "pageNavSub"=>$pageNavSub,
            "type_status"=>$type_status,
            "status"=>$status,
            "list"=>$list,
            "attachs"=>$attachs,
            "input"=>$input,
            "page_info"=>array(
                "page"=>$page,
                "pagecount"=>$pagecount,
                "list_count"=>$list_count,
                "page_count"=>$page_count
            )
        ));
    }
    // 审批剧目
    public function actionMediaAudit(){
        $post=Yii::$app->request->post();
        $id=$post["id"];
        $type=$post["type"];
        $class_mediaProgramLog=new MediaProgramLog;
        $result=$class_mediaProgramLog->audit($id,$type);
        echo json_encode($result);
    }
    // 审批录入单
    public function actionInputAudit(){
        $post=Yii::$app->request->post();
        $input_id=$post["id"];
        $pass=0;
        $fail=0;
        $r=0;
        $msg="操作失败";

        do{
            $class_mediaProgramLog=new MediaProgramLog;
            $result=$class_mediaProgramLog->get_list(array("input_id"=>$input_id));
            $programs=$result["data"];
            foreach($programs as $p){
                $status=intval($p["status"]);
                if($status===1){
                    $msg="有未审批剧目";
                    break 2;
                }
                if($status===2){
                    $update[]=$p;
                    $pass++;
                }
                if($status===-2){
                    $fail++;
                }
            }
            $transaction=Yii::$app->db->beginTransaction();
            try{
                foreach($update as $u){
                    $class_mediaProgramLog->set_online($u);
                }
                Yii::$app->db->createCommand()->update('media_input',array("status"=>1,"update_date"=>date("Y-m-d"),"pass"=>$pass,"fail"=>$fail),array("input_id"=>$input_id))->execute();
                $transaction->commit();
                $r=1;
                $msg="操作成功";
            }catch(Exception $e){
                $transaction->rollback();
            }
        }while(false);

        echo json_encode(array(
            "r"=>$r,
            "msg"=>$msg
        ));
    }

    // 补充媒体数据
    // 补充媒体数据页面
    public function actionUploadList(){
        $pageNavSub="23";
        $unvalids=array();
        $attachs=array();
        $class_mediaProgamLog=new MediaProgramLog;
        $class_mediaUnvalid=new MediaUnvalid;

        $get = Yii::$app->request->get();
        $page=isset($get["p"])?intval($get["p"]):1; // 当前页数
        $pagecount=isset($get["c"])?intval($get["c"]):5; // 每页显示数量
        $offset=($page-1)*$pagecount;


        $result=$class_mediaProgamLog->get_list(array("status"=>"0","user_id"=>"0"),$offset,$pagecount);
        $list=$result["data"];
        $list_count=$result["total_count"];
        $page_count=$result["page_count"];

        if(count($list)>0){
            foreach($list as $l){
                $media_id=$l["media_id"];
                $results=$class_mediaUnvalid->get_all($media_id);
                if(count($results)>0){
                    foreach($results as $r){
                        $unvalids[$media_id][]=$r["field"];
                    }

                }
                $results=MediaAttach::get_log($media_id);
                if(count($results)>0){
                    foreach($results as $r){
                        $attachs[$media_id][$r["type"]]="/temp/".$r["name"];
                    }
                }
            }
        }

        return $this->render('upload',array(
            "pageNavSub"=>$pageNavSub,
            "list"=>$list,
            "unvalids"=>$unvalids,
            "attachs"=>$attachs,
            "page_info"=>array(
                "page"=>$page,
                "pagecount"=>$pagecount,
                "list_count"=>$list_count,
                "page_count"=>$page_count
            )
        ));
    }

    /**
     * 上传excel
     */
    public function actionUploadExcel(){
        $r=0;
        $msg="";
        do{
            // 将excel上传至服务器
            $class_upload=new Upload;
            $result=$class_upload->upload_file();
            if($result["r"]==0){
                $msg=$result["msg"];
                break;
            }
            // 读取服务器上excel数据
            $file_path=$result["file_path"];
            $result=$class_upload->import_excel( $file_path);
            unlink($file_path);
            if(is_string($result)){
                $msg=$result;
                break;
            }
            // 将数据写入数据库
            $class_mediaProgramLog=new MediaProgramLog;
            foreach($result as $input){
                $result=$class_mediaProgramLog->add($input);
                if($result["r"]==0){
                    $msg=$result["msg"];
                    break;
                }
            }
            $r=1;
            $msg="上传成功";
        }while(false);

        echo json_encode(array(
            "r"=>$r,
            "msg"=>$msg
        ));
    }

    /**
     * 上传海报,资源包,视频
     * @throws \yii\db\Exception
     */
    public function actionUploadAttach(){
        $r=0;
        $msg="";
        $path="";
        do{
            $post=Yii::$app->request->post();
            $media_id=$post["media_id"];
            $type=$post["type"];


            $class_upload=new Upload;
            $result=$class_upload->upload_file();
            if($result["r"]==0){
                $msg=$result["msg"];
                break;
            }
            $file_path=$result["file_path"];
            $file_name=$result["file_name"];
            $class_mediaAttach=new MediaAttach;
            $result=$class_mediaAttach->add($media_id,$type,$file_name,$file_path);
            if($result!=true){
                $msg=$result;
                break;
            }
            $r=1;
            $msg="上传成功";
            $path=Yii::$app->params['UPLOAD_URL']."/temp/".$file_name;
        }while(false);

        echo json_encode(array(
            "r"=>$r,
            "msg"=>$msg,
            "path"=>$path
        ));
    }
    // 删除某个剧目及其所有附件
    public function actionDeleteLog(){
        $r=0;
        $msg="";
        $post = Yii::$app->request->post();
        $media_id=$post["media_id"];

        $class_mediaProgramLog=new MediaProgramLog;
        $class_mediaUnvalid=new MediaUnvalid;
        $class_mediaAttachLog=new MediaAttachLog;

        do{
            $transaction=Yii::$app->db->beginTransaction();
            try{
                $class_mediaProgramLog->delete($media_id);

                if(count($class_mediaUnvalid->get_all($media_id))){
                    $class_mediaUnvalid->delete($media_id);
                }
                $attachs=$class_mediaAttachLog->get_all($media_id);
                if(count($attachs)>0){
                    $class_mediaAttachLog->delete($media_id);
                }
                $transaction->commit();
                $r=1;
                $msg="操作成功";
            }catch(Exception $e){
                $transaction->rollback();
                $r=0;
                $msg="操作失败";
            }
            if(count($attachs)>0){
                foreach($attachs as $a){
                    unlink($a["url"]);
                }
            }
        }while(false);

        echo json_encode(array("r"=>$r,"msg"=>$msg));
    }
    // 导出excel
    public function actionExportUpload(){
        $class_mediaProgamLog=new MediaProgramLog;
        $result=$class_mediaProgamLog->get_list(array("status"=>"0","user_id"=>"0"));
        $results=$result["data"];

        $class_system=new old;
        $media_fields=$class_system->get_media_fields();

        $class_mediaUnvalid=new MediaUnvalid;

        if($results){
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
            $spreadsheet->setActiveSheetIndex(0);

            $row=1;
            $col=1;
            $field_array=array();
            foreach($media_fields as $field){
                $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $field["name"]);
                $field_array[$col]=$field["field"];
                $col++;
            }

            $row++;

            foreach($results as $result){
                $col=1;

                $unvalid=array();
                $media_id=$result["media_id"];
                $fields=$class_mediaUnvalid->get_by_id($media_id);

                if($fields){
                    foreach($fields as $f){
                        $unvalid[]=$f["field"];
                    }
                }

                foreach($field_array as $f){
                    $column = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
                    $cell = $column.$row;

                    $spreadsheet->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result[$f]);

                    if(in_array($f,$unvalid)){
                        $spreadsheet->getActiveSheet()->getStyle($cell)
                            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                        $spreadsheet->getActiveSheet()->getStyle($cell)
                            ->getFill()->getStartColor()->setRGB('FFFF00');
                    }
                    $col++;
                }
                $row++;
            }

            $spreadsheet->setActiveSheetIndex(0);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.date("YmdHis").'.xlsx"');
            header('Cache-Control: max-age=0');
            $objWriter=new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $objWriter->save('php://output');

        }
        exit();
    }
    // 生成录入单
    public function actionInputAdd(){
        $r=0;
        $msg=array();
        $post=Yii::$app->request->post();
        $date=$post["date"];
        $remark=$post["remark"];
        $default_date=date("Y-m-d");
        $default_time=date("Y-m-d H:i:s");

        do{
            $class_mediaProgamLog=new MediaProgramLog;
            $result=$class_mediaProgamLog->get_list(array("status"=>"0","user_id"=>"0"));
            $programs=$result["data"];
            if(count($programs)===0){
                $msg="无数据";
                break;
            }
            $this_platform='';
            $class_mediaInput=new MediaInput;
            $class_mediaUser=new MediaUser;

            $media_data=array();
            foreach($programs as $program){
                $platform=trim($program["platform"]);
                if($this_platform!==$platform){
                    $media_data[$platform]=array();
                    $this_platform=$platform;
                }
                $media_data[$platform][]=$program;
            }

            $transaction=Yii::$app->db->beginTransaction();
            try{
                foreach($media_data as $platform=>$medias){
                    $user=$class_mediaUser->get(array("platform"=>$platform));
                    $user_id=$user["user_id"];
                    $input_name=$class_mediaInput->make_name($default_date,$user_id);
                    $class_mediaInput->create($user_id,$input_name,$platform,$remark,$medias);
                }
                $transaction->commit();
                $r=1;
                $msg="操作成功";
            }catch(Exception $e){
                $transaction->rollBack();
                $r=0;
                $msg="操作失败";
            }
        }while(false);

        echo json_encode(array(
            "r"=>$r,
            "msg"=>$msg
        ));

    }

    // 剧目列表
    // 剧目列表页面
    public function actionOnlineList(){
        $pageNavSub="22";

        $get = Yii::$app->request->get();

        $page=isset($get["p"])?intval($get["p"]):1; // 当前页数
        $pagecount=isset($get["c"])?intval($get["c"]):5; // 每页显示数量
        $offset=($page-1)*$pagecount;

        $class_mediaProgram=new MediaProgram;
        $result=$class_mediaProgram->get_list($get,$offset,$pagecount);

        $list=$result["data"];
        $list_count=$result["total_count"];
        $page_count=$result["page_count"];

        return $this->render('online',array(
            "pageNavSub"=>$pageNavSub,
            "platforms"=>array("爱奇艺"),
            "filters"=>$get,
            "list"=>$list,
            "page_info"=>array(
                "page"=>$page,
                "pagecount"=>$pagecount,
                "list_count"=>$list_count,
                "page_count"=>$page_count
            )
        ));
    }
}
