<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _composeLink.tpl 2010-09-07 17:53 idris $
 * @author     Idris
 */
?>

<?php 
switch($this->link){
	case 'enable': 
		echo '<a style="background-image: url(application/modules/Like/externals/images/enable_like_club.png);" class="buttonlink enable_like_club menu_user_profile" href="'
			.$this->url(array('action' => 'manage', 'object_id' => $this->object_id,'object' => $this->object,'active' => 1), 'like_club').'">'.$this->translate("like_Enable Likes").'</a>';
		break;
	case 'disable':
		echo '<a style="background-image: url(application/modules/Like/externals/images/disable_like_club.png);" class="buttonlink disable_like_club menu_user_profile" href="'
			.$this->url(array('action' => 'manage', 'object_id' => $this->object_id,'object' => $this->object,'active' => 0), 'like_club').'">'.$this->translate("like_Disable Likes").'</a>';
		break;
	case 'like':
		echo '<a style="background-image: url(application/modules/Like/externals/images/like.png);" class="buttonlink like menu_user_profile" href="'
			.$this->url(array('action' => 'become', 'object_id' => $this->object_id, 'object' => $this->object), 'like_default').'">'.$this->translate("like_Like").'</a>';
		break;
	case 'unlike':
		echo '<a style="background-image: url(application/modules/Like/externals/images/unlike.png);" class="buttonlink unlike menu_user_profile" href="'
			.$this->url(array('action' => 'remove', 'object_id' => $this->object_id, 'object' => $this->object), 'like_default').'">'.$this->translate("like_Unlike").'</a>';
		break;
	default: 
		echo '';
		break;
}
?>