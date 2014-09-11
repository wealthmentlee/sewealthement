
<?php
  // Render the menu
  echo $this->navigation()
    ->menu()
    ->setContainer($this->gutterNavigation)
    ->setUlClass('navigation blogs_gutter_options')
    ->render();
?>
