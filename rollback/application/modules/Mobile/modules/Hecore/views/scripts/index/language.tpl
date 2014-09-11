<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: language.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>
<?php
  $langVars = array(
    'Confirm' => $this->translate('MOBILE_Confirm'),
    'Cancel' => $this->translate('Cancel'),
    'or' => $this->translate('or'),
    'close' => $this->translate('close')
  );
?>

en4.core.runonce.add(function(){
  he_add_lang_vars(<?php echo $this->jsonInline($langVars); ?>);
});