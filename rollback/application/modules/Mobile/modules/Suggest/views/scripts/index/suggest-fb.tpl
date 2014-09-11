<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: suggest-fb.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<div id="fb-root"></div>
<script src="http://connect.facebook.net/en_US/all.js"></script>

<script type="text/javascript">
var appId = <?php echo Zend_Json_Encoder::encode($this->appId); ?>;
en4.core.runonce.add(function() {
  FB.init({
    appId  : appId,
    status : true, // check login status
    cookie : true, // enable cookies to allow the server to access the session
    xfbml  : true  // parse XFBML
  });
  FB.getLoginStatus(function(response) {
    if (response.session) {
      FB.XFBML.parse();
    } else {
      FB.login(function(response){
        if (response.session) {
          FB.XFBML.parse();
        } else {
        }
      });
    }
  })
});
</script>

<fb:serverfbml style="width: 700px;" class="fb_iframe_widget">
<script type="text/fbml">
<fb:fbml>
  <fb:request-form action="http://project.hire-experts.com/" method="POST" invite="true" type="<?php echo $this->translate($this->type); ?>"
    content="<?php echo $this->translate($this->content); ?>">
    <fb:multi-friend-selector showborder="false" actiontext="<?php echo $this->translate($this->actionText); ?>" max="200" exclude_ids="">
  </fb:request-form>
</fb:fbml>
</script>
</fb:serverfbml>