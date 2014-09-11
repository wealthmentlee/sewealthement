<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: requireuser.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>
<?php
  if( $this->form ):
    echo $this->form->render($this);
  else:
    echo $this->translate('Please sign in to continue.');
  endif;
?>