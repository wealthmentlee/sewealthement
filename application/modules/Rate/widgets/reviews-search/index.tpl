<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl:  2012-12-17 14:38:00 ratbek $
 * @author     Ratbek
 */

$this->headScript()
  ->appendFile($this->baseUrl() . '/application/modules/Rate/externals/scripts/Review.js');

$this->headTranslate(array(
  'Search'
));
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    ReviewManager.init();
  });
</script>
<?php echo $this->filterForm->render($this); ?>

<?php
  /* Include the common user-end field switching javascript */
  echo $this->partial('_jsSwitch.tpl', 'rate', array(
'topLevelId' => (int) @$this->topLevelId,
'topLevelValue' => (int) @$this->topLevelValue
))
?>