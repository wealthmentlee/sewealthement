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

<ul class="items classifieds_profile_tab">
  <?php foreach( $this->paginator as $item ): ?>
    <li>
      <div class='item_photo'>
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.normal')) ?>
      </div>
      <div class='item_body classifieds_profile_tab_info'>
        <div class='classifieds_profile_tab_title'>
          <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
          <?php if( $item->closed ): ?>
            <img src='application/modules/Classified/externals/images/close.png'/>
          <?php endif;?>
        </div>
        <div class='item_date'>
          <?php echo $this->timestamp(strtotime($item->creation_date)) ?>
        </div>
        <div class='classifieds_browse_info_blurb'>
          <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
        </div>
      </div>
    </li>
  <?php endforeach; ?>
</ul>

<?php echo $this->htmlLink($this->url(array('user' => Engine_Api::_()->core()->getSubject()->getIdentity()), 'classified_general'), $this->translate('View All Listings'), array('class' => 'buttonlink')) ?>
