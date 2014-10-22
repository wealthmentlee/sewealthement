<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _formEditImage.tpl 9694 2012-04-19 23:22:08Z richard $
 * @author     John
 */
?>
<?php if( $this->subject()->photo_id !== null ): ?>

      <div data-role="navbar" data-grid="d" class="component-gallery">
        <ul class="thumbs">
          <li><a class="thumbs_photo" href="<?php echo $this->subject()->getPhotoUrl('') ?>">
            <span style="background-image: url('<?php echo $this->subject()->getPhotoUrl('thumb.profile') ?>');"></span>
          </a></li>
        </ul>
      </div>
<!--  <br />-->
<!--  <div id="preview-thumbnail" class="preview-thumbnail">-->
<!--    --><?php //echo $this->itemPhoto($this->subject(), 'thumb.icon', "", array('id' => 'previewimage')) ?>
<!--  </div>-->
<?php endif; ?>