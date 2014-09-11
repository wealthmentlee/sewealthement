<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: LikeButton.php 2010-09-07 16:05 idris $
 * @author     Idris
 */

/**
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 */

class Like_View_Helper_LikeButton extends Engine_View_Helper_HtmlImage
{
  public function likeButton($item, $attrs = array(), $requireUser = true, $viewer_liked = null)
  {
    if( !($item instanceof Core_Model_Item_Abstract)){
      throw new Zend_View_Exception("Item must be a valid item");
    }
		
		$viewer = Engine_Api::_()->user()->getViewer();
    if ($requireUser && !$viewer->getIdentity()) {
      return "";
    }

    if ($viewer_liked === null) {
      $viewer_liked = Engine_Api::_()->like()->isLike($item);
    }

    if (!$viewer_liked) {
      $label = $this->view->translate('like_Like');
      $class = 'like_button';
      $action = 'like';
      $onClick = (!empty($attrs['onLike'])) ? ' onClick = "' . $attrs['onLike'] . '"' : '';
    } else {
      $label = $this->view->translate('like_Unlike');
      $class = 'unlike_button';
      $action = 'unlike';
      $onClick = (!empty($attrs['onUnlike'])) ? ' onClick = "' . $attrs['onUnlike'] . '"' : '';
    }

    $attributes = "";
    if (!empty($attrs)) {
      unset($attrs['class']);
      unset($attrs['id']);
      unset($attrs['href']);
      unset($attrs['onLike']);
      unset($attrs['onUnlike']);

      foreach ($attrs as $key => $value) {
        $attributes .= " " .$key . "='" . $value . "'";
      }
    }

    $view = Zend_Registry::get('Zend_View');
    $html = Zend_Json_Encoder::encode($this->likeMenu($item));
    
    $wrapper = <<<HTML
<script type="text/javascript">
en4.core.runonce.add(function(){
  var options = {
    object_type: '{$item->getType()}',
    object_id: '{$item->getIdentity()}',
    likeBtn: '_{$item->getGuid()}',
    loader: 'like_loader_{$item->getGuid()}',
    menuHtml: {$html},
    menuId: 'like_menu_{$item->getGuid()}',
    suggestBtn: '#like_menu_{$item->getGuid()} .like_suggest',
    likeUrl: '{$view->url(array("object" => $item->getType(), "object_id" => $item->getIdentity(), "action" => "like"), "like_default")}',
    unlikeUrl: '{$view->url(array("object" => $item->getType(), "object_id" => $item->getIdentity(), "action" => "unlike"), "like_default")}',
    switcher: 'like_switcher_{$item->getGuid()}'
  };
  if (!window.likeBtns) {
    window.likeBtns = {};
  }
  window.likeBtns.{$item->getGuid()} = new LikeButton(options);
});
</script>
HTML;


    $wrapper .= ' '
      . '<div class="like_button_container">'
      . '<div id="like_loader_'.$item->getGuid().'" class="like_button_loader hidden"></div>'
      . '<a ' . $attributes . '  id="_' . $item->getGuid() . '" '. $onClick .' onFocus="this.blur();" class="like_button_link ' . $action . '" href="javascript:void(0)">'
      . '<span class="' . $class . '">' . $label . '</span>'
      . '</a>';

		if ($item->getType() == 'page') {
			$allowed = $item->isTeamMember();
		}else{
			$allowed = $item->isOwner($viewer);
		}

		if ($allowed) {
			$wrapper .= ''
				. '<a class="like_menu_switcher like_button_link" id="like_switcher_'.$item->getGuid().'" href="javascript:void(0)" onFocus="this.blur();"><span class="show_options">&nbsp;</span></a>';
		}

		$wrapper .= ''
			. '<div class="clr"></div>'
      . '</div>';

    return $wrapper;
  }

	public function likeMenu($subject = null)
  {
    $viewer = Engine_Api::_()->user()->getViewer();
    if ($subject === null) {
      $subject = Engine_Api::_()->core()->getSubject();
    }

		if ($subject->getType() == 'page') {
			if (!$subject->isTeamMember()) {
				return false;
			}
		} else {
			if (!$subject->isOwner($viewer)) {
				return false;
			}
		}

    if ( !$viewer->getIdentity() ) {
      return false;
    }

    $menu = $this->getAppropriateMenu($viewer, $subject);
    $rendered = $this->renderMenu($menu);

    $wrapper = "<div id='like_menu_".$subject->getGuid()."' class='like_container_menu_wrapper hidden'><div class='like_profile_menu_arrow'></div>"
      . $rendered
      . "<div class='clr'></div>"
      . "</div>";

    return $wrapper;
  }

  public function renderMenu($menu)
  {
    if (empty($menu)) {
      return "";
    }

    $output = "<div class='like_profile_menu'>";
    foreach ($menu as $item) {
      $url = Zend_Controller_Front::getInstance()->getRouter()->assemble($item['params'], $item['route']);
      $background = $item['icon'];
      $title = $item['title'] ? 'title="' . $this->view->translate($item['title']) . '"' : '';
      $html = "<div class='like_profile_item_container'> " .
        " <a " . $title . " class='like_profile_item_item " . $item['class'] . "' href='" . $url . "'> " .
        " <span> " . $this->view->translate($item['label']) . " </span> " .
        " </a> " .
        "</div>";

      $output .= $html;
    }

    $output .= "<div class='clr'></div></div>";

    return $output;
  }

  public function getAppropriateMenu($viewer, $subject)
  {
  	if ($subject->getType() == 'page') {
  		$func = "isTeamMember";
  	} else {
  		$func = "isOwner";
  	}

    $menu = array();
  	$ownerMode = $subject->$func($viewer);

		if ($ownerMode) {
      if (!Engine_Api::_()->getDbTable('modules', 'hecore')->isModuleEnabled('suggest')) {
        $menu[] = array(
          'label' => 'like_Suggest',
          'title' => 'like_Suggest to friends, may be they will like it.',
          'icon' => 'application/modules/Like/externals/images/suggest.png',
          'route' => 'like_club',
          'class' => 'like_suggest',
          'params' => array(
            'action' => 'suggest',
            'object' => $subject->getType(),
            'object_id' => $subject->getIdentity()
          )
        );
      }

			$menu[] = array(
				'label' => 'like_Send',
				'title' => 'like_Send any internal message and e-mail for those members who liked it.',
				'icon' => 'application/modules/Like/externals/images/send_update.png',
				'class' => "smoothbox like_send_update",
				'route' => 'like_club',
				'params' => array(
					'action' => 'send-update',
					'object_id' => $subject->getIdentity(),
					'object' => $subject->getType()
				)
			);

			$menu[] = array(
				'label' => 'like_Promote',
				'title' => 'like_Promote your content in Internet by Like Box or Like Button widgets.',
				'icon' => 'application/modules/Like/externals/images/add_like_box.png',
				'class' => "smoothbox like_promote",
				'route' => 'like_club',
				'params' => array(
					'action' => 'promote',
					'object_id' => $subject->getIdentity(),
					'object' => $subject->getType()
				)
			);

			if ($subject->getType() == 'user') {
				$menu[] = array(
					'label' => 'like_I liked',
					'title' => 'like_Users and pages that you liked.',
					'icon' => 'application/modules/Like/externals/images/my_like_clubs.png',
					'route' => 'like_default',
					'class' => 'my_like_clubs',
					'params' => array(
						'action' => 'index'
					)
				);
			}
		}

    return $menu;
  }
}