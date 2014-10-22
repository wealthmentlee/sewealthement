<div id="general-templates" style="display: none;">
  <div id="template-page">
    <div data-role="page" class="template-page">
      <div class="board-in-cover"></div>
      <div data-role="header" class="ui-header">
        <a class="main-back-btn apptouch_dashboard_icon" data-role="button" data-icon="arrow-left" data-rel="back"
           data-iconpos="notext" data-prefetch="true"></a>
        <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'board', 'action' => 'index'), 'default') ?>" class="apptouch-dashboard apptouch_dashboard_icon" data-role="button" data-icon="reorder" onclick="core.helper.boardIn($(this).closest('.ui-page'))"
           data-iconpos="notext" data-prefetch="true" <?php echo Engine_Api::_()->apptouch()->isTabletMode() ? 'data-transition="none"': 'data-transition="slide" data-direction="reverse"' ?>></a>
        <h1 class="page-title"></h1>
      </div>
      <a href="<?php echo $this->url(array('module' => 'core', 'controller' => 'board', 'action' => 'index'), 'default') ?>" data-prefetch="true" style="display: none;"></a>

      <div data-role="content" class="ui-content">
      </div>

      <div data-role="footer" class="ui-footer"></div>
    </div>
  </div>
  <div id="template-dialog">
    <div data-role="dialog" class="template-page template-dialog">
      <div data-role="header" class="ui-header">
        <h1 class="page-title"></h1>
      </div>

      <div data-role="content" class="ui-content">
      </div>

      <div data-role="footer" class="ui-footer"></div>
    </div>

  </div>
  <div id="template-popup">
    <div data-role="popup" class="template-popup">
      <div data-role="header"  class="ui-header">
        <h1 class="page-title"></h1>
      </div>

      <div data-role="content"  class="ui-content">
      </div>

      <div data-role="footer"  class="ui-footer"></div>
    </div>

  </div>
</div>

<script data-cfasync="false" type="text/javascript">

  $(document).bind('pageinit', function () {
    var topLevelId = '<?php echo sprintf('%d', (int)@$this->topLevelId) ?>';
    var topLevelValue = '<?php echo sprintf('%d', (int)@$this->topLevelValue) ?>';
    var elementCache = {};

    function getFieldsElements(selector) {
//    if( $.inArray(selector, elementCache) || elementCache[selector].length > 0 ) {
//      return elementCache[selector];
//    } else {
      return elementCache[selector] = $(selector);
//    }
    }

    function updateFieldValue(element, value) {
      if (element.attr('tag') == 'option') {
        element = element.closest('select');
      } else if (element.attr('type') == 'checkbox' || element.attr('type') == 'radio') {
        element.attr('checked', Boolean(value));
        return;
      }
      if (element.attr('tag') == 'select') {
        if (element.attr('multiple')) {
          element.find('option').each(function (subEl) {

            $(subEl).attr('selected', false);
          });
        }
      }
      if (element) {
        element.attr('value', value);
      }
    }

    var changeFields = window.changeFields = function (element, force, isLoad) {
        element = $(element);

        // We can call this without an argument to start with the top level fields

        if (element.length == 0) {
            getFieldsElements('.parent_' + topLevelId).each(function () {
                changeFields(this, force, isLoad);
            });
            return;
        }

        // If this cannot have dependents, skip
        if (element.length == 0 || !$.type(element[0].onchange)) {
            return;
        }

        // Get the input and params
        var field_id = (element.attr('class') + ' ').match(/field_([\d]+)/i)[1];
        var parent_field_id = (element.attr('class') + ' ').match(/parent_([\d]+)/i)[1];
        var parent_option_id = (element.attr('class') + ' ').match(/option_([\d]+)/i)[1];


        if (!field_id || !parent_option_id || !parent_field_id) {
            return;
        }

        force = ( $.type(force) ? force : false );

        // Now look and see
        // Check for multi values
        var option_id = [];
        if (element.attr('name') && element.attr('name').indexOf('[]') > 0) {
            if (element.type == 'checkbox') { // MultiCheckbox
                getFieldsElements('.field_' + field_id).each(function () {
                    var multiEl = $(this);
                    if (multiEl.attr('checked')) {
                        option_id.push(multiEl.val());
                    }
                });
            } else if (element.attr('tag') == 'select' && element.multiple) { // Multiselect
                element.getChildren().each(function () {
                    var multiEl = $(this);
                    if (multiEl.attr('selected')) {
                        option_id.push(multiEl.val());
                    }
                });
            }
        } else if (element.type == 'radio') {
            if (element.attr('checked')) {
                option_id = [element.val()];
            }
        } else {
            option_id = [element.val()];
        }

        // Iterate over children
        getFieldsElements('.parent_' + field_id).each(function () {
            var childElement = $(this);

            var childParentContainer = null;
            if (childElement.type == 'radio' || childElement.type == 'checkbox') {
                childParentContainer = childElement.closest('li').closest('li');
            }

            var childContainer;
            if (childElement.closest('form').attr('class') == 'field_search_criteria') {
                childContainer = $try(function () {
                    return childElement.closest('li').closest('li');
                });
            }
            if (!childContainer) {
                childContainer = childElement.closest('div.form-wrapper');
            }
            if (!childContainer) {
                childContainer = childElement.closest('div.form-wrapper-heading');
            }
            if (!childContainer) {
                childContainer = childElement.closest('li');
            }
            //var childLabel = childContainer.getElement('label');
            var childOptions = childElement.attr('class').match(/option_([\d]+)/gi);
            for (var i = 0; i < childOptions.length; i++) {
                for (var j = 0; j < option_id.length; j++) {
                    if (childOptions[i] == "option_" + option_id[j]) {
                        var childOptionId = option_id[j];
                        break;
                    }
                }
            }

            //var childOptionId = childElement.attr('class').match(/option_([\d]+)/i)[1];
            var childIsVisible = ( 'none' != childContainer.css('display') );
            var skipPropagation = false;
            //var childFieldId = childElement.attr('class').match(/field_([\d]+)/i)[1];

            // Forcing hide
            var nextForce;
            if (force == 'hide' && option_id.indexOf(childOptionId) == -1) {
                if (!childElement.hasClass('field_toggle_nohide')) {
                    childContainer.css('display', 'none');
                    if (!isLoad) {
                        updateFieldValue(childElement, null);
                    }
                }
                nextForce = force;
            } else if (force == 'show') {
                childContainer.css('display', '');
                nextForce = force;
            } else if (!$.type(option_id) == 'array' || option_id.indexOf(childOptionId) == -1) {
                // Hide fields not tied to the current option (but propogate hiding)
                if (!childElement.hasClass('field_toggle_nohide')) {
                    childContainer.css('display', 'none');
                    if (!isLoad) {
                        updateFieldValue(childElement, null);
                    }
                }
                nextForce = 'hide';
                if (!childIsVisible) {
                    skipPropagation = true;
                }
            } else {
                // Otherwise show field and propogate (nothing, show?)
                childContainer.css('display', '');
                nextForce = undefined;
                //if( childIsVisible ) {
                //  skipPropagation = true;
                //}
            }

            if (!skipPropagation) {
                changeFields(childElement, nextForce, isLoad);
            }
        });

        $(window).trigger('onChangeFields');
    }

    changeFields(null, null, true);
  });

</script>