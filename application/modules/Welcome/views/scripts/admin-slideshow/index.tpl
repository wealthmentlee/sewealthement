
<link href='<?php echo $this->baseUrl().'/application/modules/Welcome/externals/styles/main/main.css'; ?>' rel='stylesheet' type="text/css" />
<link href='<?php echo $this->baseUrl().'/application/modules/Welcome/externals/styles/main/constants.css'; ?>' rel='stylesheet' type="text/css" />


<h2>
    <?php echo $this->translate('Manage Slideshows'); ?>
</h2>

<p>
    <div class="button" style="float: right;">
        <?php echo $this->htmlLink(
        array(
        'route' => 'admin_default',
        'module' => 'welcome',
        'controller' => 'slideshow',
        'action' => 'create'
        ),
        $this->translate('Create Slideshow'),
        array('class' => 'smoothbox')
        );?>
    </div>
    <?php echo $this->translate("WELCOME_ADMIN_SLIDESHOWS_DESCRIPTION") ?>
</p><br>

<br />


<?php if( $this->paginator->count() > 0 ) : ?>

<?php foreach( $this->paginator as $slideshow ) : ?>

<div class="slideshow_admin">
    <div class="l">
        <div class="slide_title"><?php echo ucwords( $slideshow->title ); ?></div>
        <div class="slide_desc"><?php echo $this->translate( 'Recommended image size : ' ); ?><b><?php echo $slideshow->getRecommendedSize(); ?></b></div>
    </div>
    <div class="slide_options">
        <a class="smoothbox" href="<?php echo $this->url( array(
            'module' => 'welcome',
            'controller' => 'slideshow',
            'action' => 'edit',
            'slideshow_id' => $slideshow->slideshow_id
        ), 'admin_default' ); ?>">
            <img src="<?php echo $this->baseUrl().'/application/modules/Welcome/externals/images/admin/edit.png'; ?>" title="edit" />
        </a>

        <a class="smoothbox" href="<?php echo $this->url( array(
            'module' => 'welcome',
            'controller' => 'slideshow',
            'action' => 'delete',
            'slideshow_id' => $slideshow->slideshow_id
        ), 'admin_default' ); ?>">
            <img src="<?php echo $this->baseUrl().'/application/modules/Welcome/externals/images/admin/delete.png'; ?>" title="delete" />
        </a>

        <a class="smoothbox" href="<?php echo $this->url( array(
            'module' => 'welcome',
            'controller' => 'slideshow',
            'action' => 'settings',
            'slideshow_id' => $slideshow->slideshow_id
        ), 'admin_default' ); ?>">
            <img src="<?php echo $this->baseUrl().'/application/modules/Welcome/externals/images/admin/settings.gif'; ?>" title="settings" />
        </a>

        <a href="<?php echo $this->url( array(
            'module' => 'welcome',
            'controller' => 'steps',
            'action' => 'create',
            'slideshow_id' => $slideshow->slideshow_id
        ), 'admin_default' ); ?>">
            <img src="<?php echo $this->baseUrl().'/application/modules/Welcome/externals/images/admin/addslide.png'; ?>" title="add slide" />
        </a>
    </div>
    <br clear="all" />

    <?php if( $slideshow->getSlides() ) : ?>
    <?php foreach( $slideshow->getSlides() as $slide ) : ?>
    <div class="gallery">
        <div class="gallery_item">
            <div class="goptions">
                <a href="<?php echo $this->url( array(
                    'module' => 'welcome',
                    'controller' => 'steps',
                    'action' => 'edit',
                    'step' => $slide->step_id
                ), 'admin_default' ); ?>">
                    <img src="<?php echo $this->baseUrl().'/application/modules/Welcome/externals/images/admin/edit.png'; ?>" title="edit" />
                </a>
                <a href="<?php echo $this->url( array(
                    'module' => 'welcome',
                    'controller' => 'steps',
                    'action' => 'delete',
                    'step' => $slide->step_id
                ), 'admin_default' ); ?>">
                    <img src="<?php echo $this->baseUrl().'/application/modules/Welcome/externals/images/admin/delete.png'; ?>" title="delete" />
                </a>
            </div>
            <img src="<?php echo $slide->getPhotoUrl(); ?>">
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <br clear="all" />
</div>

<?php endforeach; ?>
<?php endif; ?>

<br/>
<div>
    <?php echo $this->paginationControl( $this->paginator ); ?>
</div>
