<h3><?=$Project->Title?>: <span><?=$L['terms:view:header']?></span></h3>

<div class="mt mb container">
    <div class="columns">
        <div class="delete-selected-container">
            <button class="btn btn-sm hide" id="doDeleteSelected"><i class="icon-delete"></i></button>
        </div>
        <div class="input-group term-search">
            <input type="text" class="form-input input-sm" id="search" placeholder="<?=$L['terms:view:search']?>">
            <button class="btn btn-primary btn-sm input-group-btn" id="doSearch"><i class="icon-search"></i></button>
        </div>
        <div class="col-ml-auto">
            <button class="btn btn-primary btn-sm" id="doAddTerm"><i class="icon-add"></i> <?=$L['terms:view:add_button']?></button>
            <a class="btn btn-primary btn-sm" href="/terms/import/<?=$Project->ID?>/"><i class="icon-import"></i> <?=$L['terms:view:import_button']?></a>
        </div>
    </div>
</div>

<div id="term-container" data-pid="<?=$Project->ID?>">
    
</div>

<div id="empty-terms" class="text-center hide">
    <div class="empty">
        <div class="empty-icon">
            <i class="icon-emo-P"></i>
        </div>
        <p class="empty-title h5"><?=$L['terms:view:msg:no_terms_found']?></p>
    </div>
</div>

<div class="tile term-tile hide" id="tile-sample" data-tid="0">
    <div class="tile-icon">
        <label class="form-checkbox">
            <input type="checkbox" class="term-check" data-tid="0">
            <i class="form-icon"></i>
            <span class="term-num">0</span>
        </label>
    </div>
    <div class="tile-content">
        <div class="form-group has-icon-right">
            <input class="form-input input-sm term-input" type="text" value="" data-num="0">
            <i class="form-icon loading hide"></i>
        </div>
    </div>
    <div class="tile-action">
        <button class="btn btn-sm doDeleteTerm"><i class="icon-delete"></i></button>
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
        <?=$L['terms:view:msg:sure_delete']?>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn modal-close"><i class="icon-close"></i> <?=$L['cancel']?></button>
      <button class="btn btn-primary" id="confirmDelete" data-err="<?=$L['terms:view:msg:delete_err']?>"><i class="icon-ok"></i> <?=$L['yes']?></button>
    </div>
  </div>
</div>

<div class="modal modal-sm" id="modal-delete-selected">
  <span class="modal-overlay modal-close" aria-label="Close"></span>
  <div class="modal-container">
    <div class="modal-header">
        <span class="btn btn-clear float-right modal-close" aria-label="Close"></span>
    </div>
    <div class="modal-body">
      <div class="content">
        <?=$L['terms:view:msg:sure_delete_selected']?>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn modal-close"><i class="icon-close"></i> <?=$L['cancel']?></button>
      <button class="btn btn-primary" id="confirmDeleteSelected" data-err="<?=$L['terms:view:msg:delete_selected_err']?>"><i class="icon-ok"></i> <?=$L['yes']?></button>
    </div>
  </div>
</div>

<div class="modal modal-sm" id="modal-add">
  <span class="modal-overlay modal-close" aria-label="Close"></span>
  <div class="modal-container">
    <div class="modal-header">
        <span class="btn btn-clear float-right modal-close" aria-label="Close"></span>
    </div>
    <div class="modal-body">
      <div class="content">
        <div class="form-group">
            <input class="form-input" type="text" id="newTermName" placeholder="<?=$L['terms:view:enter_name']?>">
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn modal-close"><i class="icon-close"></i> <?=$L['cancel']?></button>
      <button class="btn btn-primary" id="confirmAdd" data-err="<?=$L['terms:view:msg:add_err']?>"><i class="icon-add"></i> <?=$L['add']?></button>
    </div>
  </div>
</div>