<?php if ($this->homeScreen() !== false): ?>
<div id="preview-thumbnail" class="preview-thumbnail">
  <?php echo $this->htmlImage($this->homeScreen()->getLink57x57(true), '57x57', array('id' => 'previewimage')) ?>
  <div class="hs_mask"></div>
</div>
<div id="thumbnail-controller" class="thumbnail-controller">
  <?php if ($this->homeScreen())
  echo '<a href="javascript:void(0);" onclick="lassoStart();">' . $this->translate('Edit Thumbnail') . '</a>';?>
</div>
<?php endif; ?>