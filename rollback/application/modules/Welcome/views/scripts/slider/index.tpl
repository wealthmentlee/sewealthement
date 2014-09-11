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

<?php $this->headScript()->appendFile($this->baseUrl().'/application/modules/Welcome/externals/scripts/slider/slider.js'); ?>
<?php $this->headLink()->appendStylesheet($this->baseUrl().'/application/modules/Welcome/externals/styles/slider/slider.css'); ?>

<script type="text/javascript">
en4.core.runonce.add(function () {
	gc = new GoodCarousel("<?php echo $this->container; ?>", {width: <?php echo ( $this->width - 22 ); ?>, height: <?php echo ( $this->height - 68 ); ?>});
});
</script>


<style type="text/css">
<?php $counter = 0; ?>
<?php foreach( $this->paginator as $item ): ?>

	li#slide_link_<?php echo $counter; ?> a.selected{
		background-color: <?php echo $this->colors[$counter]; ?>;
		border-color: <?php echo $this->colors[$counter]; ?>;
	}
	a#carousel-slide-<?php echo $counter; ?>:hover .carousel-item-info strong,
	a#carousel-slide-<?php echo $counter; ?>:hover .carousel-item-info span,
	a#carousel-slide-<?php echo $counter; ?>:hover .carousel-item-info em{
		background-color: <?php echo $this->colors[$counter]; ?>;
		background-image: none;
	}
	<?php $counter++; ?>
	
<?php endforeach; ?>
</style>

<div id="<?php echo $this->container; ?>" class="carousel">
	<?php $counter = 0; ?>
	<?php foreach( $this->paginator as $item ): ?>
		<div class="carousel-data" id="carousel-slide-<?php echo $counter; ?>">
			<?php if ($item->getTitle()): ?><h3><?php echo strip_tags($item->getTitle()); ?></h3> <?php endif; ?>
			<?php if ($item->getBody()): ?><p class="data"><a rel="nofollow" href='<?php echo $item->link; ?>' target="_blank"><?php echo strip_tags($item->getBody()); ?></a></p><?php endif; ?>
			<img src="<?php echo $item->getPhotoUrl(); ?>" />
		</div>
	<?php $counter++; ?>
	<?php endforeach; ?>
</div>