<div class="container">
    <div class="columns">

        <div class="column col-5 col-mr-auto">
            <h5><?=$L['login:signin:title']?></h5>
            <div class="form-group">
                <input class="form-input" type="text" id="email" placeholder="<?=$L['login:signin:form:email']?>">
            </div>
            <div class="form-group">
                <input class="form-input" type="password" id="password" placeholder="<?=$L['login:signin:form:password']?>">
            </div>
            <div class="form-group">
                <label class="form-switch">
                    <input type="checkbox">
                    <i class="form-icon"></i> <?=$L['login:signin:form:remember']?>
                </label>
            </div>
            <div class="form-group text-center">
                <button class="btn btn-primary" id="doSignIn"><?=$L['login:signin:form:button']?></button>
            </div>
        </div>
        <div class="divider-vert" data-content="<?=$L['login:or']?>"></div>
        <div class="column col-5 col-ml-auto">
        <h5><?=$L['login:signup:title']?></h5>
            <div class="form-group">
                <input class="form-input" type="text" id="rName" placeholder="<?=$L['login:signup:form:fullname']?>">
            </div>
            <div class="form-group">
                <input class="form-input" type="text" id="rEmail" placeholder="<?=$L['login:signup:form:email']?>">
            </div>
            <div class="form-group">
                <input class="form-input" type="password" id="rPassword" placeholder="<?=$L['login:signup:form:password']?>">
            </div>
            <div class="form-group">
                <input class="form-input" type="password" id="rPassword2" placeholder="<?=$L['login:signup:form:password2']?>">
            </div>
            <div class="form-group text-right">
                <button class="btn btn-primary" id="doSignUp"><?=$L['login:signup:form:button']?></button>
            </div>
        </div>

    </div>
</div>