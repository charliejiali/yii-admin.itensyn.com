<?php
namespace app\models;

use Yii;
use yii\db\Query;
use yii\base\Model;

class old extends Model{
    public function get_media_fields(){
        return (new Query)
            ->select('*')
            ->from('media_field')
            ->all();
    }
}
