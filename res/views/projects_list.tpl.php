<?php if($Projects):?>

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