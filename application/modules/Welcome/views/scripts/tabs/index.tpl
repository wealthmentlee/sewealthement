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

<?php $this->headScript()->appendFile($this->baseUrl().'/application/modules/Welcome/externals/scripts/tabs/tabs.js'); ?>
<?php $this->headLink()->appendStylesheet($this->baseUrl().'/application/modules/Welcome/externals/styles/tabs/tabs.css'); ?>

<script type="text/javascript" xmlns="http://www.w3.org/1999/html">
  en4.core.runonce.add(function () {
		var tabs = new HE_Tabs({
			tabs: '#' + '<?php echo $this->tabs_id ?>' + ' .' + '<?php echo $this->tabs_class; ?>',
			content: '<?php echo $this->content; ?>',
			element:'.' + '<?php echo $this->element; ?>',
			container: '<?php echo $this->container; ?>',
			tab_fx: {
				_selected : '<?php echo $this->selected; ?>'
			},
			<?php echo Engine_Api::_()->welcome()->displayOptions($this->settings); ?>,
			width: <?php echo $this->width - 40; ?>,
			height: <?php echo $this->height - 40; ?>,
			<?php if ($this->paginator->getTotalItemCount() > 1): ?>
			enable_auto: true
			<?php else: ?>
			enable_auto: false
			<?php endif; ?>
		});
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
    height: <?php echo $this->height + 80; ?>px; /* match target height */
    z-index: -100001;
    background: #000;
    filter:progid:DXImageTransform.Microsoft.Blur(PixelRadius='10', MakeShadow='true', ShadowOpacity='0.60');
}
</style>
<![endif]-->

<div class="FC_wrapper">
  <div id="<?php echo $this->container; ?>" class="FC_container">
    <div id="<?php echo $this->content; ?>">
      <?php $counter = 0; ?>
      <?php foreach( $this->paginator as $item ): ?>
      <?php $counter++; ?>
        <a href="<?php echo $item->link; ?>" target="_blank">
        <div class="<?php echo $this->element; ?> content" style="background: url(<?php echo $item->getPhotoUrl(); ?>); <?php if ($counter == 1) echo "display: block;"; else echo "display: none;"; ?>">
        <?php if ($item->getTitle() || $item->getBody()): ?>
          <span class="span_content">
          <?php if ($item->getTitle()): ?>
            <h1 class="h1_header"><?php echo $item->getTitle(); ?></h1>
          <?php endif; ?>
          <?php if ($item->getBody()): ?>
            <?php echo $item->getBody(); ?>
          <?php endif; ?>
          </span>
        <?php endif; ?>
          </div>
        </a>
      <?php endforeach; ?>
    </div>
    <div id="<?php echo $this->tabs_id; ?>" class="FC_tabs">
      <?php $counter = 0; ?>
      <?php foreach( $this->paginator as $item ): ?>
      <?php $counter++; ?>
        <a class="<?php echo $this->tabs_class; ?> tab <?php if ($counter == 1) echo $this->selected . " first" ?>" id="tab_<?php echo $counter; ?>">
          <?php echo $this->itemPhoto($item, '', '', array('height' => 50)); ?>
        </a>
      <?php endforeach; ?>
      <div style="clear: both;"></div>
    </div>
  </div>
  <!--div class="ie-shadow">&nbsp;</div-->
</div>