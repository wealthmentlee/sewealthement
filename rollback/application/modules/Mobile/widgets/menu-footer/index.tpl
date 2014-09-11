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
<?php echo $this->translate('Copyright &copy;%s', date('Y')) ?>
<?php foreach( $this->navigation as $item ):
  $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
    'reset_params', 'route', 'module', 'controller', 'action', 'type',
    'visible', 'label', 'href'
  )));
  ?>
  &nbsp;-&nbsp; <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), $attribs) ?>
<?php endforeach; ?>

<?php if( 1 !== count($this->languageNameList) ): ?>
    &nbsp;-&nbsp;
    <form method="post" action="<?php echo $this->url(array('module' => 'mobile', 'controller' => 'utility', 'action' => 'locale'), 'default', true) ?>" style="display:inline-block">
      <?php $selectedLanguage = $this->translate()->getLocale() ?>
      <?php echo $this->formSelect('language', $selectedLanguage, null, $this->languageNameList) ?>
      <button type="submit"><?php echo $this->translate('MOBILE_CHANGE_LANGUAGE')?></button>
      <?php echo $this->formHidden('return', $this->url()) ?>
    </form>
<?php endif; ?>