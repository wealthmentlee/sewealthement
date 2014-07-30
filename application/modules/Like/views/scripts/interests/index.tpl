<div class="headline">
    <h2>
        <?php echo $this->translate('like_My Interests'); ?>
    </h2>
    <div class="tabs">
        <?php
        echo $this->navigation()
                ->menu()
                ->setContainer($this->navigation)
                ->render();
        ?>
    </div>
</div>

<div class="description">
    <?php echo $this->translate('LIKE_INTERESTS_DESC'); ?>
</div>

<?php
$this->headScript()
        ->appendFile($this->baseUrl() . "/externals/autocompleter/Observer.js")
        ->appendFile($this->baseUrl() . "/externals/autocompleter/Autocompleter.js")
        ->appendFile($this->baseUrl() . "/externals/autocompleter/Autocompleter.Local.js")
        ->appendFile($this->baseUrl() . "/externals/autocompleter/Autocompleter.Request.js");

$data = array(
    'page' => array(
        'input' => 'interest_page',
        'list' => 'interests_list_page',
        'selected' => 'selected_interest_page',
    ),
    'event' => array(
        'input' => 'interest_event',
        'list' => 'interests_list_event',
        'selected' => 'selected_interest_event'
    ),
    'classified' => array(
        'input' => 'interest_classified',
        'list' => 'interests_list_classified',
        'selected' => 'selected_interest_classified'
    ),
    'group' => array(
        'input' => 'interest_group',
        'list' => 'interests_list_group',
        'selected' => 'selected_interest_group'
    ),
    'music_playlist' => array(
        'input' => 'interest_music_playlist',
        'list' => 'interests_list_music_playlist',
        'selected' => 'selected_interest_music_playlist'
    ),
    'blog' => array(
        'input' => 'interest_blog',
        'list' => 'interests_list_blog',
        'selected' => 'selected_interest_blog'
    ),
    'document' => array(
        'input' => 'interest_document',
        'list' => 'interests_list_document',
        'selected' => 'selected_interest_document'
    ),
    'list_listing' => array(
        'input' => 'interest_list_listing',
        'list' => 'interests_list_list_listing',
        'selected' => 'selected_interest_list_listing'
    ),
    'video' => array(
        'input' => 'interest_video',
        'list' => 'interests_list_video',
        'selected' => 'selected_interest_video'
    ),
    'avp_video' => array(
        'input' => 'interest_avp_video',
        'list' => 'interests_list_avp_video',
        'selected' => 'selected_interest_avp_video'
    ),
    'album' => array(
        'input' => 'interest_album',
        'list' => 'interests_list_album',
        'selected' => 'selected_interest_album'
    ),
    'quiz' => array(
        'input' => 'interest_quiz',
        'list' => 'interests_list_quiz',
        'selected' => 'selected_interest_quiz'
    ),
    'poll' => array(
        'input' => 'interest_poll',
        'list' => 'interests_list_poll',
        'selected' => 'selected_interest_poll'
    ),
    'store_product' => array(
        'input' => 'interest_store_product',
        'list' => 'interests_list_store_product',
        'selected' => 'selected_interest_store_product'
    ),
    'article' => array(
        'input' => 'interest_article',
        'list' => 'interests_list_article',
        'selected' => 'selected_interest_article'
    ),
    'artarticle' => array(
        'input' => 'interest_article',
        'list' => 'interests_list_artarticle',
        'selected' => 'selected_interest_artarticle'
    ),
);
$html = array();

$defaultText = array(
    'blog' => $this->translate('like_What do you like to read?'),
    'document' => $this->translate('like_What documents you like to read?'),
    'list_listing' => $this->translate('like_What listings you like to read?'),
    'page' => $this->translate('like_What pages do you want to visit?'),
    'event' => $this->translate('like_What kind of events do you like?'),
    'group' => $this->translate('like_What groups do you like?'),
    'classified' => $this->translate('like_What classifieds do you like?'),
    'album' => $this->translate('like_What albums do you like?'),
    'video' => $this->translate('like_What videos do you like?'),
    'avp_video' => $this->translate('like_What videos do you like?'),
    'music_playlist' => $this->translate('like_What music do you like?'),
    'quiz' => $this->translate('like_What quizzes do you like to experience?'),
    'poll' => $this->translate('like_What polls do you like?'),
    'store_product' => $this->translate('like_What products do you like?'),
    'article' => $this->translate('like_What articles do you like?'),
    'artarticle' => $this->translate('like_What articles do you like?'),
);

$mostTexts = array(
    'blog' => 'like_most liked blogs',
    'document' => 'like_most liked documents',
    'list_listing' => 'like_most liked listings',
    'page' => 'like_most liked pages',
    'event' => 'like_most liked events',
    'group' => 'like_most liked groups',
    'classified' => 'like_most liked classifieds',
    'album' => 'like_most liked albums',
    'video' => 'like_most liked videos',
    'avp_video' => 'like_most liked videos',
    'music_playlist' => 'like_most liked musics',
    'quiz' => 'like_most liked quizzes',
    'poll' => 'like_most liked polls',
    'store_product' => 'like_most liked products',
    'article' => 'like_most liked articles',
    'artarticle' => 'like_most liked articles',
);

$added = array();
$added_fake = array();
?>

<div class="like_interests_wrapper">

    <div class="like_interests">

        <?php if ($this->message): ?>
            <div class="like_notification">
                <?php echo $this->translate($this->message); ?>
            </div>
        <?php endif; ?>

        <?php $counter = 0; ?>
        <?php foreach ($this->labels as $type => $label): ?>
            <?php
            $checkModule = '';
            switch ($type)
            {
                case 'advgroup' :
                    {
                        $type = 'group';
                        $checkModule = 'advgroup';
                    }break;
                case 'avp_video' :
                    {
                        $checkModule = 'avp';
                    }
                    break;
                case 'artarticle' :
                    {
                        $checkModule = 'advancedarticles';
                    }
                    break;
                case 'music_playlist' :
                    {
                        $checkModule = 'music';
                    }
                    break;
                case 'store_product' :
                    {
                        $checkModule = 'store';
                    }
                    break;
                case 'list_listing' :
                    {
                        $checkModule = 'list';
                    }
                    break;
                default :
                    {
                        $checkModule = $type;
                    }
                    break;
            }
            if (!$this->moduleApi->isModuleEnabled($checkModule))
            {
                if ($checkModule == 'group' && $type == 'group'){}
                else unset($data[$type]);
                continue;
            }
            ?>

            <?php $counter++ ?>
            <div class="interest <?php echo $type; ?> <?php if ($counter == 1): ?>first<?php endif; ?>">
                <div class="left">
                    <span><?php echo $this->translate($label); ?>:</span>
                </div>
                <div class="center">
                    <div class="interests_wrapper">
                        <div class="input_wrapper">
                            
                            <input type="text" name="interest" class="interest_input default_text" id="<?php echo $data[$type]['input']; ?>" value="<?php echo $defaultText[$type]; ?>" />
                        </div>
                        <div class="interests_list" id="<?php echo $data[$type]['list']; ?>">
                            <?php
                            $added[$type] = array();
                            $added_fake[$type] = array();
                            if (isset($this->items[$type]) && count($this->items[$type]) > 0):
                                ?>
                                <?php foreach ($this->items[$type] as $item_paginator): ?>
                                    <?php if (isset($item_paginator['resource_id'])): ?>
                                        <?php if (null != $item = Engine_Api::_()->getItem($item_paginator['resource_type'], $item_paginator['resource_id'])): ?>
                                            <div class="item">
                                                <a href="javascript:void(0)" id="select_<?php echo $item->getGuid(); ?>" class="select"><?php echo $item->getTitle(); ?></a>
                                            </div>
                                            <?php
                                            $added[$item->getType()][] = $item->getIdentity();
                                            $img = $item->getPhotoUrl('thumb.icon');
                                            if (!$img)
                                            {
                                                $img = $this->baseUrl() . $this->nophoto[$item->getType()];
                                            }
                                            $photo = "<img width='48px' height='48px' class='thumb_icon item_photo_" . $item->getType() . "' src='" . $img . "' />";
                                            $html[$item->getGuid()] = '
                      <div class="pic">
                        ' . $this->htmlLink($item, $photo) . '
                      </div>
                      <div class="wrapper">
                        <div class="link">
                          ' . $this->htmlLink($item, $item->getTitle()) . '
                        </div>
                        <div class="delete">
                          ' . $this->htmlLink('javascript:likeInterest.doRemove("' . $item->getType() . '", ' . $item->getIdentity() . ')', $this->translate('Remove')) . '
                        </div>
                      </div>';
                                            ?>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <div class="item">
                                            <a href="javascript:void(0)" id="select_<?php echo $item_paginator['resource_type'] . '_' . $item_paginator['resource_title'] ?>" class="select"><?php echo $item_paginator['resource_title']; ?></a>
                                        </div>
                                        <?php
                                        $added_fake[$item_paginator['resource_type']][] = $item_paginator['resource_title'];
                                        $img = $this->baseUrl() . $this->nophoto[$item_paginator['resource_type']];
                                        $photo = "<img width='48px' height='48px' class='thumb_icon item_photo_" . $item_paginator['resource_type'] . "' src='" . $img . "' />";
                                        $html[$item_paginator['resource_type'] . '_' . $item_paginator['resource_title']] = '
                    <div class="pic">
                        ' . $photo . '
                    </div>
                    <div class="wrapper">
                      <div class="link">
                        <label>' . $item_paginator['resource_title'] . '</label>
                      </div>
                      <div class="delete">
                        ' . $this->htmlLink('javascript:likeInterest.doRemove("' . $item_paginator['resource_type'] . '", ' . "'" . $item_paginator['resource_title'] . '\')', $this->translate('Remove')) . '
                      </div>
                    </div>';
                                        ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <div class="clr"></div>
                        </div>
                    </div>
                </div>
                <div class="right">
                    <div class="selected_interest_wrapper" id="<?php echo $data[$type]['selected']; ?>">
                        <?php if (isset($this->items[$type]) && count($this->items[$type]) > 0): ?>
                            <?php foreach ($this->items[$type] as $item_paginator): ?>
                                <?php if (isset($item_paginator['resource_id'])): ?>
                                    <?php if (null != $item = Engine_Api::_()->getItem($item_paginator['resource_type'], $item_paginator['resource_id'])): ?>
                                        <?php
                                        echo isset($html[$item->getGuid()]) ? $html[$item->getGuid()] : '&nbsp;';
                                        break;
                                        ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?php
                                    echo isset($html[$item_paginator['resource_type'] . '_' . $item_paginator['resource_title']]) ? $html[$item_paginator['resource_type'] . '_' . $item_paginator['resource_title']] : '&nbsp';
                                    ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <div class="clr"></div>
                    </div>
                    <?php if (isset($this->paginators[$type]) && count($this->paginators[$type]) > 0): ?>
                        <div class="most-interested">
                            <div class="title"><?php echo $this->translate($mostTexts[$type]); ?></div>
                            <div class="clr"></div>
                            <div class="list">
                                <?php foreach ($this->paginators[$type] as $item_paginator): ?>
                                    <?php if (null != $mostItem = Engine_Api::_()->getItem($item_paginator['resource_type'], $item_paginator['resource_id'])): ?>
                                        <div class="item">
                                            <?php
                                            if ($item_paginator['resource_type'] == 'blog')
                                            {
                                                echo $this->htmlLink($mostItem->getHref(), $this->itemPhoto($mostItem->getOwner(), 'thumb.icon'), array('class' => 'most-liked-item display_block', 'id' => 'most_liked_' . $item_paginator['resource_type'] . '_' . $mostItem->getIdentity()));
                                            }
                                            elseif ($item_paginator['resource_type'] == 'pageblog')
                                            {
                                                echo $this->htmlLink($mostItem->getHref(), $this->itemPhoto($mostItem->getPage(), 'thumb.icon'), array('class' => 'most-liked-item display_block', 'id' => 'most_liked_' . $item_paginator['resource_type'] . '_' . $mostItem->getIdentity()));
                                            }
                                            else
                                            {
                                                echo $this->htmlLink($mostItem->getHref(), $this->itemPhoto($mostItem, 'thumb.icon'), array('class' => 'most-liked-item display_block', 'id' => 'most_liked_' . $item_paginator['resource_type'] . '_' . $mostItem->getIdentity()));
                                            }
                                            ?>
                                            <div title="<?php echo $this->translate(array('like_%s like', 'like_%s likes', $item_paginator['like_count']), $item_paginator['like_count']); ?>" class="like_count"><?php echo $item_paginator['like_count']; ?></div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <div class="clr"></div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="clr"></div>
            </div>
            <div class="clr"></div>
        <?php endforeach; ?>
        <div class="clr"></div>

        <?php if (count($this->viewOptions) > 1): ?>
            <div class="like_interests_privacy">
                <div class="title">
                    <span>
                        <script type="text/javascript">
                            en4.core.runonce.add(function(){
                                var miniTipsOptions = {
                                    'htmlElement': '.he-hint-text',
                                    'delay': 1,
                                    'className': 'he-tip-mini',
                                    'id': 'he-mini-tool-tip-id',
                                    'ajax': false,
                                    'visibleOnHover': false
                                };

                                var descTip = new HETips($$('.like-privacy-description'), miniTipsOptions);
                            });
                        </script>
                        <a href='javascript:void(0)' class='like-privacy-description'>(<?php echo $this->translate("?"); ?>)<?php echo $this->translate("like_Privacy:"); ?></a>
                        <div class="he-hint-text hidden"><?php echo $this->translate("like_Who is allowed to view your Interests?"); ?></div>
                    </span>
                </div>
                <div class="options">
                    <?php foreach ($this->viewOptions as $value => $label): ?>
                        <a href="javascript:void(0)" id="<?php echo $value; ?>" class="option <?php if ($value == $this->privacyValue): ?>active<?php endif; ?>"><span><?php echo $this->translate($label); ?></span></a>
                    <?php endforeach; ?>
                    <div class="clr"></div>
                </div>
            </div>
            <div class="clr"></div>
        <?php endif; ?>

        <div class="like_interests_buttons">
            <button id="save_changes" type="submit"><?php echo $this->translate("Save Changes"); ?></button>
        </div>
    </div>

    <div class="clr"></div>

    <script type="text/javascript">
        var internalTips = null;
        en4.core.runonce.add(function(){
            var options = {
                url: '<?php echo $this->url(array("action" => "show-matches"), "like_default"); ?>',
                delay: 300,
                onShow: function(tip, element){
                    var miniTipsOptions = {
                        'htmlElement': '.he-hint-text',
                        'delay': 1,
                        'className': 'he-tip-mini',
                        'id': 'he-mini-tool-tip-id',
                        'ajax': false,
                        'visibleOnHover': false
                    };

                    internalTips = new HETips($$('.he-hint-tip-links'), miniTipsOptions);
                    Smoothbox.bind();
                }
            };

            var $thumbs = $$('.like_match_item');
            var $matches_tips = new HETips($thumbs, options);

            var options = {
                url: '<?php echo $this->url(array("action" => "show-content"), "like_default"); ?>',
                delay: 300,
                onShow: function(tip, element) {
                    var miniTipsOptions = {
                        'htmlElement': '.he-hint-text',
                        'delay': 1,
                        'className': 'he-tip-mini',
                        'id': 'he-mini-tool-tip-id',
                        'ajax': false,
                        'visibleOnHover': false
                    };

                    internalTips = new HETips($$('.he-hint-tip-links'), miniTipsOptions);
                    Smoothbox.bind();
                }
            };

            var $thumbs = $$('.most-liked-item');
            var $matches_tips2 = new HETips($thumbs, options);

        });
    </script>

    <?php if ($this->matches->getTotalItemCount() > 0): ?>

        <div class="like_matches">
            <div class="see_all_container">
                <a href="<?php echo $this->url(array('action' => 'see-matches', 'user_id' => $this->subject->getIdentity()), 'like_default'); ?>" class="smoothbox">
                    <?php echo $this->translate(array("like_%s user liked same things as you did.", "like_%s users liked same things as you did.", $this->matches->getTotalItemCount()), ($this->matches->getTotalItemCount())); ?>
                </a>
            </div>
            <div class="clr"></div>
            <?php
            $counter = 0;
            foreach ($this->matches as $item):
                $counter++;
                ?>
                <div class="item">
                    <div class="photo">
                        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon'), array('class' => 'like_match_item', 'id' => 'like_match_item_' . $item->getGuid())); ?>
                    </div>
                </div>
            <?php endforeach; ?>
            <div class="clr"></div>
        </div>

    <?php endif; ?>

</div>

<script type="text/javascript">
    likeInterest.data = <?php echo Zend_Json_Encoder::encode($data); ?>;
    likeInterest.addedInterests = <?php echo Zend_Json_Encoder::encode($added); ?>;
    likeInterest.addedInterestsFake = <?php echo Zend_Json_Encoder::encode($added_fake); ?>;
    likeInterest.html = <?php echo Zend_Json_Encoder::encode($html); ?>;
    likeInterest.defaultText = <?php echo Zend_Json_Encoder::encode($defaultText); ?>;
    likeInterest.privacyValue = '<?php echo $this->privacyValue; ?>';
    likeInterest.url.suggest = '<?php echo $this->url(array('action' => 'suggest'), 'like_interests'); ?>';
    likeInterest.url.add = '<?php echo $this->url(array('action' => 'add'), 'like_interests'); ?>';
    likeInterest.url.remove = '<?php echo $this->url(array('action' => 'remove'), 'like_interests'); ?>';
    likeInterest.url.submit = '<?php echo $this->url(array('action' => 'index'), 'like_interests'); ?>';
    en4.core.runonce.add(function(){
        likeInterest.init(<?php echo (int) $this->viewer->getIdentity(); ?>);
    });
</script>