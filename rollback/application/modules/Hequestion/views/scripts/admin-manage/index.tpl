<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 17.08.12 06:04 michael $
 * @author     Michael
 */
?>


<script type="text/javascript">

function multiDelete()
{
  return confirm("<?php echo $this->translate("HEQUESTION_ADMIN_DELETE_CONFIRM") ?>");
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

<h2><?php echo $this->translate("HEQUESTION_ADMIN_HEQUESTION_TITLE") ?></h2>

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
  <?php echo $this->translate("HEQUESTION_VIEWS_SCRIPTS_ADMINMANAGE_INDEX_DESCRIPTION") ?>
</p>

<br />
<?php if( count($this->paginator) ): ?>
  <form id='multidelete_form' method="post" action="<?php echo $this->url();?>" onSubmit="return multiDelete()">
  <table class='admin_table'>
    <thead>
      <tr>
        <th class='admin_table_short'><input onclick='selectAll();' type='checkbox' class='checkbox' /></th>
        <th class='admin_table_short'>ID</th>
        <th><?php echo $this->translate("HEQUESTION_Title") ?></th>
        <th><?php echo $this->translate("HEQUESTION_Owner") ?></th>
        <th><?php echo $this->translate("HEQUESTION_Votes") ?></th>
        <th><?php echo $this->translate("HEQUESTION_Date") ?></th>
        <th><?php echo $this->translate("HEQUESTION_Options") ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($this->paginator as $item): ?>
        <tr>
          <td><input type='checkbox' class='checkbox' name='delete_<?php echo $item->question_id;?>' value='<?php echo $item->question_id ?>' /></td>
          <td><?php echo $item->question_id ?></td>
          <td title="<?php echo $this->escape($item->getTitle()) ?>">
            <a href="<?php echo $item->getHref()?>" target="_blank"><?php echo $this->string()->truncate($item->getTitle(), 48) ?></a>
          </td>
          <td><a href="<?php echo $item->getOwner()->getHref();?>" target="_blank"><?php echo $item->getOwner()->getTitle() ?></a></td>
          <td><?php echo $item->vote_count ?></td>
          <td><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
          <td>
            <a href="<?php echo $this->url(array('question_id' => $item->question_id), 'hequestion_view') ?>">
              <?php echo $this->translate("view") ?>
            </a>
            |
            <?php echo $this->htmlLink(
                array('route' => 'default', 'module' => 'hequestion', 'controller' => 'admin-manage', 'action' => 'delete', 'id' => $item->question_id),
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
      <?php echo $this->translate("HEQUESTION_There are no questions created yet.") ?>
    </span>
  </div>
<?php endif; ?>
