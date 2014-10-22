<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: editphotos.tpl 8380 2011-02-02 04:59:12Z john $
 * @author     Sami
 */
?>

<h3>
  <?php echo $this->htmlLink($this->album->getHref(), $this->album->getTitle()) ?>
  (<?php echo $this->translate(array('%s photo', '%s photos', $this->album->count()), $this->locale()->toNumber($this->album->count())) ?>
  )
</h3>
<form action="<?php echo $this->escape($this->form->getAction()) ?>"
      method="<?php echo $this->escape($this->form->getMethod()) ?>">
  <?php echo $this->form->album_id; ?>
  <ul class='albums_editphotos'>
    <?php foreach ($this->paginator as $photo): ?>
    <li>
      <div class="albums_editphotos_photo">
        <?php echo $this->htmlLink($photo->getHref(), $this->itemPhoto($photo, 'thumb.normal'))  ?>
      </div>
      <div class="albums_editphotos_info">
        <?php
        $key = $photo->getGuid();
        echo $this->form->getSubForm($key)->render($this);
        ?>
        <input id="cover-<?php echo $photo->getIdentity() ?>" type="radio" name="cover"
               value="<?php echo $photo->getIdentity() ?>" <?php if ($this->album->photo_id == $photo->getIdentity()): ?>
               checked="checked"<?php endif; ?> />
        <label for="cover-<?php echo $photo->getIdentity() ?>"><?php echo $this->translate('Album Cover');?></label>
      </div>
    </li>
    <?php endforeach; ?>
  </ul>

  <?php echo $this->form->submit->render(); ?>
</form>
