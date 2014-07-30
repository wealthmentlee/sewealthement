<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2011-02-14 07:29:38 mirlan $
 * @author     Mirlan
 */

if ($this->isReview): ?>

  <div class="reviews_container">

    <?php

    foreach ($this->types as $type):?>

    <div class="review_stars_static review_widget">
      <div class="rating" title="<?= $this->translate('RATE_REVIEW_Add review'); ?>">


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

        <div style="clear:both;"></div>

      </div>

      </div>
      <div class="title"><?php echo $type->label?> (<?php echo $type->value?>)</div>
      <div style="clear:both;"></div>
    </div>

    <?php endforeach;?>

    </div>
    <div class="clr"></div>

<?php else: ?>

    <div class="rate_stars_cont" style="width:160px;">
        <?php for ($i = 0; $i < $this->maxRate; $i++){
                  if (($i + 0.125) > $this->item_score) {
                    $star_value = 'no_rate';
                  } else if (($i + 0.375) > $this->item_score) {
                    $star_value = 'quarter_rated';
                  } else if (($i + 0.625) > $this->item_score) {
                    $star_value = 'half_rated';
                  } else if (($i + 0.875) > $this->item_score) {
                    $star_value = 'fquarter_rated';
                  } else {
                    $star_value = 'rated';
                  }
        ?>

          <?php echo $this->htmlLink(array(
            'route' => 'widget_rate',
            'QUERY' => array(
              'type' => $this->item_type,
              'id' => $this->item_id,
              'score' => $i + 1,
              'return_url' => urlencode($_SERVER['REQUEST_URI'])
            )
          ), $this->htmlImage($this->baseUrl() . '/application/modules/Mobile/externals/images/rate/' . $star_value . '.png'),
          array('class' => 'mobile_rate'))
      ?>

        <?php }?>
      <div class="clr"></div>
    </div>
    <div class="item_rate_info">
        <?php $this->translate('Score:') ?> <span class="item_score"><?php echo $this->item_score?>/<?php echo $this->maxRate?></span>
        <span class="item_votes"><?php echo ($this->rate_info) ? $this->rate_info['rate_count'] : 0; ?></span>

        <?php echo $this->htmlLink(array(
        'route' => 'default',
        'module' => 'hecore',
        'controller' => 'list',
        'action' => 'index',
        'QUERY' => array(
          'params' => array(
            'item_type' => $this->item_type,
            'item_id' => $this->item_id,
            'list_title2' => $this->translate('MOBILE_FRIEND_TAB')
          ),
          't' => $this->translate('MOBILE_Item votes'),
          'mm' => 'rate',
          'l' => 'getItemVoters',
          'not_logged_in' => 0 ,
          'return_url' => urlencode($_SERVER['REQUEST_URI']),
        )
      ), $this->translate(array('vote', 'votes', (($this->rate_info) ? $this->rate_info['rate_count'] : 0))));

  ?>

    </div>

<div class="clr"></div>

<?php endif?>