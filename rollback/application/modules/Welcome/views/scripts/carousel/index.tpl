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

<?php $this->headScript()->appendFile($this->baseUrl().'/application/modules/Welcome/externals/scripts/jquery-1.6.2.min.js'); ?>
<?php $this->headLink()->appendStylesheet($this->baseUrl().'/application/modules/Welcome/externals/styles/carousel/carousel.css'); ?>
<?php $this->headScript()->appendFile($this->baseUrl().'/application/modules/Welcome/externals/scripts/carousel/easySlider1.7.js'); ?>

<script>
if( typeof jQuery == 'function' ){
    $jq = jQuery.noConflict(true);
}
$jq(document).ready(function(){
	$jq("#" + "<?php echo $this->containerId; ?>").easySlider({
		controlsShow: false,
		<?php if ($this->paginator->getTotalItemCount() > 1): ?>
		auto: true,
		<?php else: ?>
		auto: false,
		<?php endif; ?>
		continuous: true,
		<?php echo Engine_Api::_()->welcome()->displayOptions($this->settings); ?>
	});
});
</script>

<div id="slider_container" style="width: <?php echo $this->width; ?>px; height: <?php echo $this->height; ?>px; overflow: hidden;">
	<div id="<?php echo $this->containerId; ?>" class="slider">
		<ul style="overflow: hidden; width: <?php echo $this->width; ?>px; height: <?php echo $this->height; ?>px;">
		<?php foreach( $this->paginator as $item ): ?>
			<li style="width: <?php echo $this->width; ?>px; height: <?php echo $this->height; ?>px; background-image: url(<?php echo $item->getPhotoUrl(); ?>);">
                <a href="<?php echo $item->link;?>" target="_blank">
                    <div style="width: <?php echo $this->width; ?>px; height: <?php echo $this->height; ?>px;">
			        <?php if ($item->getTitle()): ?>
				        <span><?php echo $item->getTitle(); ?></span>
			        <?php endif; ?>
			        <?php if ($item->getBody()): ?>
				        <div class="slide_desc"><?php echo $item->getBody(); ?></div>
			        <?php endif; ?>
                    </div>
                </a>
			</li>
		<?php endforeach; ?>
		</ul>
	</div>
</div>