<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>
<?php
  $photos_uid = array();
?>


<script type="text/javascript">


  Wall.runonce.add(function (){

    <?php foreach ($this->paginator as $photo):?>

      (function (){

        <?php
          $photos_uid[$photo->getGuid()] = rand(1111,9999);
        ?>

        var $nextElement = $$('.wall-slideshow .photo .photo_uid<?php echo $photos_uid[$photo->getGuid()]?> img')[0];
        var $tags = $$('.wall-slideshow .tags.photo_uid<?php echo $photos_uid[$photo->getGuid()]?>')[0];


        var tagger = new Tagger($nextElement, {
          'title' : '<?php echo $this->string()->escapeJavascript($this->translate('ADD TAG'));?>',
          'description' : '<?php echo $this->string()->escapeJavascript($this->translate('Type a tag or select a name from the list.'));?>',
          'createRequestOptions' : {
            'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'add'), 'default', true) ?>',
            'data' : {
              'subject' : '<?php echo $photo->getGuid() ?>'
            }
          },
          'deleteRequestOptions' : {
            'url' : '<?php echo $this->url(array('module' => 'core', 'controller' => 'tag', 'action' => 'remove'), 'default', true) ?>',
            'data' : {
              'subject' : '<?php echo $photo->getGuid() ?>'
            }
          },
          'cropOptions' : {
            'container' : $nextElement
          },
          'tagListElement' : $tags,
          <?php
          // :(((((
            Engine_Api::_()->core()->clearSubject();
            Engine_Api::_()->core()->setSubject($photo);
          ?>
          'existingTags' : <?php echo $this->action('retrieve', 'tag', 'core', array('sendNow' => false)) ?>,
          'suggestProto' : 'request.json',
          'suggestParam' : "<?php echo $this->url(array('module' => 'user', 'controller' => 'friends', 'action' => 'suggest', 'includeSelf' => true), 'default', true) ?>",
          'guid' : <?php echo ( $this->viewer()->getIdentity() ? "'".$this->viewer()->getGuid()."'" : 'false' ) ?>,
          'enableCreate' : <?php echo ( $this->canTag ? 'true' : 'false') ?>,
          'enableDelete' : <?php echo ( $this->canUntagGlobal ? 'true' : 'false') ?>
        });

        tagger.addEvents({
          'onBegin' : function() {
            $nextElement.addClass('tagging');
          },
          'onEnd' : function() {
            $nextElement.removeClass('tagging');
          }
        });


        Wall.slideshows.taggers.add(<?php echo $photos_uid[$photo->getGuid()]?>, tagger);


      })();

    <?php endforeach ; ?>

  });

</script>




<div class="photo">
  <?php foreach ($this->paginator as $photo):?>

    <div
        class="item item_<?php echo $photo->getIdentity()?> <?php if ($this->subject_id == $photo->getIdentity()): ?>is_active<?php endif?> photo_uid<?php echo $photos_uid[$photo->getGuid()]?>">
      <?php if ("" != $photo->getTitle() || "" != $photo->getDescription()):?>
        <div class="header">
          <?php if ("" != $photo->getTitle()):?>
            <div class="title"><?php echo $this->viewMore($photo->getTitle())?></div>
          <?php endif;?>
          <?php if ("" != $photo->getDescription()):?>
            <div class="description"><?php echo $this->viewMore($photo->getDescription())?></div>
          <?php endif;?>
        </div>
      <?php endif;?>

      <table cellpadding="0" cellspacing="0">
            <tr>
                <td valign="middle" align="center" style="width:960px;height:600px;text-align: center;">
          <a href="javascript:void(0);" class="wall_blurlink">
            <img src="<?php echo $photo->getPhotoUrl()?>" alt="<?php echo $photo->getTitle()?>"/>
          </a>
                </td>
            </tr>
      </table>
    </div>
  <?php endforeach ; ?>
</div>

<div class="body">

  <div class="photo_items_container" style="position: relative;">

    <div class="photo_items">
            <a href="javascript:void(0);"
               class="wall_blurlink photos_prev <?php if (isset($this->paginator->getPages()->previous)): ?>has_more<?php endif;?>"
               rev="item_<?php if (isset($this->paginator->getPages()->previous)): ?><?php echo $this->paginator->getPages()->previous ?><?php endif;?>"></a>

      <div class="photo_list">
        <div class="items">
          <?php foreach ($this->paginator as $photo):?>
                    <div
                        class="item item_<?php echo $photo->getIdentity()?> <?php if ($this->subject_id == $photo->getIdentity()): ?>is_active<?php endif?>">
              <table cellpadding="0" cellspacing="0">
                <tr>
                                <td style="width:103px;height:80px;text-align: center;"><a href="javascript:void(0);"
                                                                                           rev="item_<?php echo $photo->getIdentity()?>"
                                                                                           class="<?php if ($this->subject_id == $photo->getIdentity()): ?>is_active<?php endif?> wall_blurlink"><?php echo $this->itemPhoto($photo, 'thumb.normal')?></a>
                                </td>
                </tr>
              </table>
            </div>
          <?php endforeach ; ?>
        </div>
      </div>
            <a href="javascript:void(0);"
               class="wall_blurlink photos_next <?php if (isset($this->paginator->getPages()->next)): ?>has_more<?php endif;?>"
               rev="item_<?php if (isset($this->paginator->getPages()->next)): ?><?php echo $this->paginator->getPages()->next ?><?php endif;?>"></a>
    </div>
    
        <div class="loader wall-overloader loader2"
             style="height: 90px; position: absolute; top: 0pt; width: 788px;display: none;">
      <div class="wall_icon" style="margin-top: 21px; margin-left: 372px;"></div>
    </div>

  </div>

  <div class="photos_info">
    <?php foreach ($this->paginator as $photo):?>
        <div
            class="item item_<?php echo $photo->getIdentity();?> <?php if ($this->subject_id == $photo->getIdentity()): ?>is_active<?php endif?>">

        <div class="photo_details">

          <div class="tags photo_uid<?php echo $photos_uid[$photo->getGuid()]?>" style="display: none;">
            <?php echo $this->translate('Tagged:') ?>
          </div>
          <div class="options">
            <div class="date">
              <?php echo $this->translate('Added %1$s', $this->timestamp($photo->modified_date)) ?>
              <?php if( $this->canTag ): ?>
                - <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Add Tag'), array('onclick' => 'Wall.slideshows.taggers.get('.$photos_uid[$photo->getGuid()].').begin()')) ?>
              <?php endif; ?>

              <?php if ($this->makePhoto):?>
                - <?php echo $this->htmlLink(Array('module'=> 'wall', 'controller' => 'index', 'action' => 'share', 'route' => 'default', 'type' => $photo->getType(), 'id' => $photo->getIdentity(), 'format' => 'smoothbox'), $this->translate("Share"), array('class' => 'smoothbox')); ?>
                - <?php echo $this->htmlLink(Array('module'=> 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' => $photo->getGuid(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox')); ?>
                            - <?php
                                // Timeline Page
                                    if ($this->page_id) {
                                        echo $this->htmlLink(array('route' => 'default', 'module' => 'wall', 'controller' => 'photo', 'action' => 'external-photo', 'photo' => $photo->getGuid(), 'page_id'=>$this->page_id,'format' => 'smoothbox'), $this->translate('Make Profile Photo'), array('class' => 'smoothbox'));
                                    } else {
                                        echo $this->htmlLink(array('route' => 'default', 'module' => 'wall', 'controller' => 'photo', 'action' => 'external-photo', 'photo' => $photo->getGuid(), 'format' => 'smoothbox'), $this->translate('Make Profile Photo'), array('class' => 'smoothbox'));
                                    }
                                // Timeline Page
                            ?>
              <?php endif;?>
              
            </div>
          </div>

        </div>

        <?php echo $this->wallComments($photo, $this->viewer())?>

      </div>
    <?php endforeach ; ?>
  </div>

</div>

