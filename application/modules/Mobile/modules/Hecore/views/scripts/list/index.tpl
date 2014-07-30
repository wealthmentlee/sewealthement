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

<?php if ($this->error): ?>
  <div class="contacts_error no_content"><?php echo $this->message; ?></div>
<?php else: ?>



    <h4>
      <?php if ($this->title): ?>&raquo; <?php echo $this->title; ?><?php endif; ?>&nbsp;
      <?php if ($this->return_url):?>
        ( <a href="<?php echo urldecode($this->return_url)?>"><?php echo $this->translate('MOBILE_RETURN_BACK')?></a> )
      <?php endif;?>
    </h4>


  <div class="he_list_tabs">

    <?php
      $params_link1 = ($this->list_type == 'all') ? array('class' => 'active') : array();
      $params_link2 = ($this->list_type == 'mutual') ? array('class' => 'active') : array();
    ?>

    <?php echo $this->translate('MOBILE_SHOW')?>

    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'hecore', 'controller' => 'list', 'action' => 'index', 'QUERY' => array_merge($this->url_params, array('list_type' => 'all'))), (!empty($this->params['list_title1']) ? $this->params['list_title1'] : $this->translate("Everyone")), $params_link1)?>
      |
    <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'hecore', 'controller' => 'list', 'action' => 'index', 'QUERY' => array_merge($this->url_params, array('list_type' => 'mutual'))), (!empty($this->params['list_title2']) ? $this->params['list_title2'] : $this->translate("Mutual")), $params_link2)?>

  </div>

  <?php if (count($this->items)):?>

  <ul class="items">

  <?php foreach ($this->items as $item):?>

      <li>
        <div class="item_photo">
          <?php echo $this->itemPhoto($item, 'thumb.icon')?>
        </div>
        <div class="item_body">
          <div class="item_title">
            <?php echo $item->__toString()?>
          </div>
        </div>

      </li>

  <?php endforeach;?>

  </ul>

  <?php echo $this->paginationControl($this->items, null, array('pagination/search.tpl', 'mobile'), array('query' => $this->url_params)); ?>

  <?php else:?>

      <div class="result_message"><?php echo $this->translate('MOBILE_NO_ITEMS')?></div>

  <?php endif;?>

<?php endif;?>
