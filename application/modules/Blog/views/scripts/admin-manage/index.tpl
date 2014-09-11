<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     Jung
 */
?>

<script type="text/javascript">

function multiDelete()
{
  return confirm("<?php echo $this->translate('Are you sure you want to delete the selected blog entries?');?>");
}

function selectAll()
{
  var i;
  var multidelete_form = $('multidelete_form');
  var inputs = multidelete_form.elements;
  for (i = 1; i < inputs.length; i++) {
    if (!inputs[i].disabled) {
      inputs[i].checked = inputs[0].checked;
    }
  }
}
</script>

<h2>
  <?php echo $this->translate('Blogs Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      //->setUlClass()
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p>
  <?php echo $this->translate("BLOG_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>

<?php
$settings = Engine_Api::_()->getApi('settings', 'core');
if( $settings->getSetting('user.support.links', 0) == 1 ) {
	echo 'More info: <a href="http://support.socialengine.com/questions/199/Admin-Panel-Plugins-Blogs" target="_blank">See KB article</a>.';	
} 
?>	
<br />	
<br />

<?php if( count($this->paginator) ): ?>
<form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
<table class='admin_table'>
  <thead>
    <tr>
      <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
      <th class='admin_table_short'>ID</th>
      <th><?php echo $this->translate("Title") ?></th>
      <th><?php echo $this->translate("Owner") ?></th>
      <th><?php echo $this->translate("Views") ?></th>
      <th><?php echo $this->translate("Date") ?></th>
      <th><?php echo $this->translate("Options") ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($this->paginator as $item): ?>
      <tr>
        <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->getIdentity(); ?>' value="<?php echo $item->getIdentity(); ?>" /></td>
        <td><?php echo $item->getIdentity() ?></td>
        <td><?php echo $item->getTitle() ?></td>
        <td><?php echo $item->getOwner()->getTitle() ?></td>
        <td><?php echo $this->locale()->toNumber($item->view_count) ?></td>
        <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
        <td>
          <?php echo $this->htmlLink($item->getHref(), $this->translate('view')) ?>
          |
          <?php echo $this->htmlLink(
                array('route' => 'default', 'module' => 'blog', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->blog_id),
                $this->translate("delete"),
                array('class' => 'smoothbox')) ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<br />

<div class='buttons'>
  <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
</div>
</form>

<br/>
<div>
  <?php echo $this->paginationControl($this->paginator); ?>
</div>

<?php else: ?>
  <div class="tip">
    <span>
      <?php echo $this->translate("There are no blog entries by your members yet.") ?>
    </span>
  </div>
<?php endif; ?>
