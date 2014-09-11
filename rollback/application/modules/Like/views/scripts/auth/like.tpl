<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: like.tpl 2010-09-07 17:53 idris $
 * @author     Idris
 */
?>
<html>
<head>

<script type="text/javascript">
//<![CDATA[
function load_viewer(){
  var parent = window.opener;

  var userAgent = window.navigator.userAgent.toLowerCase();
  var browser = {
    version: (userAgent.match( /.+(?:rv|it|ra|ie)[\/: ]([\d.]+)/ ) || [])[1],
    safari: /webkit/.test( userAgent ),
    opera: /opera/.test( userAgent ),
    msie: /msie/.test( userAgent ) && !/opera/.test( userAgent ),
    mozilla: /mozilla/.test( userAgent ) && !/(compatible|webkit)/.test( userAgent )
  };
  
  if (browser.msie){
    window.close();
    parent.location.reload();
    return ;
  }

  parent.like_api.viewer = <?php echo Zend_Json_Encoder::encode($this->viewer->toArray()); ?>;
  parent.like_api.viewer_url = '<?php echo $this->viewer->getHref(); ?>';
  parent.like_api.add_like(parent.like_api.viewer);
  parent.like_api.build();
  parent.like_api.set_unlike();
  window.close();
}
//
//]]>
</script>

</head>
<body onload="load_viewer();"></body>
</html>