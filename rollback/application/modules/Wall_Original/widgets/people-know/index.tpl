<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Wall
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 18.06.12 10:52 michael $
 * @author     Michael
 */
?>

<?php if (!$this->feedOnly):?>

<script type="text/javascript">

  en4.core.runonce.add(function (){
    if (Wall.rolldownload){
      window.addEvent('scroll', function (){
        var link = $$('.wall-stream-welcome.is_active .utility-viewall .pagination a:not(.wall_feed_loading)')[0];
        if (!link){
          return ;
        }
        if (window.getScrollTop()+5 >= window.getScrollSize().y - window.getSize().y){
          link.fireEvent('click');
        }
      });
    }
  });

  function wallPeopleKnow()
  {

    $$('.wall_people_know .utility-viewall .pagination a').addEvent('click', function (){

      var c = $(this).getParent('.wall_people_know');

      $(this).addClass('wall_feed_loading');

      var $loader = $(this).getParent('.utility-viewall').getElement('.loader');
      var loader = new Wall.Loader($loader);

      $(this).getParent('.wall_people_know').getElement('.utility-viewall .pagination').setStyle('display', 'none');
      loader.show();

      var url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'widget', 'action' => 'index', 'name' => 'wall.people-know'), 'default', true) ?>';


      Wall.requestHTML(url + '?page=' + $(this).get('rev') + '&feedOnly=1', function (html){

        c.getElements('.utility-viewall').destroy();

        var ul_temp = new Element('ul', {'html': html});
        var ul = c.getElement('ul');
        ul_temp.getChildren().each(function (item){
          item.inject(ul, 'bottom');
        });

        wallPeopleKnow();

      });

    });
  }


  en4.core.runonce.add(wallPeopleKnow);

</script>


<div class="wall_people_know">
  <ul>


<?php endif;?>


    <?php foreach ($this->paginator as $user):?>
      <li>
        <div class="item_photo">
          <?php echo $this->itemPhoto($user, 'thumb.normal');?>
        </div>
        <div class="item_body">
          <div class="item_title">
            <a href="<?php echo $user->getHref();?>"><?php echo $user->getTitle();?></a>
          </div>
          <div class="item_description">
            <?php
              $mutual_friends = Engine_Api::_()->wall()->getMutualFriendsPaginator($user, $this->viewer());
            ?>
            <?php if ($mutual_friends->getTotalItemCount() > 0):?>
              <?php
                $items = $mutual_friends->getCurrentItems();
              ?>
              <?php if ($mutual_friends->getTotalItemCount() == 1):?>
                <?php echo $this->translate('WALL_%1$s is a mutual friend', '<a href="'.$items[0]->getHref().'">' . $items[0]->getTitle() . '</a>' );?>
              <?php elseif ($mutual_friends->getTotalItemCount() == 2): ?>
                <?php echo $this->translate('WALL_%1$s and %2$s are mutual friends', array('<a href="'.$items[0]->getHref().'">' . $items[0]->getTitle() . '</a>', '<a href="'.$items[1]->getHref().'">' . $items[1]->getTitle() . '</a>'));?>
              <?php elseif ($mutual_friends->getTotalItemCount() > 2):?>
                <?php echo $this->translate('WALL_%1$s and %2$s other mutual friends', array('<a href="'.$items[0]->getHref().'">' . $items[0]->getTitle() . '</a>', $mutual_friends->getTotalItemCount()));?>
              <?php endif;?>
            <?php endif;?>
          </div>
        </div>
        <div class="item_options">
          <?php echo $this->userFriendship($user);?>
        </div>
      </li>
    <?php endforeach; ?>

    <?php if (isset($this->paginator->getPages()->next) && $this->paginator->getPages()->next):?>
      <li class="utility-viewall">
        <div class="pagination">
          <a href="javascript:void(0);" rev="<?php echo $this->paginator->getPages()->next;?>"><?php echo $this->translate('View More');?></a>
        </div>
        <div class="loader" style="display: none;">
          <div class="wall_icon"></div>
          <div class="text">
            <?php echo $this->translate('Loading ...')?>
          </div>
        </div>
      </li>
    <?php endif;?>

<?php if (!$this->feedOnly):?>

  </ul>
</div>

<?php endif;?>