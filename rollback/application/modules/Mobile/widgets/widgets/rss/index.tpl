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

?>

<?php if( !empty($this->channel) ): ?>
  <ul>
    <?php $count=0;foreach( $this->channel['items'] as $item ): $count++ ?>
      <li class="rss_item">
        <div class="rss_item_<?php echo $count ?>">
          <?php echo $this->htmlLink($item['guid'], $item['title'], array('target' => '_blank', 'class' => 'rss_link_'.$count)) ?>
          <p class="rss_desc">
            <?php $desc = Engine_String::strip_tags($item['description']); if( Engine_String::strlen($desc) > 350 ): ?>
              <?php echo Engine_String::substr($desc, 0, 350) ?>...
            <?php else: ?>
              <?php echo $desc ?>
            <?php endif; ?>
          </p>
        </div>
        <div class="rss_time">
          <?php echo $this->locale()->toDatetime(strtotime($item['pubDate']), array('size' => 'long')) ?>
        </div>
      </li>
    <?php endforeach; ?>
    <li class="rss_last_row">
      <div>
        &nbsp;
      </div>
      <div>
        &#187; <?php echo $this->htmlLink($this->chanel['link'], $this->translate("More")) ?>
      </div>
    </li>
  </ul>
<?php endif; ?>