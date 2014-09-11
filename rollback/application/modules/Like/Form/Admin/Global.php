<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: Global.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_Form_Admin_Global extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('like_Like Plugin Settings')
      ->setDescription('like_FAN_FORM_ADMIN_GLOBAL_DESCRIPTION')
      ->setOptions(array('class' => 'he_likes_settings'));

    $module_path = Engine_Api::_()->getModuleBootstrap('like')->getModulePath();
    $this->addPrefixPath('Engine_Form_Decorator_', $module_path . '/Form/Decorator/', 'decorator');

    $settings = Engine_Api::_()->getApi('settings', 'core');
    $api = Engine_Api::_()->getDbTable('modules', 'core');


    $this->addElement('Text', 'like_profile_count', array(
      'label' => 'like_Profile Likes',
      'description' => 'like_Count of profile likes',
      'value' => $settings->getSetting('like.profile_count', 9)
    ));

    $this->addElement('Checkbox', 'like_profile_period', array(
      'description' => 'like_Period Description',
      'label' => 'like_Period Enable',
      'value' => $settings->getSetting('like.profile_period', 1)
    ));

    $this->addDisplayGroup(
      array(
        'like_profile_count',
        'like_profile_period'
      ),
      'like_profile_settings',
      array('class' => 'he_setting_fieldset', 'legend' => 'Like Profile Settings')
    );

    $this->addElement('Text', 'like_likes_count', array(
      'label' => 'like_Member Likes',
      'description' => 'like_Count of likes',
      'value' => $settings->getSetting('like.likes_count', 9)
    ));

    $this->addElement('Checkbox', 'like_likes_period', array(
      'description' => 'like_Period Description',
      'label' => 'like_Period Enable',
      'value' => $settings->getSetting('like.likes_period', 1)
    ));

    $this->addDisplayGroup(
      array(
        'like_likes_count',
        'like_likes_period'
      ),
      'like_likes_settings',
      array('class' => 'he_setting_fieldset', 'legend' => 'Likes Settings')
    );

    $this->addElement('Text', 'like_matches_count', array(
      'label' => 'like_Like Matches',
      'description' => 'like_Count of matches',
      'value' => $settings->getSetting('like.matches_count', 9)
    ));

    $this->addDisplayGroup(
      array(
        'like_matches_count'
      ),
      'like_matches_settings',
      array('class' => 'he_setting_fieldset', 'legend' => 'like_Like Matches')
    );

    if($api->isModuleEnabled('event') || ($api->isModuleEnabled('page') && $api->isModuleEnabled('pageevent') && $settings->getSetting('page.browse.pageevent',0))){
    $this->addElement('Text', 'like_event_count', array(
      'label' => 'like_Most Liked Events',
      'description' => 'like_Count of most liked events',
      'value' => $settings->getSetting('like.event_count', 9)
    ));

    $this->addElement('Checkbox', 'like_event_period', array(
      'description' => 'like_Period Description',
      'label' => 'like_Period Enable',
      'value' => $settings->getSetting('like.event_period', 1)
    ));

    $this->addDisplayGroup(
      array(
        'like_event_count',
        'like_event_period'
      ),
      'like_event_settings',
      array('class' => 'he_setting_fieldset', 'legend' => 'Most Liked Events Settings')
    );
    }

    if($api->isModuleEnabled('user')){
    $this->addElement('Text', 'like_user_count', array(
      'label' => 'like_Most Liked Members',
      'description' => 'like_Count of most liked users',
      'value' => $settings->getSetting('like.user_count', 9)
    ));

    $this->addElement('Checkbox', 'like_user_period', array(
      'description' => 'like_Period Description',
      'label' => 'like_Period Enable',
      'value' => $settings->getSetting('like.user_period', 1)
    ));

    $this->addDisplayGroup(
      array(
        'like_user_count',
        'like_user_period'
      ),
      'like_user_settings',
      array('class' => 'he_setting_fieldset', 'legend' => 'Most Liked Users Settings')
    );
    }

    if($api->isModuleEnabled('page')){
    $this->addElement('Text', 'like_page_count', array(
      'label' => 'like_Most Liked Pages',
      'description' => 'like_Count of most liked pages',
      'value' => $settings->getSetting('like.page_count', 9)
    ));

    $this->addElement('Checkbox', 'like_page_period', array(
      'description' => 'like_Period Description',
      'label' => 'like_Period Enable',
      'value' => $settings->getSetting('like.page_period', 1)
    ));

    $this->addDisplayGroup(
      array(
        'like_page_count',
        'like_page_period'
      ),
      'like_page_settings',
      array('class' => 'he_setting_fieldset', 'legend' => 'Most Liked Pages Settings')
    );
    }

    if($api->isModuleEnabled('group')){
    $this->addElement('Text', 'like_group_count', array(
      'label' => 'like_Most Liked Groups',
      'description' => 'like_Count of most liked groups',
      'value' => $settings->getSetting('like.group_count', 9)
    ));

    $this->addElement('Checkbox', 'like_group_period', array(
      'description' => 'like_Period Description',
      'label' => 'like_Period Enable',
      'value' => $settings->getSetting('like.group_period', 1)
    ));

    $this->addDisplayGroup(
      array(
        'like_group_count',
        'like_group_period'
      ),
      'like_group_settings',
      array('class' => 'he_setting_fieldset', 'legend' => 'Most Liked Groups Settings')
    );
    }

    if($api->isModuleEnabled('store')){
    $this->addElement('Text', 'like_store_count', array(
      'label' => 'like_Most Liked Stores',
      'description' => 'like_Count of most liked stores',
      'value' => $settings->getSetting('like.store_count', 9)
    ));

    $this->addElement('Checkbox', 'like_store_period', array(
      'description' => 'like_Period Description',
      'label' => 'like_Period Enable',
      'value' => $settings->getSetting('like.store_period', 1)
    ));

    $this->addDisplayGroup(
      array(
        'like_store_count',
        'like_store_period'
      ),
      'like_store_settings',
      array('class' => 'he_setting_fieldset', 'legend' => 'Most Liked Stores Settings')
    );


    $this->addElement('Text', 'like_store_product_count', array(
      'label' => 'like_Most Liked Products',
      'description' => 'like_Count of most liked products',
      'value' => $settings->getSetting('like.store_product_count', 9)
    ));

    $this->addElement('Checkbox', 'like_store_product_period', array(
      'description' => 'like_Period Description',
      'label' => 'like_Period Enable',
      'value' => $settings->getSetting('like.store_product_period', 1)
    ));

    $this->addDisplayGroup(
      array(
        'like_store_product_count',
        'like_store_product_period'
      ),
      'like_store_product_settings',
      array('class' => 'he_setting_fieldset', 'legend' => 'Most Liked Products Settings')
    );
    }

    if($api->isModuleEnabled('blog') || ($api->isModuleEnabled('page') && $api->isModuleEnabled('pageblog') && $settings->getSetting('page.browse.pageblog',0))){
    $this->addElement('Text', 'like_blog_count', array(
      'label' => 'like_Most Liked Blogs',
      'description' => 'like_Count of most liked blogs',
      'value' => $settings->getSetting('like.blog_count', 9)
    ));

    $this->addElement('Checkbox', 'like_blog_period', array(
      'description' => 'like_Period Description',
      'label' => 'like_Period Enable',
      'value' => $settings->getSetting('like.blog_period', 1)
    ));

    $this->addDisplayGroup(
      array(
        'like_blog_count',
        'like_blog_period'
      ),
      'like_blog_settings',
      array('class' => 'he_setting_fieldset', 'legend' => 'Most Liked Blogs Settings')
    );
    }

    if($api->isModuleEnabled('video') || ($api->isModuleEnabled('page') && $api->isModuleEnabled('pagevideo') && $settings->getSetting('page.browse.pagevideo',0))){
    $this->addElement('Text', 'like_video_count', array(
      'label' => 'like_Most Liked Videos',
      'description' => 'like_Count of most liked videos',
      'value' => $settings->getSetting('like.video_count', 9)
    ));

    $this->addElement('Checkbox', 'like_video_period', array(
      'description' => 'like_Period Description',
      'label' => 'like_Period Enable',
      'value' => $settings->getSetting('like.video_period', 1)
    ));

    $this->addDisplayGroup(
      array(
        'like_video_count',
        'like_video_period'
      ),
      'like_video_settings',
      array('class' => 'he_setting_fieldset', 'legend' => 'Most Liked Videos Settings')
    );
    }

    if($api->isModuleEnabled('music') || ($api->isModuleEnabled('page') && $api->isModuleEnabled('pagemusic') && $settings->getSetting('page.browse.pagemusic',0))){
    $this->addElement('Text', 'like_music_count', array(
      'label' => 'like_Most Liked Musics',
      'description' => 'like_Count of most liked musics',
      'value' => $settings->getSetting('like.music_count', 9)
    ));

    $this->addElement('Checkbox', 'like_music_period', array(
      'description' => 'like_Period Description',
      'label' => 'like_Period Enable',
      'value' => $settings->getSetting('like.music_period', 1)
    ));

    $this->addDisplayGroup(
      array(
        'like_music_count',
        'like_music_period'
      ),
      'like_music_settings',
      array('class' => 'he_setting_fieldset', 'legend' => 'Most Liked Musics Settings')
    );
    }

    if($api->isModuleEnabled('album') || ($api->isModuleEnabled('page') && $api->isModuleEnabled('pagealbum') && $settings->getSetting('page.browse.pagealbum',0))){
    $this->addElement('Text', 'like_photo_count', array(
      'label' => 'like_Most Liked Photos',
      'description' => 'like_Count of most liked photos',
      'value' => $settings->getSetting('like.photo_count', 9)
    ));

    $this->addElement('Checkbox', 'like_photo_period', array(
      'description' => 'like_Period Description',
      'label' => 'like_Period Enable',
      'value' => $settings->getSetting('like.photo_period', 1)
    ));

    $this->addDisplayGroup(
      array(
        'like_photo_count',
        'like_photo_period'
      ),
      'like_photo_settings',
      array('class' => 'he_setting_fieldset', 'legend' => 'Most Liked Photos Settings')
    );
    }

    if($api->isModuleEnabled('job')){
    $this->addElement('Text', 'like_job_count', array(
      'label' => 'like_Most Liked Jobs',
      'description' => 'like_Count of most liked jobs',
      'value' => $settings->getSetting('like.job_count', 9)
    ));

    $this->addElement('Checkbox', 'like_job_period', array(
      'description' => 'like_Period Description',
      'label' => 'like_Period Enable',
      'value' => $settings->getSetting('like.job_period', 1)
    ));

    $this->addDisplayGroup(
      array(
        'like_job_count',
        'like_job_period'
      ),
      'like_job_settings',
      array('class' => 'he_setting_fieldset', 'legend' => 'Most Liked Jobs Settings')
    );
    }
    //
    if($api->isModuleEnabled('article')){
    $this->addElement('Text', 'like_article_count', array(
      'label' => 'like_Most Liked Articles',
      'description' => 'like_Count of most liked articles',
      'value' => $settings->getSetting('like.article_count', 9)
    ));

    $this->addElement('Checkbox', 'like_article_period', array(
      'description' => 'like_Period Description',
      'label' => 'like_Period Enable',
      'value' => $settings->getSetting('like.article_period', 1)
    ));

    $this->addDisplayGroup(
      array(
        'like_article_count',
        'like_article_period'
      ),
      'like_article_settings',
      array('class' => 'he_setting_fieldset', 'legend' => 'Most Liked Articles Settings')
    );
    }
    //
    $this->addElement('File', 'logo', array(
      'label' => 'like_Like Logo',
      'description' => 'like_Pictures for Like Button and Like Box',
    ));

    $this->logo->addValidator('Extension', false, 'jpg,png,gif');
    $this->logo->addDecorator('LikeLogo', array());

    // Add submit button
    $this->addElement('Button', 'submit', array(
      'label' => 'Save Changes',
      'type' => 'submit',
      'ignore' => true
    ));
  }
}