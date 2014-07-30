<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: ActiveThemeStyles.php 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

/**
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
    
class Mobile_View_Helper_ActiveThemeStyles extends Zend_View_Helper_Abstract
{
  public function activeThemeStyles()
  {
		include APPLICATION_PATH . '/application/settings/scaffold.php';
		include APPLICATION_PATH . '/application/libraries/Scaffold/libraries/Bootstrap.php';

		$table = Engine_Api::_()->getDbtable('themes', 'mobile');

		if (null === ($theme = $table->fetchRow($table->select()->where('active=?', 1)->limit(1))))
		{
			$theme = $table->fetchRow($table->select()->where('name=?', 'default')->limit(1));
		}

		// Double check some of the config options
		if( isset($config['log_path']) && !@is_dir($config['log_path']) ) {
			@mkdir($config['log_path'], 0777, true);
		}
		if( isset($config['cache']) && !@is_dir($config['cache']) ) {
			@mkdir($config['cache'], 0777, true);
		}

    $_GET['c'] = 0; // fix log notice

		$file = 'application/modules/Mobile/themes/' . $theme->name . '/theme.css';

		$result = Scaffold::parse(array($file), $config, null, 1);

		return $result['content'];
	}
}
