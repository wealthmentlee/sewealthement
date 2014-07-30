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

<div class="page-search-tag-cloud">
  <?php foreach ($this->cloud as $item): ?><a class="he_tag<?php echo $item['class']; ?>" href="javascript:void(0)" onclick="this.blur(); page_search.search_by_tag(<?php echo (int)$item['tag_id']; ?>);"><?php echo $item['text']; ?></a> <?php endforeach; ?>
  <div class="clr"></div>
</div>