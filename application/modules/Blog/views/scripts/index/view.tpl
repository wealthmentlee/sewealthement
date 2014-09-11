<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @author     Jung
 */
?>

<h2>
  <?php echo $this->blog->getTitle() ?>
</h2>
<ul class='blogs_entrylist'>
  <li>
    <div class="blog_entrylist_entry_date">
      <?php echo $this->translate('Posted by');?> <?php echo $this->htmlLink($this->owner->getHref(), $this->owner->getTitle()) ?>
      <?php echo $this->timestamp($this->blog->creation_date) ?>
      <?php if( $this->category ): ?>
        -
        <?php echo $this->translate('Filed in') ?>
        <a href='javascript:void(0);' onclick='javascript:categoryAction(<?php echo $this->category->category_id?>);'><?php echo $this->translate($this->category->category_name) ?></a>
      <?php endif; ?>
      <?php if (count($this->blogTags )):?>
        -
        <?php foreach ($this->blogTags as $tag): ?>
          <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text?></a>&nbsp;
        <?php endforeach; ?>
      <?php endif; ?>
      -
      <?php echo $this->translate(array('%s view', '%s views', $this->blog->view_count), $this->locale()->toNumber($this->blog->view_count)) ?>
    </div>
    <div class="blog_entrylist_entry_body rich_content_body">
      <?php echo $this->blog->body ?>
    </div>
  </li>
</ul>


<script type="text/javascript">
  $$('.core_main_blog').getParent().addClass('active');
</script>
