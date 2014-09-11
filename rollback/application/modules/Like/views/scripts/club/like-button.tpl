<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: like-button.tpl 2010-09-07 17:53 idris $
 * @author     Idris
 */
?>

<?php if (!$this->error): ?>
<html dir="<?php echo $this->orientation; ?>">
<head>

  <?php
    $modulesTbl = Engine_Api::_()->getDbTable('modules', 'core');
    $coreItem = $modulesTbl->getModule('core')->toArray();

    if (version_compare($coreItem['version'], '4.1.7') < 0) {
      echo '<script type="text/javascript" src="' . $this->layout()->staticBaseUrl . 'externals/mootools/mootools-1.2.4-core-yc.js"></script>';
      echo '<script type="text/javascript" src="' . $this->layout()->staticBaseUrl . 'application/modules/Like/externals/scripts/remote.js"></script>';
    }
    elseif(version_compare($coreItem['version'], '4.2.1') > 0){
      echo '<script type="text/javascript" src="' . $this->layout()->staticBaseUrl . 'externals/mootools/mootools-core-1.4.5-full-compat-yc.js"></script>';
      echo '<script type="text/javascript" src="' . $this->layout()->staticBaseUrl . 'application/modules/Like/externals/scripts/remote.js"></script>';
    }
    else {
      echo '<script type="text/javascript" src="' . $this->layout()->staticBaseUrl . 'externals/mootools/mootools-1.2.5-core-yc.js"></script>';
      echo '<script type="text/javascript" src="' . $this->layout()->staticBaseUrl . 'application/modules/Like/externals/scripts/remote.js"></script>';
    }
  ?>

<script type="text/javascript">
//<![CDATA[
var langs = {
  l_and: '<?php echo $this->translate("like_and"); ?>',
  l_you: '<?php echo $this->translate("like_You"); ?>',
  l_people: '<?php echo $this->translate("like_other people"); ?>',
  l_like_it: '<?php echo $this->translate("like_like it."); ?>',
  l_Like: '<?php echo $this->translate("like_Like"); ?>',
  l_Unlike: '<?php echo $this->translate("like_Unlike"); ?>',
  l_person: '<?php echo $this->translate("like_other person"); ?>',
  l_noResult: '<?php echo $this->translate("like_No one like it."); ?>'
};
window.addEvent('domready', function(){
  like_api.likes = <?php echo $this->likes_json; ?>;
  like_api.viewer = <?php echo Zend_Json_Encoder::encode($this->viewer->toArray()); ?>;
  like_api.like_url = '<?php echo $this->like_url; ?>';
  like_api.unlike_url = '<?php echo $this->unlike_url; ?>';
  like_api.viewer_url = '<?php echo $this->viewer_url; ?>';
  like_api.urls = <?php echo Zend_Json_Encoder::encode($this->urls); ?>;
  like_api.like_img = '<?php echo $this->like_img; ?>';
  like_api.unlike_img = '<?php echo $this->unlike_img; ?>';
  like_api.like_count = <?php echo (int)$this->like_count; ?>;
  like_api.block = false;
  like_api.login_url = '<?php echo $this->login_url; ?>';
  like_api.subject_guid = '<?php echo $this->subject->getGuid(); ?>';
  like_api.init();
});
//]]>
</script>
</head>
<body style="margin: 0px; color: #555;">
  <?php echo $this->html; ?>
</body>
</html>

<?php endif; ?>