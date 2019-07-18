<?php
namespace app\models;

use Yii;
use yii\base\Model;

class Upload extends Model{
    // 上传文件
    public function upload_file(){
        $r=0;
        $msg="文件上传成功";
        $filePath="";

        do{
            /**
             * upload.php
             *
             * Copyright 2013, Moxiecode Systems AB
             * Released under GPL License.
             *
             * License: http://www.plupload.com/license
             * Contributing: http://www.plupload.com/contributing
             */

            #!! IMPORTANT:
            #!! this file is just an example, it doesn't incorporate any security checks and
            #!! is not recommended to be used in production environment as it is. Be sure to
            #!! revise it and customize to your needs.

            // Make sure file is not cached (as it happens for example on iOS devices)
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");

            /*
               // Support CORS
               header("Access-Control-Allow-Origin: *");
               // other CORS headers if any...
               if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                   exit; // finish preflight CORS requests here
               }
            */

            // 5 minutes execution time
            @set_time_limit(5 * 60);

            // Uncomment this one to fake upload time
            // usleep(5000);

            // Settings
            // $targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
            $targetDir = Yii::$app->params['UPLOAD_DIR'].'/temp';
            $cleanupTargetDir = true; // Remove old files
            $maxFileAge = 5 * 3600; // Temp file age in seconds

            // Create target dir
            if (!file_exists($targetDir)) {
                @mkdir($targetDir);
            }

            // Get a file name
            if (isset($_REQUEST["name"])) {
                $fileName = $_REQUEST["name"];
            } elseif (!empty($_FILES)) {
                $fileName = $_FILES["file"]["name"];
            } else {
                $fileName = uniqid("file_");
            }

            $temp_fileName=explode(".",$fileName);

            $fileName=rand(10000000,99999999).time().".".$temp_fileName[count($temp_fileName)-1];

            $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

            // Chunking might be enabled
            $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
            $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

            // Remove old temp files
            if ($cleanupTargetDir) {
                if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                    $msg="未能打开上传文件夹,请联系管理员";
                    break;
                }

                while (($file = readdir($dir)) !== false) {
                    $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                    // If temp file is current file proceed to the next
                    if ($tmpfilePath == "{$filePath}.part") {
                        continue;
                    }

                    // Remove temp file if it is older than the max age and is not the current file
                    if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                        @unlink($tmpfilePath);
                    }
                }
                closedir($dir);
            }

            // Open temp file
            if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
                $msg="未能打开输出流,请联系管理员";
                break;
            }

            if (!empty($_FILES)) {
                if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                    $msg="未能移动上传文件,请联系管理员";
                    break;
                }

                // Read binary input stream and append it to temp file
                if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                    $msg="未能打开输出流,请联系管理员";
                    break;
                }
            } else {
                if (!$in = @fopen("php://input", "rb")) {
                    $msg="未能打开输出流,请联系管理员";
                    break;
                }
            }

            while ($buff = fread($in, 4096)) {
                fwrite($out, $buff);
            }

            @fclose($out);
            @fclose($in);

            // Check if file has been uploaded
            if (!$chunks || $chunk == $chunks - 1) {
                // Strip the temp .part suffix off
                rename("{$filePath}.part", $filePath);
            }
            $r=1;

        }while(false);

        return array(
            "r"=>$r,
            "msg"=>$msg,
            "file_name"=>$fileName,
            "file_path"=>$filePath
        );
    }
    // 读取excel数据,
    public function import_excel($file){
        $input=array();
        $en_data=array();
        $class_system=new System;

        $media_fields=$class_system->get_media_fields();
        foreach($media_fields as $mf){
            $table_fields[$mf["name"]]=$mf["field"];
        }

        $file = iconv("utf-8", "gb2312", $file);

        if (empty($file) OR !file_exists($file)) {
            return "文件不存在 !";
        }

        /** @var Xlsx $objRead */
        $objRead = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');

        if (!$objRead->canRead($file)) {
            /** @var Xls $objRead */
            $objRead = IOFactory::createReader('Xls');

            if (!$objRead->canRead($file)) {
                return '只支持导入Excel文件！';
            }
        }

        /* 如果不需要获取特殊操作，则只读内容，可以大幅度提升读取Excel效率 */
        empty($options) && $objRead->setReadDataOnly(true);
        /* 建立excel对象 */
        $obj = $objRead->load($file);
        /* 获取指定的sheet表 */
        $currSheet = $obj->getSheet(0);

        if (isset($options['mergeCells'])) {
            /* 读取合并行列 */
            $options['mergeCells'] = $currSheet->getMergeCells();
        }


        /* 取得最大的列号 */
        $columnH = $currSheet->getHighestColumn();
        /* 兼容原逻辑，循环时使用的是小于等于 */
        $columnCnt = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnH);


        /* 获取总行数 */
        $rowCnt = $currSheet->getHighestRow();
        $data   = [];

        /* 读取内容 */
        for ($_row = 1; $_row <= $rowCnt; $_row++) {
            $isNull = true;

            for ($_column = 1; $_column <= $columnCnt; $_column++) {
                $cellName = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($_column);
                $cellId   = $cellName . $_row;
                $cell     = $currSheet->getCell($cellId);

                if($_row===1){
                    foreach($table_fields as $cn=>$en){
                        if(strpos($cell,$cn)!==false){
                            $en_data[]=$en;
                        }
                    }
                }else{
                    $data[$_row][$cellName] = trim($currSheet->getCell($cellId)->getFormattedValue());
                }

                if (!empty($data[$_row][$cellName])) {
                    $isNull = false;
                }
            }
            if($_row>1){
                /* 判断是否整行数据为空，是的话删除该行数据 */
                if ($isNull) {
                    unset($data[$_row]);
                }else{
                    $input[]=array_combine($en_data,$data[$_row]);
                }
            }
        }
        return $input;
    }
}
