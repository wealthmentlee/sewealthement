<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Rate
 * @copyright  Copyright Hire-Experts LLC
 * @license    http://www.hire-experts.com
 * @version    $Id: index.tpl 2010-07-02 17:53 ermek $
 * @author     Ermek
 */
?>

<link href='<?php echo $this->baseUrl().'/application/css.php?request=application/modules/Rate/externals/styles/main.css'; ?>' rel='stylesheet' type="text/css" />
<h2>
  <?php echo $this->translate('Rate Plugin FAQ'); ?>
</h2>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<p class="faq_desc">
  <?php echo $this->translate("RATE_ADMIN_MANAGE_FAQ") ?>
</p>
<br />

<ul class="rate_faq_list">
  <li>
    <a href="javascript://" class="faq_question" onclick="$(this).getNext('.faq_answer').toggleClass('faq_hidden'); this.blur();"><?php echo $this->translate("Members Page."); ?></a>
    <div class="faq_answer faq_hidden">
      <div class="faq_file_src"><?php echo $this->translate('File:'); ?>application\modules\User\views\scripts\_browseUsers.tpl</div>
      <div class="faq_file_line"><?php echo $this->translate('Line:') ?>27</div>
      <div class="faq_snippet_old_code">
        <p>&lt;div class='browsemembers_results_info'&gt;<br>&nbsp; &lt;?php echo $this-&gt;htmlLink($user-&gt;getHref(), $user-&gt;getTitle()) ?&gt;<br>&nbsp; &lt;?php echo $user-&gt;status; ?&gt;<br>&nbsp; &lt;?php if( $user-&gt;status != "" ): ?&gt;<br>&nbsp;&nbsp;&nbsp; &lt;div&gt;<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &lt;?php echo $this-&gt;timestamp($user-&gt;status_date) ?&gt;<br>&nbsp;&nbsp;&nbsp; &lt;/div&gt;<br>&nbsp; &lt;?php endif; ?&gt;<br>&lt;/div&gt;</p>
      </div>
      <div class="faq_snippet_code">
        <p>&lt;div class='browsemembers_results_info'&gt;<br>&nbsp; &lt;?php echo $this-&gt;htmlLink($user-&gt;getHref(), $user-&gt;getTitle()) ?&gt;<br>&nbsp; &lt;?php echo $user-&gt;status; ?&gt;<br>&nbsp; &lt;?php if( $user-&gt;status != "" ): ?&gt;<br>&nbsp;&nbsp;&nbsp; &lt;div&gt;<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &lt;?php echo $this-&gt;timestamp($user-&gt;status_date) ?&gt;<br>&nbsp;&nbsp;&nbsp; &lt;/div&gt;<br>&nbsp; &lt;?php endif; ?&gt;<br>&lt;/div&gt;<br><br>&lt;div class="rate_user_item"&gt;<br>&nbsp; &lt;?php echo $this-&gt;itemRate('user', $user-&gt;getIdentity()); ?&gt;<br>&lt;/div&gt;</p>
      </div>
    </div>
  </li>
  <li>
    <a href="javascript://" class="faq_question" onclick="$(this).getNext('.faq_answer').toggleClass('faq_hidden'); this.blur();"><?php echo $this->translate("Album Photos Page."); ?></a>
    <div class="faq_answer faq_hidden">
      <div class="faq_file_src"><?php echo $this->translate('File:'); ?>application\modules\Album\views\scripts\album\view.tpl</div>
      <div class="faq_file_line"><?php echo $this->translate('Line:') ?>82 (<?php echo $this->translate("RATE_or search red-highlighted text via editor"); ?>)</div>
      <div class="faq_snippet_old_code">
        <p>&lt;a class="thumbs_photo" href="&lt;?php echo $photo-&gt;getHref(); ?&gt;"&gt;<br>&nbsp; &lt;span style="background-image: url(&lt;?php echo $photo-&gt;getPhotoUrl('thumb.normal');   ?&gt;);"&gt;&lt;/span&gt;<br>&lt;/a&gt;</p>
      </div>
      <div class="faq_snippet_code">
        <p>&lt;a class="thumbs_photo" href="&lt;?php echo $photo-&gt;getHref(); ?&gt;"&gt;<br>&nbsp; &lt;span style="background-image: url(&lt;?php echo $photo-&gt;getPhotoUrl('thumb.normal'); ?&gt;);"&gt;&lt;/span&gt;<br>&lt;/a&gt;</p>
        <p>&lt;div class="rate_album_photo_item"&gt;<br>&nbsp; &lt;?php echo $this-&gt;itemRate('album_photo', $photo-&gt;getIdentity()); ?&gt;<br>&lt;/div&gt;</p>
      </div>
    </div>
  </li>
  <li>
    <a href="javascript://" class="faq_question" onclick="$(this).getNext('.faq_answer').toggleClass('faq_hidden'); this.blur();"><?php echo $this->translate("Blogs. Browse Entries Page."); ?></a>
    <div class="faq_answer faq_hidden">
      <div class="faq_file_src"><?php echo $this->translate('File:'); ?>application\modules\Blog\views\scripts\index\index.tpl</div>
      <div class="faq_file_line"><?php echo $this->translate('Line:') ?>57</div>
      <div class="faq_snippet_old_code">
        <p>&lt;span class='blogs_browse_info_title'&gt;<br>&nbsp; &lt;h3&gt;&lt;?php echo $this-&gt;htmlLink($item-&gt;getHref(), $item-&gt;getTitle()) ?&gt;&lt;/h3&gt;<br>&lt;/span&gt;</p>
      </div>
      <div class="faq_snippet_code">
        <p>&lt;span class='blogs_browse_info_title'&gt;<br>&nbsp; &lt;h3&gt;&lt;?php echo $this-&gt;htmlLink($item-&gt;getHref(), $item-&gt;getTitle()) ?&gt;&lt;/h3&gt;<br>&lt;/span&gt;</p>
        <p>&lt;div class="rate_blog_item"&gt;<br>&nbsp; &lt;?php echo $this-&gt;itemRate('blog', $item-&gt;getIdentity()); ?&gt;<br>&lt;/div&gt;</p>
      </div>
    </div>
  </li>
  <li>
    <a href="javascript://" class="faq_question" onclick="$(this).getNext('.faq_answer').toggleClass('faq_hidden'); this.blur();"><?php echo $this->translate("Blogs. My Entries Page."); ?></a>
    <div class="faq_answer faq_hidden">
      <div class="faq_file_src"><?php echo $this->translate('File:'); ?>application\modules\Blog\views\scripts\index\manage.tpl</div>
      <div class="faq_file_line"><?php echo $this->translate('Line:') ?>79</div>
      <div class="faq_snippet_old_code">
        <p>&lt;p class='blogs_browse_info_title'&gt;<br>&nbsp; &lt;?php echo $this-&gt;htmlLink($item-&gt;getHref(), $item-&gt;getTitle()) ?&gt;<br>&lt;/p&gt;</p>
      </div>
      <div class="faq_snippet_code">
        <p>&lt;p class='blogs_browse_info_title'&gt;<br>&nbsp; &lt;?php echo $this-&gt;htmlLink($item-&gt;getHref(), $item-&gt;getTitle()) ?&gt;<br>&lt;/p&gt;</p>
        <p>&lt;div class="rate_blog_item"&gt;<br>&nbsp; &lt;?php echo $this-&gt;itemRate('blog', $item-&gt;getIdentity()); ?&gt;<br>&lt;/div&gt;</p>
      </div>
    </div>
  </li>
  <li>
    <a href="javascript://" class="faq_question" onclick="$(this).getNext('.faq_answer').toggleClass('faq_hidden'); this.blur();"><?php echo $this->translate("Groups. Browse Groups Page."); ?></a>
    <div class="faq_answer faq_hidden">
      <div class="faq_file_src"><?php echo $this->translate('File:'); ?>application\modules\Group\views\scripts\index\browse.tpl</div>
      <div class="faq_file_line"><?php echo $this->translate('Line:') ?>57</div>
      <div class="faq_snippet_old_code">
        <p>&lt;div class="groups_title"&gt;<br>&nbsp; &lt;h3&gt;&lt;?php echo $this-&gt;htmlLink($group-&gt;getHref(), $group-&gt;getTitle()) ?&gt;&lt;/h3&gt;<br>&lt;/div&gt;</p>
      </div>
      <div class="faq_snippet_code">
        <p>&lt;div class="groups_title"&gt;<br>&nbsp; &lt;h3&gt;&lt;?php echo $this-&gt;htmlLink($group-&gt;getHref(), $group-&gt;getTitle()) ?&gt;&lt;/h3&gt;<br>&lt;/div&gt;</p>
        <p>&lt;div class="rate_group_item"&gt;<br>&nbsp; &lt;?php echo $this-&gt;itemRate('group', $group-&gt;getIdentity()); ?&gt;<br>&lt;/div&gt;</p>
      </div>
    </div>
  </li>
  <li>
    <a href="javascript://" class="faq_question" onclick="$(this).getNext('.faq_answer').toggleClass('faq_hidden'); this.blur();"><?php echo $this->translate("Groups. My Groups Page."); ?></a>
    <div class="faq_answer faq_hidden">
      <div class="faq_file_src"><?php echo $this->translate('File:'); ?>application\modules\Group\views\scripts\index\manage.tpl</div>
      <div class="faq_file_line"><?php echo $this->translate('Line:') ?>73</div>
      <div class="faq_snippet_old_code">
        <p>&lt;div class="groups_title"&gt;<br>&nbsp; &lt;h3&gt;&lt;?php echo $this-&gt;htmlLink($group-&gt;getHref(), $group-&gt;getTitle()) ?&gt;&lt;/h3&gt;<br>&lt;/div&gt;</p>
      </div>
      <div class="faq_snippet_code">
        <p>&lt;div class="groups_title"&gt;<br>&nbsp; &lt;h3&gt;&lt;?php echo $this-&gt;htmlLink($group-&gt;getHref(), $group-&gt;getTitle()) ?&gt;&lt;/h3&gt;<br>&lt;/div&gt;</p>
        <p>&lt;div class="rate_group_item"&gt;<br>&nbsp; &lt;?php echo $this-&gt;itemRate('group', $group-&gt;getIdentity()); ?&gt;<br>&lt;/div&gt;</p>
      </div>
    </div>
  </li>
  <li>
    <a href="javascript://" class="faq_question" onclick="$(this).getNext('.faq_answer').toggleClass('faq_hidden'); this.blur();"><?php echo $this->translate("Events. Upcoming and Past Events Page."); ?></a>
    <div class="faq_answer faq_hidden">
      <div class="faq_file_src"><?php echo $this->translate('File:'); ?>application\modules\Event\views\scripts\index\browse.tpl</div>
      <div class="faq_file_line"><?php echo $this->translate('Line:') ?>56</div>
      <div class="faq_snippet_old_code">
        <p>&lt;div class="events_title"&gt;<br>&nbsp; &lt;h3&gt;&lt;?php echo $this-&gt;htmlLink($event-&gt;getHref(), $event-&gt;getTitle()) ?&gt;&lt;/h3&gt;<br>&lt;/div&gt;</p>
      </div>
      <div class="faq_snippet_code">
        <p>&lt;div class="events_title"&gt;<br>&nbsp; &lt;h3&gt;&lt;?php echo $this-&gt;htmlLink($event-&gt;getHref(), $event-&gt;getTitle()) ?&gt;&lt;/h3&gt;<br>&lt;/div&gt;</p>
        <p>&lt;div class="rate_event_item"&gt;<br>&nbsp; &lt;?php echo $this-&gt;itemRate('event', $event-&gt;getIdentity()); ?&gt;<br>&lt;/div&gt;</p>
      </div>
    </div>
  </li>
  <li>
    <a href="javascript://" class="faq_question" onclick="$(this).getNext('.faq_answer').toggleClass('faq_hidden'); this.blur();"><?php echo $this->translate("Events. My Events Page"); ?></a>
    <div class="faq_answer faq_hidden">
      <div class="faq_file_src"><?php echo $this->translate('File:'); ?>application\modules\Event\views\scripts\index\manage.tpl</div>
      <div class="faq_file_line"><?php echo $this->translate('Line:') ?>72</div>
      <div class="faq_snippet_old_code">
        <p>&lt;div class="events_title"&gt;<br>&nbsp; &lt;h3&gt;&lt;?php echo $this-&gt;htmlLink($event-&gt;getHref(), $event-&gt;getTitle()) ?&gt;&lt;/h3&gt;<br>&lt;/div&gt;</p>
      </div>
      <div class="faq_snippet_code">
        <p>&lt;div class="events_title"&gt;<br>&nbsp; &lt;h3&gt;&lt;?php echo $this-&gt;htmlLink($event-&gt;getHref(), $event-&gt;getTitle()) ?&gt;&lt;/h3&gt;<br>&lt;/div&gt;</p>
        <p>&lt;div class="rate_event_item"&gt;<br>&nbsp; &lt;?php echo $this-&gt;itemRate('event', $event-&gt;getIdentity()); ?&gt;<br>&lt;/div&gt;</p>
      </div>
    </div>
  </li>
  <li>
    <a href="javascript://" class="faq_question" onclick="$(this).getNext('.faq_answer').toggleClass('faq_hidden'); this.blur();"><?php echo $this->translate("Articles. Browse Articles"); ?></a>
    <div class="faq_answer faq_hidden">
      <div class="faq_file_src"><?php echo $this->translate('File:'); ?>application\modules\Article\views\scripts\index\browse.tpl</div>
      <div class="faq_file_line"><?php echo $this->translate('Line:') ?>137</div>
      <div class="faq_snippet_old_code">
        <p>&lt;div class='articles_browse_info_date'&gt;</p>
      </div>
      <div class="faq_snippet_code">
        <p>&lt;div class="rate_article_item"&gt;<br> &nbsp; &lt;?php echo $this-&gt;itemRate('article', $item-&gt;getIdentity()); ?&gt;<br> &lt;/div&gt;</p>
        <p>&lt;div class='articles_browse_info_date'&gt;</p>
      </div>
    </div>
  </li>
  <li>
    <a href="javascript://" class="faq_question" onclick="$(this).getNext('.faq_answer').toggleClass('faq_hidden'); this.blur();"><?php echo $this->translate("Articles. My Articles"); ?></a>
    <div class="faq_answer faq_hidden">
      <div class="faq_file_src"><?php echo $this->translate('File:'); ?>application\modules\Article\views\scripts\index\manage.tpl</div>
      <div class="faq_file_line"><?php echo $this->translate('Line:') ?>144</div>
      <div class="faq_snippet_old_code">
        <p>&lt;div class='articles_browse_info_date'&gt;</p>
      </div>
      <div class="faq_snippet_code">
        <p>&lt;div class="rate_article_item"&gt;<br> &nbsp; &lt;?php echo $this-&gt;itemRate('article', $item-&gt;getIdentity()); ?&gt;<br> &lt;/div&gt;</p>
        <p>&lt;div class='articles_browse_info_date'&gt;</p>
      </div>
    </div>
  </li>
  <li>
</ul>



