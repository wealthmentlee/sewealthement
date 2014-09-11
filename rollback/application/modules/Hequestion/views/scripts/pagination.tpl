<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Hequestion
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: pagination.tpl 17.08.12 06:04 michael $
 * @author     Michael
 */
?>

<?php
  // Parse query and remove page
  if( !empty($this->query) && ( is_string($this->query) || is_array($this->query)) ) {
    $query = $this->query;
    if( is_string($query) ) $query = parse_str(trim($query, '?'));
    unset($query['page']);
    $query = http_build_query($query);
    if( $query ) $query = '?' . $query;
  } else {
    $query = '';
  }
  // Add params
  $params = ( !empty($this->params) && is_array($this->params) ? $this->params : array() );
  unset($params['page']);
?>


<?php if( $this->pageCount > 1 ): ?>

  <div class="hequestion_pagination">

    <div class="pages">
      <ul class="paginationControl">
        <?php if( isset($this->previous) ): ?>
          <li>
            <?php echo $this->htmlLink(array_merge($params, array(
              'reset' => false,
              'page' => ( $this->pageAsQuery ? null : $this->previous ),
              'QUERY' => $query . ( $this->pageAsQuery ? '&page=' . $this->previous : '' ),
            )), $this->translate('&#171; Previous'), array('onclick' => "Hequestion.requestHTML('".$this->ajax_url."', null, $$('.".$this->ajax_class."')[0], {'page': ".$this->previous."});$(this).getParent('.hequestion_pagination').getElement('.hequestion_pagination_loader').addClass('active');return false;")) ?>
          </li>
        <?php endif; ?>
        <?php if (empty($this->mini)):?>
          <?php foreach ($this->pagesInRange as $page): ?>
            <?php if ($page != $this->current): ?>
              <li>
                <?php echo $this->htmlLink(array_merge($params, array(
                  'reset' => false,
                  'page' => ( $this->pageAsQuery ? null : $page ),
                  'QUERY' => $query . ( $this->pageAsQuery ? '&page=' . $page : '' ),
                )), $page, array('onclick' => "Hequestion.requestHTML('".$this->ajax_url."', null, $$('.".$this->ajax_class."')[0], {'page': ".$page."});$(this).getParent('.hequestion_pagination').getElement('.hequestion_pagination_loader').addClass('active');return false;")) ?>
              </li>
            <?php else: ?>
              <li class="selected">
                <a href='javascript:void(0)'><?php echo $page; ?></a>
              </li>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php endif;?>
        <?php if (isset($this->next)): ?>
          <li>
            <?php echo $this->htmlLink(array_merge($params, array(
              'reset' => false,
              'page' => ( $this->pageAsQuery ? null : $this->next ),
              'QUERY' => $query . ( $this->pageAsQuery ? '&page=' . $this->next : '' ),
            )), $this->translate('Next &#187;'), array('onclick' => "Hequestion.requestHTML('".$this->ajax_url."', null, $$('.".$this->ajax_class."')[0], {'page': ".$this->next."});$(this).getParent('.hequestion_pagination').getElement('.hequestion_pagination_loader').addClass('active');return false;")) ?>
          </li>
        <?php endif; ?>
      </ul>
      
      <div class="hequestion_pagination_loader"></div>

    </div>

  </div>

<?php endif; ?>

