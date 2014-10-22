<?php
$protocol = constant('_ENGINE_SSL') ? 'https://' : 'http://';
$site_url = rtrim($_SERVER['HTTP_HOST'] . $this->baseUrl(), '/')
?>

<script type="text/javascript">

  window.addEvent('load', function () {
    Cookie.write('windowwidth', 337, {path: '/'});
    window.onfocus = function () {
      Cookie.write('windowwidth', 337, {path: '/'});
    };

    $('ips_browser').contentWindow.location = "<?php echo $this->url(array('action' => 'home'), 'user_general') ?>";
  });
  window.addEvent('domready', function () {
    var update_frequence = 1000;
    var _event = false;
    var can = false;
    var iphoneSimulator = $('phone_container');
    var addressBar = iphoneSimulator.getElement('#ips_address_bar');
    var ipsBrowser = $('ips_browser');
    var iphoneSimulatorToolbar = $('ip_simulator_toolbar');
    var onfocus = false;
    var oldloc = addressBar.get('value');
    addressBar.addEvent('keypress', function (event) {
      if (event.key == 'enter') {
        ipsBrowser.contentWindow.location.hash = '/' + this.get('value');
      }

    });
    addressBar.addEvent('focus', function (event) {
      onfocus = true;
      $$('.ips_address_bar')[0].addClass('ips_address_bar_focus');
    });
    addressBar.addEvent('blur', function (event) {
      onfocus = false;
      $$('.ips_address_bar')[0].removeClass('ips_address_bar_focus');
    });
    var range = [50, 150];
  <?php if (!$this->update_settings) { ?>
    Cookie.dispose('<?php echo $this->setting_pref . 'pos.x'?>');
    Cookie.dispose('<?php echo $this->setting_pref . 'pos.y'?>');
    Cookie.dispose('<?php echo $this->setting_pref . 'showing'?>');
    Cookie.dispose('<?php echo $this->setting_pref . 'change.time'?>');
    Cookie.dispose('<?php echo $this->setting_pref . 'scale'?>');
    <?php } ?>
    new Drag.Move(iphoneSimulator, {
      droppables:$$('#droppables div'),
      handle:iphoneSimulator.getElement('#move_handle'),
      onDrop:function (element, droppable, event) {
        if (!can)
          can = true;
        Cookie.write('<?php echo $this->setting_pref . 'pos.x'?>', parseInt(iphoneSimulator.getStyle('left')));
        Cookie.write('<?php echo $this->setting_pref . 'pos.y'?>', parseInt(iphoneSimulator.getStyle('top')));
        Cookie.write('<?php echo $this->setting_pref . 'change.time'?>', new Date().getTime());
      }
    });
    var updateAddress = function () {
      if (!onfocus) {
        var txt = ipsBrowser.contentWindow.location.hash.substr(2);
        $('ips_address_bar').set('value', txt);
        window
      }
    }

    var showHide = function () {
      if (!can)
        can = true;
      if (!iphoneSimulator.hasClass('simulator_hidden')) {
        iphoneSimulator.addClass('simulator_hidden');
        Cookie.write('<?php echo $this->setting_pref . 'showing'?>', false);
        Cookie.write('<?php echo $this->setting_pref . 'change.time'?>', new Date().getTime());
        iphoneSimulatorToolbar.getElement('.show_hide span').set('text', '<?php echo strtoupper($this->translate('APPTOUCH_SHOW')) ?>')
      } else {
        iphoneSimulator.removeClass('simulator_hidden');
        iphoneSimulatorToolbar.getElement('.show_hide span').set('text', '<?php echo strtoupper($this->translate('APPTOUCH_HIDE')) ?>')
        Cookie.write('<?php echo $this->setting_pref . 'showing'?>', true);
        Cookie.write('<?php echo $this->setting_pref . 'change.time'?>', new Date().getTime());
      }
    };
    var refresh_button = $('refresh_stop_button');
    refresh_button.addEvent('click', function (e) {
      if (this.hasClass('refresh_button')) {
        ipsBrowser.contentWindow.location.reload();
        this.removeClass('refresh_button');
        this.addClass('stop_button');
      } else {
        ipsBrowser.contentWindow.stop();
        this.addClass('refresh_button');
        this.removeClass('stop_button');
      }
    });
    var listenInterval = window.setInterval(updateAddress, update_frequence);
    new Slider($('ipst_zoom_slider'), $('ipst_zoom_slider_knob'), {
      range:range,
      wheel:true,
      mode:'vertical',
      onChange:function (step) {
        var zoom_per_cent = 200 - step;
        var zoom = (zoom_per_cent) / 100;
        iphoneSimulator.setStyle('-moz-transform', 'scale(' + zoom + ')');
        iphoneSimulator.setStyle('-webkit-transform', 'scale(' + zoom + ')');
        iphoneSimulator.setStyle('-o-transform', 'scale(' + zoom + ')');
        iphoneSimulator.setStyle('-ms-transform', 'scale(' + zoom + ')');
        iphoneSimulatorToolbar.getElement('.zoom_value span').set('text', zoom_per_cent + '%')
      },
      onComplete:function (step) {
        Cookie.write('<?php echo $this->setting_pref . 'scale'?>', step);
        if (can)
          Cookie.write('<?php echo $this->setting_pref . 'change.time'?>', new Date().getTime());
      }
    }).set(<?php echo $this->settings[$this->setting_pref . 'scale'] ?>);
    iphoneSimulatorToolbar.getElement('.show_hide').addEvent('click', showHide);
    iphoneSimulator.getElement('.main_button').addEvent('click', showHide);
    iphoneSimulator.getElement('.back_control').addEvent('click', function () {
      ipsBrowser.contentWindow.history.back()
    });
    iphoneSimulator.getElement('.forward_control').addEvent('click', function () {
      ipsBrowser.contentWindow.history.forward()
    });
//    ipsBrowser.contentWindow.set('onload', function(event){ });
  });
  function ipsb_onload() {
    if ($$('.stop_button')[0]) $$('.stop_button')[0].addClass('refresh_button').removeClass('stop_button');
  }
</script>

<div id="ip_simulator_toolbar" class="metal_cover">
  <div class="plastic_cover">
    <div class="sensor_glass">
      <div class="ipst_button ipst_button_close"
           onclick="$$('.layout_apptouch_admin_iphone_simulator')[0].setStyle('display', 'none');">
        &#215;
      </div>
      <div class="ipst_group ipst_group_zoom">
        <div id="ipst_zoom_slider" class="ipst_zoom_slider">
          <div id="ipst_zoom_slider_knob" class="ipst_zoom_slider_knob"></div>
        </div>
      </div>
      <div class="ipst_label zoom_value">
        <span><?php echo (200 - $this->settings[$this->setting_pref . 'scale']) ?>%</span>
      </div>
      <div class="ipst_button show_hide">
        <span><?php echo $this->settings[$this->setting_pref . 'showing'] == 'true' ? strtoupper($this->translate('APPTOUCH_HIDE')) : strtoupper($this->translate('APPTOUCH_SHOW')) ?></span>
      </div>
    </div>
  </div>
</div>
<div id='phone_container'
     class="phone_container<?php echo $this->settings[$this->setting_pref . 'showing'] == 'false' ? ' simulator_hidden' : ''?>"
     style="left:<? echo $this->settings[$this->setting_pref . 'pos.x']?>px; top: <? echo $this->settings[$this->setting_pref . 'pos.y']?>px;">
  <div class="top_button_container">
    <div class="turn_off_button"></div>
  </div>
  <div class="left_buttons_container">
    <div></div>
    <div class="volume_up_button"></div>
    <div class="volume_down_button"></div>
    <span class="left_split"></span>
  </div>
  <div class="corpus_container">
    <div class="metal_cover">
      <span class="top_split"></span>

      <div class="plastic_cover">
        <div class="sensor_glass">
          <div id="move_handle">
            <div class="web_cam_n_speaker_container">
              <div class="web_cam">
                <div class="web_cam_optic"></div>
              </div>
              <div class="speaker"></div>
            </div>
          </div>
          <div class="display_container">
            <div class="iphone_display">
              <div class="top_bar_container">
                <div class="antenna_indicator">
                  <div class="antenna_indicator_ level_1"></div>
                  <div class="antenna_indicator_ level_2"></div>
                  <div class="antenna_indicator_ level_3"></div>
                  <div class="antenna_indicator_ level_4"></div>
                  <div class="antenna_indicator_ level_5"></div>
                </div>
                <div class="connection_3g_indicator">3G</div>
                <div class="battery_indicator">
                  <div class="battery_icon_base">
                    <div class="battery_indicator_bar"></div>
                  </div>
                  <div class="battery_icon_nose"></div>
                </div>
              </div>
              <div class="browser_address_bar">
                <div class='address_input_container'>
                  <div class="ips_address_bar">
                    <span class="base-url"><?php echo $site_url ?>/</span>
                    <input id="ips_address_bar" type="text" name="address_bar" class="address_bar_input"/>
                  </div>
                  <div id="refresh_stop_button" class='stop_button'>
                    <div class='button_icon1'></div>
                    <div class='button_icon2'></div>
                  </div>
                </div>
              </div>
              <div class="browser_window">
                <iframe id="ips_browser" scrolling="auto" class="browser_iframe" src="" onload="ipsb_onload()"></iframe>
              </div>
              <div class="browser_controls">
                <div class="browser_control back_control">
                  <div class="icon_shadow">
                  </div>
                  <div class="control_icon">
                  </div>
                </div>
                <div class="browser_control forward_control">
                  <div class="icon_shadow">
                  </div>
                  <div class="control_icon">
                  </div>
                </div>
                <div class="browser_control new_window_control">
                  <div class="control_icon">
                  </div>
                  <div class="control_icon">
                  </div>
                </div>
                <div class="browser_control bookmark_control">
                  <div class="control_icon">
                  </div>
                  <div class="control_icon">
                  </div>
                </div>
                <div class="browser_control tab_switch_control">
                  <div class="control_icon">
                  </div>
                  <div class="control_icon">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="main_button_container">
            <div class="main_button">
              <div class='main_button_icon'></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="right_buttons_container">
    <span class="right_split"></span>
  </div>
</div>
<style type="text/css">
.phone_container {
  float: left;
  position: absolute;
  z-index: 999;
  -moz-transition: -moz-transform 0.4s ease 0s;
  -webkit-transition: -webkit-transform 0.4s ease 0s;
  -o-transition: -o-transform 0.4s ease 0s;
  -ms-transition: -ms-transform 0.4s ease 0s;
}

.phone_container.simulator_hidden {
  display: none;
}

.top_button_container {
  height: 4px;
}

.turn_off_button {
  background-color: #F4F3F2;
  box-shadow: 0 0 1px #92908E;
  display: block;
  float: right;
  height: 4px;
  margin-right: 64px;
  width: 60px;
}

.main_button {
  background-color: #161616;
  border: 1px solid #151515;
  border-radius: 36px;
  box-shadow: 0 0 1px #262626;
  height: 70px;
  margin-left: 129px;
  width: 70px;
  background: -moz-linear-gradient(left center, #646464, #161616, #010000);
  background: -webkit-linear-gradient(left, #646464, #161616, #010000);
  background: -o-linear-gradient(left, #646464, #161616, #010000);
  background: -ms-linear-gradient(left, #646464, #161616, #010000);
}

.main_button_container {
  overflow: hidden;
  padding-bottom: 16px;
  padding-top: 23px;
  text-align: center;
}

.corpus_container {
  border-radius: 55px;
  box-shadow: 0 0 1px #92908E;
}

.metal_cover {
  background-color: #F4F3F2;
  border: 1px solid #92908E;
  border-radius: 55px;
  box-shadow: 0 0 1px #92908E inset;
  padding: 3px;
}

.top_split {
  background-color: #504E4B;
  border-color: #2F2D2D #7F7C7B;
  border-left: 1px solid #7F7C7B;
  border-right: 1px solid #7F7C7B;
  border-style: solid;
  border-width: 1px;
  box-shadow: 0 1px 2px #504E4B;
  display: block;
  height: 2px;
  margin-left: 117px;
  margin-top: -4px;
  width: 3px;
}

.plastic_cover {
  background-color: #3B3B3C;
  border: 1px solid #404040;
  border-radius: 52px;
  box-shadow: 0 0 3px #060707 inset;
  padding: 3px;
}

.sensor_glass {
  background-color: #010000;
  border: 1px solid #060505;
  border-radius: 48px;
  padding: 13px;
  box-shadow: 0 0 1px #000000;
  background: -moz-linear-gradient(#000000, #101010);
  background: -webkit-linear-gradient(#000000, #101010);
  background: -o-linear-gradient(#000000, #101010);
  background: -ms-linear-gradient(#000000, #101010);
}

#move_handle {
  overflow: hidden;
  cursor: move
}

.web_cam_n_speaker_container {
  margin-bottom: 49px;
  margin-top: 35px;
  overflow: hidden;
}

.web_cam, .speaker {
  border: 5px solid;
  border-radius: 9px;
  height: 8px;
  background-color: #787878;
  border-bottom-color: #404040;
  border-left-color: #0B0B0B;
  border-right-color: #6A6A6B;
  border-top-color: #101010;
  float: left;
}

.web_cam {
  margin-left: 102px;
  width: 8px;
}

.web_cam_optic {
  background-color: #05243C;
  border: 3px solid;
  border-radius: 6px;
  height: 2px;
  width: 2px;
  border-top-color: #3c2b90;
  border-left-color: #2b5d90;
  border-right-color: #091321;
  border-bottom-color: #1b3d70;
  -moz-transform: rotate(30deg);
  -webkit-transform: rotate(30deg);
  -o-transform: rotate(30deg);
  -ms-transform: rotate(30deg);
}

.speaker {
  margin-left: 19px;
  width: 62px;
}

.display_container {
  background-color: #0E0E0D;
  border-radius: 5px;
  padding: 5px;
}

.iphone_display {
  height: 480px;
  width: 320px;
}

.top_bar_container {
  background-color: #080A0A;
  padding: 4px 3px;
  overflow: hidden;
}

.antenna_indicator,
.connection_3g_indicator,
.battery_indicator {
  height: 10px;
}

.antenna_indicator {
  width: 19px;
  float: left;
}

.antenna_indicator_ {
  width: 3px;
  background-color: #d3d3d3;
  float: left;
}

.antenna_indicator_ + .antenna_indicator_ {
  margin-left: 1px;
}

.antenna_indicator_.level_1 {
  margin-top: 8px;
  height: 2px;
}

.antenna_indicator_.level_2 {
  margin-top: 6px;
  height: 4px;
}

.antenna_indicator_.level_3 {
  margin-top: 4px;
  height: 6px;
}

.antenna_indicator_.level_4 {
  margin-top: 2px;
  height: 8px;
}

.antenna_indicator_.level_5 {
  height: 10px;
}

.battery_indicator {
  width: 21px;
  float: right;
}

.battery_icon_base {
  border: 1px solid #D3D3D3;
  float: left;
  padding: 1px;
  width: 15px;
}

.battery_indicator_bar {
  background-color: #D3D3D3;
  height: 6px;
}

.battery_icon_nose {
  border: 1px solid #D3D3D3;
  float: left;
  height: 2px;
  margin: 3px 0;
  width: 1px;
  border-left-width: 0;
}

.connection_3g_indicator {
  color: #D3D3D3;
  float: left;
  font-family: arial;
  font-size: 14px;
  font-weight: bold;
  line-height: 10px;
  margin-left: 56px;
}

.browser_address_bar {
  background: -moz-linear-gradient(#B3BFCE, #6D86A4);
  background: -webkit-linear-gradient(#B3BFCE, #6D86A4);
  background: -o-linear-gradient(#B3BFCE, #6D86A4);
  background: -ms-linear-gradient(#B3BFCE, #6D86A4);
  border-top: 1px solid #CDD5DF;
  height: 58px;
}

.browser_window {
  background-color: #25262B;
  height: 353px;
  overflow: hidden;
}

.browser_iframe {
  border: medium none;
  display: block;
  height: 353px;
  width: 337px;
}

.browser_controls {
  background: -moz-linear-gradient(#B0BCCD, #6D83A1);
  background: -webkit-linear-gradient(#B0BCCD, #6D83A1);
  background: -o-linear-gradient(#B0BCCD, #6D83A1);
  background: -ms-linear-gradient(#B0BCCD, #6D83A1);
  border-top: 1px solid #D8DEE6;
  height: 47px;
}

.address_input_container {
  background-color: #FFFFFF;
  border: 1px solid #576E8A;
  border-radius: 6px 6px 6px 6px;
  box-shadow: 0 2px 3px #888888 inset;
  height: 29px;
  margin: 20px 6px 0;
  cursor: text;
  position: relative;

}

.address_bar_form {
  display: block;
}

div.ips_address_bar {
  height: 27px;
  position: absolute;
  width: 90%;
  overflow: hidden;
  white-space: nowrap;
}

input.address_bar_input[type="text"] {
  background: none repeat scroll 0 0 transparent;
  border: medium none;
  display: inline;
  height: 27px;
  padding: 0;
}

div.ips_address_bar_focus input.address_bar_input[type="text"] {
  width: 100%;
  display: block;
}

div.ips_address_bar span.base-url {
  color: lightgray;
  display: inline;
  float: left;
  font-weight: bold;
  height: 27px;
  line-height: 27px;
}

div.ips_address_bar_focus span.base-url {
  display: none;
}

.refresh_button .button_icon1 {
  -moz-transform: rotate(45deg);
  -webkit-transform: rotate(45deg);
  -o-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  border-radius: 14px;
  border: 2px solid #8596AA;
  border-top-color: transparent;
  height: 10px;
  width: 10px;
}

.refresh_button,
.stop_button {
  float: right;
  padding: 9px 6px 6px 11px;
  position: relative;
}

.stop_button {
  height: 14px;
  overflow: hidden;
  width: 14px;
}

.refresh_button .button_icon2 {
  border: 3px solid #8596AA;
  border-left-width: 4px;
  height: 0;
  left: 18px;
  position: absolute;
  top: 7px;
  width: 0;
  border-top-color: transparent;
  border-right-color: transparent;
  border-bottom-color: transparent;
}

.stop_button .button_icon1,
.stop_button .button_icon2 {
  background: none repeat scroll 0 0 #8596AA;
  border-radius: 2px;
  height: 18px;
  margin-left: 6px;
  position: absolute;
  width: 3px;
  margin-left: 5px;
  margin-top: -2px;
}

.stop_button .button_icon1 {
  -moz-transform: rotate(45deg);
}

.stop_button .button_icon2 {
  -moz-transform: rotate(135deg);
}

.main_button_icon {
  border: 3px solid #A0A0A0;
  border-radius: 5px 5px 5px 5px;
  box-shadow: 0 0 1px #000000;
  height: 20px;
  margin-left: 22px;
  margin-top: 22px;
  opacity: 0.8;
  width: 20px;
}

.stop_button,
.refresh_button,
.main_button {
  cursor: pointer;
}

.browser_control {
  width: 20%;
  background: transparent;
  float: left;
  height: 47px;
  position: relative;
}

.browser_control .icon_shadow,
.browser_control .control_icon {
  height: 0;
  width: 0;
  display: block;
  margin-top: 14px;
  margin-left: 25px;
  position: absolute;
  border: 9px solid transparent;
}

.back_control .control_icon {
  border-right: 15px solid #ffffff;
  border-left: none;
}

.forward_control .control_icon {
  border-left: 15px solid #ffffff;
  border-right: none;
}

.new_window_control .control_icon + .control_icon {
  border: none;
  border-top: 1px solid;
  height: 18px;
  width: 4px;
  background-color: #fff;
  margin-left: 32px;
  margin-top: 13px;
}

.new_window_control .control_icon {
  border: none;
  border-top: 1px solid;
  width: 18px;
  height: 4px;
  background-color: #fff;
  margin-top: 20px;
}

.back_control .icon_shadow {
  border-right: 15px solid #333;
  border-left: none;
  margin-top: 13px;
}

.forward_control .icon_shadow {
  border-left: 15px solid #333;
  border-right: none;
  margin-top: 13px;
  margin-left: 24px;
}

  /* Toolbar Styles */
#ip_simulator_toolbar.metal_cover {
  border-radius: 6px 6px 6px 6px;
  left: 0;
  margin: 3px;
  position: fixed;
  top: 150px;
}

#ip_simulator_toolbar .plastic_cover .sensor_glass {
  padding: 0;
  border-radius: 0;
}

#ip_simulator_toolbar .plastic_cover {
  border-radius: 3px 3px 3px 3px;
  padding: 0;
}

.ipst_button {
  background-color: transparent;
  border-radius: 3px 3px 3px 3px;
  color: #A0A0A0;
  font-family: Lucida Console;
  font-size: 13px;
  height: 24px;
  width: 24px;
  line-height: 24px;
  text-shadow: 0 0 1px #000000;
  cursor: pointer;
  text-align: center;
}

.ipst_label {
  color: #A0A0A0;
  font-size: 9px;
  height: 10px;
  text-align: center;
  width: 24px;
}

#ip_simulator_toolbar .ipst_button+.ipst_button {
  border-top: 1px groove #151515;
}

.ipst_button_close {
  /*padding-left: 7px;*/
  /*width: 17px;*/
}

.show_hide {
  font-size: 9px;
}

.ipst_button a {
  line-height: 32px;
  text-align: center;
}

.ipst_zoom_slider {
  border-right: 2px dotted #A0A0A0;
  height: 100px;
  width: 22px;
}

.ipst_zoom_slider_knob {
  width: 0;
  height: 0;
  border-left-width: 22px;
  border-top-width: 4px;
  border-right-width: 0;
  border-bottom-width: 4px;
  border-color: transparent;
  border-left-color: #A0A0A0;
  cursor: pointer;
}
</style>