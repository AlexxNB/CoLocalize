<?php if($Projects):?>
  <a class="btn btn-primary btn-sm float-right" href="/projects/add/"><i class="icon-add"></i> Create Project</a>
  <h3><?=$L['projects:list:title']?></h3>
  <div class="mt"></div>

  <?php foreach($Projects as $project):?>

  <div class="tile project-tile">
    <div class="tile-icon">
        <div class="progress-icon">100%</div>
    </div>
    <div class="tile-content">
      <p class="title"><a href="/languages/<?=$project['id']?>/"><?=$project['title']?></a></p>
      <p class="descr"><?=$project['descr']?></p>
    </div>
    <div class="tile-action">
      <a class="btn" href="/projects/edit/<?=$project['id']?>/"><i class="icon-edit"></i> <?=$L['projects:list:edit']?></a>
      <button class="btn"><i class="icon-delete"></i> <?=$L['projects:list:delete']?></button>
    </div>
  </div>
  <?php endforeach;?>

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