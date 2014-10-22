<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: Album.php 9747 2012-07-26 02:08:08Z john $
 * @author     Sami
 */

/**
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 */
class Apptouch_Form_Album extends Engine_Form
{
    protected $_addPhoto = FALSE;
    protected $_albumId = 0;
    public function getAddPhoto()
    {
        return $this -> _addPhoto;
    }
    public function setAddPhoto($addPhoto)
    {
        $this -> _addPhoto = $addPhoto;
    }
    public function getAlbumId()
    {
        return $this -> _albumId;
    }
    public function setAlbumId($albumId)
    {
        $this -> _albumId = $albumId;
    }
    public function init()
    {
        $user = Engine_Api::_() -> user() -> getViewer();
        $user_level = $user -> level_id;
        // Init form
        $this -> setTitle('Add New Photos') -> setDescription('Choose photos on your computer to add to this album.') -> setAttrib('id', 'form-upload') -> setAttrib('name', 'albums_create') -> setAttrib('enctype', 'multipart/form-data') -> setAction(Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array()));

        // Init album
        $this -> addElement('Select', 'album', array(
            'label' => 'Choose Album',
            'multiOptions' => array('0' => 'Create A New Album'),
            'onchange' => "updateTextFields()",
        ));
        $my_albums = Engine_Api::_() -> advalbum() -> getUserAlbums(Engine_Api::_() -> user() -> getViewer());
        $album_options = Array();
        foreach ($my_albums as $my_album)
        {
            $album_options[$my_album -> album_id] = htmlspecialchars_decode($my_album -> getTitle());
        }
        if($this -> _addPhoto)
        {
            $album = Engine_Api::_() -> getItem('advalbum_album', $this -> _albumId);
            $album_options[$album -> album_id] = htmlspecialchars_decode($album -> getTitle());
        }
        $this -> album -> addMultiOptions($album_options);

        // Init name
        $this -> addElement('Text', 'title', array(
            'label' => 'Album Title',
            'maxlength' => '40',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                new Engine_Filter_StringLength( array('max' => '64')),
            )
        ));

        // prepare categories
        $categories = Engine_Api::_() -> advalbum() -> getCategories();
        if (count($categories) != 0)
        {
            $categories_prepared[0] = "";
            foreach ($categories as $category)
            {
                $categories_prepared[$category -> category_id] = Zend_View_Helper_Translate::translate($category -> category_name);
            }

            // category field
            $this -> addElement('Select', 'category_id', array(
                'label' => 'Category',
                'multiOptions' => $categories_prepared
            ));
        }

        // Init descriptions
        $this -> addElement('Textarea', 'description', array(
            'label' => 'Album Description',
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
                //new Engine_Filter_HtmlSpecialChars(),
                new Engine_Filter_EnableLinks(),
            ),
        ));

        //ADD AUTH STUFF HERE

        // Init search
        $this -> addElement('Checkbox', 'search', array(
            'label' => Zend_Registry::get('Zend_Translate') -> _("Show this album in search results"),
            'value' => 1,
            'disableTranslator' => true
        ));

        // View
        $availableLabels = array(
            'everyone' => 'Everyone',
            'owner_network' => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member' => 'Friends Only',
            'owner' => 'Just Me'
        );

        $options = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('advalbum_album', $user, 'auth_view');
        $options = array_intersect_key($availableLabels, array_flip($options));

        // View
        $this -> addElement('Select', 'auth_view', array(
            'label' => 'Privacy',
            'description' => 'Who may see this album?',
            'multiOptions' => $options,
            'value' => 'everyone'
        ));

        $options = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('advalbum_album', $user, 'auth_comment');
        $options = array_intersect_key($availableLabels, array_flip($options));

        // Comment
        $this -> addElement('Select', 'auth_comment', array(
            'label' => 'Comment Privacy',
            'description' => 'Who may post comments on this album?',
            'multiOptions' => $options,
            'value' => 'everyone'
        ));

        $options = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('advalbum_album', $user, 'auth_add_photo');
        $options = array_intersect_key($availableLabels, array_flip($options));

        // Add photo privacy
        $this -> addElement('Select', 'auth_add_photo', array(
            'label' => 'Add Photo Privacy',
            'description' => 'Who may add photos on this album?',
            'multiOptions' => $options,
            'value' => 'everyone'
        ));

        $options = (array)Engine_Api::_() -> authorization() -> getAdapter('levels') -> getAllowed('advalbum_album', $user, 'auth_tag');
        $options = array_intersect_key($availableLabels, array_flip($options));

        // Tag
        $this -> addElement('Select', 'auth_tag', array(
            'label' => 'Tagging',
            'description' => 'Who may tag photos in this album?',
            'multiOptions' => $options,
            'value' => 'everyone'
        ));


            $this -> addElement('File', 'photos', array(
                'label' => 'Photos',
                'description' => 'Select one or more photos from your mobile.',
                'multiple' => 'multiple',
                'isArray' => true
            ));
            $this -> addElement('Cancel', 'add_more', array(
                'label' => 'Add more',
                'link' => true,
                'onclick' => 'addMoreFile()',
            ));

        // Init submit
        $this -> addElement('Button', 'submit', array(
            'label' => 'Save Photos',
            'type' => 'submit',
            'onclick' => 'disable()',
        ));
    }

    public function clearAlbum()
    {
        $this -> getElement('advalbum_album') -> setValue(0);
    }

    public function getMaxOrder($album_id)
    {
        $table = Engine_Api::_() -> getItemTable('advalbum_photo');
        $name = $table -> info('name');
        $select = $table -> select() -> from($name, array('max_order' => "MAX($name.order)")) -> where('album_id = ?', $album_id);

        $album = $table -> fetchRow($select);
        return $album -> max_order;
    }

    public function saveValues($arr_photo_id = array())
    {
        $set_cover = False;
        $values = $this -> getValues();

        $params = Array();
        if ((empty($values['owner_type'])) || (empty($values['owner_id'])))
        {
            $params['owner_id'] = Engine_Api::_() -> user() -> getViewer() -> user_id;
            $params['owner_type'] = 'user';
        }
        else
        {
            $params['owner_id'] = $values['owner_id'];
            $params['owner_type'] = $values['owner_type'];
            throw new Zend_Exception("Non-user album owners not yet implemented");
        }
        if (($values['album'] == 0))
        {
            $params['title'] = $values['title'];
            if (empty($params['title']))
            {
                $params['title'] = "Untitled Album";
            }
            $params['category_id'] = $values['category_id'];
            $params['description'] = $values['description'];
            $params['search'] = $values['search'];
            $album = Engine_Api::_() -> getDbtable('albums', 'advalbum') -> createRow();
            $set_cover = True;
            $album -> setFromArray($params);
            $album -> save();

            // CREATE AUTH STUFF HERE
            $auth = Engine_Api::_() -> authorization() -> context;
            $roles = array(
                'owner',
                'owner_member',
                'owner_member_member',
                'owner_network',
                'everyone'
            );
            if ($values['auth_view'])
                $auth_view = $values['auth_view'];
            else
                $auth_view = "everyone";
            $viewMax = array_search($auth_view, $roles);
            foreach ($roles as $i => $role)
            {
                $auth -> setAllowed($album, $role, 'view', ($i <= $viewMax));
            }

            if ($values['auth_comment'])
                $auth_comment = $values['auth_comment'];
            else
                $auth_comment = "everyone";
            $commentMax = array_search($values['auth_comment'], $roles);
            foreach ($roles as $i => $role)
            {
                $auth -> setAllowed($album, $role, 'comment', ($i <= $commentMax));
            }

            if ($values['auth_add_photo'])
                $auth_add_photo = $values['auth_add_photo'];
            else
                $auth_add_photo = "everyone";
            $addphotoMax = array_search($values['auth_add_photo'], $roles);
            foreach ($roles as $i => $role)
            {
                $auth -> setAllowed($album, $role, 'addphoto', ($i <= $addphotoMax));
            }

            if ($values['auth_tag'])
                $auth_tag = $values['auth_tag'];
            else
                $auth_tag = "everyone";
            $tagMax = array_search($values['auth_tag'], $roles);
            foreach ($roles as $i => $role)
            {
                $auth -> setAllowed($album, $role, 'tag', ($i <= $tagMax));
            }
        }
        else
        {
            if (!isset($album))
            {
                $album = Engine_Api::_() -> getItem('advalbum_album', $values['album']);
            }
        }
        if(isset($values['html5uploadfileids']))
        {
            $values['file'] = explode(' ', trim($values['html5uploadfileids']));
        }
        if ($arr_photo_id)
        {
            $values['file'] = $arr_photo_id;
        }
        // Add action and attachments
        $api = Engine_Api::_() -> getDbtable('actions', 'activity');
        $action = $api -> addActivity(Engine_Api::_() -> user() -> getViewer(), $album, 'advalbum_photo_new', null, array('count' => count($values['file'])));

        // Do other stuff
        $count = 0;
        if ($values['album'] != 0)
        {
            $order_number = $this -> getMaxOrder($values['album']) + 1;
        }
        else
            $order_number = 0;
        //check mobile upload
        foreach ($values['file'] as $photo_id)
        {
            if(!$photo_id)
                continue;
            $photo = Engine_Api::_() -> getItem("advalbum_photo", trim($photo_id));
            if (!($photo instanceof Core_Model_Item_Abstract) || !$photo -> getIdentity())
                continue;

            if ($set_cover)
            {
                $album -> photo_id = $photo_id;
                $album -> save();
                $set_cover = false;
            }

            $photo -> album_id = $album -> album_id;
            $photo -> order = $order_number;
            $order_number++;
            $photo -> save();

            if ($action instanceof Activity_Model_Action && $count < 8)
            {
                $api -> attachActivity($action, $photo, Activity_Model_Action::ATTACH_MULTI);
            }
            $count++;
        }

        return $album;
    }


}
