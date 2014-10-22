<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Setting.php 2010-07-02 19:27 vadim $
 * @author     Vadim
 */

/**
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */


class Rate_Form_Admin_Settings extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Global Settings')
      ->setDescription('RATE_REVIEW_These settings affect all members in your community.')
      ->setOptions( array('class'=>'he_rates_settings') );

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $modulesTable = Engine_Api::_()->getDbtable('modules', 'core');
    $hecoreApi = Engine_Api::_()->getDbtable('modules', 'hecore');

    $module_path = Engine_Api::_()->getModuleBootstrap('rate')->getModulePath();
    $this->addDisplayGroupPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');

    if ($modulesTable->isModuleEnabled('user')) {
      $this->addElement('Checkbox', 'rate_user_period_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_user_period_enabled',
        'value' => $settings->getSetting('rate.user.period_enabled', true),
      ));

      $this->addElement('Checkbox', 'rate_own_user_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_own_user_enabled',
        'value' => $settings->getSetting('rate.own.user.enabled', false),
      ));

      $this->addElement('Text', 'rate_user_min_votes', array(
        'label' => 'Profile Min Votes',
        'value' => $settings->getSetting('rate.user.min.votes', 1),
      ));
      
      $this->addElement('Text', 'rate_user_max_items', array(
        'label' => 'Profile Max Items',
        'value' => $settings->getSetting('rate.user.max.items', 5),
      ));

      $this->addDisplayGroup(
        array('rate_user_min_votes', 'rate_user_max_items', 'rate_user_period_enabled', 'rate_own_user_enabled'),
        'profile_settings',
        array('class' => 'he_setting_fieldset', 'legend' => 'Profile Settings')
      );
    }

    if ($modulesTable->isModuleEnabled('group')) {
      $this->addElement('Checkbox', 'rate_group_period_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_group_period_enabled',
        'value' => $settings->getSetting('rate.group.period_enabled', true),
      ));

      $this->addElement('Checkbox', 'rate_own_group_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_own_group_enabled',
        'value' => $settings->getSetting('rate.own.group.enabled', false),
      ));

      $this->addElement('Text', 'rate_group_min_votes', array(
        'label' => 'Group Min Votes',
        'value' => $settings->getSetting('rate.group.min.votes', 1),
      ));
      
      $this->addElement('Text', 'rate_group_max_items', array(
        'label' => 'Group Max Items',
        'value' => $settings->getSetting('rate.group.max.items', 5),
      ));

      $this->addDisplayGroup(
        array('rate_group_min_votes', 'rate_group_max_items', 'rate_group_period_enabled', 'rate_own_group_enabled'),
        'group_settings',
        array('class' => 'he_setting_fieldset', 'legend' => 'Group Settings')
      );
    }

    if ($modulesTable->isModuleEnabled('event')) {
      $this->addElement('Checkbox', 'rate_event_period_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_event_period_enabled',
        'value' => $settings->getSetting('rate.event.period_enabled', true),
      ));

      $this->addElement('Checkbox', 'rate_own_event_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_own_event_enabled',
        'value' => $settings->getSetting('rate.own.event.enabled', false),
      ));

      $this->addElement('Text', 'rate_event_min_votes', array(
        'label' => 'Event Min Votes',
        'value' => $settings->getSetting('rate.event.min.votes', 1),
      ));
      
      $this->addElement('Text', 'rate_event_max_items', array(
        'label' => 'Event Max Items',
        'value' => $settings->getSetting('rate.event.max.items', 5),
      ));

      $this->addDisplayGroup(
        array('rate_event_min_votes', 'rate_event_max_items', 'rate_event_period_enabled', 'rate_own_event_enabled'),
        'event_settings',
        array('class' => 'he_setting_fieldset', 'legend' => 'Event Settings')
      );
    }

    if ($hecoreApi->isModuleEnabled('quiz')) {
      $this->addElement('Checkbox', 'rate_quiz_period_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_quiz_period_enabled',
        'value' => $settings->getSetting('rate.quiz.period_enabled', true),
      ));

      $this->addElement('Checkbox', 'rate_own_quiz_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_own_quiz_enabled',
        'value' => $settings->getSetting('rate.own.quiz.enabled', false),
      ));

      $this->addElement('Text', 'rate_quiz_min_votes', array(
        'label' => 'Quiz Min Votes',
        'value' => $settings->getSetting('rate.quiz.min.votes', 1),
      ));
      
      $this->addElement('Text', 'rate_quiz_max_items', array(
        'label' => 'Quiz Max Items',
        'value' => $settings->getSetting('rate.quiz.max.items', 5),
      ));

      $this->addDisplayGroup(
        array('rate_quiz_min_votes', 'rate_quiz_max_items', 'rate_quiz_period_enabled', 'rate_own_quiz_enabled'),
        'quiz_settings',
        array('class' => 'he_setting_fieldset', 'legend' => 'Quiz Settings')
      );
    }

    if ($modulesTable->isModuleEnabled('album')) {
      $this->addElement('Checkbox', 'rate_album_photo_enabled', array(
        'label' => 'Enable Widget',
        'description' => 'rate_album_photo_enabled',
        'value' => $settings->getSetting('rate.album_photo.enabled', true),
      ));

      $this->addElement('Checkbox', 'rate_own_album_photo_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_own_album_photo_enabled',
        'value' => $settings->getSetting('rate.own.album_photo.enabled', false),
      ));

      $this->addElement('Checkbox', 'rate_album_photo_period_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_album_photo_period_enabled',
        'value' => $settings->getSetting('rate.album_photo.period_enabled', true),
      ));
      
      $this->addElement('Text', 'rate_album_photo_min_votes', array(
        'label' => 'Album Min Votes',
        'value' => $settings->getSetting('rate.album_photo.min.votes', 1),
      ));
      
      $this->addElement('Text', 'rate_album_photo_max_items', array(
        'label' => 'Album Max Items',
        'value' => $settings->getSetting('rate.album_photo.max.items', 5),
      ));

      $this->addDisplayGroup(
        array('rate_album_photo_min_votes', 'rate_album_photo_max_items', 'rate_album_photo_enabled', 'rate_album_photo_period_enabled', 'rate_own_album_photo_enabled'),
        'album_settings',
        array('class'=>'he_setting_fieldset', 'legend'=>'Album Settings')
      );
    }

    if ($modulesTable->isModuleEnabled('blog')) {
      $this->addElement('Checkbox', 'rate_blog_enabled', array(
        'label' => 'Enable Widget',
        'description' => 'rate_blog_enabled',
        'value' => $settings->getSetting('rate.blog.enabled', true),
      ));

      $this->addElement('Checkbox', 'rate_own_blog_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_own_blog_enabled',
        'value' => $settings->getSetting('rate.own.blog.enabled', false),
      ));

      $this->addElement('Checkbox', 'rate_blog_period_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_blog_period_enabled',
        'value' => $settings->getSetting('rate.blog.period_enabled', true),
      ));
      
      $this->addElement('Text', 'rate_blog_min_votes', array(
        'label' => 'Blog Min Votes',
        'value' => $settings->getSetting('rate.blog.min.votes', 1),
      ));
      
      $this->addElement('Text', 'rate_blog_max_items', array(
        'label' => 'Blog Max Items',
        'value' => $settings->getSetting('rate.blog.max.items', 5),
      ));

      $this->addDisplayGroup(
        array('rate_blog_min_votes', 'rate_blog_max_items', 'rate_blog_enabled', 'rate_blog_period_enabled', 'rate_own_blog_enabled'),
        'blog_settings',
        array('class' => 'he_setting_fieldset', 'legend' => 'Blog Settings')
      );
    }

    if ($hecoreApi->isModuleEnabled('page')) {
      $this->addElement('Checkbox', 'rate_reviewteamremove', array(
        'label' => 'RATE_REVIEW_TEAM_REMOVE_TITLE',
        'description' => 'RATE_REVIEW_TEAM_REMOVE_DESCRIPTION',
        'value' => $settings->getSetting('rate.reviewteamremove', true),
      ));

      $this->addElement('Checkbox', 'rate_page_period_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_page_period_enabled',
        'value' => $settings->getSetting('rate.page.period_enabled', true),
      ));

      $this->addElement('Checkbox', 'rate_own_page_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_own_page_enabled',
        'value' => $settings->getSetting('rate.own.page.enabled', false),
      ));

	    $this->addElement('Text', 'rate_page_min_votes', array(
        'label' => 'Page Min Votes',
        'value' => $settings->getSetting('rate.page.min.votes', 1),
      ));

      $this->addElement('Text', 'rate_page_max_items', array(
        'label' => 'Page Max Items',
        'value' => $settings->getSetting('rate.page.max.items', 5),
      ));

      $this->addDisplayGroup(
        array('rate_reviewteamremove', 'rate_page_min_votes', 'rate_page_max_items',  'rate_page_period_enabled', 'rate_own_page_enabled'),
        'review_settings',
        array('class' => 'he_setting_fieldset', 'legend' => 'RATE_REVIEW_SETTINGS')
      );
    }

    if ($hecoreApi->isModuleEnabled('offers')) {
      $this->addElement('Checkbox', 'rate_offer_period_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_offer_period_enabled',
        'value' => $settings->getSetting('rate.offer.period_enabled', true),
      ));

	    $this->addElement('Text', 'rate_offer_min_votes', array(
        'label' => 'Offer Min Votes',
        'value' => $settings->getSetting('rate.offer.min.votes', 1),
      ));

      $this->addElement('Text', 'rate_offer_max_items', array(
        'label' => 'Offer Max Items',
        'value' => $settings->getSetting('rate.offer.max.items', 5),
      ));

      $this->addDisplayGroup(
        array('rate_offer_min_votes', 'rate_offer_max_items',  'rate_offer_period_enabled'),
        'offer_review_settings',
        array('class' => 'he_setting_fieldset', 'legend' => 'RATE_OFFERS_REVIEW_SETTINGS')
      );
    }

    if ($modulesTable->isModuleEnabled('article')) {
      $this->addElement('Checkbox', 'rate_article_enabled', array(
        'label' => 'Enable Widget',
        'description' => 'rate_article_enabled',
        'value' => $settings->getSetting('rate.article.enabled', true),
      ));

      $this->addElement('Checkbox', 'rate_own_article_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_own_article_enabled',
        'value' => $settings->getSetting('rate.own.article.enabled', false),
      ));

      $this->addElement('Checkbox', 'rate_article_period_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_article_period_enabled',
        'value' => $settings->getSetting('rate.article.period_enabled', true),
      ));
      
      $this->addElement('Text', 'rate_article_min_votes', array(
        'label' => 'Article Min Votes',
        'value' => $settings->getSetting('rate.article.min.votes', 1),
      ));
      
      $this->addElement('Text', 'rate_article_max_items', array(
        'label' => 'Article Max Items',
        'value' => $settings->getSetting('rate.article.max.items', 5),
      ));

      $this->addDisplayGroup(
        array('rate_article_min_votes', 'rate_article_max_items', 'rate_article_enabled', 'rate_article_period_enabled', 'rate_own_article_enabled'),
        'article_settings',
        array('class' => 'he_setting_fieldset', 'legend' => 'Article Settings')
      );
    }

    if ($hecoreApi->isModuleEnabled('store')) {
      $this->addElement('Checkbox', 'rate_store_product_enabled', array(
        'label' => 'Enable Widget',
        'description' => 'rate_store_product_enabled',
        'value' => $settings->getSetting('rate.store_product.enabled', true),
      ));

      $this->addElement('Checkbox', 'rate_own_store_product_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_own_store_product_enabled',
        'value' => $settings->getSetting('rate.own.store_product.enabled', false),
      ));

      $this->addElement('Checkbox', 'rate_store_product_period_enabled', array(
        'label' => 'Enable',
        'description' => 'rate_store_product_period_enabled',
        'value' => $settings->getSetting('rate.store_product.period_enabled', true),
      ));

      $this->addElement('Text', 'rate_store_product_min_votes', array(
        'label' => 'store_product Min Votes',
        'value' => $settings->getSetting('rate.store_product.min.votes', 1),
      ));

      $this->addElement('Text', 'rate_store_product_max_items', array(
        'label' => 'store_product Max Items',
        'value' => $settings->getSetting('rate.store_product.max.items', 5),
      ));

      $this->addDisplayGroup(
        array('rate_store_product_min_votes', 'rate_store_product_max_items', 'rate_store_product_enabled', 'rate_store_product_period_enabled', 'rate_own_store_product_enabled'),
        'store_product_settings',
        array('class' => 'he_setting_fieldset', 'legend' => 'store_product Settings')
      );
    }

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}