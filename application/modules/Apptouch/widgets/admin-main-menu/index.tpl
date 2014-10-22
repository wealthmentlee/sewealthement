<h2><?php echo $this->translate("APPTOUCH_New Touch-Mobile Plugin") ?></h2>
<script type="text/javascript">
  //<![CDATA[
  function configureCSS() {
    new Request.JSON({
      url:'<?php echo $this->url(array('action' => 'change-environment-mode'), 'admin_default', true) ?>',
      method:'post',
      onSuccess:function (responseJSON) {
        if ($type(responseJSON) == 'object') {
          if (responseJSON.success || !$type(responseJSON.error))
            new Request.HTML({
              onSuccess:function (r) {
                new Request.JSON({
                  url:'<?php echo $this->url(array('action' => 'change-environment-mode'), 'admin_default', true) ?>',
                  method:'post',
                  onSuccess:function (responseJSON) {
                    if ($type(responseJSON) == 'object') {
                      if (responseJSON.success || !$type(responseJSON.error))
                        window.location.href = window.location.href;
                      else
                        alert(responseJSON.error);
                    } else
                      alert('An unknown error occurred; changes have not been saved.');
                  }
                }).send('format=json&environment_mode=production');
              }
            }).send();
          else
            alert(responseJSON.error);
        } else
          alert('An unknown error occurred; changes have not been saved.');
      }
    }).send('format=json&environment_mode=development');
  }
  window.addEvent('domready', function (e) {
    if ($('configure_css').getStyle('display') != 'none')
      configureCSS();
  });
  //]]>
</script>
<h3 id="configure_css" style="text-decoration: blink;"><?php echo $this->translate('APPTOUCH_Please Wait...') ?></h3>
<?php if (count($this->navigation)): ?>
<div class='tabs'>
  <?php
  echo $this->navigation()->menu()->setContainer($this->navigation)->render()
  ?>
</div>
<?php endif; ?>
