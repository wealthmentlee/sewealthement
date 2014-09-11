
<script type="text/javascript">
  var pageAction = function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
  var categoryAction = function(category){
    $('page').value = 1;
    $('blog_search_field').value = '';
    $('category').value = category;
    $('tag').value = '';
    $('start_date').value = '';
    $('end_date').value = '';
    $('filter_form').submit();
  }
  var tagAction = function(tag){
    $('page').value = 1;
    $('blog_search_field').value = '';
    $('tag').value = tag;
    $('category').value = '';
    $('start_date').value = '';
    $('end_date').value = '';
    $('filter_form').submit();
  }
  var dateAction = function(start_date, end_date){
    $('page').value = 1;
    $('blog_search_field').value = '';
    $('start_date').value = start_date;
    $('end_date').value = end_date;
    $('tag').value = '';
    $('category').value = '';
    $('filter_form').submit();
  }

  en4.core.runonce.add(function(){
    new OverText($('blog_search_field'), {
      poll: true,
      pollInterval: 500,
      positionOptions: {
        position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
        edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
        offset: {
          x: ( en4.orientation == 'rtl' ? -4 : 4 ),
          y: 2
        }
      }
    });
  });
</script>

<?php $searchUrl = $this->url(array('user_id' => $this->owner->getIdentity()), 'blog_view', true); ?>

<form id='filter_form' class="blog_search_form" method='GET' action="<?php echo $this->escape($searchUrl) ?>">
  <input type='text' class='text suggested' name='search' id='blog_search_field' size='20' maxlength='100' alt='<?php echo $this->translate('Search Blogs') ?>' value="<?php if( $this->search ) echo $this->search; ?>" />
  <input type="hidden" id="tag" name="tag" value="<?php if( $this->tag ) echo $this->tag; ?>"/>
  <input type="hidden" id="category" name="category" value="<?php if( $this->category ) echo $this->category; ?>"/>
  <input type="hidden" id="page" name="page" value="<?php if( $this->page ) echo $this->page; ?>"/>
  <input type="hidden" id="start_date" name="start_date" value="<?php if( $this->start_date) echo $this->start_date; ?>"/>
  <input type="hidden" id="end_date" name="end_date" value="<?php if( $this->end_date) echo $this->end_date; ?>"/>
</form>

<?php if( count($this->userCategories) ): ?>
  <h4><?php echo $this->translate('Categories');?></h4>
  <ul>
    <li>
      <a href='javascript:void(0);' onclick='javascript:categoryAction(0);' <?php if( $this->category == 0 ) echo " style='font-weight: bold;'" ?>>
          <?php echo $this->translate('All Categories') ?>
      </a>
    </li>
    <?php foreach( $this->userCategories as $categoryId => $categoryName ): ?>
      <li>
        <a href='javascript:void(0);' onclick='javascript:categoryAction(<?php echo $categoryId ?>);' <?php if( $this->category == $categoryId ) echo " style='font-weight: bold;'" ?>>
          <?php echo $this->translate($categoryName) ?>
        </a>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<?php if( count($this->userTags) ): ?>
  <h4><?php echo $this->translate('Tags'); ?></h4>
  <ul>
    <?php foreach ($this->userTags as $tag): ?>
      <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->tag_id; ?>);' <?php if ($this->tag==$tag->tag_id) echo " style='font-weight: bold;'";?>>#<?php echo $tag->text?></a>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<?php if( count($this->archiveList) ):?>
  <h4><?php echo $this->translate('Archives');?></h4>
  <ul>
    <?php foreach( $this->archiveList as $archive ): ?>
    <li>
      <a href='javascript:void(0);' onclick='javascript:dateAction(<?php echo $archive['date_start']?>, <?php echo $archive['date_end']?>);' <?php if ($this->start_date==$archive['date_start']) echo " style='font-weight: bold;'";?>>
        <?php echo $archive['label'] ?>
      </a>
    </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>
