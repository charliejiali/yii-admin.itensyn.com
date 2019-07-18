<?php
use yii\helpers\Html;
use app\assets\AppAsset;

$pageTitle=$this->context->pageTitle;

$titleArr = explode("|", $pageTitle);
$titleStr = "OWL SYS";
if (strlen($pageTitle) > 0) {
    $titleStr = $titleStr . " -- " . implode(" -- ", $titleArr);
}
$asset=AppAsset::register($this);
$baseUrl = $asset->baseUrl;

$this->registerMetaTag(['http-equiv' => 'Content-type', 'content' => 'text/html; charset=utf-8']);
$this->registerMetaTag(['name' => 'apple-mobile-web-app-capable', 'content' => 'yes']);
$this->registerMetaTag(['charset' => 'utf-8']);
$this->registerMetaTag(['HTTP-EQUIV' => 'Pragma', 'CONTENT' => 'no-cache']);
$this->registerMetaTag(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge']);
$this->registerMetaTag(['name' => 'Description', 'content' => 'OWL SYS']);

$this->registerLinkTag(["rel"=>"icon","href"=>"/images/favicon.ico","type"=>"image/x-icon"]);
$this->registerLinkTag(["rel"=>"shortcut icon","href"=>"/images/favicon.ico"]);
?>
<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html>
<head>
<!--    --><?php //$this->registerCssFile("/css/style.css");?>
    <?php $this->registerJs(" var base_url='".$baseUrl."';",\yii\web\View::POS_HEAD);?>
    <title><?= Html::encode($titleStr); ?></title>
    <?php $this->head(); ?>
</head>
<body>
<?php $this->beginBody(); ?>
<?php $this->endBody(); ?>
<?php $this->endPage(); ?>
