<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: viewcomment.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */


$this->headScript()
    ->appendFile($this->wallBaseUrl() . 'application/modules/Wall/externals/scripts/core.js');

?>




<?php if( !isset($this->form) ) return; ?>

<script type="text/javascript">

    var form_ajax = function (){

      $('wall-comment-form').getElement('form').addEvent('submit', function (e){

        e.stop();

        var loader = new Wall.OverLoader($('global_content_simple'), 'loader2', {is_smoothbox: true});
        loader.show();

        Wall.request('<?php echo $this->url(array('action' => 'comment'), 'wall_extended', true)?>', $(this).toQueryString(), function (obj){

          loader.hide();

          if (window.parent && window.parent.Wall){

            window.parent.Wall.dialog.message(obj.message||obj.error, obj.status);

            if (obj.html){
              $('wall-comment-form').set('html', obj.html);
              form_ajax();
            }
            if (obj.status){
              window.parent.Wall.instances.getAll().each(function (item){
                var $item = item.getFeed().getChildren('li[rev=item-'+obj.id+']')[0];
                $item.set('html', obj.body);
                item.initAction($item);
              });
              window.parent.Smoothbox.close();
            }
          }
          }.bind(this));

      });

   };

  Wall.runonce.add(form_ajax);

</script>


<div id="wall-comment-form-container" style="padding:10px;width:350px;">
  
  <h3><?php echo $this->translate("Comment:") ?></h3>

  <div id="wall-comment-form">
    <?php echo $this->form->render($this) ?>
  </div>

  <script type="text/javascript">
    Wall.runonce.add(function (){
      document.getElementsByTagName('form')[0].style.display = 'block';
    });
  </script>

</div>