<?php
namespace app\controllers;

use Yii;
use app\models\User;

class SiteController extends UserController{
    public $pageTitle='';
    public $pageNavId=0;

    public function actionIndex(){
        return $this->render('index');
    }
    // 登录
    public function actionLogin(){
        $get = Yii::$app->request->get();
        $email = $get["email"];
        $password = $get["password"];
        $auto = $get["auto"];

        $class_user=new User;
        $re=$class_user->login($email,$password,$auto);
        if($re===true){
            $r=1;
            $msg="登录成功";
        }else{
            $r=0;
            $msg=$re;
        }
        return json_encode(array(
            "r"=>$r,
            "msg"=>$msg,
        ));
    }
}
