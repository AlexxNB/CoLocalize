<h3><?=$Project->Title?>: <span><?=$L['terms:view:header']?></span></h3>

<div class="mt mb">
    <div class="float-right">
        <a class="btn btn-primary btn-sm" href="/terms/add/<?=$Project->ID?>/"><i class="icon-add"></i> <?=$L['terms:view:add_button']?></a>
        <a class="btn btn-primary btn-sm" href="/terms/import/<?=$Project->ID?>/"><i class="icon-import"></i> <?=$L['terms:view:import_button']?></a>
    </div>
    <div class="input-group term-search">
        <input type="text" class="form-input input-sm" id="search" placeholder="<?=$L['terms:view:search']?>">
        <button class="btn btn-primary btn-sm input-group-btn" id="doSearch"><i class="icon-search"></i></button>
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
            <input type="checkbox" class="term-check">
            <i class="form-icon"></i>
            <span class="term-num">0</span>
        </label>
    </div>
    <div class="tile-content">
        <div class="form-group">
            <input class="form-input input-sm term-input" type="text" value="">
        </div>
    </div>
    <div class="tile-action">
        <button class="btn btn-sm doDeleteTerm"><i class="icon-delete"></i></button>
    </div>
</div>