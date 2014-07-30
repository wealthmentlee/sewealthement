<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Mobile
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: topic.tpl 2011-02-14 06:58:57 mirlan $
 * @author     Mirlan
 */

?>

<h4>
  &raquo; <?php echo $this->subject->__toString()?>
  &raquo; <?php echo $this->htmlLink(array('route' => 'page_discussion', 'action' => 'index', 'page_id' => $this->subject->getIdentity()), $this->translate('MOBILE_PAGE_DISCUSSION')) ?>
  &raquo; <?php echo $this->topic->getTitle()?>
</h4>

  <div class="mobile_box">

    <?php if ($this->hasViewer):?>

      <?php if ($this->canPost && !$this->topic->closed):?>
        <a href="<?php echo $this->url(array('action' => 'post', 'topic_id' => $this->topic_id, 'return_url' => urlencode($_SERVER['REQUEST_URI'])), 'page_discussion', true)?>" class="post">
          <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_POST')?>
        </a>

        |

      <?php endif;?>

      <?php if ($this->isWatching):?>
        <a href="<?php echo $this->url(array('action' => 'discussion', 'task' => 'watch', 'set' => 0,  'topic_id' => $this->topic_id), 'page_discussion', true)?>" class="unwatching">
          <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_UNWATCHING')?>
        </a>
      <?php else: ?>
         <a href="<?php echo $this->url(array('action' => 'discussion', 'task' => 'watch', 'set' => 1,  'topic_id' => $this->topic_id), 'page_discussion', true)?>" class="watching">
          <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_WATCHING')?>
        </a>
      <?php endif;?>

      <?php if ($this->isTeamMember):?>

        |

        <?php if ($this->topic->sticky):?>
        <a href="<?php echo $this->url(array('action' => 'discussion', 'task' => 'sticky', 'set' => 0,  'topic_id' => $this->topic_id), 'page_discussion', true)?>" class="unsticky">
          <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_UNSTICKY')?>
        </a>
        <?php else: ?>
          <a href="<?php echo $this->url(array('action' => 'discussion', 'task' => 'sticky', 'set' => 1,  'topic_id' => $this->topic_id), 'page_discussion', true)?>" class="sticky">
            <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_STICKY')?>
          </a>
        <?php endif;?>

        |

        <?php if ($this->topic->closed):?>
          <a href="<?php echo $this->url(array('action' => 'discussion', 'task' => 'close', 'set' => 0,  'topic_id' => $this->topic_id), 'page_discussion', true)?>" class="unclose">
            <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_UNCLOSE')?>
          </a>
        <?php else: ?>
          <a href="<?php echo $this->url(array('action' => 'discussion', 'task' => 'close', 'set' => 1,  'topic_id' => $this->topic_id), 'page_discussion', true)?>" class="close">
            <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_CLOSE')?>
          </a>
        <?php endif;?>

      <?php endif;?>

      <?php if ($this->isTeamMember || $this->isOwner):?>

        |

      <a href="<?php echo $this->url(array('action' => 'rename', 'topic_id' => $this->topic_id, 'return_url' => urlencode($_SERVER['REQUEST_URI'])), 'page_discussion', true)?>" class="rename">
        <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_RENAME')?>
      </a>

        |

      <a href="<?php echo $this->url(array('action' => 'delete-topic', 'topic_id' => $this->topic_id, 'return_url' => urlencode($_SERVER['REQUEST_URI'])), 'page_discussion', true)?>" class="delete">
        <?php echo $this->translate('PAGEDISCUSSION_OPTIONS_DELETE')?>
      </a>

      <?php endif;?>

      <?php if ($this->topic->closed && !$this->isTeamMember):?>
        <div class="pagediscussion_topic_closed result_message"><?php echo $this->translate('PAGEDISCUSSION_CLOSED');?></div>
      <?php endif;?>

    <?php endif;?>

  </div>

<ul class="items">

<?php foreach ($this->paginator as $post):?>

    <li class="pagediscussion_topic">
      <div class="item_photo">
        <?php
          $owner = $post->getOwner();
          echo $this->htmlLink($owner->getHref(), $owner->getTitle());
          echo $this->htmlLink($owner->getHref(), $this->itemPhoto($owner, 'thumb.icon'));
        ?>
      </div>

      <div class="item_body">

        <div class="item_options">

           <?php if ($this->canPost && !$this->topic->closed):?>
             <a href="<?php echo $this->url(array('action' => 'post', 'post_id' => $post->getIdentity(), 'topic_id' => $this->topic_id, 'return_url' => urlencode($_SERVER['REQUEST_URI'])), 'page_discussion', true)?>" class="quote">
              <?php echo $this->translate('PAGEDISCUSSION_POST_QUOTE')?>
            </a>
          <?php endif;?>
          <?php if ($this->hasViewer && ($owner->isSelf($this->viewer) || $this->isTeamMember)):?>
            <a href="<?php echo $this->url(array('action' => 'edit', 'post_id' => $post->getIdentity(), 'return_url' => urlencode($_SERVER['REQUEST_URI'])), 'page_discussion', true)?>">
              <?php echo $this->translate('PAGEDISCUSSION_POST_EDIT')?>
            </a>
            <a href="<?php echo $this->url(array('action' => 'delete-post', 'post_id' => $post->getIdentity(), 'return_url' => urlencode($_SERVER['REQUEST_URI'])), 'page_discussion', true)?>">
              <?php echo $this->translate('PAGEDISCUSSION_POST_DELETE')?>
            </a>
          <?php endif;?>

        </div>

        <div class="item_date">
          <?php echo $this->translate('Posted');?> <?php echo $this->timestamp(strtotime($post->creation_date)) ?>
        </div>

        <?php echo nl2br($this->BBCode($post->body)) ?>

      </div>

    </li>

<?php endforeach;?>

</ul>

<?php if( $this->paginator->count() > 1 ): ?>
  <?php echo $this->paginationControl($this->paginator, null, array('pagination/search.tpl', 'mobile')); ?>
<?php endif; ?>
