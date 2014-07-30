<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: search.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<?php
  if( !empty($this->query) && ( is_string($this->query) || is_array($this->query)) ) {
    $query = $this->query;

    if (is_array($query)) {
      unset($query['page']);
      $query = http_build_query($query);
    }
    if( $query ) $query = '?' . $query;
  } else {
    $query = '';
  }
  
  // Add params
  $params = ( !empty($this->params) && is_array($this->params) ? $this->params : array() );
  unset($params['page']);
?>

<?php if( $this->pageCount > 1 ): ?>
  <div class="pages">
    <ul class="paginationControl">
			<li class="paginator_previous">
				<?php if( $this->previous ): ?>
						<?php echo $this->htmlLink(array_merge($params, array(
							'reset' => false,
							'page' => ( $this->pageAsQuery ? null : $this->previous ),
							'QUERY' => $query . ( $this->pageAsQuery ? '&page=' . $this->previous : '' ),
						)), $this->translate('<img src="application/modules/Mobile/themes/' . $this->mobileActiveTheme()->name . '/images/prev.png" alt="' . $this->translate('Prev') . '"/>')) ?>
				<?php else: ?>
					<span><img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/prev_disabled.png" alt="<?php echo $this->translate('Prev') ?>"/></span>
				<?php endif; ?>
			</li>

			<li class="paginator_middle">
				<span>
					<?php echo $this->translate('MOBILE_Page %1$s of %2$s', $this->current, $this->pageCount) ?>
				</span>
			</li>

			<li class="paginator_next">
				<?php if ( $this->next): ?>
						<?php echo $this->htmlLink(array_merge($params, array(
							'reset' => false,
							'page' => ( $this->pageAsQuery ? null : $this->next ),
							'QUERY' => $query . ( $this->pageAsQuery ? '&page=' . $this->next : '' ),
						)), $this->translate('<img src=\'application/modules/Mobile/themes/' . $this->mobileActiveTheme()->name . '/images/next.png\' alt=\'' . $this->translate('Next') . '\'/>')) ?>
				<?php else: ?>
					<span><img src="application/modules/Mobile/themes/<?php echo $this->mobileActiveTheme()->name; ?>/images/next_disabled.png" alt="<?php echo $this->translate('Next') ?>"/></span>
				<?php endif; ?>
			</li>

    </ul>
  </div>
	<div class="clr"></div>
<?php endif; ?>
