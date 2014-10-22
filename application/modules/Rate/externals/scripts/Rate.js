
/* $Id: Rate.js 2010-05-25 01:44 ermek $ */

function Rate(id, type, uid, options)
{
    this.id = id;
    this.type = type;
    this.uid = uid;
    this.can_rate = (options && options.can_rate != undefined) ? options.can_rate : true;
    this.error_msg = (options && options.error_msg) ? options.error_msg : '';

    this.$stars_cont = $('rate_uid_' + this.uid);
    this.construct();
}

Rate.prototype =
{
    construct: function()
    {
        var self = this;

        this.disabled_rate = false;

        this.$stars = this.$stars_cont.getElements('.rate_star');
        this.$stars.addEvent('mouseover', function()
        {
            self.$stars.removeClass('rate');

            $star = $(this);
            var $previous = $star.getAllPrevious();
            if ($previous) {
                $previous.addClass('rate');
            }
            $star.addClass('rate');
        })
        .addEvent('mouseout', function()
        {
            self.$stars.removeClass('rate');
        })
        .addEvent('click', function()
        {
            if (this.disabled_rate) {
                return false;
            }

            if (!self.can_rate) {
                he_show_message(self.error_msg, 'error');
                return;
            }

            $star = $(this);
            var score = $star.getProperty('id').substr(10);
            self.rate(score);
        });

        var $voters_link = this.$stars_cont.getElement('.item_voters');
        if ($voters_link) {
            $voters_link.addEvent('click', function(){
                var title = (self.langvars && self.langvars.title) ? self.langvars.title : 'Who has voted?';
                var data = {
                    'item_id': self.id,
                    'list_title1': (self.langvars && self.langvars.list_title1) ? self.langvars.list_title1 : '',
                    'list_title2': (self.langvars && self.langvars.list_title2) ? self.langvars.list_title2 : '',
                    'item_type': self.type
                };
                
                he_list.box('rate', 'getItemVoters', title, data);
                this.blur();
            });
        }
    },

    rate: function(score)
    {
        var self = this;

        this.disabled_rate = true;
        this.$stars_cont.getElement('.item_rate_info').addClass('display_none');
        this.$stars_cont.getElement('.rate_loading').removeClass('display_none');

	    en4.core.request.send(new Request.JSON({
            url : this.rate_url,
            data: {format: 'json', type: this.type, id: this.id, score: score, noCache: Math.random()},
            onSuccess : function(data){
                if (data && data.result) {
                    self.setScore(data);
                    he_show_message(data.message);
                } else {
                    he_show_message(data.message, 'error');
                }

                self.$stars_cont.getElement('.item_rate_info').removeClass('display_none');
                self.$stars_cont.getElement('.rate_loading').addClass('display_none');
                self.disabled_rate = false;
            }
        }));
    },

    setScore: function(rate_info)
    {
        this.$stars.removeClass('rated')
            .removeClass('half_rated')
            .removeClass('no_rate')
            .removeClass('quarter_rated')
            .removeClass('fquarter_rated');

        this.$stars_cont.getElement('.item_score').set('html', rate_info.item_score + '/' + rate_info.maxRate);
        this.$stars_cont.getElement('.item_votes').set('html', rate_info.rate_count);

        if (this.$stars_cont.getElement('.item_voters')) {
            this.$stars_cont.getElement('.item_voters').set('text', rate_info.label);
        }

        for (var i = 0; i < this.$stars.length; i++) {
            var star_score = this.$stars[i].getProperty('id').substr(10).toFloat();

            if ((i + 0.125) > rate_info.item_score) {
                this.$stars[i].addClass('no_rate');
            } else if ((i + 0.375) > rate_info.item_score) {
                this.$stars[i].addClass('quarter_rated');
            } else if ((i + 0.625) > rate_info.item_score) {
                this.$stars[i].addClass('half_rated');
            } else if ((i + 0.875) > rate_info.item_score) {
                this.$stars[i].addClass('fquarter_rated');
            } else {
                this.$stars[i].addClass('rated');
            }
        }
    }
};

function getRateContainer(plugins_settings)
{
    window.addEvent('domready', function() {
        if (en4 && en4.core && en4.core.subject) {
            subject = en4.core.subject;
        } else {
            return;
        }

        $container = new Element('div', {'id': 'he_rate_container'});
        $loading = new Element('div', {'id': 'he_rate_loader', 'class': 'he_rates_loading'});

        if (subject.type=='blog' && plugins_settings.blog.enabled) {
            action_url = plugins_settings.blog.url_rate;
            $parent_container = $$('a.blogs_gutter_name')[0];
        } else if (subject.type=='album_photo' && plugins_settings.album.enabled) {
            action_url = plugins_settings.album.url_rate;
            $parent_container = $('media_photo_next');
        } else if (subject.type=='article' && plugins_settings.article.enabled) {
            action_url = plugins_settings.article.url_rate;
            $parent_container = ($$('.articles_gutter_options') && $$('.articles_gutter_options').length > 0) ? $$('.articles_gutter_options')[0] : false;
            if (!$parent_container) { return; }
            $container.setStyle('margin-top', '20px');
        } else {
            return;
        }

        $container.inject($parent_container, 'after');
        $loading.inject($container);

        var ajax_request = new Request.JSON({
            method: 'get',
            url: action_url,
            data: {'item_type':subject.type, 'item_id':subject.id},
            onSuccess : function(response)
            {
                if ( response.rate_info ) {
                    $container.set('html', response.html);

                    var rateVar = new Rate(subject.id, subject.type, response.rate_uid, response.can_rate);
                    rateVar.rate_url = response.rate_url;
                    rateVar.langvars = response.lang_vars;
                }
            }
        });

        window.setTimeout(function(){ ajax_request.send(); }, 5);
    });
}

function showRatesList($node, type) {
  $node = $($node);
  var $box = $node.getParents('.he_rate_cont');

  if (!$box || !$box[0]) {
    return;
  }

  $box = $box[0];
  $box.getElements('ul.rate_list_switcher li a').removeClass('active');
  $node.addClass('active');

  $cur_list = $box.getElement('.rates_' + type);
  $old_list = $box.getElement('div.active_list');

  if ($cur_list.hasClass('active_list')) {
    $node.blur();
    return;
  }

  var cur_tween = new Fx.Morph($cur_list, {duration: 300, opacity: 0});
  var old_tween = new Fx.Morph($old_list, {duration: 300, opacity: 1});

  old_tween.start({opacity: 0}).chain(function(){
    $old_list.removeClass('active_list');
    $cur_list.addClass('active_list');

    cur_tween.start({opacity: 1}).chain(function(){
      $node.blur();
    });
  });
}