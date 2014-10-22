<div class="content-shape-wrapper-wrapper">
  <div class="content-shape-wrapper">
    <div class="content-shape">
      <div class="content-header content-toolbar"><?php if($this->pageObject) echo $this->pageObject->displayname; else echo $this->translate('APPTOUCH_Header'); ?></div>
      <div class="content-body"><?php echo $this->translate('APPTOUCH_Content'); ?></div>
      <div class="content-footer content-toolbar"><?php echo $this->translate('APPTOUCH_Footer'); ?></div>
    </div>
    <div class="content-adv"><?php if($this->ad) echo $this->ad->html_code; else echo $this->translate('Advertisement'); ?></div>
  </div>
  <script type="text/javascript">
    var preview = function(options){
      var anim_types = ['slide', 'pop', 'fade'];
      var adv = $$('.content-adv')[0];
      var delay;
      var duration;
      var anim_type;
      if(options){
        delay = options.delay;
        duration = options.duration;
        anim_type = anim_types[options.type];
      } else {
        delay = parseFloat($('anim_delay').value);
        duration = parseFloat($('anim_duration').value);
        if(anim_type = $$('input[name="anim_type"]:checked')[0]){
          anim_type = anim_types[parseInt(anim_type.value)];
        }
      }
      if(anim_type == 'slide'){
        var dir = adv.hasClass('bottom') ? '+' : '-';
        adv.setStyles({
          'transform': 'translate(0, ' + dir + adv.offsetHeight + 'px)',
          '-webkit-transform': 'translate(0,' + dir + adv.offsetHeight + 'px)',
          'transition-property': 'transform',
          '-webkit-transition-property': 'transform'
        });
      }
      if(anim_type == 'pop'){
        adv.setStyles({
          'transform': 'scale(0)',
          '-webkit-transform': 'scale(0)',
          'transition-property': 'transform',
          '-webkit-transition-property': 'transform'
        });
      }
      if(anim_type == 'fade'){
        adv.setStyles({
          'opacity': 0,
          'transition-property': 'opacity',
          '-webkit-transition-property': 'opacity'
        });
      }
      setTimeout(function(){
        adv.setStyles({
          'transition-delay': delay + 's',
          'transition-duration': duration + 's',
          '-webkit-transition-delay': delay + 's',
          '-webkit-transition-duration': duration + 's'
        });
      }, 100);
      setTimeout(function(){
        adv.addClass('start-animation');
      }, 200);
      setTimeout(function(){
        adv.removeClass('start-animation');
        adv.set('style', '');
        adv.setStyle('display', 'block');
        setTimeout(function(){
          adv.set('style', '');
        }, 1000*(delay + duration + 100));
      }, 1000*(delay + duration + 1));


    };
    var setPos = function(el){
      var value;
      if($type(el) == 'element'){
        value = el.value;
      } else if($type(el) == 'number')
        value = el;
      var adv = $$('.content-shape>.content-adv')[0];
      if(adv){
        if(value == 1){
          $$('.content-adv').addClass('bottom');
          adv.inject($$('.content-body')[0], 'after');
          document.querySelector('.content-shape-wrapper').scrollTop = 20000;
        }else {
          $$('.content-adv').removeClass('bottom');
          adv.inject($$('.content-body')[0], 'before');
          document.querySelector('.content-shape-wrapper').scrollTop = 0;
        }
      } else
        if(value == 1){
          $$('.content-adv').addClass('bottom');
        } else
          $$('.content-adv').removeClass('bottom');
    };
    var setFixed = function(el){
      var adv = $$('.content-adv');
      var checked;
      if($type(el) == 'element'){
        checked = el.checked;
      } else if($type(el) == 'number')
        checked = el;

      var $atw = $('anim_type-wrapper');
      if(checked == false){
        if($atw)
          $atw.hide().getElements('input:checked');
        if(adv[0].hasClass('bottom')){
          adv.inject($$('.content-body')[0], 'after');
          document.querySelector('.content-shape-wrapper').scrollTop = 20000;
        }else {
          adv.inject($$('.content-body')[0], 'before');
          document.querySelector('.content-shape-wrapper').scrollTop = 0;
        }
      } else {
        if($atw)
          $atw.show();
          adv.inject($$('.content-shape')[0], 'before');
      }
    };

  </script>
</div>
