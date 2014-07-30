
function getCannedResponse(idx,fObj)
{
    if(idx==0) { return false; }
    Http.get({
        url: "ajax.php?api=kbase&f=cannedResp&id="+idx,
        callback: setCannedResponse
    },[fObj]);

}

function setCannedResponse(xmlreply,fObj)
{
    if (xmlreply.status == Http.Status.OK)
    {
        var resp=xmlreply.responseText;
        if(fObj.response && resp){
            fObj.response.value=(fObj.append && fObj.append.checked)?trim(fObj.response.value+"\n\n"+resp):trim(resp)+"\n\n";
        }else {
            alert("Invalid form or tag");
        }
    }
    else{
        alert("Cannot handle the AJAX call. Error#"+ xmlreply.status);
    }
}

function getSelectedCheckbox(formObj) {
   var retArr = new Array();
   var x=0;
	for (var i= 0; i< formObj.length; i++)
    {
        fldObj = formObj.elements[i];
        if ((fldObj.type == 'checkbox') && fldObj.checked)
			retArr[x++]=fldObj.value;
   	}
   return retArr;
} 


function selectAll(formObj,task,highlight)
{
   var highlight = highlight || false;

   for (var i=0;i < formObj.length;i++)
   {
      var e = formObj.elements[i];
      if (e.type == 'checkbox')
      {
         if(task==0) {
            e.checked =false;
         }else if(task==1) {
            e.checked = true;
         }else{
            e.checked = (e.checked) ? false : true;
         }
         
	     if(highlight) {
			highLight(e.value,e.checked);
		 }
       }
   }
   //Return false..to mute submits or href.
   return false;
}

function reset_all(formObj){
    return selectAll(formObj,0,true);
}
function select_all(formObj,highlight){
    return selectAll(formObj,1,highlight);
}
function toogle_all(formObj,highlight){

	var highlight = highlight || false;
    
	return selectAll(formObj,2,highlight);
}


function checkbox_checker(formObj, min,max,sure,action)
{

	var checked=getSelectedCheckbox(formObj); 
	var total=checked.length;
    var action= action?action:"process";
 	
	if (max>0 && total > max )
 	{
 		msg="You're limited to only " + max + " selections.\n"
 		msg=msg + "You have made " + total + " selections.\n"
 		msg=msg + "Please remove " + (total-max) + " selection(s)."
 		alert(msg)
 		return (false);
 	}
 
 	if (total< min )
 	{
 		alert("Please make at least " + min + " selections. " + total + " entered so far.")
 		return (false);
 	}
   
  if(sure){
  	if(confirm("PLEASE CONFIRM\n About to "+ action +" "+ total + " record(s).")){
 		return (true);
  	}else{
        reset_all(formObj);
	 	return (false);
  	}
  }
 
  return (true);
}

function showHide(){

	for (var i=0; i<showHide.arguments.length; i++){
        toggleLayer(showHide.arguments[i]);
	}
    return false;
}

function visi(){	 
	for (var i=0; i<visi.arguments.length; i++){
        var element = document.getElementById(visi.arguments[i]);
        element.style.visibility=(element.style.visibility == "hidden")?"visible" : "hidden";
    }
}
function visible(id){
	var element = document.getElementById(id).style.visibility="visible";
}


function highLight(trid,checked) {

    var class_name='highlight';
    var elem;

    if( document.getElementById )
        elem = document.getElementById( trid );
    else if( document.all )
        elem = document.all[trid];
    else if( document.layers )
        elem = document.layers[trid];
    if(elem){
        var found=false;
        var temparray=elem.className.split(' ');
        for(var i=0;i<temparray.length;i++){
            if(temparray[i]==class_name){found=true;}
        }
        if(found && checked) { return; }

        if(found && checked==false){ //remove
            var rep=elem.className.match(' '+class_name)?' '+class_name:class_name;
            elem.className=elem.className.replace(rep,'');
        }
        if(checked && found==false) { //add
            elem.className+=elem.className?' '+class_name:class_name;
        }
    }
}

function highLightToggle(trid) {

    var class_name='highlight';
    var e;
    if( document.getElementById )
        e = document.getElementById(trid);
    else if( document.all )
        e = document.all[trid];
    else if( document.layers )
        e = document.layers[trid];

    if(e){
        var found=false;
        var temparray=e.className.split(' ');
        for(var i=0;i<temparray.length;i++){
            if(temparray[i]==class_name){found=true;}
        }
        if(found){ //remove
            var rep=e.className.match(' '+class_name)?' '+class_name:class_name;
            e.className=e.className.replace(rep,'');
        }else { //add
            e.className+=e.className?' '+class_name:class_name;
        }
    }
}

//trim
function trim (str) {
    str = this != window? this : str;
    return str.replace(/^\s+/,'').replace(/\s+$/,'');
}

//strcmp
function strcmp(){
    var arg1=arguments[0];
    if(arg1) {
        for (var i=1; i<arguments.length; i++){
            if(arg1==arguments[i])
                return true;
        }
    }
    return false;
}


function change_dev_log(log_id, start_time, end_time)
{
	$.getJSON('ajax_requests.php',
    {'command': 'change_dev_log', 'log_id': log_id, 'start_time': start_time, 'end_time': end_time},
		function(response) {
			if (response.status)
			{
			}
			else
				alert('Failed. Please reload page and try again.');
		}
  );
}


function change_dev_point(day, month, year, user_id, $input) {
  $input = $($input);
	$.getJSON('ajax_requests.php',
    {
			'command': 'change_dev_point', 'user_id': user_id, 
			'day': day, 'month': month, 'year': year, 
			'point_id': $input.attr('id').substr(6), 'point': $input.val()
		},
		function(response) {
			if (response.point_id) {
				$input.attr('id', 'point_' + response.point_id);
			}
			else {
        alert('Failed. Please reload page and try again.');
      }
    }
  );
}

function show_manage_license(id, node) {
  var $manage = $('#edit_product_license');
  $manage.find('input[name="cp_id"]').val(id);
  $(node).parent().append($manage);
}

function cancel_manage_key() {
  var $manage = $('#edit_product_license');
  var $manage_cont = $('#edit_product_license_cont');

  $manage_cont.append($manage);
}

function he_clone(obj){
    if(obj == null || typeof(obj) != 'object')
        return obj;

    var temp = new obj.constructor(); // changed (twice)
    for(var key in obj)
        temp[key] = he_clone(obj[key]);

    return temp;
}

var developer_panel = {

  check_flag: true,
  disabled_flag: false,
  check_period: 30,
  page: 1,
  on_page: 8,
  total: 0,
  list_type: 'my',
  hash: '',
  tickets: {},
  new_tickets: {},

  $developer_panel: {},
  $tickets_cont: {},
  $dp_ticket_tpl: {},

  construct: function() {
    var self = this;
    this.$developer_panel = $('#developer_panel');
    this.$tickets_cont = this.$developer_panel.find('.developer_panel_tickets');
    this.$loader = this.$developer_panel.find('.dp_loader');
    this.$dp_ticket_tpl = $('#dp_ticket_tpl');
    
    this.draw_tickets(true);
    this.init_updates();

    this.$developer_panel.find('.dp_page_previous').bind('click', function(){
      if (self.page == 1) {
        return;
      }

      self.page--;
      self.get_updates();
    });

    this.$developer_panel.find('.dp_page_next').bind('click', function(){
      if (self.page * self.on_page >= self.total) {
        return;
      }

      self.page++;
      self.get_updates();
    });
  },

  change_list: function($node) {
    this.page = 1;
    this.list_type = (this.list_type == 'my') ? 'all' : 'my';
    this.hash = '';

    $node.find('a').toggleClass('display_none');
    $node.blur();

    this.get_updates();
  },

  draw_tickets: function(init) {

    var tickets = (init) ?  this.tickets : this.new_tickets;

    if (!init) {
      this.$tickets_cont.empty();
    }

    if (tickets.length == 0) {
      this.$tickets_cont.html('<div class="developer_panel_ticket dp_no_tickets">No tickets</div>');
      this.$developer_panel.find('.dp_page_info').html(' - ');
      return;
    }

    for(var ticket_id in tickets) {
      var ticket = tickets[ticket_id];
      var $ticket = this.$dp_ticket_tpl.clone();

      $ticket.attr('id', 'db_ticket_' + ticket_id);

      var $ticket_url = $ticket.find('.dp_ticket_url');
      $ticket_url.attr('href', $ticket_url.attr('href').replace('#ticket_id', ticket_id));
      $ticket_url.html(ticket.subject);

      var $ticket_client = $ticket.find('.dp_ticket_client');
      $ticket_client.attr('href', $ticket_client.attr('href').replace('#account_id', ticket.account_id));
      $ticket_client.html(ticket.account_name);

      $ticket.find('.dp_ticket_date').html(ticket.updated_date);

      if (!init) {
        //$ticket.addClass('db_' + this.check_updated(ticket_id));
      }

      if (!ticket.is_read) {
        $ticket.find('.dp_ticket_title').addClass('dp_new_ticket');
      }

      $ticket.addClass('importance_' + ticket.ltr);

      this.$tickets_cont.append($ticket);
    }

    var page_info = ((this.page - 1) * this.on_page + 1) + ' - '
      + (this.page*this.on_page > this.total ? this.total : this.page*this.on_page) + ' of ' + this.total;
    this.$developer_panel.find('.dp_page_info').html(page_info);
  },

  init_updates: function() {
    var self = this;
    window.setInterval(function() {
      if (self.check_flag) {
        self.get_updates();
      }
    }, self.check_period * 1000);
  },

  check_updated: function(ticket_id) {
    var ticket = this.tickets[ticket_id];
    var new_ticket = this.new_tickets[ticket_id];

    if (!ticket) {
      this.tickets[ticket_id] = he_clone(new_ticket);
      return 'new';
    }

    if (!new_ticket) {
      delete this.tickets[ticket_id];
      return 'deleted';
    }

    if (ticket.updated != new_ticket.updated) {
      delete this.tickets[ticket_id];
      this.tickets[ticket_id] = he_clone(new_ticket);
      return 'updated';
    }
  },

  get_updates: function() {
    var self = this;
    var cookie_key = 'dp_' + this.list_type + this.page;
    var hash_from_cookie = $.cookie(cookie_key);

    if (this.disabled_flag) {
      return;
    }

    if (this.hash != '' && this.hash == hash_from_cookie) {
      return;
    }

    this.disabled_flag = true;
    this.$loader.removeClass('display_none');
    $.getJSON('ajax_requests.php',
      {
        'command': 'dp_get_updates',
        'list_type': self.list_type,
        'page': self.page,
        'hash': self.hash,
        'no_cache': Math.random()
      },
      function(response) {
        self.disabled_flag = false;
        self.$loader.addClass('display_none');

        if (response.hash && response.tickets) {
          self.hash = response.hash;
          self.page = response.page;
          self.total = response.total;
          self.new_tickets = response.tickets;

          var cookie_key = 'dp_' + self.list_type + self.page;
          var hash_from_cookie = $.cookie(cookie_key);

          self.draw_tickets();
        }
      }
    );
  }
};



function showAccountCrd()
{
  $('#account_credentials').toggleClass('display_none');
}

function showEditCrdInfo(node)
{
  var $node = $(node);
  var $crd_box = $node.parent().parent();
  var $crd_detail = $crd_box.find('.crd_detail');
  var $textarea = $crd_box.find('textarea');

  var box_value = $crd_detail.html();
  var txt_value = $textarea.val();

  if (!window.crd_scroll_initiated) {
    window.crd_scroll_initiated = true;
    
    var $textareas = $crd_box.parent().find('textarea');
    
    $textareas
      .bind('scroll', function() {
        $(this).attr('rows', parseInt($(this).attr('rows'))+1);
      })
      .bind('blur', function() {
        $(this).attr('rows', 3);
      });
  }

  if ($crd_box.find('.crd_info').hasClass('display_none')) {
    $crd_box.find('.crd_info').removeClass('display_none');
    $crd_box.find('.crd_toggle_icon').removeClass('show');
  }

  if ($textarea.hasClass('display_none')) {
    $textarea.val(box_value.br2nl());
    $crd_detail.addClass('display_none');
    $textarea.removeClass('display_none');
  } else {
    if (txt_value == box_value.br2nl()) {
      $textarea.addClass('display_none');
      $crd_detail.removeClass('display_none');
      return;
    }
  }
}

function showCrdInfo(node)
{
  var $node = $(node);
  $node.toggleClass('show');
  $node.parents('.crd_main_label').next('.crd_info').toggleClass('display_none');
}

function saveCrdChanges(node)
{
  if (window.crd_disabled_flag) {return;}
  
  var $textarea = $(node);
  var $crd_box = $(node).parent().parent();

  $textarea.val($textarea.val().trim());

  var crd_type = $textarea.attr('name');
  var crd_value = $textarea.val().nl2br();
  
  //check changed
  if ($crd_box.find('.crd_detail').html().br2nl() == crd_value.br2nl()) {
    $textarea.addClass('display_none');
    $crd_box.find('.crd_detail').removeClass('display_none').html(crd_value);
    
    return;
  }

  window.crd_disabled_flag = true;
  $textarea.attr('disabled', true);

  $.getJSON('ajax_requests.php',
    {
      'command': 'crd_save_changes',
      'account_id': window.account_id,
      'type': crd_type,
      'value': crd_value,
      'no_cache': Math.random()
    },
    function(response) {
      window.crd_disabled_flag = false;
      $textarea.attr('disabled', false);

      $textarea.addClass('display_none');
      $crd_box.find('.crd_detail').removeClass('display_none').html(crd_value);
    }
  );
}

function br2nl()
{
  var text = this.toString();
  var text_arr = [];
  
  text_arr =  text.split(/\<br\s?\/?\>/i);
  text = text_arr.join("\n");

  return text;
}

function nl2br()
{
  var text = this.toString();
  var text_arr = [];

  text_arr =  text.split("\n");
  text = text_arr.join("<br/>");

  return text;
}

String.prototype.br2nl = br2nl;
String.prototype.nl2br = nl2br;
