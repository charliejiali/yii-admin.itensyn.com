<?php $this->registerJsFile('/js/pages/index.js');?>
<?= $this->render('../module/head_tag'); ?>
<div class="wrap-full index">
    <div class="logo"></div>
    <div class="login-box">
        <h2 class="title">用户登录</h2>
        <div class="form-login">
            <form class="pure-form">
                <input id="email" type="email" placeholder="登录邮箱" class="input-login label-email">
                <input id="password" type="password" placeholder="登录密码" class="input-login label-passwd">
                <label for="auto" class="pure-checkbox">
                    <input id="auto" id="auto" type="checkbox"> 自动登录
                </label>
                <p class="text-center">
                    <button id="submit" type="button" class="pure-btn btn-large btn-submit">登 录</button>
                </p>
            </form>
        </div>
    </div>
    <?= $this->render('../module/footer') ?>
