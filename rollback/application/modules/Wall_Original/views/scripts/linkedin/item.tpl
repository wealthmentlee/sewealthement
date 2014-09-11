<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: item.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>
<?php

$action = $this->action;
if (empty($action)){
  return ;
}

?>


<?php

$id = @$action['updateKey'];
$update_type = @$action['updateType'];



$user_url = '';
if (
  !empty($action['updateContent']) &&
  !empty($action['updateContent']['person']) &&
  !empty($action['updateContent']['person']['siteStandardProfileRequest']) &&
  !empty($action['updateContent']['person']['siteStandardProfileRequest']['url'])
){
  $user_url = $action['updateContent']['person']['siteStandardProfileRequest']['url'];
}



$user_photo = $this->wallBaseUrl() . 'application/modules/Wall/externals/images/services/icon_no_photo_no_border_60x60.png';
if (
  !empty($action['updateContent']) &&
  !empty($action['updateContent']['person']) &&
  !empty($action['updateContent']['person']['pictureUrl'])
){
  $user_photo = $action['updateContent']['person']['pictureUrl'];
}

$user_name = '';

if (
  !empty($action['updateContent']) &&
  !empty($action['updateContent']['person']) &&
  !empty($action['updateContent']['person']['firstName'])
){
  $user_name = $action['updateContent']['person']['firstName'] . ' ' . $action['updateContent']['person']['lastName'];
}


$user_link = '<a href="'.$user_url.'" class="feed_item_username" target="_blank">'.$user_name.'</a>';






$body = '';






/* ANSW */

if ($update_type == 'ANSW'){


  $user_url = @$action['updateContent']['question']['answers']['values'][0]['author']['url'];
  $user_name = @$action['updateContent']['question']['answers']['values'][0]['author']['firstName'] . ' ' . @$action['updateContent']['question']['answers']['values'][0]['author']['lastName'];
  $user_link = '<a href="'.$user_url.'" class="feed_item_username" target="_blank">'.$user_name.'</a>';

  if (!empty($action['updateContent']['question']['answers']['values'][0]['author']['pictureUrl'])){
    $user_photo = @$action['updateContent']['question']['answers']['values'][0]['author']['pictureUrl'];
  }

  $second_name = @$action['updateContent']['question']['title'];
  $second_link = @$action['updateContent']['question']['answers']['values'][0]['webUrl'];

  $body = $this->translate('WALL_LINKEDIN_%1$s answered: %2$s', array($user_link, '<a href="'.$second_link.'" class="feed_item_username" target="_blank">'.$second_name.'</a>'));



/* APPS */

} else if ($update_type == 'APPS'){


  $body = $this->translate('WALL_LINKEDIN_%1$s %2$s', array($user_link, @$action['updateContent']['person']['personActivities']['values'][0]['body']));


} else if ($update_type == 'APPM'){


  $body = $this->translate('WALL_LINKEDIN_%1$s %2$s', array($user_link, @$action['updateContent']['person']['personActivities']['values'][0]['body']));



/* CMPY */

} else if ($update_type == 'CMPY'){

  return; // is not supported


/* CONN */

} else if ($update_type == 'CONN'){

  $second_name = @$action['updateContent']['person']['connections']['values'][0]['firstName'] . ' ' . @$action['updateContent']['person']['connections']['values'][0]['lastName'];
  $second_link = @$action['updateContent']['person']['connections']['values'][0]['siteStandardProfileRequest']['url'];

  $body = $this->translate('WALL_LINKEDIN_%1$s is now connected to %2$s.', array($user_link, '<a href="'.$second_link.'" class="feed_item_username" target="_blank">' . $second_name . '</a>'));

} else if ($update_type == 'NCON'){

  $body = $this->translate('WALL_LINKEDIN_%1$s is now a connection.', array($user_link));

} else if ($update_type == 'CCEM'){

  $body = $this->translate('WALL_LINKEDIN_%1$s has joined LinkedIn.', array($user_link));



/* JOBS */


} else if ($update_type == 'JOBS'){

  return; // is not supported


} else if ($update_type == 'JOBP'){

  return; // is not supported



/* JGRP */

} else if ($update_type == 'JGRP'){

  $second_name = @$action['updateContent']['person']['memberGroups']['values'][0]['name'];
  $second_link = @$action['updateContent']['person']['memberGroups']['values'][0]['siteGroupRequest']['url'];

  $body = $this->translate('WALL_LINKEDIN_%1$s joined the group %2$s.', array($user_link, '<a href="'.$second_link.'" class="feed_item_username" target="_blank">'.$second_name.'</a>'));



/* PICT */


} else if ($update_type == 'PICU'){



  $body = $this->translate('WALL_LINKEDIN_%1$s has added a new profile photo.', array($user_link));


  $body .= '
    <div class="attachment">
      <div class="media">
        '.( (!empty($action['updateContent']['person']['pictureUrl'])) ? '<div class="media_photo"><img src="'.@$action['updateContent']['person']['pictureUrl'].'" /></div>' : '' ).'
      </div>
    </div>
  ';



/* PRFX */

} else if ($update_type == 'PRFX'){

  $body = $this->translate('WALL_LINKEDIN_%1$s has an updated own profile.', array($user_link));



/* RECU */

} else if ($update_type == 'PREC'){




  if (empty($action['updateContent']['person']['recommendationsGiven'])){

    $second_name = @$action['updateContent']['person']['recommendationsReceived']['values'][0]['recommender']['firstName'] . ' ' . @$action['updateContent']['person']['recommendationsReceived']['values'][0]['recommender']['lastName'];
    $second_link = @$action['updateContent']['person']['recommendationsReceived']['values'][0]['recommender']['siteStandardProfileRequest']['url'];

    $body = $this->translate('WALL_LINKEDIN_%1$s was recommended %2$s', array($user_link,  '<a href="'.$second_link.'" class="feed_item_username" target="_blank">'.$second_name.'</a>'));

  } else {

    $second_name = @$action['updateContent']['person']['recommendationsGiven']['values'][0]['recommendee']['firstName'] . ' ' . @$action['updateContent']['person']['recommendationsGiven']['values'][0]['recommendee']['lastName'];
    $second_link = @$action['updateContent']['person']['recommendationsGiven']['values'][0]['recommendee']['siteStandardProfileRequest']['url'];

    $body = $this->translate('WALL_LINKEDIN_%1$s recommends %2$s', array($user_link,  '<a href="'.$second_link.'" class="feed_item_username" target="_blank">'.$second_name.'</a>'));


  }




} else if ($update_type == 'SVPR'){

  return; // is not supported



/* PRFU */


} else if ($update_type == 'PROF'){

  $body = $this->translate('WALL_LINKEDIN_%1$s has an updated own profile.', array($user_link));






/* QSTN */

} else if ($update_type == 'QSTN'){

  return; // is not supported




} else if ($update_type == 'SHAR'){


  $share_content = '

    '.( (!empty($action['updateContent']['person']['currentShare']['comment'])) ? @$action['updateContent']['person']['currentShare']['comment'] : '' ).'

    <div class="attachment">
      <div class="media">
        '.( (!empty($action['updateContent']['person']['pictureUrl'])) ? '<div class="media_photo"><a href="'.@$action['updateContent']['person']['currentShare']['content']['submittedUrl'].'" target="_blank"><img src="'.@$action['updateContent']['person']['currentShare']['content']['thumbnailUrl'].'" /></a></div>' : '' ).'
        <div class="media_content">
          '.( ($action['updateContent']['person']['currentShare']['content']['title']) ? '<div class="name">'.@$action['updateContent']['person']['currentShare']['content']['title'].'</div>' : '' ).'
          '.( ($action['updateContent']['person']['currentShare']['content']['description']) ? '<div class="caption">'.@$action['updateContent']['person']['currentShare']['content']['description'].'</div>' : '' ).'
        </div>
      </div>
    </div>
  ';
  $body = $this->translate('WALL_LINKEDIN_%1$s %2$s', array($user_link, $share_content));

} else if ($update_type == 'STAT'){

  $body = $this->translate('WALL_LINKEDIN_%1$s %2$s', array($user_link, @$action['updateContent']['person']['currentStatus']));

} else if ($update_type == 'VIRL'){

  return; 




} else if ($update_type == 'MSFC'){

  $user_url = @$action['updateContent']['companyPersonUpdate']['person']['url'];
  $user_name = @$action['updateContent']['companyPersonUpdate']['person']['firstName'] . ' ' . @$action['updateContent']['companyPersonUpdate']['person']['lastName'];
  $user_link = '<a href="'.$user_url.'" class="feed_item_username" target="_blank">'.$user_name.'</a>';


  if (!empty($action['updateContent']['companyPersonUpdate']['person']['pictureUrl'])){
    $user_photo = @$action['updateContent']['companyPersonUpdate']['person']['pictureUrl'];
  }


  $second_name = @$action['updateContent']['company']['name'];
  $second_link = '';

  $body = $this->translate('WALL_LINKEDIN_%1$s is now following %2$s.', array($user_link, '<a href="'.$second_link.'" class="feed_item_username" target="_blank">'.$second_name.'</a>'));


} else {

  return;

}



?>



<div class="item_photo">
  <a href="<?php echo $user_url?>" target="_blank"><img src="<?php echo $user_photo?>" alt=""/></a>
</div>

<div class="item_body">
  <div class="item_text">
    <?php echo $body;?>
  </div>
  <div class="item_line">

    <ul class="item_options">
      <?php if (!empty($action['isLikable'])):?>
      <li>
            <span class="wall_linkedin_likes">
              <a href="javascript:void(0);" class="wall_linkedin_likes_like <?php if (empty($action['isLiked'])):?>wall_active<?php endif;?>" rev="<?php echo $id;?>"><?php echo $this->translate('WALL_LINKEDIN_LIKE');?></a>
              <a href="javascript:void(0);" class="wall_linkedin_likes_unlike <?php if (!empty($action['isLiked'])):?>wall_active<?php endif;?>" rev="<?php echo $id;?>"><?php echo $this->translate('WALL_LINKEDIN_UNLIKE');?></a>
            </span>
      </li>
      <li>&middot;</li>
      <?php endif;?>
      <?php if (!empty($action['isCommentable'])):?>
      <li>
        <a href="javascript:void(0);" class="wall_linkedin_comment"><?php echo $this->translate('WALL_LINKEDIN_COMMENT');?></a>
      </li>
      <li>&middot;</li>
      <?php endif;?>

      <li>
        <div class="item_date">
          <?php echo $this->timestamp(@$action['timestamp']/1000);?>
        </div>
      </li>

    </ul>

  </div>

  <?php if ((!empty($action['updateComments']) && !empty($action['updateComments']['_total'])) || (!empty($action['numLikes']) && !empty($action['numLikes']))):?>
  <div class="comments wall-comments">
    <ul>
      <?php if (!empty($action['numLikes']) && !empty($action['numLikes'])):?>
      <li class="container-comment_likes">
        <div></div>
        <div class="comments_likes">
          <?php echo $this->translate(array('%s person likes this', '%s people like this', @$action['numLikes']), $this->locale()->toNumber(@$action['numLikes']));?>
        </div>
      </li>
      <?php endif;?>

      <?php if (!empty($action['updateComments']) && !empty($action['updateComments']['_total'])):?>
      <?php if( $action['updateComments']['_total'] > 5): ?>
        <li>
          <div></div>
          <div class="comments_viewall">
            <?php if( $action['updateComments']['_total'] > 2): ?>
            <?php echo $this->translate(array('%s comment', '%s comments', $action['updateComments']['_total']), $this->locale()->toNumber($action['updateComments']['_total']));?>
            <?php endif; ?>
          </div>
        </li>
        <?php endif; ?>

      <?php endif; ?>

      <?php if (!empty($action['updateComments']['values'])):?>


      <?php foreach ($action['updateComments']['values'] as $comment):?>

        <?php


        $poster_name = @$comment['person']['firstName'] . ' ' . @$comment['person']['lastName'];
        $poster_link = @$comment['person']['siteStandardProfileRequest']['url'];
        $poster_photo = @$comment['person']['pictureUrl'];

        ?>
        <li>
          <div class="comments_author_photo">
            <a href="<?php echo $poster_link;?>" target="_blank"><img src="<?php echo $poster_photo?>" alt=""/></a>
          </div>
          <div class="comments_info">
                      <span class="comments_author">
                        <a href="<?php echo $poster_link;?>" target="_blank"><?php echo $poster_name?></a>
                      </span><br />
            <?php echo $comment['comment']; ?>
          </div>
        </li>
        <?php endforeach;?>
      <?php endif;?>
    </ul>
  </div>
  <?php endif;?>


  <div class="wall-linkedin-comment">
    <form action="" onsubmit="return false;">
      <textarea rows="1" cols="1" name="message"></textarea>
      <input type="hidden" name="id" value="<?php echo $id?>" />
      <div class="wall-submit-container">
        <div class="wall-submit-container-button">
          <button type="submit"><?php echo $this->translate('WALL_LINKEDIN_COMMENT_SUBMIT')?></button>
        </div>
      </div>

    </form>
  </div>


</div>

