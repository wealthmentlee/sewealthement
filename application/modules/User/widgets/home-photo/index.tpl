<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>
<!-- change code -->
<h3>
  <?php echo $this->translate('Hi %1$s!', $this->viewer()->getTitle()); ?>
</h3>
<div>
  <?php echo $this->htmlLink($this->viewer()->getHref(), $this->itemPhoto($this->viewer(), 'thumb.profile')) ?>
  <?php
				$tbl_fieldValues = Engine_Api::_()->fields()->getTable('user', 'values');
				$selectPro = $tbl_fieldValues->select()
									->where("item_id =?",$this->viewer()->getIdentity())->where('field_id =?',24);				
				$proVal = $tbl_fieldValues->fetchRow($selectPro);
				if($proVal->value!=''){
				$optiontable = Engine_Api::_()->fields()->getTable('user', 'options');
				
				$selectLabel = $optiontable->select()->where("option_id =?",$proVal->value);
				$label = $tbl_fieldValues->fetchRow($selectLabel);
				$isprofessional = $label->label;
				
				if($isprofessional== 'Yes'){
		  ?>
					<div class="badge"><img src="./application/modules/Pinfeed/externals/images/badge.png"></div>
		  <?php } }?>
</div>
<style>
#global_page_pinfeed-index-index .layout_user_home_photo {
	position : relative;
}
#global_page_pinfeed-index-index .layout_user_home_photo div .badge > img {
	bottom: 0;
    position: absolute;
    right: 0;
    width: 78px;
}
</style>
<!-- change code -->