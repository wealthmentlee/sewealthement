<?php


  if( count($this->navigation) > 0 ): ?>
    <div class="tabs">
      <?php
      // Render the menu
      echo $this->navigation()
        ->menu()
        ->setContainer($this->navigation)
        ->render();
      ?>
    </div>
  <?php
  endif;

?>

<div class="settings">
<?php

//$this->setDescription('DESCRIPTION_ADMIN_HASHTAG');
$this->form->getDecorator('Description')->setOption('escape', false);
echo $this->form->render($this);

?>
  </div>