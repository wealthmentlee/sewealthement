
<?php echo $this->htmlLink($this->owner->getHref(), 
    $this->itemPhoto($this->owner),
    array('class' => 'blogs_gutter_photo')) ?>

<?php echo $this->htmlLink($this->owner->getHref(), 
    $this->owner->getTitle(), 
    array('class' => 'blogs_gutter_name')) ?>
