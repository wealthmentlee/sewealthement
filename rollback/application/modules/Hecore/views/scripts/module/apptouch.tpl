<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hecore
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: license.tpl 2010-07-02 17:53 ermek $
 * @author     Ermek
 */
?>
<?php if ($this->redirect) : ?>
<p style="padding-left: 5px; padding-top: 5px;">
    <?php echo $this->translate('Please Wait'); ?>
</p>
<script type="text/javascript">
    setTimeout(function () {
        parent.location.href = parent.location.href;
        parent.Smoothbox.close();
    }, 500);
</script>
<?php endif; ?>
