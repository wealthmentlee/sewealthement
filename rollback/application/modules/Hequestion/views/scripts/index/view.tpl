<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: view.tpl 17.08.12 06:04 michael $
 * @author     Michael
 */
?>


<div class="heQuestionTitle">
  <?php echo $this->htmlLink($this->question->getHref(), $this->question->getTitle());?>
</div>

<div class="heQuestionContent">
  <?php
  echo $this->render('application/modules/Hequestion/views/scripts/_question.tpl');
  ?>
</div>