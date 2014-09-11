<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: content.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */
 
return array(
	array(
    'title' => 'Members Like This',
    'description' => 'Displays members who liked this(Profile, Page, Event...). Please put it on Member Profile, Event Profile, Group Profile pages',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.box',
		'defaultParams' => array(
      'title' => 'like_Like Club',
      'titleCount' => true
    )
  ),
  array(
    'title' => 'Profile Like Status',
    'description' => 'Displays Profile status with like options. Please replace `Profile Status` widget with it on Member/Event/Group Profile pages.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.status'
  ),
  array(
    'title' => 'Donation Like Status',
    'description' => 'Displays Profile status with like options. Please replace `Profile Status` widget with it on Donation Profile page.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.donation-status'
  ),
  array(
    'title' => 'Likes',
    'description' => 'Displays things that current member liked, please put it on Member Profile page.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.profile-likes',
		'defaultParams' => array(
      'title' => 'like_Likes',
      'titleCount' => true
    )
  ),
	array(
    'title' => 'Matches',
    'description' => 'Users who liked the same things that you liked.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.matches',
		'defaultParams' => array(
      'title' => 'like_Matches',
      'titleCount' => true
    )
  ),
	array(
    'title' => 'Profile Fields and Interests',
    'description' => 'Displays Profile Fields information and `Interests`. Please replace `Profile Fields` widget by this one on Member Profile page.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.interests',
		'defaultParams' => array(
      'title' => 'like_Info',
      'titleCount' => true
    )
  ),
	array(
    'title' => 'Most Liked Members Widget',
    'description' => 'Displays most liked members. Please put it on any wished page.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.most-liked-users',
		'defaultParams' => array(
      'title' => 'like_Most Liked Users Widget',
      'titleCount' => false
    )
  ),
	array(
    'title' => 'Most Liked Pages',
    'description' => 'Displays most liked pages. You need to have Pages plugin installed to use this widget. You can find this plugin at Hire-Experts.com',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.most-liked-pages',
		'defaultParams' => array(
      'title' => 'like_Most Liked Pages Widget',
      'titleCount' => false
    )
  ),
	array(
    'title' => 'Most Liked Events Widget',
    'description' => 'Displays most liked events.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.most-liked-events',
		'defaultParams' => array(
      'title' => 'like_Most Liked Events Widget',
      'titleCount' => false
    )
  ),
	array(
    'title' => 'Most Liked Groups',
    'description' => 'Displays most liked groups',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.most-liked-groups',
		'defaultParams' => array(
      'title' => 'like_Most Liked Groups Widget',
      'titleCount' => false
    )
  ),
  array(
    'title' => 'Most Liked Products',
    'description' => 'Displays most liked products.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.most-liked-products',
		'defaultParams' => array(
      'title' => 'like_Most Liked Products',
      'titleCount' => false
    )
  ),
	array(
    'title' => 'Most Liked Stores',
    'description' => 'Displays most liked stores.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.most-liked-stores',
		'defaultParams' => array(
      'title' => 'like_Most Liked Stores',
      'titleCount' => false
  )
  ),
  array(
    'title' => 'Most Liked Videos',
    'description' => 'Displays most liked videos.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.most-liked-videos',
    'defaultParams' => array(
      'title' => 'like_Most Liked Videos',
      'titleCount' => false
    )
  ),
  array(
    'title' => 'Most Liked Musics',
    'description' => 'Displays most liked musics.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.most-liked-musics',
    'defaultParams' => array(
      'title' => 'like_Most Liked Musics',
      'titleCount' => false
    )
  ),
  array(
    'title' => 'Most Liked Blogs',
    'description' => 'Displays most liked blogs.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.most-liked-blogs',
    'defaultParams' => array(
      'title' => 'like_Most Liked Blogs',
      'titleCount' => false
    )
  ),
    array(
    'title' => 'Most Liked Documents',
    'description' => 'Displays most liked documents.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.most-liked-documents',
    'defaultParams' => array(
      'title' => 'like_Most Liked Documents',
      'titleCount' => false
    )
  ),
    array(
    'title' => 'Most Liked Albums',
    'description' => 'Displays most liked albums.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.most-liked-albums',
    'defaultParams' => array(
      'title' => 'like_Most Liked Albums',
      'titleCount' => false
    )
  ),
  array(
    'title' => 'Most Liked Listings',
    'description' => 'Displays most liked listings.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.most-liked-listings',
    'defaultParams' => array(
      'title' => 'like_Most Liked Listings',
      'titleCount' => false
    )
  ),  
  array(
    'title' => 'Most Liked Photos',
    'description' => 'Displays most liked photos.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.most-liked-photos',
    'defaultParams' => array(
      'title' => 'like_Most Liked Photos',
      'titleCount' => false
    )
  ),

  array(
    'title' => 'Most Liked Jobs',
    'description' => 'Displays most liked jobs.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.most-liked-jobs',
    'defaultParams' => array(
      'title' => 'like_Most Liked Jobs',
      'titleCount' => false
    )
  ),

  array(
    'title' => 'Most Liked Articles',
    'description' => 'Displays most liked articles.',
    'category' => 'like_Like',
    'type' => 'widget',
    'name' => 'like.most-liked-articles',
    'defaultParams' => array(
      'title' => 'like_Most Liked Articles',
      'titleCount' => false
    )
  )
);