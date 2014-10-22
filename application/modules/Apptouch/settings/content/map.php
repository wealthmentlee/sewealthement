<?php
/**
 * Created by Hire-Experts LLC.
 * Author: Ulan
 * Date: 31.05.12
 * Time: 18:43
 */

return array(
    'content_settings' => array(
        'ref' => '_content_'
    ),
    'page_model_types' => array(
        'browse',
        'manage',
        'view',
        'profile',
        'create',
        'edit',
        'html'
    ),

    'ui_components' => array(

// Session Components
        'dashboard' => array(
            'parent' => 'session'
        ),
        'footerMenu' => array(
            'parent' => 'session'
        ),
// Session Components

// General Components
        'quickLinks' => array(
            'parent' => 'header'
        ),
        'navigation' => array(
            'parent' => 'content'
        ),
        'mediaControls' => array(
            'parent' => 'footer'
        ),
      'adCampaign' => array(
          'parent' => 'content'
      ),
// General Components

// Content Components
        'subjectPhoto' => array(
            'parent' => 'content'
        ),
        'form' => array(
            'parent' => 'content'
        ),
        'html' => array(
            'parent' => 'content'
        ),
        'date' => array(
            'parent' => 'content'
        ),
		'inviter' => array(
            'parent' => 'content'
        ),
        'inviterInvitesList' => array(
            'parent' => 'content'
        ),
        'inviterContactsList' => array(
            'parent' => 'content'
        ),
        'itemSearch' => array(
            'parent' => 'content'
        ),
        'itemList' => array(
            'parent' => 'content'
        ),
        'cartTotal' => array(
            'parent' => 'content'
        ),
        'creditCheckout' => array(
            'parent' => 'content'
        ),
        'badgesUsersList' => array(
            'parent' => 'content'
        ),
        'badgesList' => array(
            'parent' => 'content'
        ),
        'manageBadgesList' => array(
            'parent' => 'content'
        ),
        'badgeProfile' => array(
            'parent' => 'content'
        ),
        'profileBadgesList' => array(
            'parent' => 'content'
        ),
        'timelineCover' => array(
            'parent' => 'content'
        ),
        'heEventCover' => array(
            'parent' => 'content'
        ),
        'timelineCoverAlbums' => array(
            'parent' => 'content'
        ),
        'timelineCoverPhotos' => array(
            'parent' => 'content'
        ),
        'transactionFinish' => array(
            'parent' => 'content'
        ),
        'paginator' => array(
            'parent' => 'content'
        ),
        'page' => array(
            'parent' => 'content'
        ),
        'gallery' => array(
            'parent' => 'content'
        ),
        'feed' => array(
            'parent' => 'content'
        ),
        'checkinMap' => array(
          'parent' => 'content'
        ),
        'comments' => array(
            'parent' => 'content'
        ),
        'rate' => array(
            'parent' => 'content'
        ),
        'like' => array(
            'parent' => 'content'
        ),
        'fieldsValues' => array(
            'parent' => 'content'
        ),
        'tabs' => array(
            'parent' => 'content'
        ),
        'playlist' => array(
            'parent' => 'content'
        ),
        'video' => array(
            'parent' => 'content'
        ),
        'tip' => array(
            'parent' => 'content'
        ),
        'discussion' => array(
            'parent' => 'content'
        ),
        'crumb' => array(
            'parent' => 'header'
        ),
        'map' => array(
            'parent' => 'content'
        ),
        'chatRoom' => array(
          'parent' => 'content'
        )
        // Content Components
    ),

    'modules_settings' => array(

        'default' => array(
            'create_action' => 'create',
            'manage' => array(
                'options' => array(

                )
            )
        ),
// Album Settings
        'album' => array(

            'create_action' => 'upload',

            'manage' => array(
                'options' => array(
                    array(
                        'label' => 'Add More Photos',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'upload'
                            ),
                            'route' => 'album_general',
                        ),
                        'class' => 'buttonlink icon_photos_new'
                    ),
                    array(
                        'label' => 'Manage Photos',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'editphotos'
                            ),
                            'route' => 'album_specific',
                        ),
                        'class' => 'buttonlink icon_photos_manage'
                    ),
                    array(
                        'label' => 'Edit Settings',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'edit'
                            ),
                            'route' => 'album_specific',
                        ),
                        'class' => 'buttonlink icon_photos_settings'
                    ),
                    array(
                        'label' => 'Delete Album',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'delete'
                            ),
                            'route' => 'album_specific',
                        ),
                        'class' => 'buttonlink smoothbox icon_photos_delete'
                    )
                )
            )
        ),
        'advalbum' => array(

//            'create_action' => 'upload',
            'manage' => array(
                'identity_param' => 'album_id',
                'options' => array(
                    array(
                        'label' => 'Add More Photos',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'upload'
                            ),
                            'route' => 'album_general',
                        ),
                        'class' => 'buttonlink icon_photos_new'
                    ),
                    array(
                        'label' => 'Manage Photos',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'editphotos'
                            ),
                            'route' => 'album_specific',
                        ),
                        'class' => 'buttonlink icon_photos_manage'
                    ),
                    array(
                        'label' => 'Edit Settings',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'edit'
                            ),
                            'route' => 'album_specific',
                        ),
                        'class' => 'buttonlink icon_photos_settings'
                    ),
                    array(
                        'label' => 'Delete Album',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'delete'
                            ),
                            'route' => 'album_specific',
                        ),
                        'class' => 'buttonlink smoothbox icon_photos_delete'
                    )
                )
            )
        ),

        'blog' => array(
            'manage' => array(
                'options' => array(
                    array(
                        'label' => 'Edit Entry',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'edit'
                            ),
                            'route' => 'blog_specific',
                        ),
                        'class' => 'buttonlink icon_blog_edit'
                    ),
                    array(
                        'label' => 'Delete Entry',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'blog',
                                'controller' => 'index',
                                'action' => 'delete',
                            ),
                            'route' => 'default',
                        ),
                        'class' => 'buttonlink smoothbox icon_blog_delete'
                    )
                )
            )
        ),

        'ynblog' => array(
            'manage' => array(
                'identity_param' => 'blog_id',
                'options' => array(
                    array(
                        'label' => 'Delete Entry',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'ynblog',
                                'controller' => 'index',
                                'action' => 'delete',
                            ),
                            'route' => 'default',
                        ),
                        'class' => 'buttonlink smoothbox icon_blog_delete'
                    )
                )
            )
        ),

        'advgroup' => array(
            'manage' => array(
                'identity_param' => 'group_id',
                'options' => array(
                    array(
                        'label' => 'Edit Group',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'advgroup',
                                'controller' => 'group',
                                'action' => 'edit'
                            ),
                            'route' => 'default',
                        ),
                        'class' => 'buttonlink icon_group_edit'
                    ),
                    array(
                        'label' => 'Delete Group',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'group',
                                'controller' => 'group',
                                'action' => 'delete'
                            ),
                            'route' => 'default',
                        ),
                        'class' => 'buttonlink smoothbox icon_group_delete'
                    ),
                    array(
                        'label' => 'Join Group',
                        'href' => array(
                            'url_options' => array(
                                'controller' => 'member',
                                'action' => 'join'
                            ),
                            'route' => 'group_extended',
                        ),
                        'class' => 'buttonlink smoothbox icon_group_join'
                    ),
                    array(
                        'label' => 'Leave Group',
                        'href' => array(
                            'url_options' => array(
                                'controller' => 'member',
                                'action' => 'leave'
                            ),
                            'route' => 'group_extended',
                        ),
                        'class' => 'buttonlink smoothbox icon_group_leave'
                    )
                )
            )
        ),

        'classified' => array(
            'manage' => array(
                'options' => array(
                    array(
                        'label' => 'Edit Listing',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'edit'
                            ),
                            'route' => 'classified_specific',
                        ),
                        'class' => 'buttonlink icon_classified_edit'
                    ),
                    array(
                        'label' => 'Add Photos',
                        'href' => array(
                            'url_options' => array(
                                'controller' => 'photo',
                                'action' => 'upload'
                            ),
                            'route' => 'classified_extended',
                        ),
                        'class' => 'buttonlink icon_classified_photo_new'
                    ),
                    array(
                        'label' => 'Close Listing',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'close',
                                'closed' => 1,
                            ),
                            'route' => 'classified_specific',
                        ),
                        'class' => 'buttonlink icon_classified_close'
                    ),
                    array(
                        'label' => 'Open Listing',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'close',
                                'closed' => 0,
                            ),
                            'route' => 'classified_specific',
                        ),
                        'class' => 'buttonlink icon_classified_open'
                    ),
                    array(
                        'label' => 'Delete Listing',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'classified', 'controller' => 'index', 'action' => 'delete'
                            ),
                            'route' => 'default',
                        ),
                        'class' => 'buttonlink smoothbox icon_classified_delete'
                    ),
                    array(
                        'label' => 'Share',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'activity',
                                'controller' => 'index',
                                'action' => 'share',
                                'type' => 'classified',
                            ),
                            'route' => 'default',
                        ),
                        'class' => 'buttonlink smoothbox icon_classified_share'
                    ),
                )
            )
        ),

        'event' => array(
            'manage' => array(
                'options' => array(
                    array(
                        'label' => 'Edit Event',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'edit'
                            ),
                            'route' => 'event_specific',
                        ),
                        'class' => 'buttonlink icon_event_edit'
                    ),
                    array(
                        'label' => 'Delete Event',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'event',
                                'controller' => 'event',
                                'action' => 'delete'
                            ),
                            'route' => 'default',
                        ),
                        'class' => 'buttonlink smoothbox icon_event_delete'
                    ),
                    array(
                        'label' => 'Join Event',
                        'href' => array(
                            'url_options' => array(
                                'controller' => 'member',
                                'action' => 'join'
                            ),
                            'route' => 'event_extended',
                        ),
                        'class' => 'buttonlink smoothbox icon_event_join'
                    ),
                    array(
                        'label' => 'Leave Event',
                        'href' => array(
                            'url_options' => array(
                                'controller' => 'member',
                                'action' => 'leave'
                            ),
                            'route' => 'event_extended',
                        ),
                        'class' => 'buttonlink smoothbox icon_event_leave'
                    )
                )
            )
        ),

        'group' => array(
            'manage' => array(
                'options' => array(
                    array(
                        'label' => 'Edit Group',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'edit'
                            ),
                            'route' => 'group_specific',
                        ),
                        'class' => 'buttonlink icon_group_edit'
                    ),
                    array(
                        'label' => 'Delete Group',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'group',
                                'controller' => 'group',
                                'action' => 'delete'
                            ),
                            'route' => 'default',
                        ),
                        'class' => 'buttonlink smoothbox icon_group_delete'
                    ),
                    array(
                        'label' => 'Join Group',
                        'href' => array(
                            'url_options' => array(
                                'controller' => 'member',
                                'action' => 'join'
                            ),
                            'route' => 'group_extended',
                        ),
                        'class' => 'buttonlink smoothbox icon_group_join'
                    ),
                    array(
                        'label' => 'Leave Group',
                        'href' => array(
                            'url_options' => array(
                                'controller' => 'member',
                                'action' => 'leave'
                            ),
                            'route' => 'group_extended',
                        ),
                        'class' => 'buttonlink smoothbox icon_group_leave'
                    )
                )
            )
        ),

        'music' => array(
        ),

        'poll' => array(
            'manage' => array(
                'options' => array(
                    array(
                        'label' => 'Edit Privacy',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'edit'
                            ),
                            'route' => 'poll_specific',
                        ),
                        'class' => 'buttonlink icon_poll_edit'
                    ),
                    array(
                        'label' => 'Close Poll',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'close',
                                'closed' => 1,
                            ),
                            'route' => 'poll_specific',
                        ),
                        'class' => 'buttonlink icon_poll_close'
                    ),
                    array(
                        'label' => 'Open Poll',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'close',
                                'closed' => 0,
                            ),
                            'route' => 'poll_specific',
                        ),
                        'class' => 'buttonlink icon_poll_open'
                    ),
                    array(
                        'label' => 'Delete Poll',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'delete',
                            ),
                            'route' => 'poll_specific',
                        ),
                        'class' => 'buttonlink smoothbox icon_poll_delete'
                    )
                )
            )
        ),

        'page' => array(
            'manage' => array(
                'options' => array(
                    array(
                        'label' => 'Edit',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'edit'
                            ),
                            'route' => 'page_team',
                        ),
                        'class' => 'buttonlink icon_page_edit'
                    ),
                    array(
                        'label' => 'Delete',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'delete'
                            ),
                            'route' => 'page_team',
                        ),
                        'class' => 'buttonlink smoothbox icon_page_delete'
                    )
                )
            )
        ),

        'video' => array(
            'manage' => array(
                'options' => array(
                    array(
                        'label' => 'Edit Video',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'video',
                                'controller' => 'index',
                                'action' => 'edit',
                            ),
                            'route' => 'default',
                        ),
                        'class' => 'buttonlink icon_video_edit'
                    ),
                    array(
                        'label' => 'Delete Video',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'video',
                                'controller' => 'index',
                                'action' => 'delete',
                            ),
                            'route' => 'default',
                        ),
                        'class' => 'buttonlink smoothbox icon_video_delete'
                    )
                )
            )
        ),

        'user' => array(
            'manage' => array(
                'options' => array(

// if( !$direction )
                    array(
                        'label' => 'Follow',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'user',
                                'controller' => 'friends',
                                'action' => 'add',
                            ),
                            'route' => 'user_extended',
                        ),
                        'class' => 'buttonlink smoothbox icon_friend_add'
                    ),
                    array(
                        'label' => 'Cancel Follow Request',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'user',
                                'controller' => 'friends',
                                'action' => 'cancel',
                            ),
                            'route' => 'user_extended',
                        ),
                        'class' => 'buttonlink smoothbox icon_friend_cancel'
                    ),
                    array(
                        'label' => 'Unfollow',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'user',
                                'controller' => 'friends',
                                'action' => 'remove',
                            ),
                            'route' => 'user_extended',
                        ),
                        'class' => 'buttonlink smoothbox icon_friend_remove'
                    ),

// else
                    array(
                        'label' => 'Add Friend',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'user',
                                'controller' => 'friends',
                                'action' => 'add',
                            ),
                            'route' => 'user_extended',
                        ),
                        'class' => 'buttonlink smoothbox icon_friend_add'
                    ),
                    array(
                        'label' => 'Cancel Request',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'user',
                                'controller' => 'friends',
                                'action' => 'cancel',
                            ),
                            'route' => 'user_extended',
                        ),
                        'class' => 'buttonlink smoothbox icon_friend_cancel'
                    ),
                    array(
                        'label' => 'Accept Request',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'user',
                                'controller' => 'friends',
                                'action' => 'confirm',
                            ),
                            'route' => 'user_extended',
                        ),
                        'class' => 'buttonlink smoothbox icon_friend_add'
                    ),
                    array(
                        'label' => 'Remove Friend',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'user',
                                'controller' => 'friends',
                                'action' => 'remove',
                            ),
                            'route' => 'user_extended',
                        ),
                        'class' => 'buttonlink smoothbox icon_friend_remove'
                    ),
                )
            )
        ),
// --------------------------=: Page Extensions { :=--------------------------
        'pageblog' => array(
            'manage' => array(
                'identity_param' => 'blog_id',
                'options' => array(
                    array(
                        'label' => 'Edit Entry',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'edit'
                            ),
                            'route' => 'blog_specific',
                        ),
                        'class' => 'buttonlink icon_blog_edit'
                    ),
                    array(
                        'label' => 'Delete Entry',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'blog',
                                'controller' => 'index',
                                'action' => 'delete',
                            ),
                            'route' => 'default',
                        ),
                        'class' => 'buttonlink smoothbox icon_blog_delete'
                    )
                )
            )
        ),

        'pagealbum' => array(

            'create_action' => 'upload',

            'manage' => array(
                'identity_param' => 'album_id',
                'options' => array(
                    array(
                        'label' => 'Manage Photos',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'editphotos'
                            ),
                            'route' => 'album_specific',
                        ),
                        'class' => 'buttonlink icon_photos_manage'
                    ),
                    array(
                        'label' => 'Delete Album',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'delete'
                            ),
                            'route' => 'album_specific',
                        ),
                        'class' => 'buttonlink smoothbox icon_photos_delete'
                    )
                )
            )
        ),

        'pageevent' => array(
            'manage' => array(
                'identity_param' => 'event_id',
                'options' => array(
                    array(
                        'label' => 'Edit Event',
                        'href' => array(
                            'url_options' => array(
                                'action' => 'edit'
                            ),
                            'route' => 'event_specific',
                        ),
                        'class' => 'buttonlink icon_event_edit'
                    ),
                    array(
                        'label' => 'Delete Event',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'event',
                                'controller' => 'event',
                                'action' => 'delete'
                            ),
                            'route' => 'default',
                        ),
                        'class' => 'buttonlink smoothbox icon_event_delete'
                    ),
                    array(
                        'label' => 'Leave Event',
                        'href' => array(
                            'url_options' => array(
                                'controller' => 'member',
                                'action' => 'leave'
                            ),
                            'route' => 'event_extended',
                        ),
                        'class' => 'buttonlink smoothbox icon_event_leave'
                    )
                )
            )
        ),

        'pagevideo' => array(
            'manage' => array(
                'identity_param' => 'video_id',
                'options' => array(
                    array(
                        'label' => 'Edit Video',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'video',
                                'controller' => 'index',
                                'action' => 'edit',
                            ),
                            'route' => 'default',
                        ),
                        'class' => 'buttonlink icon_video_edit'
                    ),
                    array(
                        'label' => 'Delete Video',
                        'href' => array(
                            'url_options' => array(
                                'module' => 'video',
                                'controller' => 'index',
                                'action' => 'delete',
                            ),
                            'route' => 'default',
                        ),
                        'class' => 'buttonlink smoothbox icon_video_delete'
                    )
                )
            )
        ),

// --------------------------=: } Page Extensions :=--------------------------
    )
);