<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _composeTag.tpl 10110 2013-10-31 02:04:11Z andres $
 * @author     John
 */
?>

<?php 
  if (APPLICATION_ENV == 'production')
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.min.js');
  else
    $this->headScript()
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
      ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js');
?>
<script type="text/javascript">
  en4.core.runonce.add(function() {
    if (Composer.Plugin.Tag)
      return;

    Asset.javascript('<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/scripts/composer_tag.js', {
      onLoad:  function() {
        composeInstance.addPlugin(new Composer.Plugin.Tag({
          suggestOptions : {
            'url' : '<?php echo $this->url(array(), 'default', true) . '/user/friends/suggest' ?>',
            'data' : {
              'format' : 'json'
            }
          },
          'suggestProto' : 'local',
          'suggestParam' : <?php echo Zend_Json::encode($this->friends()) ?>
        }));
      }});
  });
</script>