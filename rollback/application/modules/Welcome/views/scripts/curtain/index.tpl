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
<?php $this->headScript()->appendFile($this->baseUrl().'/application/modules/Welcome/externals/scripts/curtain/jqFancyTransitions.1.8.js'); ?>
<?php $this->headLink()->appendStylesheet($this->baseUrl().'/application/modules/Welcome/externals/styles/curtain/curtain.css'); ?>

<script>
if( typeof jQuery == 'function' ){
    $jq = jQuery.noConflict(true);
}
$jq(document).ready(function($){
	$jq('#' + '<?php echo $this->containerId; ?>').jqFancyTransitions({
		<?php echo Engine_Api::_()->welcome()->displayOptions($this->settings); ?>,
		width: <?php echo $this->width-20; ?>,height: <?php echo $this->height - 20; ?>, links: true
	});
	$jq("#ft-title-" + "<?php echo $this->containerId; ?>").width(<?php echo $this->width - 26; ?>);
});
</script>

<!--[if IE]>
<style type="text/css">
.ie-shadow {
    display: block;
    position: absolute;
    top: 0px;
    left: 0px;
    width: <?php echo $this->width - 20; ?>px; /* match target width */
    height: <?php echo $this->height; ?>px; /* match target height */
    z-index: 1;
    background: #000;
    filter:progid:DXImageTransform.Microsoft.Blur(PixelRadius='10', MakeShadow='true', ShadowOpacity='0.60');
}
</style>
<![endif]-->

<div class="curtain_wrapper" style="width: <?php echo $this->width - 20; ?>px; height: <?php echo $this->height - 20; ?>px;">
  <div class="curtain_container">
    <div id='<?php echo $this->containerId; ?>'>
      <?php foreach( $this->paginator as $item ): ?>
        <img src='<?php echo $item->getPhotoUrl(); ?>' alt='<?php echo $item->getBody(); ?>' />
        <a href="<?php echo $item->link; ?>" target="_blank"></a>
      <?php endforeach; ?>
    </div>
  </div>
  <!--div class="ie-shadow">&nbsp;</div-->
</div>