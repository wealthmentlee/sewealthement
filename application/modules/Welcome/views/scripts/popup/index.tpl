 <?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Welcome
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 17:53 idris $
 * @author     Idris
 */
?>

<?php $this->headScript()->appendFile($this->baseUrl().'/application/modules/Welcome/externals/scripts/popup/popup.js'); ?>
<?php $this->headLink()->appendStylesheet($this->baseUrl().'/application/modules/Welcome/externals/styles/popup/popup.css'); ?>

<script type="text/javascript">
  en4.core.runonce.add(function () {
		var mySlideMenu = new BySlideMenu('<?php echo $this->containerId; ?>', {
			<?php echo Engine_Api::_()->welcome()->displayOptions($this->settings); ?>,
			elementWidth: <?php echo $this->width; ?>,
			elementHeight: <?php echo $this->height-22; ?>
		});
	});
</script>

<!--[if IE]>
<style type="text/css">
.obj{
	display: none;
}
.ie-shadow {
    display: block;
    position: absolute;
    top: 3px;
    left: -9px;
    width: <?php echo $this->width - 10 + (40 * $this->paginator->getCurrentItemCount()); ?>px; /* match target width */
    height: <?php echo $this->height + 10; ?>px; /* match target height */
    z-index: -100001;
    background: #000;
    filter:progid:DXImageTransform.Microsoft.Blur(PixelRadius='5', MakeShadow='true', ShadowOpacity='0.60');
}
</style>
<![endif]-->

<div style="position: relative;">
<div class="popup_container">
	<ul id="<?php echo $this->containerId; ?>" class="popup_slides">
		<?php $counter = 0; ?>
		<?php foreach( $this->paginator as $item ): ?>
		<?php $counter++; ?>
			<li class="popup_slide <?php if ($counter == 1) echo "first_slide"; elseif ($counter == ($this->paginator->getCurrentItemCount() - 1)) echo "last_slide"; ?>">
              <a href="<?php echo $item->link; ?>" target="_blank">
			  <div class="popup_img">
			    <?php echo $this->itemPhoto($item, "", "", array('class' => 'noclass', 'width' => ($this->width))); ?>
			  </div>

			  <?php if ($item->getTitle()): ?>
			  <!--   <table class="popup_title" style="position: absolute; top: 0px; left: 0px;"><tr><td valign="center"> -->
			  <div class="popup_title">
					<div class="popup_title_text">
						<?php echo $item->getTitle(); ?>
					</div>
				</div>
				<!--  </td></tr></table>  -->
				<?php endif; ?>
				<?php if ($item->getBody()): ?>
				<div class="popup_text">
					<?php echo $item->getBody(); ?>				
				</div>
				<?php endif; ?>
              </a>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
<!--div class="ie-shadow">&nbsp;</div-->
</div>