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

<?php if( !$this->classified): ?>
<?php echo $this->translate('The classified you are looking for does not exist or has been deleted.');?>
<?php return; // Do no render the rest of the script in this mode
endif; ?>

<div class='layout_middle'>
  <h4>
    &raquo;
    <?php echo $this->classified->getTitle(); ?>
    <?php if( $this->classified->closed == 1 ): ?>
      <img src='application/modules/Classified/externals/images/close.png'/>
    <?php endif;?>
  </h4>
  <ul class='classifieds_entrylist'>
    <li>
      <div class="classifide_entrylist_entry_date">
        <?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->classified->getParent(), $this->classified->getParent()->getTitle()) ?>
        <?php echo $this->timestamp($this->classified->creation_date) ?>
        <?php if ($this->category):?>- <?php echo $this->translate('Filed in');?>
        <?php echo $this->translate($this->category->category_name); ?>
        <?php endif; ?>

        <div><?php echo $this->translate(array('%s view', '%s views', $this->classified->view_count), $this->locale()->toNumber($this->classified->view_count)) ?></div>

        <?php echo $this->fieldValueLoop($this->classified, $this->fieldStructure) ?>

      </div>

      <?php if ($this->classified->closed == 1):?>
        <div class="result_message">
            <?php echo $this->translate('This classified listing has been closed by the poster.');?>
        </div>
      <?php endif; ?>

      <div class="classified_entrylist_entry_body">
        <?php echo nl2br($this->classified->body) ?>
      </div>
        <ul class='classified_thumbs'>
          <?php if($this->main_photo):?>
            <li>
              <div class="classifieds_thumbs_description">
                <?php if( '' != $this->main_photo->getDescription() ): ?>
                  <?php echo $this->string()->chunk($this->main_photo->getDescription(), 100) ?>
                <?php endif; ?>
              </div>
              <?php echo $this->htmlImage($this->main_photo->getPhotoUrl(), $this->main_photo->getTitle(), array(
                'id' => 'media_photo'
              )); ?>
            </li>
          <?php endif; ?>

          <?php foreach( $this->paginator as $photo ): ?>
            <?php if($this->classified->photo_id != $photo->file_id):?>
              <li>
                <div class="classifieds_thumbs_description">
                  <?php if( '' != $photo->getDescription() ): ?>
                    <?php echo $this->string()->chunk($photo->getDescription(), 100) ?>
                  <?php endif; ?>
                </div>
                <?php echo $this->htmlImage($photo->getPhotoUrl(), $photo->getTitle(), array(
                  'id' => 'media_photo'
                )); ?>
              </li>
            <?php endif; ?>
          <?php endforeach;?>
        </ul>
    </li>
  </ul>

  <div class="classified_stats">
    <?php

    echo $this->htmlLink(
      array(
        'route' => 'default',
        'module' => 'activity',
        'controller' => 'index',
        'action' => 'share',
        'type' => $this->classified->getType(),
        'id' => $this->classified->getIdentity(),
        'return_url' => urlencode($_SERVER['REQUEST_URI'])
      ),
      $this->translate('Share This Classified')
    );?>

  </div>

  <?php echo $this->mobileAction("list", "comment", "core", array("type"=>"classified", "id"=>$this->classified->getIdentity(), 'viewAllLikes'=>true)); ?>

</div>