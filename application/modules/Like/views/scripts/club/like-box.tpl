<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: like-box.tpl 2010-09-07 17:53 idris $
 * @author     Idris
 */
?>

<?php if (!$this->error): ?>

function he_like_box(box, html){
	var $container = null; 
	if (document.getElementById(box)){
		var $container = document.getElementById(box);
	}else{
		return false;
	}
	$container.innerHTML = html;
}
he_like_box('he_like_box', <?php echo Zend_Json_Encoder::encode($this->html);  ?>);

<?php endif; ?>