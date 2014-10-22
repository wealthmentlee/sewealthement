<div class="common-templates">
  <div id="file-upload-selector" data-role="dialog" class="file-upload-selector"
       data-title="<?php echo $this->translate('Upload') ?>">
    <div data-role="header" data-position="fixed">
      <h1>&nbsp;&nbsp;&nbsp;</h1>
      <a data-icon="arrow-u" data-role="button"
         class="do-upload no-files files"><?php echo $this->translate('Upload') ?></a>
    </div>
    <!-- /header -->
    <ul data-role="listview" class="proceeded-files">
      <li class="file-count" data-role="list-divider"><?php echo $this->translate('APPTOUCH_Uploaded Files') ?><span
        class="ui-li-count">0</span></li>
    </ul>
    <ul data-role="listview" class="failed-files">
      <li class="file-count" data-role="list-divider"><?php echo $this->translate('APPTOUCH_Upload Failed') ?><span
        class="ui-li-count">0</span></li>
      <li class="clear-files" data-icon="minus"><a><?php echo $this->translate('Clear List') ?></a></li>
      <li class="re-upload" data-icon="refresh"><a><?php echo $this->translate('APPTOUCH_Retry') ?></a></li>
    </ul>
    <ul data-role="listview" class="files-to-upload no-files files">
      <li class="file-count" data-role="list-divider"><?php echo $this->translate('APPTOUCH_Files To Upload') ?><span
        class="ui-li-count">1</span></li>
      <li class="file fileTpl success" data-icon="check"><a class="file-item"><img src="" class="ui-li-icon">
        <filename></filename>
        <span class="ui-li-count status"><?php echo $this->translate('Success') ?></span></li>
      <li class="file fileTpl fail" data-theme="e"><a class="file-item"><img src="" class="ui-li-icon">
        <filename></filename>
        <span class="ui-li-count status"><?php echo $this->translate('Upload failed') ?></span></a><a data-theme="e"
                                                                                                      data-icon="alert"></a>
      </li>
      <li class="file fileTpl to-upload"><a class="file-item"><img src="" class="ui-li-icon">img</a><a
        data-icon="delete" data-theme="c" class="delete-file"><?php echo $this->translate('Delete') ?></a></li>
    </ul>
    <div data-role="collapsible-set" data-theme="b" data-content-theme="d">
      <div data-role="collapsible" data-collapsed="false" data-theme="b" data-content-theme="d"
           data-collapsed-icon="arrow-r" data-expanded-icon="arrow-d" data-inset="false">
        <h2><?php echo $this->translate('Pictures') ?></h2>
        <ul data-role="listview">
          <li data-icon="camera"><a
            class="filetype photo upload-from camera"><?php echo $this->translate('Camera') ?></a></li>
          <li data-icon="album"><a
            class="filetype photo upload-from album-library"><?php echo $this->translate('Album Library') ?></a></li>
          <li data-icon="photos"><a
            class="filetype photo upload-from saved-photos"><?php echo $this->translate('Saved Photos') ?></a></li>
        </ul>
      </div>
      <!-- /collapsible -->
      <div data-role="collapsible" data-theme="b" data-content-theme="d" data-collapsed-icon="arrow-r"
           data-expanded-icon="arrow-d">
        <h2><?php echo $this->translate('APPTOUCH_Audios') ?></h2>
        <ul data-role="listview">
          <li data-icon='microphone'><a class="filetype capture"
                                        media-type="audio"><?php echo $this->translate('Record') ?></a></li>
          <li><a class="filetype browse" media-type="audio"><?php echo $this->translate('APPTOUCH_Browse') ?></a></li>
        </ul>
      </div>
      <!-- /collapsible -->
      <!-- /collapsible -->
      <div data-role="collapsible" data-theme="b" data-content-theme="d" data-collapsed-icon="arrow-r"
           data-expanded-icon="arrow-d">
        <h2><?php echo $this->translate('APPTOUCH_Videos') ?></h2>
        <ul data-role="listview">
          <li data-icon='camera'><a class="filetype capture"
                                    media-type="video"><?php echo $this->translate('APPTOUCH_Take a video') ?></a></li>
          <li><a class="filetype browse" media-type="video"><?php echo $this->translate('APPTOUCH_Browse') ?></a></li>
        </ul>
      </div>
      <!-- /collapsible -->
      <!-- /collapsible -->
      <div data-role="collapsible" data-theme="b" data-content-theme="d" data-collapsed-icon="arrow-r"
           data-expanded-icon="arrow-d">
        <h2><?php echo $this->translate('Files') ?></h2>
        <ul data-role="listview">
          <li><a class="filetype video"><?php echo $this->translate('Video') ?></a></li>
          <li><a class="filetype music"><?php echo $this->translate('Music') ?></a></li>
          <li><a class="filetype other"><?php echo $this->translate('File') ?></a></li>
        </ul>
      </div>
      <!-- /collapsible -->
    </div>
  </div>
  <!-- /page -->
  <div id="bad-response-error" data-role="page" class="page-component bad-response-error" data-url="#bad-response-error">
    <div class="board-in-cover"></div>

    <div data-role="header">
      <a
        href="<?php echo $this->url(array('module' => 'core', 'controller' => 'board', 'action' => 'index'), 'default', true) ?>"
        class="apptouch_dashboard_icon" data-role="button" data-icon="reorder"
        data-iconpos="notext"></a>

      <h1><?php echo $this->translate('APPTOUCH_Bad server response.') ?></h1>
    </div>
    <!-- /header -->
    <div data-role="content">
      <div></div>
      <h3><?php echo $this->translate('APPTOUCH_Bad server response.') ?></h3>

      <div data-role="collapsible" data-theme="e" data-content-theme="e" data-collapsed-icon="arrow-r"
           data-expanded-icon="arrow-d">
        <h3><?php echo $this->translate('APPTOUCH_Response body') ?></h3>

        <p class="response-body">
          <?php echo $this->translate('APPTOUCH_Could not connect to remote server. Make sure that your device is connected to the network.') ?>
        </p>
      </div>
      <div data-role="controlgroup" data-type="horizontal">
        <a data-role="button" data-icon="arrow-l" data-theme="b"
           data-rel="back"><?php echo $this->translate('Go Back') ?></a>
        <a data-role="button" data-icon="refresh" data-theme="b"
           class="retry"><?php echo $this->translate('APPTOUCH_Refresh') ?></a>
      </div>
      <div></div>
    </div>
    <!-- /content -->

    <div data-role="footer">
      <h1>&nbsp;&nbsp;&nbsp;</h1>
    </div>
    <!-- /footer -->

  </div>
  <!-- /page -->

  <div id="empty-page-error" data-role="page" class="empty-page-error">
    <div class="board-in-cover"></div>

    <div data-role="header">
      <a
        href="<?php echo $this->url(array('module' => 'core', 'controller' => 'board', 'action' => 'index'), 'default', true) ?>"
        class="apptouch_dashboard_icon" data-role="button" data-icon="reorder"
        data-iconpos="notext" data-prefetch="true"></a>

      <h1><?php echo $this->translate('APPTOUCH_Empty Page') ?></h1>
    </div>
    <!-- /header -->

    <div data-role="content">
      <div></div>
      <div data-role="collapsible" data-theme="e" data-content-theme="e" data-collapsed="false"
           data-collapsed-icon="alert" data-expanded-icon="alert">
        <h3><?php echo $this->translate('APPTOUCH_Empty Page') ?></h3>

        <p>
          <?php echo $this->translate('APPTOUCH_There is nothing to show in this page.') ?>
        </p>
      </div>
      <a data-role="button" data-icon="back" data-theme="b"
         data-rel="back"><?php echo $this->translate('Go Back') ?></a>

      <div></div>
    </div>
    <!-- /content -->

    <div data-role="footer">
      <h4>&nbsp;&nbsp;&nbsp;</h4>
    </div>
    <!-- /footer -->

  </div>
  <!-- /page -->

  <div id="file-selector" data-role="page" class="file-selector">

    <div data-role="header" data-position="fixed">
      <a href="././" data-icon="arrow-u" data-iconpos="notext" data-direction="reverse">Up level</a>

      <div class="file-view-format" data-role="controlgroup" data-type="horizontal">
        <a class="as-grid ui-btn-active" data-role="button" data-icon="grid">Tiles</a>
        <a class="as-list" data-role="button" data-icon="list">List</a>
      </div>
    </div>
    <!-- /header -->

    <div data-role="content" class="entry-browser-template">
      <h3 class="entry-name">Directory Name</h3>

      <div class="ui-grid-b file-view file-view-container tiles active">
        <div class="entry-wrapper tile-wrapper ui-block-a">
          <a data-role="button" data-corners="false" data-theme="c" class="entry tile">Block A</a>
        </div>
      </div>
      <!-- /grid-b -->

      <div class="file-view list">
        <ul class="file-view-container" data-role="listview" data-filter="true" data-autodividers="true">
          <li class="entry-wrapper"><a class="entry" href="index.html"><img src="" class="ui-li-icon">folder<span
            class="ui-li-count">4</span></a></li>
        </ul>
      </div>
    </div>
    <!-- /content -->

    <div data-role="footer" data-position="fixed">
      <div data-role="navbar">
        <ul>
          <li><a href="#">OK</a></li>
          <li><a href="#">Cancel</a></li>
        </ul>
      </div>
      <!-- /navbar -->
    </div>
    <!-- /footer -->

  </div>
  <!-- /page -->

</div>