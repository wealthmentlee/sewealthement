<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 17.08.12 06:04 michael $
 * @author     Michael
 */
?>


<ul class="hqBrowseQuestions">
  <?php foreach ($this->answers as $item):?>
    <li>
      <div class="item_photo">
        <?php echo $this->htmlLink($item['voter']->getHref(), $this->itemPhoto($item['voter'], 'thumb.icon')) ?>
      </div>
      <div class="item_body">
        <div class="item_description">
          <?php
            echo $this->translate('HEQUESTION_ANSWER_BODY', array(
              $this->htmlLink($item['voter']->getHref(), $item['voter']->getTitle()),
              $this->htmlLink($item['question']->getHref(), $item['question']->getTitle()),
              $item['answer']
            ));
          ?>
        </div>
        <div class="item_date">
          <?php echo $this->timestamp(strtotime($item['vote']->creation_date)) ?>
        </div>
      </div>
    </li>
  <?php endforeach;?>
</ul>