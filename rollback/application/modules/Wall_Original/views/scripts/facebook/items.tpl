<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: items.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>


<?php if (empty($this->stream['data'])):?>

  <?php
    if ($this->viewall){
      echo '<script type="text/javascript">Wall.runonce.add(function (){ Wall.dialog.message(en4.core.language.translate("WALL_STREAM_EMPTY_VIEWALL"), 2); });</script>';
      return ;
    }
  ?>

  <?php if (empty($this->getUpdate)):?>
  <li>
    <div class="tip">
      <span>
        <?php echo $this->translate('WALL_STREAM_EMPTY')?>
      </span>
    </div>
  </li>
  <?php endif;?>

<?php return ; endif ;?>


<?php foreach ($this->stream['data'] as $action):?>
  <li class="wall_facebook_item">
    <?php
      $this->action = $action;
      echo $this->render('facebook/item.tpl');
    ?>
  </li>
<?php endforeach;?>



<?php if( empty($this->stream['data']) ): ?>
  <?php if (empty($this->getUpdate)):?>
    <li class="utility-empty" style="display: none;">
      <div class="tip">
        <span>
          <?php
            if ($this->viewall){
              echo $this->translate("WALL_STREAM_EMPTY_VIEWALL");
            } else {
              echo $this->translate("WALL_STREAM_EMPTY");
            }
          ?>
        </span>
      </div>
    </li>
  <?php endif;?>
<?php endif;?>

<?php if ($this->show_viewall):?>
	<li class="utility-viewall">
	  <div class="pagination">
		<a href="javascript:void(0);" rev="item_<?php echo $this->next?>"><?php echo $this->translate('View More')?></a>
	  </div>
	  <div class="loader" style="display: none;">
		<div class="wall_icon"></div>
		<div class="text">
		  <?php echo $this->translate('Loading ...')?>
		</div>
	  </div>
	</li>
<?php endif;?>


<?php if ($this->since && !$this->viewall):?>
  <li class="utility-setsince wall_displaynone" rev="<?php echo $this->since;?>"></li>
<?php endif;?>
