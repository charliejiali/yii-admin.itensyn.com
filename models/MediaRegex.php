<?php
namespace app\models;

class MediaRegex{
    // 剧目名称
    // 此维度只可填写汉字、数字、英文字母。不可填写标点符号。
    // 此维度为必填维度。此维度不允许出现相同内容：如2行“剧目名称”同时填写“中国有嘻哈”
    public static function check_program_name($data){
        return preg_match("/^[a-zA-Z0-9\x{4e00}-\x{9fa5}]+$/u",$data);
    }
    // 剧目原名
    // 此维度只可填写汉字、数字、英文字母。不可填写标点符号。
    // 备注：此纬度仅当“剧目名称”需要修改是填写。填写内容为“剧目名称”修改前内容
    // “剧目名称”与“剧目原名”维度不可相同。
    public static function check_program_default_name($data){
        return preg_match("/^[a-zA-Z0-9\x{4e00}-\x{9fa5}]+$/u",$data);
    }
    // 资源类型
    // 要求：填写内容需为下列八个中的一个。新秀自制综艺、新秀自制剧、迭代自制综艺、迭代自制剧。新秀版权综艺、新秀版权剧、迭代版权综艺、迭代版权剧
    public static function check_type($data){
        $array=array("新秀自制综艺","新秀自制剧","迭代自制剧","迭代自制综艺","新秀版权综艺","新秀版权剧","迭代版权综艺","迭代版权剧");
        return in_array($data,$array);
    }
    // 播出时间
    // 要求：必须填写为XXXX（大于等于2017的正整数）年QX（Q只能为数字1,2,3,4中的一个）
    // 或XXXX年
    // 或汉字“时间待定”"  2017.12.21 改为 xxxx年时间待定
    public static function check_play_time($data){
        $length=mb_strlen($data,"utf8");
        if($length==7){ // xxxx年Qx
            $year=mb_substr($data,0,4);
            // 大于等于2017正整数
            if(!preg_match("/^\d+$/",$year)||intval($year)<2017){return false;}
            // 年Q
            $season1=mb_substr($data,4,2);;
            if(trim($season1)!=="年Q"){return false;}
            // 1-4
            $season2=mb_substr($data,-1);
            if(!preg_match("/^[1-4]$/",$season2)){return false;}
        }else if($length==5){ // xxxx年
            $year=mb_substr($data,0,4);
            if(!preg_match("/^\d+$/",$year)||intval($year)<2017){return false;}
            if(mb_substr($data,4,1)!="年"){return false;}
            // }else if($length==4){
            // 	if(trim($data)!=="时间待定"){return false;}
        }else if($length==9){
            $year=mb_substr($data,0,4);
            if(!preg_match("/^\d+$/",$year)||intval($year)<2017){return false;}
            if(mb_substr($data,4,5)!="年时间待定"){return false;}
        }else{
            return false;
        }
        return true;
    }
    // 媒体平台
    // 要求：填写内容需为下列八个中的。腾讯视频、爱奇艺、优酷土豆、搜狐视频、乐视视频、芒果TV。此维度不可多选
    public static function check_platform($data){
        return in_array($data,array("腾讯视频","爱奇艺","优酷土豆","搜狐视频","乐视视频","芒果TV","PPTV"));
    }
    // // 开播时间
    // // "此维度有以下3种格式：
    //    // 1.XXXX(大于等于2017正整数)-XX（1-12间正整数）-XX（1-31间正整数）
    //    // 2.XXXX（大于等于2017正整数）-XX（1-12间正整数）
    //    // 3.XXXX(大于等于2017正整数)
    //    // 4.时间待定"
    // public static function check_start_time($data){
    // 	$length=mb_strlen($data,"utf8");
    // 	if($length==10){ // yyyy-mm-dd
    // 		if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/",$data)){ return false;}
    // 		$date=explode("-",$data);
    // 		$year=$date[0];
    // 		$month=$date[1];
    // 		$day=$date[2];
    // 		if(intval($year)<2017){return false;}
    // 		return checkdate($month,$day,$year);
    // 	}else if($length==7){ // yyyy-mm
    // 		if(!preg_match("/^[0-9]{4}-[0-9]{2}$/",$data)){ return false;}
    // 		$date=explode("-",$data);
    // 		$year=intval($date[0]);
    // 		$month=intval($date[1]);
    // 		if($year<2017){return false;}
    // 		if($month<0||$month>12){return false;}
    // 	}else if($length==4){ // yyyy/时间待定
    // 		if(preg_match("/^[0-9]{4}$/",$data)){
    // 			if(intval($data)<2017){return false;}
    // 		}else if(trim($data)==="时间待定"){

    // 		}else{return false;}
    // 	}else{
    // 		return false;
    // 	}
    // 	return true;
    // }

    // 开播时间
    // "此维度有以下3种格式：
    // 1.XXXX(大于等于2017正整数)年XX（1-12间正整数）月XX（1-31间正整数）日
    // 2.XXXX（大于等于2017正整数）年-XX（1-12间正整数）月
    // 3.XXXX(大于等于2017正整数) 年
    // 4.时间待定"
    public static function check_start_time($data){
        if(preg_match("/^[0-9]{4}\x{5E74}[0-9]{1,2}\x{6708}[0-9]{1,2}\x{65E5}$/u",$data)){ // yyyy年mm月dd日
            $date=preg_split("/\x{5E74}|\x{6708}|\x{65E5}/u",$data);
            $year=$date[0];
            $month=$date[1];
            $day=$date[2];
            if(intval($year)<2017){return false;}
            return checkdate($month,$day,$year);
        }else if(preg_match("/^[0-9]{4}\x{5E74}[0-9]{1,2}\x{6708}$/u",$data)){ // yyyy年mm月
            $date=preg_split("/\x{5E74}|\x{6708}/u",$data);
            $year=intval($date[0]);
            $month=intval($date[1]);
            if($year<2017){return false;}
            if($month<0||$month>12){return false;}
        }else if(preg_match("/^[0-9]{4}\x{5E74}$/u",$data)){ // yyyy年
            $date=preg_split("/\x{5E74}/u",$data);
            $year=intval($date[0]);
            if($year<2017){return false;}
        }else{
            if(trim($data)!=="时间待定"){return false;}
        }
        return true;
    }
    // 版权情况
    // 此纬度仅可填写：“独播”或“非独播”
    public static function check_copyright($data){
        return in_array($data,array("独播","非独播"));
    }
    // 播出状态
    // 此纬度仅可填写：“已播完”或“播出中”“待播出”
    public static function check_start_type($data){
        return in_array($data,array("已播完","播出中","待播出"));
    }
    // 播出卫视
    // 此维度仅可填写汉字。可使用“/”区隔
    public static function check_satellite($data){
        return preg_match("/^[\x{4e00}-\x{9fa5}]+(\/[\x{4e00}-\x{9fa5}]+)*$/u",$data);
    }
    // 主创/嘉宾
    // 要求：可以填写汉字或英文字母。字段间使用“/，,、”作为间隔
    public static function check_creator($data){
        return preg_match("/^[ a-zA-Z\x{4e00}-\x{9fa5}\d]+([\/，,、][ a-zA-Z\x{4e00}-\x{9fa5}\d]+)*$/u",$data);
    }
    // 本季预估播放量（单位：亿）
    // 要求：填写内容为数字和小数点、小数点后保留一位。
    public static function check_play1($data){
        if(!preg_match("/^[0-9]+(\.[0-9])?$/",$data)){return false;}
        $data=doubleval($data);
        return $data>=0?true:false;
    }
    // // 累计播放量（单位：亿）
    // // 要求：填写内容为数字和小数点、小数点后保留一位。
    // public static function check_play2($data){
    // 	if(!preg_match("/^[0-9]+(\.[0-9])?$/",$data)){return false;}
    // 	$data=doubleval($data);
    // 	return $data>=0?true:false;
    // }
    // 集数/期数
    // 要求：填写正整数且不得大于150
    public static function check_play3($data){
        if(!preg_match("/^[0-9]+$/",$data)){return false;}
        $data=intval($data);
        return $data>0&&$data<=150?true:false;
    }
    // // 已播集数
    // // 正整数。“播出状态”维度是“待播出”的不可填写此维度
    // public static function check_play4($data){
    // 	return preg_match("/^[1-9]+[0-9]*$/",$data);
    // }
    // 内容类型
    // 要求：可以填写汉字。字段间使用“/”作为间隔
    public static function check_content_type($data){
        return preg_match("/^[\x{4e00}-\x{9fa5}]+(\/[\x{4e00}-\x{9fa5}]+)*$/u",$data);
    }
    // 制作团队
    // 要求：可以填写汉字或英文字母。字段间使用“/”作为间隔/”作为间隔
    public static function check_team($data){
        return preg_match("/^[a-zA-Z\x{4e00}-\x{9fa5}]+([\/:：][a-zA-Z\x{4e00}-\x{9fa5}]+)*$/u",$data);
    }
    // 简介
    // 要求：内容不得超过400字
    public static function check_intro($data){
        $length=mb_strlen($data,"utf8");
        return $length<=400?true:false;
    }

    // 两位小数
    private static function _is_double($data){
        return preg_match("/^[0-9]+(\.[0-9]{1,2})?$/",$data);
    }
}
