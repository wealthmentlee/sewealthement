<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: request.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>
<div class="suggest-object">
  <?php if (!in_array($this->object->getType(), array('blog'))): ?>
    <div class="photo">
      <?php echo $this->htmlLink($this->suggest->getObjectHref(), $this->itemPhoto($this->object, $this->thumb, '', array('height' => '110px'))); ?>
    </div>
  <?php else: ?>
    <div class="photo">
      <?php echo $this->htmlLink($this->suggest->getObjectHref(), $this->htmlImage('/application/modules/Blog/externals/images/nophoto/blog.png', '', array('height' => '110px'))); ?>
    </div>
  <?php endif; ?>
  <div class="info">
    <div class="title">
      <?php echo $this->htmlLink($this->suggest->getObjectHref(), $this->object->getTitle()); ?>
      <?php if ($this->likeEnabled): ?>
        <?php
        if (!Engine_Api::_()->like()->isLike($this->object)) {
          $url = $this->url(array('action' => 'like', 'object_type' => $this->object->getType(), 'object_id' => $this->object->getIdentity()), 'like_default');
          $label = 'like_Like';
        } else {
          $url = $this->url(array('action' => 'unlike', 'object_type' => $this->object->getType(), 'object_id' => $this->object->getIdentity()), 'like_default');
          $label = 'like_Unlike';
        }
        ?>
        <div class="like_button_container">
          <form action="<?php echo $url; ?>">
            <button type="submit" class="like_button"><?php echo $this->translate($label); ?></button>
            <input type="hidden" name="return_url" value="<?php echo $_SERVER['REQUEST_URI']; ?>"/>
          </form>
        <div class="clr"></div>
        </div>
      <?php endif; ?>
    </div>
    <div class="clr"></div>
    <div class="description">
      <?php echo $this->suggest->getDescription(); ?>
    </div>
    <div class="clr"></div>
    <?php
    $suggest = $this->suggest;
    $html = '<div class="options">';
    $object = $suggest->getObject();
    $type = $object->getType();
    $id = $object->getIdentity();
    $or = $this->translate('or');
    $label = $this->translate("suggest_view_this_".$type);

    switch ($type) {
      case 'group':
      case 'event':
        $url = $this->url(array(
            'controller' => 'member',
            'action' => 'join',
            $type.'_id' => $id
          ), $type.'_extended');

        $params = array('class' => 'smoothbox button', 'style' => 'float: left;');
      break;
      case 'user':
        $url = $this->url(array(
            'controller' => 'friends',
            'action' => 'add',
              'user_id' => $id
          ), 'user_extended');

        $params = array('class' => 'smoothbox button');
      break;
      case 'suggest_profile_photo':
        $url = $this->url(array(
          'action' => 'profile-photo',
          'photo_id' => $object->getIdentity(),
          'format' => 'smoothbox'), 'suggest_general');
        $params = array('class' => 'smoothbox button');
      break;
      default:
        $url = $this->url(array(
            'controller' => 'index',
            'action' => 'accept-suggest',
            'object_type' => $type,
            'object_id' => $id,
          ), 'suggest_general');

        $params = array('class' => 'button', 'target' => '_blank');
      break;
    }

    $link = $this->htmlLink($url, $label, $params);
    $cancel = $this->htmlLink($this->url(array(
        'action' => 'index',
        'controller' => 'index',
        'suggest_id' => $suggest->getIdentity()
      ), 'suggest_general').'?return_url='.$_SERVER['REQUEST_URI'],
      $this->translate('suggest_cancel_suggest_'.$type),
      array(
        'class' => 'button disabled'
      )
    );

    $html .= '<div class="accept">'.$link.'</div>';
    $html .= '<div class="or">'.$or.'</div>';
    $html .= '<div class="cancel">'.$cancel.'</div>';
    $html .= '<div class="clr"></div></div>';
    ?>
    <?php echo $html; ?>
  </div>
  <div class="clr"></div>
</div>