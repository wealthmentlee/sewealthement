<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<h4>
  &raquo; <?php echo $this->subject->__toString()?>
  &raquo; <?php echo $this->htmlLink(array('route' => 'page_review', 'action' => 'index', 'page_id' => $this->subject->getIdentity()), $this->translate('MOBILE_PAGE_REVIEWS')) ?>
</h4>

<?php if ($this->paginator->count() > 0):?>

  <ul class="items">

  <?php foreach ($this->paginator as $item):?>

    <li class="<?php if ($item['is_owner']):?> owner<?php endif;?>">

      <div class="item_body">

        <div class="item_options">

          <?php if ($item['is_owner']):?>
            <?php echo $this->htmlLink(array('route' => 'page_review', 'action'  => 'edit', 'pagereview_id' => $item->getIdentity(), 'return_url' => urlencode($_SERVER['REQUEST_URI'])), $this->translate('Edit'))?>
          <?php endif;?>

          <br />

          <?php if ($item['is_owner'] || $this->isAllowedRemove):?>
            <?php echo $this->htmlLink(array('route' => 'page_review', 'action' => 'remove', 'review_id' => $item->getIdentity(), 'return_url' => urlencode($_SERVER['REQUEST_URI'])), $this->translate('Delete'))?>
          <?php endif;?>

        </div>

        <div class="item_title">
          <?php echo $this->htmlLink(array(
            'route' => 'page_review',
            'action' => 'view',
            'review_id' => $item['pagereview_id']
          ), $item['title'])?>
        </div>

        <?php if ($this->countOptions): ?>

          <div class="rate_stars_cont">

          <?php

            $star_value = 'no_rate';

            for ($i = 0; $i < 5; $i++)
            {
              if (($i + 0.125) > $item['rating']) {
                $star_value = 'no_rate';
              } else if (($i + 0.375) > $item['rating']) {
                $star_value = 'quarter_rated';
              } else if (($i + 0.625) > $item['rating']) {
                $star_value = 'half_rated';
              } else if (($i + 0.875) > $item['rating']) {
                $star_value = 'fquarter_rated';
              } else {
                $star_value = 'rated';
              }

              echo $this->htmlImage($this->baseUrl() . '/application/modules/Mobile/externals/images/rate/small_' . $star_value . '.png');
            }

          ?>

            <div class="count">(<?php echo round($item['rating'],2)?>)</div>

            <div style="clear:both;"></div>

          </div>

        <?php endif;?>

        <div class="item_date">
          <?php echo $this->translate('Posted');?> <?php echo $this->timestamp($item->creation_date)?>
        </div>

        <?php echo $this->mobileSubstr($item->body)?>

      </div>

    </li>

  <?php endforeach;?>

  </ul>

<?php if( $this->paginator->count() > 1 ): ?>
  <?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile')); ?>
<?php endif; ?>


<?php if ($this->isAllowedPost):?>

  <div class="result_message">
    <a href="<?php echo $this->url(array('action' => 'create', 'page_id' => $this->subject->getIdentity()), 'page_review', true)?>">
      <?php echo $this->translate('RATE_REVIEW_CREATE')?>
    </a>
  </div>
<?php endif; ?>

<?php else: ?>

  <div class="result_message">

  <?php echo $this->translate('RATE_REVIEW_TIP');?> <br />

    <?php if ($this->isAllowedPost):?>
      <a href="<?php echo $this->url(array('action' => 'create', 'page_id' => $this->subject->getIdentity()), 'page_review', true)?>">
        <?php echo $this->translate('RATE_REVIEW_CREATE')?>
      </a>
    <?php endif; ?>

  </div>

<?php endif; ?>