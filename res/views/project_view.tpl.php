<h3><?=$Project->Title?></h3>

<h5><?=$L['projects:view:terms:header']?></h5>
<div class="pad container pad-terms">
    <div class="columns">
        <div class="column col-3 terms-num">
            <?=( $Termsnum==0 ? 'No' : $Termsnum )?>
            <span> <?=$L['projects:view:terms:terms']?></span>
        </div>
        <div class="column col-9 text-right">
            <a class="btn" href="/terms/list/<?=$Project->ID?>/"><i class="icon-edit"></i> <?=$L['projects:view:terms:edit']?></a>
            <a class="btn btn-primary" href="/terms/import/<?=$Project->ID?>/"><i class="icon-import"></i> <?=$L['projects:view:terms:import']?></a>
        </div>
    </div>
</div>