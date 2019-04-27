<h3><?=$Project->Title?></h3>

<div class="float-right">
    <a href="/terms/view/<?=$Project->ID?>/add" class="btn btn-primary btn-sm"><i class="icon-add"></i> <?=$L['projects:view:terms:add']?></a>
    <a class="btn btn-primary btn-sm" href="/terms/import/<?=$Project->ID?>/"><i class="icon-import"></i> <?=$L['projects:view:terms:import']?></a>
</div>
<h5><?=$L['projects:view:terms:header']?></h5>
<div class="pad container pad-terms">
    <div class="columns">
        <div class="column col-3 terms-num">
            <?=( $Termsnum==0 ? 'No' : $Termsnum )?>
            <span> <?=$L['projects:view:terms:terms']?></span>
        </div>
        <div class="column col-9 text-right">
            <a class="btn" href="/terms/view/<?=$Project->ID?>/"><i class="icon-edit"></i> <?=$L['projects:view:terms:edit']?></a>
        </div>
    </div>
</div>
<div class="mt"></div>

<div class="float-right">
    <button class="btn btn-primary btn-sm" id="showAddLanguage"><i class="icon-add"></i> <?=$L['projects:view:lang:add']?></button>
</div>
<h5><?=$L['projects:view:lang:header']?></h5>
<div class="mt"></div>
<?php if(count($Langs) == 0):?>

<div class="empty">
  <div class="empty-icon">
    <i class="icon-folder"></i>
  </div>
  <p class="empty-title h5"><?=$L['projects:view:lang:no_langs']?></p>
  <p class="empty-subtitle"><?=$L['projects:view:lang:can_add']?></p>
</div>


<?php else: ?>


<?php foreach($Langs as $Lang):?>
<div class="tile lang-tile" id="lid-<?=$Lang->ID?>">
    <div class="tile-icon">
        <div class="progress-icon">100%</div>
    </div>
    <div class="tile-content">
    <p class="name"><a href="/translate/list/<?=$Lang->Orign?>/<?=$Lang->Code?>/<?=$Lang->ID?>/"><?=$Lang->Name?></a></p>
    <p class="native"><?=$Lang->Native?></p>
    </div>
    <div class="tile-action">
    <button class="btn doDeleteLanguage" data-lid="<?=$Lang->ID?>"><i class="icon-delete"></i> <?=$L['projects:view:lang:delete']?></button>
    </div>
</div>
<?php endforeach;?>


<?php endif;?>

<div class="modal modal-sm" id="modal-addlang">
  <span class="modal-overlay modal-close" aria-label="Close"></span>
  <div class="modal-container">
    <div class="modal-header">
        <span class="btn btn-clear float-right modal-close" aria-label="Close"></span>
    </div>
    <div class="modal-body">
        <div class="content">

            <div class="form-group">
                <select class="form-select" id="lang">
                    <option value="" class="loading"><?=$L['langs:add:choose']?></option>
                </select>
            </div>
            <div class="loading hide" id="listloading"></div>
        </div>
    </div>
    <div class="modal-footer">
      <button class="btn modal-close"><i class="icon-close"></i> <?=$L['cancel']?></button>
      <button class="btn btn-primary" id="doAddLanguage" data-pid="<?=$Project->ID?>"><i class="icon-add"></i> <?=$L['add']?></button>
    </div>
  </div>
</div>

<div class="modal modal-sm" id="modal-delete">
  <span class="modal-overlay modal-close" aria-label="Close"></span>
  <div class="modal-container">
    <div class="modal-header">
        <span class="btn btn-clear float-right modal-close" aria-label="Close"></span>
    </div>
    <div class="modal-body">
      <div class="content">
        <?=$L['projects:view:lang:msg:sure_delete']?>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn modal-close"><i class="icon-close"></i> <?=$L['cancel']?></button>
      <button class="btn btn-primary" id="confirmDelete"><i class="icon-ok"></i> <?=$L['yes']?></button>
    </div>
  </div>
</div>