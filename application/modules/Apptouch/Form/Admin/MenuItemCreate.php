<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Admin
 * Date: 20.05.11
 * Time: 18:03
 * To change this template use File | Settings | File Templates.
 */

class Apptouch_Form_Admin_MenuItemCreate extends Engine_Form
{
  public function init()
  {
    $this
      ->setTitle('Create Menu Item')
      ->setAttrib('class', 'global_form_popup');

    $this->addElement('Text', 'label', array(
      'label' => 'Label',
      'required' => true,
      'allowEmpty' => false,
    ));

    $this->addElement('Text', 'uri', array(
      'label' => 'URL',
      'required' => true,
      'allowEmpty' => false,
      'style' => 'width: 300px',
    ));

    $this->addElement('Select', 'icon', array(
      'label' => 'Icon',
      'description' => 'Choose Icon',
      'multiOptions' => array(
        0 => '',
        'arrow-left' => 'Arrow-left',
        'arrow-right' => 'Arrow-right',
        'arrow-up' => 'Arrow-up',
        'arrow-down' => 'Arrow-down',
        'bar-chart' => 'Bar Chart',
        'facetime-video' => 'Facetime Video',
        'remove' => 'Remove',
        'plus' => 'Plus',
        'minus' => 'Minus',
        'check' => 'Check',
        'cog' => 'Gear',
        'refresh' => 'Refresh',
        'forward' => 'Forward',
//        'backward' => 'Backward',
        'star' => 'Star',
        'info-sign' => 'Info',
        'external-link' => 'External Link',
        'home' => 'Home',
        'search' => 'Search',
        'question-sign' => 'Question',
        'twitter' => 'Twitter',
        'facebook' => 'Facebook',
//        'trash' => 'Trash',
        'bell' => 'Bell',
        'heart' => 'Heart',
        'wrench' => 'Wrench',
        'play' => 'Play',
        'pause' => 'Pause',
//        'stop' => 'Stop',
        'shopping-cart' => 'Shopping Cart',
        'music' => 'Music',
        'rss' => 'Rss',
        'phone' => 'Phone',
        'lock' => 'Lock',
        'flag' => 'Flag',
        'calendar' => 'Calendar',
        'edit' => 'Edit',
//        'glass' => 'Glass',
        'envelope' => 'Envelope',
        'user' => 'User',
        'film' => 'Film',
//        'ok' => 'Ok',
//        'zoom-in' => 'Zoom In',
//        'zoom-out' => 'Zoom Out',
        'off' => 'Off',
//        'signal' => 'Signal',
        'file' => 'File',
        'time' => 'Time',
//        'road' => 'Road',
        'download' => 'Download',
        'upload' => 'Upload',
        'inbox' => 'Inbox',
//        'repeat' => 'Repeat',
        'headphones' => 'Headphones',
        'volume-off' => 'Volume Off',
        'volume-down' => 'Volume Down',
        'volume-up' => 'Volume Up',
//        'qrcode' => 'QR Code',
//        'barcode' => 'Barcode',
        'tag' => 'Tag',
        'tags' => 'Tags',
        'book' => 'Book',
//        'bookmark' => 'Bookmark',
//        'print' => 'Print',
        'camera' => 'Camera',
//        'font' => 'Font',
//        'bold' => 'Bold',
//        'italic' => 'Italic',
        'list' => 'List',
        'picture' => 'Picture',
        'pencil' => 'Pencil',
        'map-marker' => 'Map Marker',
//        'adjust' => 'Adjust',
//        'tint' => 'Tint',
        'share' => 'Share',
//        'move' => 'Move',
//        'eject' => 'Eject',
//        'screenshot' => 'Screenshot',
//        'asterisk' => 'Asterisk',
        'gift' => 'Gift',
//        'leaf' => 'Leaf',
        'fire' => 'Fire',
//        'plane' => 'Plane',
        'random' => 'Random',
        'comment' => 'Comment',
//        'magnet' => 'Magnet',
//        'retweet' => 'Retweet',
        'key' => 'Key',
        'cogs' => 'Cogs',
        'comments' => 'Coments',
        'signout' => 'Signout',
        'signin' => 'Signin',
        'pushpin' => 'Pushpin',
//        'trophy' => 'Trophy',
//        'lemon' => 'Lemon',
//        'github' => 'Github',
        'unlock' => 'Unlock',
//        'hdd' => 'HDD',
        'bullhorn' => 'Bullhorn',
        'certificate' => 'Certificate',
        'globe' => 'Globe',
//        'tasks' => 'Tasks',
//        'filter' => 'Filter',
//        'briefcase' => 'Briefcase',
        'fullscreen' => 'Fullscreen',
        'group' => 'Group',
        'link' => 'Link',
//        'cloud' => 'Cloud',
//        'beaker' => 'Beaker',
//        'cut' => 'Cut',
        'copy' => 'Copy',
//        'save' => 'Save',
        'reorder' => 'Reorder',
//        'underline' => 'Underline',
//        'table' => 'Table',
//        'magic' => 'Magic',
//        'truck' => 'Truck',
//        'pinterest' => 'Pinterest',
        'google-plus' => 'Google-Plus',
        'money' => 'Money',
//        'columns' => 'Columns',
//        'sort' => 'Sort',
        'linkedin' => 'Linkedin',
        'undo' => 'Undo',
//        'legal' => 'Legal',
//        'dashboard' => 'Dashboard',
        'bolt' => 'Bolt',
//        'sitemap' => 'Sitemap',
//        'umbrella' => 'Umbrella',
//        'paste' => 'Paste',
        'circle-arrow-right' => 'Circle Arrow Right',
        'circle-arrow-left' => 'Circle Arrow Left',
        'circle-arrow-up' => 'Circle Arrow Up',
        'circle-arrow-down' => 'Circle Arrow Down'
      ),
      'value' => 0
    ));

    $this->addElement('Checkbox', 'target', array(
      'label' => 'Open in a Dialog?',
      'checkedValue' => 'dialog',
      'uncheckedValue' => '',
    ));

    $this->addElement('Checkbox', 'enabled', array(
      'label' => 'Enabled?',
      'checkedValue' => '1',
      'uncheckedValue' => '0',
      'value' => '1',
    ));
    if(Engine_Api::_()->hasModuleBootstrap('appmanager'))
      $this->addElement('Checkbox', 'appenabled', array(
        'label' => 'Is Enabled for Application?',
        'checkedValue' => '1',
        'uncheckedValue' => '0',
        'value' => '1',
      ));

    $this->addElement('Checkbox', 'ajax', array(
      'label' => 'APPTOUCH_Ajax Request?',
      'checkedValue' => 'true',
      'uncheckedValue' => 'false',
      'value' => 'true',
    ));

    // Buttons
    $this->addElement('Button', 'submit', array(
      'label' => 'Create Menu Item',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}
