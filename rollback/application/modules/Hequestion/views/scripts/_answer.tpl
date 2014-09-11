<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: _answer.tpl 17.08.12 06:04 michael $
 * @author     Michael
 */
?>

<?php
$this->headScript()
    ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Wall/externals/scripts/core.js');

?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    if( !$type(Hequestion)) {
      Asset.javascript('<?php echo $this->layout()->staticBaseUrl . 'application/modules/Wall/externals/scripts/core.js' ?>');
    }
    Wall.globalBind();
  });
</script>


<?php

$grouped_answers = $this->answers;
if (empty($grouped_answers)){
  return ;
}

$first_item = (isset($grouped_answers[0])) ? $grouped_answers[0] : '';
$other_num = count($grouped_answers)-1;



$translate = $this->translate(array('%1$s other', '%1$s others', $other_num), $other_num);
$other_link = '<a href="'.$first_item->getHref().'" class="wall_grouped_other">'.$translate.'</a>';

$items_str = '';
$count = count($grouped_answers);

if ($count == 1){

  echo $this->htmlLink($first_item->getHref(), $first_item->getTitle());

} else if ($count == 2) {

  $subject = $grouped_answers[1];
  $other_link = '<a href="'.$subject->getHref().'">'.$subject->getTitle().'</a>';

  $translate_key = '%1$s and %2$s';
  $first_item_link = $this->htmlLink($first_item->getHref(), $first_item->getTitle());
  echo $this->translate($translate_key, array($first_item_link, $other_link));

} else {

  for ($i=0; $i<$count; $i++){
    if ($i == 0){
      continue ;
    }
    $subject = $grouped_answers[$i];
    $items_str .= '<a href="'.$subject->getHref().'">'.$subject->getTitle().'</a>';
    if ($i < $count-1){
      $items_str .= ', ';
    }
  }


  $translate_key = '%1$s and %2$s';
  $first_item_link = $this->htmlLink($first_item->getHref(), $first_item->getTitle());
  echo $this->translate($translate_key, array($first_item_link, $other_link));

}


?>

<div style="display:none;" class="wall_grouped_other_html">
  <?php echo $items_str?>
</div>