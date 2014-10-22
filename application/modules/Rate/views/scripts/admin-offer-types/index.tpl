<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-10-01 17:53 taalay $
 * @author     TJ
 */

$this->headLink()
    ->appendStylesheet($this->baseUrl().'/application/modules/Rate/externals/styles/main.css');
?>

<h2><?php echo $this->translate("RATE_REVIEW_HEADER"); ?></h2>

<?php if (count($this->navigation)): ?>
  <div class='tabs'><?php echo $this->navigation()->menu()->setContainer($this->navigation)->render()?></div>
<?php endif; ?>

<div style="float:left;">

  <div class='settings'>

    <form action="<?php echo $this->url() ?>" method="get">
      <?php echo $this->translate('RATE_OFFERS_REVIEW_CATEGORY')?>:
      <?php echo $this->formSelect('category_id', $this->category_id, array('onChange' => '$(this).getParent("form").submit()'), $this->categories)?>
    </form>

  </div>

  <br />

  <div class='settings'>
      <?php echo $this->form->render(); ?>
  </div>

  </div>

<div style="float:left;margin-left:20px;width:250px;">
  <?php echo $this->translate('RATE_OFFERS_REVIEW_FAQ')?>
</div>
<div style="clear:both;"></div>