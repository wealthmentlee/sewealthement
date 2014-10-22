<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2012-12-12 17:08 ratbek $
 * @author     Ratbek
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

?>
<script type="text/javascript">
  ReviewManager.widget_url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'content_id' => $this->identity), 'default', true) ?>';
</script>
<div class="layout_core_container_tabs fw_active_theme_<?php echo $this->activeTheme()?>">
  <div class="tabs_alt tabs_parent">
    <ul id="main_tabs">
      <li class="<?php if ($this->sort == 'recent') echo 'active'; ?>">
        <a class="page_sort_buttons"
           id="page_sort_recent"
           href="<?php echo $this->url(array('sort_type'=>'sort', 'sort_value'=>'recent'), 'browse_reviews_sort'); ?>"
           onclick="ReviewManager.setSort('recent'); return false;"><?php echo $this->translate("Recent")?></a>
      </li>
      <li class="<?php if ($this->sort == 'rated') echo 'active'; ?>">
        <a class="page_sort_buttons"
           id="page_sort_sponsored"
           href="<?php echo $this->url(array('sort_type'=>'sort', 'sort_value'=>'rated'), 'browse_reviews_sort'); ?>"
           onclick="ReviewManager.setSort('rated'); return false;"><?php echo $this->translate("Most Rated")?></a>
      </li>
      <a style="margin-left: 5px" id="page_loader_browse" class="page_loader_browse hidden"><?php echo $this->htmlImage($this->baseUrl().'/application/modules/Page/externals/images/loader.gif', ''); ?></a>
    </ul>
  </div>
</div>

<span id="page_category_info" class="page_category_info hidden">
  <span class="bold"><?php echo $this->page_category_name; ?></span>
  category. [<a class="bold" onclick="ReviewManager.setCategory(0);" href="javascript:void(0)">x</a>]
</span>

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
  <ul class="page_reviews_browse">
    <?php foreach( $this->paginator as $item ):
        $page= Engine_Api::_()->getItem('page', $item->page_id);
        ?>
      <li>
        <div class='reviewer_photo'>
          <?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>
        </div>
        <div class='reviews_info'>
          <div class='reviews_info_title'>
            <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
          </div>
          <span class="reviews_additional_info">
            <span><?php echo $this->translate('PAGE_Posted by'); ?> <a href="<?php echo $item->getOwner()->getHref(); ?>"><?php echo $item->getOwner(); ?></a></span>
            <span id="time_create_rate_<?php echo $item->getIdentity()?>">
                <?php
                $mouths = array(
                    'January',
                    'February',
                    'March',
                    'April',
                    'May',
                    'June',
                    'July',
                    'August',
                    'September',
                    'October',
                    'November',
                    'December',
                    'Monday',
                    'Tuesday',
                    'Wednesday',
                    'Thursday',
                    'Friday',
                    'Saturday',
                    'Sunday'


                );
                $pieces = explode(">", $this->timestamp($item->creation_date));
                $pieces = explode(" ", $pieces[1]);;
                foreach($pieces as $p){

                    if(in_array($p, $mouths)){
                        $m = $p;
                    }
                }
                echo str_replace($mouths, "on ".$m, $this->timestamp($item->creation_date)); ?>
            </span>
            <?php $comments_count =  $item->comments()->getCommentCount(); ?>
            <?php if ($comments_count): ?>
              <span><?php echo $this->translate(array('PAGE_%s comment', 'PAGE_%s comments', $comments_count), $comments_count); ?></span>
            <?php endif; ?>
            <?php $likes_count = $item->likes()->getLikeCount(); ?>
            <?php if ($likes_count): ?>
              <span><?php echo $this->translate(array('PAGE_%s like', 'PAGE_%s likes', $likes_count), $likes_count); ?></span>
            <?php endif; ?>
          </span>
          <div class="for_page">
            <?php

            $url_page = $item->getHref();
            $url_page=substr($url_page, 0, strrpos($url_page, 'content/review'));
            echo $this->translate('For');?> <a href="<?php echo $url_page;?>"><?php echo $item->displayname; ?></a>
            <a style="display: block" href="<?php echo $url_page;?>"><img style="margin: 4px" src="<?php echo $page->getPhotoUrl('thumb.normal'); ?>"/></a>
          </div>
          <div class="reviews_rates">
            <?php foreach ($this->allVotes as $votes): ?>
              <?php if ($votes->review_id == $item->pagereview_id): ?>
                <div class="posted">
                  <div class="he_rate_small_cont">
                    <?php echo $this->reviewRate($votes->rating); ?>
                    <?php
                    if($votes->label !=  'Rate') echo $votes->label; ?>
                  </div>
                  <div class="clr"></div>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
          <p class='reviews_info_blurb'>
            <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
          </p>
        <div>
      </li>
    <?php endforeach; ?>
  </ul>
  <?php //echo $this->paginationControl($this->paginator); ?>
  <?php if( $this->paginator->getTotalItemCount() > 1 ): ?>
    <?php echo $this->paginationControl($this->paginator, null, array("pagination/browseReviews.tpl", "rate")); ?>
  <?php endif; ?>
<?php else: ?>
  <div class="tip"><span><?php echo $this->translate("There is no reviews"); ?></span></div>
<?php endif;?>