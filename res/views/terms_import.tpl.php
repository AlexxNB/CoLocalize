<h3><?=$L['terms:import:header']?></h3>
<div id="upload-form">
  <div class="form-group">
      <label for="importfilefile" class="form-label"><?=$L['terms:import:choose_file']?></label>
      <input type="file" id="importfile" class="form-input"/>
  </div>

  <div class="form-group" id="parsers-list">
    <label class="form-label"><?=$L['terms:import:choose_type']?></label>
    <?php foreach($Parsers as $Parser): ?>
      <div id="parser-<?=$Parser->ID?>">
        <label class="form-radio">
          <input type="radio" name="parser" value="<?=$Parser->ID?>" data-type="<?=$Parser->Filetype?>">
          <i class="form-icon"></i> <?=$Parser->Title?>
          <a href="/parserinfo/<?=$Parser->ID?>" class="nu"><i class="icon-info"></i></a>
        </label>
      </div>
    <?php endforeach; ?>
      <div id="unsupported" class="text-gray hide" data-err="<?=$L['terms:import:msg:unsupported_file']?>"><?=$L['terms:import:msg:unsupport']?></div>
  </div>

  <div class="form-group text-center mt">
      <button class="btn btn-primary" id="doImportFile" disabled="disabled" data-pid="<?=$Pid?>"><i class="icon-upload"></i> <?=$L['terms:import:upload_button']?></button> 
  </div>
</div>
<div id="upload-progress" class="hide">
  <progress class="progress" value="0" max="100"></progress>
  <div id="stage-upload" class="text-gray text-center"><?=$L['terms:import:msg:uploading']?></div>
  <div id="stage-parsing" class="text-gray text-center hide"><?=$L['terms:import:msg:parsing']?></div>
</div>