<h3><?=$Project->Title?>: <span><?=$L['translate:list:header']?> : <span><?=$Lang->Name?></span></span></h3>

<div class="mt mb container">
    <div class="columns">
        <div class="input-group translate-search">
            <input type="text" class="form-input input-sm" id="search" placeholder="<?=$L['translate:list:search']?>">
            <button class="btn btn-primary btn-sm input-group-btn" id="doSearch"><i class="icon-search"></i></button>
        </div>
        <div  class="col-ml-auto col-mr-auto">
            Translate from: <?=$Orign->Name?>
        </div>
        <div>
            <a class="btn btn-primary btn-sm" href="/translate/import/<?=$Lang->ID?>/"><i class="icon-import"></i> <?=$L['translate:list:import']?></a>
            <a class="btn btn-primary btn-sm" href="/translate/export/<?=$Lang->ID?>/"><i class="icon-export"></i> <?=$L['translate:list:export']?></a>
        </div>
    </div>
</div>

<div id="translate-container" data-pid="<?=$Project->ID?>" data-lid="<?=$Lang->ID?>">
    
</div>
