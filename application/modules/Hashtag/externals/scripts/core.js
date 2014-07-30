Brower = Browser;

function click_hashtags(url,name,resent, res_type, res_id, pinfeed_module){
   if(typeof pinfeed_module == 'undefined'){
       pinfeed_module = false;
   }

   var container = $('activity-feed'); // элемент container для полученных данных
    var conetnt;
    var data = {
        'name': name,
        'format': 'json',
        'res_id': res_id,
        'res_type':res_type
    };
    var res = ''+resent;
    if(window.location.href.indexOf('/page/') > -1){
        var id = getUrlVars();
        var id_page = getUrlVars();
        for(i=0;i<10;i++){
             if(id[i]=='page'){
                 id_page=id[i+1];
                 break;
             }
        }
        var page = 'page';
        url = url+'?type='+page+'&id='+id_page;
    }else{
    }
    var wall_button_rev = $$('.item wall_blurlink').get('rev');

    if(wall_button_rev != 'recent'){
    }
    if($('opacity')){
        var div_name = 'opacity';
    }else{
        var div_name = 'activity-feed';
    }
    if(pinfeed_module){
        $('header_hashtag').setStyle('display','block');
        if($$('.utility-viewall')) $$('.utility-viewall').set('html','');
        url = url+'?pinfeed=1';

        $('pinfeed1').set('html','');
        $('pinfeed2').set('html','');
        $('pinfeed3').set('html','');
        $('pinfeed4').set('html','');
        $('pinfeed5').set('html','');
        $('pinfeed6').set('html','');
        $('pinfeed7').set('html','');
        $('feed_block').set('html',
            '<img src="application/modules/Hashtag/externals/images/loader.gif"'+
                'style="left: 50%; '+
                'position: absolute; top: 40%;border: 17px solid #FFFFFF; '
                +'background-color: white; ">');
    }else{

    if($(div_name)) $(div_name).set('html', '<img src="application/modules/Hashtag/externals/images/loader.gif" style="left: 40%;position: relative; margin: 10px">');
    }

    if($(div_name))  $(div_name).setStyle("opacity", "1");
    Wall.request(url, data, function(response) {
        var wall_feed_id = $$('.wallFeed').get('id');
        if ($("activity-feed").getParent('.wallFeed')){
            if(pinfeed_module){
                $('feed_block').set('html', response.html);
                $$('.fake-lists').setStyle('top','33px');
                if($$('.tip')){
                    $$('.tip').set('html','');
                }

            }else{

                $("activity-feed").set('html', response.html);
            }

        }
  // $("activity-feed").set('html', response.html);//
        if($(div_name))   $(div_name).fade('1');
        if($('wall-feed-scripts'))  $('wall-feed-scripts').set('text', '');
        if($$('.utility-getlast'))  $$('.utility-getlast').set('text', '');
        if( $$('.compose-content')) $$('.compose-content').set('text', " " + '#' + name);
        if($$('.hashtag_composer_cont')) $$('.hashtag_composer_cont').removeClass('display_none');

        Wall.feeds.items[wall_feed_id].initAction($('activity-feed'));
        Wall.feeds.items[wall_feed_id].watcher.stop();
        if(pinfeed_module){


            $('header_hashtag').set('html',$$('.hashtag_composer_cont ').get('html') );
            $$('.hashtag_composer_cont ').set('html','');
        }
        if($$('.wall-list-button') && !pinfeed_module) $$('.wall-list-button').set('html','<span class="wall_icon wall-most-recent">&nbsp;</span>' + '<span class="wall_text">'+res+'</span>');

    });
    var i =1;
    var update;
    if($('hashtag-interval-check')){

        update = update_options_hashtag;
    }else{
        update = 120000;
    }
    if(window.interval_id_hashtag){
    clearInterval(interval_id_hashtag);
    }
  setTimeout(function (){
      if(window.interval_id_hashtag){
     clearInterval(interval_id_hashtag);
      }
     interval_id_hashtag = setInterval (function (){
                if ($('hashtag-interval-check')){
                var id_str = $$('.wall-action-item')[0].get('rev');
                var id_hahs = id_str.split('-')[1];


                data['update'] = id_hahs;
                Wall.request(url, data, function(response) {
                   // elementHtml.destroy();
                    try {
                        var rand_is = Math.floor(Math.random()*10000);
                        var elementHtml = new Element('<div>', {'html': response.html, 'id': 'new_'+rand_is });
                        elementHtml.inject($('hashtag_contaner'),'top');
                    } catch (e){
                        alert(e);
                    }

                    $('new_'+rand_is).setStyle("opacity", "0");
                    $('new_'+rand_is).fade('1');


                   // console.log(id_hahs+ '  - id');
              // $('hashtag_contaner').grab(elementHtml, 'top');

                var wall_feed_id = $$('.wallFeed').get('id');
                $$('.hashtag_composer_cont').removeClass('display_none');
                Wall.feeds.items[wall_feed_id].initAction($('activity-feed'));
                Wall.feeds.items[wall_feed_id].watcher.stop();
                $$('.wall-list-button').set('html','<span class="wall_icon wall-most-recent">&nbsp;</span>' + '<span class="wall_text">'+res+'</span>');
                    //remove(elementHtml);
          });
        }else{
            clearInterval(interval_id_hashtag);
        }
        i++;
        if (i >50){
            clearInterval(interval_id_hashtag);
        }
    }, update);

     }, 4000);


}
/*setInterval = (function( oldsetInterval){
    var registered=[],
        f = function(a,b){
            return registered[ registered.length ] = oldsetInterval(a,b)
        };
    f.clearAll = function(){
        var r;
        while( r = registered.pop()) {
            clearInterval( r );
        }
    };
    return f;
})(window.setInterval);*/

function getUrlVars() {
    var parts = window.location.href.split('/');
    return parts;
}
function click_hashtags_close(url){
    var container = $('activity-feed'); // элемент container для полученных данных
    var url_now = '?format=html&mode=recent&type=&list_id=0&subject=&feedOnly=true';
    if(window.location.href.indexOf('/page/') > -1){
        url_now = '?format=html&minid=292&checkUpdate=true&subject=page_7&feedOnly=true';
    }
    var conetnt;
    var data = +url_now;
    var request = new Request.HTML({
        'url': url+url_now,
        'method': 'get',
        'evalScripts' : false,
        onRequest: function(){
            $('activity-feed').set('text', 'loading...');
        },
        'onComplete': function (responseTree, responseElements, responseHTML, responseJavaScript){
            Wall.is_request = false;
            Wall.initA
            $('activity-feed').set('html', responseHTML);
            $$('.hashtag_composer_cont').addClass('display_none');
            eval(responseJavaScript);
            Wall.globalBind();
            en4.core.runonce.trigger();
            var wall_feed_id = $$('.wallFeed').get('id');
            Wall.feeds.items[wall_feed_id].initAction($('activity-feed'));
        }
    });
    request.send();
}
function click_hashtags_menu(id,rev){

    if ($('hash'+id).hasClass('box_for_hashtag display_none')) {
        $('hash'+id).removeClass('display_none');
        Wall.injectAbsolute_hash($$('.hash_link_'+id)[0], $('hash'+id), true);
    }

    return false;
}
function click_hashtags_menu_clikc(id,rev){
    if ($('hash'+id).hasClass('box_for_hashtag display_none')) {
        $('hash'+id).removeClass('display_none');
        Wall.injectAbsolute_hash($$('.hash_link_'+id)[0], $('hash'+id), true);
    }
    else  {
        $('hash'+id).addClass('display_none');
        $$('.box_for_hashtag ').addClass('display_none');

    }
    return false;
}

function click_hashtags_closes(){
    $('activity-feed').set('html', '<img src="application/modules/Hashtag/externals/images/loader.gif" style="left: 40%;position: relative; margin: 10px">');
    if(window.interval_id_hashtag) clearInterval(interval_id_hashtag);
    //setInterval.clearAll();
    $$('.compose-content').set('html', '<br>');
    var wall_feed_id = $$('.wallFeed').get('id');
    Wall.feeds.items[wall_feed_id].loadFeed('','','','',$('activity-feed'))
}

document.addEvent('DOMContentLoaded', function() {
    $$('body')[0].addEvent('click', function (e){
        if (!$(e.target).getParent('.link_hashtag_div')){
           $$('.box_for_hashtag ').addClass('display_none');
        }
    })
   /* $$('.link_hashtag_div ').addEvent('mouseover', function(event) {
        event = new Event(event).stop();
        box = 1;
    });
    $$('.box_for_hashtag ').addEvent('mouseover', function(event) {
        event = new Event(event).stop();
        box = 1;
    });
    $$('.box_for_hashtag ').addEvent('mouseout', function(event) {
        event = new Event(event).stop();
        box = 0;
    });
    $$('.link_hashtag_div ').addEvent('mouseout', function(event) {
        event = new Event(event).stop();
        box = 0;
        setTimeout(function (){
            if(box != 1){
                $$('.box_for_hashtag ').addClass('display_none');
            }
        }, 500);
    });

        $$('.box_for_hashtag ').addEvent('mouseout', function(event) {
            event = new Event(event).stop();

            setTimeout(function (){
                if(box != 1){
                $$('.box_for_hashtag ').addClass('display_none');
                }
            }, 100);
        });*/



}, false);




function mous_out_div(){
   /* clearTimeout(this.timeout);
   setTimeout(function (){
        $$('.box_for_hashtag ').addClass('display_none');
    }, 2000);*/
}


