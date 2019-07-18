<?php
namespace app\models;

use Yii;
//use yii\db\Query;
use yii\base\Model;
use yii\web\Cookie;

class User extends Model
{
    private $user_id=0;
    private $user_name="admin";
    private $user_password="70a705b1";
    private $mask_code="0023767a";
    private $hash="5b682406";
    private $user_auto_login_time=7;

    public function login($email,$password,$auto){
        $email=trim($email);
        if($email===""){
            return "请输入用户名";
        }
        if(trim($password)===""){
            return "请输入密码";
        }
        if($email!==$this->user_name){
            return "用户名错误";
        }

        $psw=$this->make_password($password,$this->mask_code);
        if($psw!==$this->user_password){
            return "密码错误";
        }
        $this->_set_cookie($this->user_id,$this->mask_code,$this->hash,$auto);
        return true;
    }
    public function logout(){
        $cookies = Yii::$app->response->cookies;
        $cookies->remove('owl_admin_uid');
        $cookies->remove('owl_admin_token');
        $cookies->remove('owl_admin_key');
    }

    private static function make_password($password,$mask_code){
        return substr(md5(substr(md5($password.$mask_code."+owl_media+a8cfe0"),0,8)),16,8);
    }
    public function make_mask_code(){
        return substr(md5(uniqid(rand(),true)),8,8);
    }
    private static function make_hash($user_id,$mask_code){
        return substr(md5(substr(md5($user_id.$mask_code."+owl_media+a8cfe1"),0,8)),16,8);
    }
    public function check_login($user_id,$mask_code,$hash){
        $check_hash=$this->make_hash($user_id,$mask_code);

        if(trim($hash)!==trim($check_hash)){return false;}

        return true;
    }

    private function _set_cookie($user_id,$mask_code,$hash,$auto_login){
        if($auto_login=="true"){
            $autoLoginExpireDays=$this->user_auto_login_time;
        }else{
            $autoLoginExpireDays=1;
        }

        $login_time=time()+$autoLoginExpireDays*24*60*60;
        $cookies = Yii::$app->response->cookies;

        $cookies->add(new Cookie([
            'name' => 'owl_admin_uid',
            'value' =>$user_id,
            'expire'=>$login_time
        ]));
        $cookies->add(new Cookie([
            'name' => 'owl_admin_token',
            'value' => $hash,
            'expire'=>$login_time
        ]));
        $cookies->add(new Cookie([
            'name' => 'owl_admin_key',
            'value' => $mask_code,
            'expire'=>$login_time
        ]));
    }
}
