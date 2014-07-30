<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: delete.tpl 17.08.12 06:04 michael $
 * @author     Michael
 */
?>

<form method="post" class="global_form_popup" action="<?php echo $this->url(array()) ?>">
  <div>
    <h3><?php echo $this->translate("HEQUESTION_ADMIN_DELETE_TITLE") ?></h3>
    <p>
      <?php echo $this->translate("HEQUESTION_ADMIN_DELETE_DESCRIPTION") ?>
    </p>
    <br />
    <p>
      <input type="hidden" name="confirm" value="<?php echo $this->hequestion_id?>"/>
      <button type='submit'><?php echo $this->translate("Delete") ?></button>
      <?php echo $this->translate("or") ?>
      <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?>
      </a>
    </p>
  </div>
</form>

<?php if( @$this->closeSmoothbox ): ?>
  <script type="text/javascript">
    TB_close();
  </script>
<?php endif; ?>
