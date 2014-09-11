<div>
  <h3>Search</h3>
  <span id="as_default_type" class="as_default_search_type"><i
      class="<?php if ($this->stype && isset($this->itemicons[$this->stype])) echo $this->itemicons[$this->stype]; else echo 'icon-globe';?>"></i><?php if ($this->stype && $this->stype!= 'all') echo $this->translate(strtoupper('ITEM_TYPE_' . $this->stype)); else echo $this->translate('Everywhere'); ?></span>
  <div style="clear: both"></div>
  <input type="text" class="as_query_input" style="padding: 5px 6px 5px 105px;" value="<?if ($this->squery) echo $this->squery;?>" id="query" name="query">
  <img id="as_loading" src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style="margin-top: -5px;vertical-align: middle; display: none"/>
  <div style="position: absolute">
    <div class="advancedsearch_types_list" id="advancedsearch_types_list" style="display: none">
      <div class="as_type_container <?php if ($this->stype == 'all') echo 'active';?>">
        <span data-type="all"><i class="icon-globe"></i><?php echo $this->translate('Everywhere'); ?></span>
        <div style="clear: both"></div>
      </div>
      <?php foreach ($this->types as $type): ?>
        <div class="as_type_container <?php if ($this->stype == $type) echo 'active';?>">
          <span data-type="<?php echo $type;?>"><i class="<?php if (isset($this->itemicons[$type])) echo $this->itemicons[$type]; else echo 'icon-globe'; ?>"></i><?php echo $this->translate(strtoupper('ITEM_TYPE_' . $type));?></span>
          <div style="clear: both"></div>
        </div>
      <?php endforeach;?>
    </div>
  </div>
  <input type="hidden" id="type" value="<?php if ($this->stype) echo $this->stype;?>" name="type"></div>
  <div id="as_found_items">

  </div>
  <div style="clear: both"></div>
  <div class="as_more_button">
    <span id="more_btn"><?php echo $this->translate('More')?></span>
  </div>
</div>
<script type="text/javascript" data-cfasync="false">
  var asSearch;
  window.addEvent('domready', function () {
    var check = false;
    var page = 1;
    if ($('query').get('value') != '' && $('query').get('value').length > 2) {
      var size = $('as_default_type').getSize();
      var padd = parseInt(size.x) + 5;
      $('query').setStyle('padding-left', padd);
      search(false, page);
    }
    $('more_btn').addEvent('click', function(){
      if (!check){
        ++page;
        search(true, page);
      }
    });
    $('advancedsearch_types_list').setStyle('display', 'none');
    $('advancedsearch_types_list').setStyle('opacity', '0');
    asSearch = new Fx.Slide($('advancedsearch_types_list')).hide();
    $('as_default_type').addEvent('click', function () {
      if ($('advancedsearch_types_list').getParent().getStyle('overflow') == 'hidden')
      {
        $('advancedsearch_types_list').getParent().setStyle('overflow', 'visible');
        $('advancedsearch_types_list').setStyle('display', 'block');
        $('advancedsearch_types_list').setStyle('opacity', '1');
        asSearch.show();
      } else {
        $('advancedsearch_types_list').getParent().setStyle('overflow', 'hidden');
        $('advancedsearch_types_list').setStyle('display', 'none');
        $('advancedsearch_types_list').setStyle('opacity', '0');
        asSearch.hide();
      }
    });
    $$('body').addEvent('keyup', function(event){
      if (event.key == 'esc') {
        $('advancedsearch_types_list').setStyle('opacity', '0');
        $('advancedsearch_types_list').setStyle('display', 'none');
        asSearch.hide();
        $('advancedsearch_types_list').getParent().setStyle('overflow', 'hidden');
      }
    });
    $$('body').addEvent('click', function(e){
      if ($(e.target).get('id') != 'as_default_type') {
        $('advancedsearch_types_list').setStyle('opacity', '0');
        $('advancedsearch_types_list').setStyle('display', 'none');
        asSearch.hide();
        $('advancedsearch_types_list').getParent().setStyle('overflow', 'hidden');
      }
    });

    $$('.as_type_container').addEvent('click', function(){
      $$('.as_type_container').removeClass('active');
      var size = $(this).getChildren('span').getSize();
      var padd = parseInt(size[0].x);
      $('query').setStyle('padding-left', padd);
      $('type').set('value', $(this).getChildren('span').get('data-type'));
      $('as_default_type').set('html', $(this).getChildren('span').get('html'));
      $(this).toggleClass('active');
      $('advancedsearch_types_list').setStyle('opacity', '0');
      $('advancedsearch_types_list').setStyle('display', 'none');
      asSearch.hide();
      $('advancedsearch_types_list').getParent().setStyle('overflow', 'hidden');
      page = 1;
      check = false;
      search(false, 1);
    });

    $('query').addEvent('keyup', function(){
      if ($(this).get('value').length > 2) {
        check = false;
        page = 1;
        search(false, 1);
      } else {
        $('as_found_items').set('html', '');
      }
    });
    $(window).addEvent('scroll', function(){
      var totalHeight, currentScroll, visibleHeight;

      if (document.documentElement.scrollTop)
      { currentScroll = document.documentElement.scrollTop; }
      else
      { currentScroll = document.body.scrollTop; }

      totalHeight = document.body.offsetHeight;
      visibleHeight = document.documentElement.clientHeight;
      if (totalHeight <= currentScroll + visibleHeight && $('as_loading').getStyle('display') == 'none' && !check)
      {
        ++page;
        search(true, page);
      }
    });

    function search(append, page){
      var query = $('query').get('value');
      var type = $('type').get('value');
      if (query != '') {
        $('as_loading').setStyle('display', 'inline');
        var url = '<?php echo $this->url(array('action' => 'search'), 'advancedsearch')?>';
        var jsonRequest = new Request.JSON({
          url: url,
          method: 'post',
          data: {
            'query': query,
            'type': type,
            'page': page,
            'format' : 'json'
          },
          onSuccess: function(data){
            if (data.html.trim() != '') {
              if (!append) {
                var found = data.html;
                found = Elements.from(found);
                $('as_found_items').set('html', data.html);
                var myFx = new Fx.Tween('as_found_items');
                $('as_found_items').setStyle('opacity', 0);
                myFx.start('opacity', 0, 1);
              } else {
                var found = data.html;
                found = Elements.from(found);
                found.inject($('as_found_items'), 'bottom');
              }
              $('more_btn').setStyle('display', 'block');
            } else if (!append){
              var div = new Element('div');
              div.addClass('tip');
              var el = new Element('span').set('text', '<?php echo $this->translate("AS_Nothing found")?>');
              el.inject(div);
              $('as_found_items').set('html', '');
              div.inject($('as_found_items'));
              $('more_btn').setStyle('display', 'none');
            } else if (data.html.trim() == '') {
              check = true;
              $('more_btn').setStyle('display', 'none');
            }
            $('as_loading').setStyle('display', 'none');
          }
        }).send();

      } else {
        $('as_loading').setStyle('display', 'none');
      }
    }
  });

</script>
