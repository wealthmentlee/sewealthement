<div class="he-hint">
    <div class="he-hint-body">
        <div class="he-hint-right">
            <?php
            $type = $this->item->getType();

            if ($type == 'blog')
            {
                $img = $this->item->getOwner()->getPhotoUrl('thumb.profile');
                $type = 'user';
            }
            else if ($type == 'avp_video')
            {
                $img = $this->item->getPhotoUrl('thumb.profile');
                $type = 'video';
            }
            else if ($type == 'pageblog')
            {
                $img = $this->item->getParent()->getPhotoUrl('thumb.profile');
                $type = 'page';
            }
            else
            {
                $img = $this->item->getPhotoUrl('thumb.profile');
            }
            if (!$img)
            {
                $img = $this->baseUrl() . '/application/modules/Like/externals/images/nophoto/' . $type . '.png';
            }
            $photo = "<img width='100px' height='100px' class='thumb_profile item_photo_" . $type . "' src='" . $img . "' />";
            echo $this->htmlLink($this->item->getHref(), $photo, array('width' => '100px', 'height' => '100px', 'target' => '_blank'));
            ?>
        </div>
        <div class="he-hint-left">
            <div class="title">
                <?php
                $display_name = $this->item->getTitle();
                echo $this->htmlLink($this->item->getHref(), $display_name, array('target' => '_blank'));
                ?>
            </div>
            <div class="clr"></div>
            <div class="info">
                <?php if ($this->like_count > 0): ?>
                    <?php echo $this->translate(array('like_%s person likes it', '%s people like it', $this->like_count), $this->like_count); ?>
                    <?php if ($this->displayFriends): ?>
                        <?php echo $this->translate(array('and %s of them is your friend.', 'and %s of them are your friends.', $this->paginator->getTotalItemCount()), $this->paginator->getTotalItemCount()); ?>
                    <?php endif; ?>
                <?php else: ?>
                    <?php echo $this->translate('like_No one like it.'); ?>
            <?php endif; ?>
            </div>
<?php if ($this->paginator->getTotalItemCount() > 0): ?>
                <div class="clr"></div>
                <div class="horizontal-list">
                        <?php foreach ($this->paginator as $item): ?>
                        <div class="item">
                            <?php
                            $img = $item->getPhotoUrl('thumb.icon');
                            if (!$img)
                            {
                                $img = $this->baseUrl() . '/application/modules/Like/externals/images/nophoto/' . $item->getType() . '.png';
                            }
                            $photo = "<img width='32px' height='32px' class='thumb_icon item_photo_" . $item->getType() . "' src='" . $img . "' />";
                            echo $this->htmlLink($item->getHref(), $photo, array('class' => 'he-hint-tip-links'));
                            ?>
                            <div class="he-hint-title hidden"></div>
                            <div class="he-hint-text hidden"><?php echo $item->getTitle(); ?></div>
                        </div>
    <?php endforeach; ?>
                    <div class="clr"></div>
                </div>
                <div class="clr"></div>
<?php endif; ?>
        </div>
        <div class="clr"></div>
    </div>
</div>