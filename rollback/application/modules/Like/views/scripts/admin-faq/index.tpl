<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Like
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-09-07 17:53 idris $
 * @author     Idris
 */
?>

<?php
  $this->headLink()->appendStylesheet($this->baseUrl() . '/application/css.php?request=application/modules/Hecore/externals/styles/main.css');
?>

<h2><?php echo $this->translate("like_Like Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render();
    ?>
  </div>
<?php endif; ?>

<p class="he_admin_faq_desc"><?php echo $this->translate('like_Some description'); ?></p>
<br/>

<h4 class="he_admin_faq_question"><?php echo $this->translate('like_Plugin Widgets'); ?></h4>
<ul class="he_admin_faq_answer">
  <li>
    <div><?php echo $this->translate('like_first screenshot'); ?></div>
    <a onclick="he_show_image('application/modules/Like/externals/images/faq.png');" href="javascript://">
      <img width="500px" style="border: 3px solid #696969" src="application/modules/Like/externals/images/faq.png"/>
    </a>
  </li>
  <li>
    <div><?php echo $this->translate('like_second screenshot'); ?></div>
    <a onclick="he_show_image('application/modules/Like/externals/images/faq2.png');" href="javascript://">
      <img width="500px" style="border: 3px solid #696969" src="application/modules/Like/externals/images/faq2.png"/>
    </a>
  </li>
</ul>