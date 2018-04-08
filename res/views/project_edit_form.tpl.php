<?php if($Project):?>
<h3><span><?=$Project->Title?></span>: <?=$L['projects:form:header_edit']?></h3>
<?php else:?>
  <h3><?=$L['projects:form:header_add']?></h3>
<?php endif; ?>

<div class="form-group">
  <label class="form-label" for="title"><?=$L['projects:form:title_label']?></label>
  <input class="form-input" type="text" id="title" placeholder="<?=$L['projects:form:title']?>" value="<?=($Project ? $Project->Title:'')?>">
</div>

<div class="form-group">
  <label class="form-label" for="descr"><?=$L['projects:form:description_label']?></label>
  <textarea class="form-input" id="descr" placeholder="<?=$L['projects:form:description']?>" rows="3"><?=($Project ? $Project->Descr:'')?></textarea>
</div>

<div class="form-group">
  <label class="form-switch">
    <input type="checkbox" id="public" data-pid="<?=($Project ? $Project->ID:'')?>" <?=(($Project && $Project->IsPublic) ? ' checked="checked"':'')?>>
    <i class="form-icon"></i> <?=$L['projects:form:make_public']?> <span class="hide" id="public_descr"><?=$L['projects:form:public_descr']?></span>
  </label>
</div>

<div class="form-group hide" id="pl_container">
  <div class="input-group">
    <span class="input-group-addon"><?=$L['projects:form:public_link']?></span>
    <input type="text" class="form-input"  disabled="disabled" id="public_link" data=code="">
    <button class="btn btn-primary input-group-btn" id="doCopyLink" data-ok="<?=$L['projects:form:msg:copy_ok']?>" data-err="<?=$L['projects:form:msg:copy_err']?>"><i class="icon-copy"></i> <?=$L['projects:form:copy_link']?></button>
  </div>
</div>

<div class="form-group text-center mt">
  <a href="/projects/" class="btn"><i class="icon-close"></i> <?=$L['projects:form:cancel_button']?></a> 
<?php if($Project):?>
  <button class="btn btn-primary" id="doSaveProject"><i class="icon-ok"></i> <?=$L['projects:form:save_button']?></button>   
<?php else:?>
  <button class="btn btn-primary" id="doCreateProject"><i class="icon-add"></i> <?=$L['projects:form:create_button']?></button>
<?php endif; ?>
</div>