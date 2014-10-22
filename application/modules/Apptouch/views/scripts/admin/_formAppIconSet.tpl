<?php if ($this->homeScreen() !== false): ?>
<?php
  $this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.js')
    ->appendFile($this->layout()->staticBaseUrl . 'externals/moolasso/Lasso.Crop.js')
  ?>
<div>
  <!--    --><?php  //echo $this->itemPhoto($this->subject(), 'thumb.profile', "", array('id' => 'lassoImg')); ?>

  <?php echo $this->htmlImage($this->homeScreen()->getLinkOriginal(), $this->translate('APPTOUCH_HOMESCREEN_Original Image'), array('title' => $this->translate('APPTOUCH_HOMESCREEN_Original Image'), 'id' => 'lassoImg')) ?>
</div>
<br/>
<?php endif; ?>

<script type="text/javascript">
  var orginalThumbSrc;
  var originalSize;
  var loader = new Element('img', { src:en4.core.staticBaseUrl + 'application/modules/Core/externals/images/loading.gif'});
  var lassoCrop;

  var lassoSetCoords = function (coords) {
    var delta = (coords.w - 57) / coords.w;

    $('coordinates').value =
      coords.x + ':' + coords.y + ':' + coords.w + ':' + coords.h;

    $('previewimage').setStyles({
      top:-( coords.y - (coords.y * delta) ),
      left:-( coords.x - (coords.x * delta) ),
      height:( originalSize.y - (originalSize.y * delta) ),
      width:( originalSize.x - (originalSize.x * delta) )
    });
  }

  var lassoStart = function () {
    if (!orginalThumbSrc) orginalThumbSrc = $('previewimage').src;
    originalSize = $("lassoImg").getSize();

    lassoCrop = new Lasso.Crop('lassoImg', {
      ratio:[1, 1],
      preset:[0, 0, 57, 57],
      min:[28, 28],
      handleSize:5,
      opacity:.6,
      color:'#7389AE',
      border:'<?php echo $this->layout()->staticBaseUrl . 'externals/moolasso/crop.gif' ?>',
      onResize:lassoSetCoords,
      bgimage:''
    });

    $('previewimage').src = $('lassoImg').src;
    var r = (originalSize.x > originalSize.y ? originalSize.y - 2 : originalSize.x - 2); // -2px for safety
    r = r > 57 ? 57 : r;
    //$('preview-thumbnail').innerHTML = '<img id="previewimage" src="'+sourceImg+'"/>';
    $('thumbnail-controller').innerHTML = '<a href="javascript:void(0);" onclick="lassoEnd();"><?php echo $this->translate('Apply Changes');?></a> <?php echo $this->translate('or');?> <a href="javascript:void(0);" onclick="lassoCancel();"><?php echo $this->translate('cancel');?></a>';
    $('coordinates').value = 0 + ':' + 0 + ':' + r + ':' + r;
  }

  var lassoEnd = function () {
    $('thumbnail-controller').innerHTML = "<div><img class='loading_icon' src='application/modules/Core/externals/images/loading.gif'/><?php echo $this->translate('Loading...');?></div>";
    lassoCrop.destroy();
    $('EditHomeScreen').submit();
  }

  var lassoCancel = function () {
    $('preview-thumbnail').innerHTML = '<img id="previewimage" src="' + orginalThumbSrc + '"/><div class="hs_mask"></div>';
    $('thumbnail-controller').innerHTML = '<a href="javascript:void(0);" onclick="lassoStart();"><?php echo $this->translate('Edit Thumbnail');?></a>';
    $('coordinates').value = "";
    lassoCrop.destroy();
  }

  var uploadHomeScreenPhoto = function () {
    $('thumbnail-controller').innerHTML = "<div><img class='loading_icon' src='application/modules/Core/externals/images/loading.gif'/><?php echo $this->translate('Loading...')?></div>";
    $('EditHomeScreen').submit();
    $('Filedata-wrapper').innerHTML = "";
  }
</script>
