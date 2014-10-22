<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: index.tpl 8872 2011-04-13 03:26:58Z john $
 * @author     John
 */
?>

<div class="apptouch_admin_home_dashboard">
  <h3 class="sep">
    <span>
      <?php echo $this->translate("Dashboard") ?>
    </span>
  </h3>

  <?php if (!empty($this->notifications) || $this->paginator->getTotalItemCount() > 0): ?>
  <ul class="admin_home_dashboard_messages">
    <?php // Hook-based notifications ?>
    <?php if (!empty($this->notifications)): ?>
    <?php foreach ($this->notifications as $notification):
      if (is_array($notification)) {
        $class = (!empty($notification['class']) ? $notification['class'] : 'notification-notice priority-info');
        $message = $notification['message'];
      } else {
        $class = 'notification-notice priority-info';
        $message = $notification;
      }
      ?>
      <li class="<?php echo $class ?>">
        <?php echo $message ?>
      </li>
      <?php endforeach; ?>
    <?php endif; ?>

    <?php // Database-based notifications ?>
    <?php if ($this->paginator->getTotalItemCount() > 0): ?>
    <?php foreach ($this->paginator as $notification):
      $class = 'notification-' . ($notification->priority >= 5 ? 'notice' : ($notification->priority >= 4 ? 'warning' : 'error'))
        . ' priority-' . strtolower($notification->priorityName);
      $message = $notification->message;
      if (!empty($notification->plugin)) {
        // Load and execute plugin
        try {
          $class = $notification->plugin;
          Engine_Loader::loadClass($class);
          if (!method_exists($class, '__toString')) continue;
          $instance = new $class($notification);
          $message = $instance->__toString();
          if (method_exists($instance, 'getClass')) {
            $class .= ' ' . $instance->getClass();
          }
        } catch (Exception $e) {
          if (APPLICATION_ENV == 'development') {
            echo $e->getMessage();
          }
          continue;
        }
      }
      ?>
      <li class="<?php echo $class ?>">
        <?php echo $message ?>
      </li>
      <?php endforeach; ?>
    <?php endif; ?>
  </ul>
  <?php endif; ?>

  <ul class="apptouch_admin_home_dashboard_links admin_home_dashboard_links">
    <li>
      <ul>
                <li>
                  <a href="
        <?php echo $this->url(array('module' => 'apptouch', 'controller' => 'layout', 'action' => 'index'), 'admin_default', true) ?>" class="apptouch_links_layout">
                    <?php echo $this->translate("APPTOUCH_Edit Layout") ?>
                  </a>
                </li>
        <li>
          <a
            href="<?php echo $this->url(array('module' => 'apptouch', 'controller' => 'menus', 'action' => 'index'), 'admin_default', true) ?>"
            class="apptouch_links_menus">
            <?php echo $this->translate("APPTOUCH_Edit Menus") ?>
          </a>
        </li>
        <?php if(isset($this->appmanager) && $this->appmanager) { ?>
        <li>
          <a
            href="<?php echo $this->url(array('module' => 'appmanager', 'controller' => 'index', 'action' => 'index'), 'admin_default', true) ?>"
            class="appmanager_app_settings">
            <?php echo $this->translate("APPMANAGER_Application Settings") ?>
          </a>
        </li>
          <?php } ?>
      </ul>
    </li>
    <li>
      <ul>
                <li>
                  <a href="
        <?php echo $this->url(array('module' => 'apptouch', 'controller' => 'themes', 'action' => 'index'), 'admin_default', true) ?>" class="apptouch_links_themes">
                    <?php echo $this->translate("APPTOUCH_Edit Theme") ?>
                  </a>
                </li>
        <li>
          <a
            href="<?php echo $this->url(array('module' => 'apptouch', 'controller' => 'stats', 'action' => 'index'), 'admin_default', true) ?>"
            class="apptouch_links_stats">
            <?php echo $this->translate("View Statistics") ?>
          </a>
        </li>
        <li>
          <a href="http://www.hire-experts.com/social-engine-plugins" class="apptouch_links_more_he_plugins"
             target="_blank">
            <?php echo $this->translate("APPTOUCH_Get More Hire-Expert's Plugins") ?>
          </a>
        </li>
      </ul>
    </li>
  </ul>
</div>