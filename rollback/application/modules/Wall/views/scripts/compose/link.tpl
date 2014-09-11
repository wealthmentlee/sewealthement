<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: link.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php $this->headScript()->appendFile($this->wallBaseUrl() . 'application/modules/Wall/externals/scripts/composer_link.js') ?>

<script type="text/javascript">

  Wall.runonce.add(function (){

    var feed = Wall.feeds.get("<?php echo $this->feed_uid?>");

    feed.compose.addPlugin(new Wall.Composer.Plugin.Link({
      title: '<?php echo $this->string()->escapeJavascript($this->translate('Add Link')) ?>',
      lang : {
        'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
        'Last' : '<?php echo $this->string()->escapeJavascript($this->translate('Last')) ?>',
        'Next' : '<?php echo $this->string()->escapeJavascript($this->translate('Next')) ?>',
        'Attach' : '<?php echo $this->string()->escapeJavascript($this->translate('Attach')) ?>',
        'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
        'Don\'t show an image' : '<?php echo $this->string()->escapeJavascript($this->translate('Don\'t show an image')) ?>',
        'Choose Image:' : '<?php echo $this->string()->escapeJavascript($this->translate('Choose Image:')) ?>',
        '%d of %d' : '<?php echo $this->string()->escapeJavascript($this->translate('%d of %d')) ?>'
      },
      requestOptions : {
        'url' :en4.core.baseUrl + 'wall/link/preview'
      }

    }));

  });

</script>