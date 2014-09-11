<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<h4>
  &raquo; <?php echo $this->subject->__toString()?>
  &raquo; <?php echo $this->htmlLink(array('route' => 'page_review', 'action' => 'index', 'page_id' => $this->subject->getIdentity()), $this->translate('MOBILE_PAGE_REVIEWS')) ?>
  &raquo; <?php echo $this->htmlLink(array('route' => 'page_review', 'action' => 'view', 'review_id' => $this->review->getIdentity()), ($this->review->getTitle()) ? $this->review->getTitle() : $this->translate('Untitled')) ?>
</h4>

<div class="layout_content">
<ul class="items subcontent">
	<li>
		<div class="item_body pagereview_review">
			<h3><?php echo $this->review->getTitle() ?></h3>

      <?php echo $this->translate("MOBILE_%1\$s's Review", $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()))?>

      <div class="item_date">
        <?php echo $this->translate('Posted');?> <?php echo $this->timestamp($this->review->creation_date)?>
      </div>

      <div>

        <?php

foreach ($this->types as $type):?>

<div class="review_stars_static view">
  <div class="rating">

      <div class="rate_stars_cont">

      <?php

        $star_value = 'no_rate';

        for ($i = 0; $i < 5; $i++)
        {
          if (($i + 0.125) > $type->value) {
            $star_value = 'no_rate';
          } else if (($i + 0.375) > $type->value) {
            $star_value = 'quarter_rated';
          } else if (($i + 0.625) > $type->value) {
            $star_value = 'half_rated';
          } else if (($i + 0.875) > $type->value) {
            $star_value = 'fquarter_rated';
          } else {
            $star_value = 'rated';
          }

          echo $this->htmlImage($this->baseUrl() . '/application/modules/Mobile/externals/images/rate/small_' . $star_value . '.png');
        }

      ?>

        <div class="count">(<?php echo round($type->value,2)?>)  </div>

        <div style="clear:both;"></div>

      </div>




  </div>
  <div class="title"><?php echo $type->label?></div>
  <div class="clr"></div>
</div>
<div class="clr"></div>

<?php endforeach;?>


      </div>

		</div>

	</li>

	<li style="border-top: 0px;">
			<div class="item_body">
				<?php echo $this->review->getDescription() ?>
			</div>
	</li>
</ul>

<div style="padding-bottom: 5px;"></div>

<?php echo $this->mobileAction("list", "comment", "core", array("type"=>"pagereview", "id"=>$this->review->getIdentity(), 'viewAllLikes'=>true)) ?>

</div>