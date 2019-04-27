<?php if($Projects):?>
  <a class="btn btn-primary btn-sm float-right" href="/projects/add/"><i class="icon-add"></i> Create Project</a>
  <h3><?=$L['projects:list:title']?></h3>
  <div class="mt"></div>

  <?php foreach($Projects as $project):?>
  <div class="tile project-tile" id="pid-<?=$project->ID?>">
    <div class="tile-icon">
        <div class="progress-icon">100%</div>
    </div>
    <div class="tile-content">
      <p class="title"><a href="/projects/view/<?=$project->ID?>/"><?=$project->Title?></a></p>
      <p class="descr"><?=$project->Descr?></p>
    </div>
    <div class="tile-action">
      <a class="btn" href="/projects/edit/<?=$project->ID?>/"><i class="icon-edit"></i> <?=$L['projects:list:edit']?></a>
      <button class="btn doDeleteProject" data-pid="<?=$project->ID?>"><i class="icon-delete"></i> <?=$L['projects:list:delete']?></button>
    </div>
  </div>
  <?php endforeach;?>

<div class="modal modal-sm" id="modal-delete">
  <span class="modal-overlay modal-close" aria-label="Close"></span>
  <div class="modal-container">
    <div class="modal-header">
        <span class="btn btn-clear float-right modal-close" aria-label="Close"></span>
    </div>
    <div class="modal-body">
      <div class="content">
        <?=$L['projects:list:msg:sure_delete']?>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn modal-close"><i class="icon-close"></i> <?=$L['cancel']?></button>
      <button class="btn btn-primary" id="confirmDelete" data-ok="<?=$L['projects:list:msg:delete_ok']?>" data-err="<?=$L['projects:list:msg:delete_err']?>"><i class="icon-ok"></i> <?=$L['yes']?></button>
    </div>
  </div>
</div>

<?php else: ?>
<div class="empty">
  <div class="empty-icon">
    <i class="icon-folder"></i>
  </div>
  <p class="empty-title h5"><?=$L['projects:no_projects']?></p>
  <p class="empty-subtitle"><?=$L['projects:you_can_create']?></p>
  <div class="empty-action">
    <a class="btn btn-primary" href="/projects/add"><?=$L['projects:create_project']?></a>
  </div>
</div>
<?php endif; ?>