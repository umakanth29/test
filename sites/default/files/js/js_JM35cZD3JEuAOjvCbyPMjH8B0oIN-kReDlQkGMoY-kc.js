/**
 * @file
 * JavaScript behaviors for webform_image_select and jQuery Image Picker integration.
 */

(function ($, Drupal) {

  'use strict';

  // @see https://rvera.github.io/image-picker/
  Drupal.webform = Drupal.webform || {};
  Drupal.webform.imageSelect = Drupal.webform.imageSelect || {};
  Drupal.webform.imageSelect.options = Drupal.webform.imageSelect.options || {};

  /**
   * Initialize image select.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.webformImageSelect = {
    attach: function (context) {
      if (!$.fn.imagepicker) {
        return;
      }

      $('.js-webform-image-select', context).once('webform-image-select').each(function () {
        var $select = $(this);

        // Apply image data to options.
        var images = JSON.parse($select.attr('data-images'));
        for (var value in images) {
          if (images.hasOwnProperty(value)) {
            var image = images[value];
            // Escape double quotes in value
            value = value.toString().replace(/"/g, '\\"');
            $select.find('option[value="' + value + '"]').attr({
              'data-img-src': image.src,
              'data-img-label': image.text,
              'data-img-alt': image.text
            });
          }
        }

        var options = $.extend({
          hide_select: false
        }, Drupal.webform.imageSelect.options);

        if ($select.attr('data-show-label')) {
          options.show_label = true;
        }

        $select.imagepicker(options);
      });
    }
  };

})(jQuery, Drupal);
;
/*! jQuery UI - v1.12.1 - 2017-03-31
* http://jqueryui.com
* Copyright jQuery Foundation and other contributors; Licensed  */
!function(a){"function"==typeof define&&define.amd?define(["jquery","./form","./version"],a):a(jQuery)}(function(a){return a.ui.formResetMixin={_formResetHandler:function(){var b=a(this);setTimeout(function(){var c=b.data("ui-form-reset-instances");a.each(c,function(){this.refresh()})})},_bindFormResetHandler:function(){if(this.form=this.element.form(),this.form.length){var a=this.form.data("ui-form-reset-instances")||[];a.length||this.form.on("reset.ui-form-reset",this._formResetHandler),a.push(this),this.form.data("ui-form-reset-instances",a)}},_unbindFormResetHandler:function(){if(this.form.length){var b=this.form.data("ui-form-reset-instances");b.splice(a.inArray(this,b),1),b.length?this.form.data("ui-form-reset-instances",b):this.form.removeData("ui-form-reset-instances").off("reset.ui-form-reset")}}}});;
/*! jQuery UI - v1.12.1 - 2017-03-31
* http://jqueryui.com
* Copyright jQuery Foundation and other contributors; Licensed  */
!function(a){"function"==typeof define&&define.amd?define(["jquery","./version"],a):a(jQuery)}(function(a){return a.ui.escapeSelector=function(){var a=/([!"#$%&'()*+,.\/:;<=>?@[\]^`{|}~])/g;return function(b){return b.replace(a,"\\$1")}}()});;
/*! jQuery UI - v1.12.1 - 2017-03-31
* http://jqueryui.com
* Copyright jQuery Foundation and other contributors; Licensed  */
!function(a){"function"==typeof define&&define.amd?define(["jquery","../escape-selector","../form-reset-mixin","../labels","../widget"],a):a(jQuery)}(function(a){return a.widget("ui.checkboxradio",[a.ui.formResetMixin,{version:"1.12.1",options:{disabled:null,label:null,icon:!0,classes:{"ui-checkboxradio-label":"ui-corner-all","ui-checkboxradio-icon":"ui-corner-all"}},_getCreateOptions:function(){var b,c,d=this,e=this._super()||{};return this._readType(),c=this.element.labels(),this.label=a(c[c.length-1]),this.label.length||a.error("No label found for checkboxradio widget"),this.originalLabel="",this.label.contents().not(this.element[0]).each(function(){d.originalLabel+=3===this.nodeType?a(this).text():this.outerHTML}),this.originalLabel&&(e.label=this.originalLabel),b=this.element[0].disabled,null!=b&&(e.disabled=b),e},_create:function(){var a=this.element[0].checked;this._bindFormResetHandler(),null==this.options.disabled&&(this.options.disabled=this.element[0].disabled),this._setOption("disabled",this.options.disabled),this._addClass("ui-checkboxradio","ui-helper-hidden-accessible"),this._addClass(this.label,"ui-checkboxradio-label","ui-button ui-widget"),"radio"===this.type&&this._addClass(this.label,"ui-checkboxradio-radio-label"),this.options.label&&this.options.label!==this.originalLabel?this._updateLabel():this.originalLabel&&(this.options.label=this.originalLabel),this._enhance(),a&&(this._addClass(this.label,"ui-checkboxradio-checked","ui-state-active"),this.icon&&this._addClass(this.icon,null,"ui-state-hover")),this._on({change:"_toggleClasses",focus:function(){this._addClass(this.label,null,"ui-state-focus ui-visual-focus")},blur:function(){this._removeClass(this.label,null,"ui-state-focus ui-visual-focus")}})},_readType:function(){var b=this.element[0].nodeName.toLowerCase();this.type=this.element[0].type,"input"===b&&/radio|checkbox/.test(this.type)||a.error("Can't create checkboxradio on element.nodeName="+b+" and element.type="+this.type)},_enhance:function(){this._updateIcon(this.element[0].checked)},widget:function(){return this.label},_getRadioGroup:function(){var b,c=this.element[0].name,d="input[name='"+a.ui.escapeSelector(c)+"']";return c?(b=this.form.length?a(this.form[0].elements).filter(d):a(d).filter(function(){return 0===a(this).form().length}),b.not(this.element)):a([])},_toggleClasses:function(){var b=this.element[0].checked;this._toggleClass(this.label,"ui-checkboxradio-checked","ui-state-active",b),this.options.icon&&"checkbox"===this.type&&this._toggleClass(this.icon,null,"ui-icon-check ui-state-checked",b)._toggleClass(this.icon,null,"ui-icon-blank",!b),"radio"===this.type&&this._getRadioGroup().each(function(){var b=a(this).checkboxradio("instance");b&&b._removeClass(b.label,"ui-checkboxradio-checked","ui-state-active")})},_destroy:function(){this._unbindFormResetHandler(),this.icon&&(this.icon.remove(),this.iconSpace.remove())},_setOption:function(a,b){if("label"!==a||b)return this._super(a,b),"disabled"===a?(this._toggleClass(this.label,null,"ui-state-disabled",b),void(this.element[0].disabled=b)):void this.refresh()},_updateIcon:function(b){var c="ui-icon ui-icon-background ";this.options.icon?(this.icon||(this.icon=a("<span>"),this.iconSpace=a("<span> </span>"),this._addClass(this.iconSpace,"ui-checkboxradio-icon-space")),"checkbox"===this.type?(c+=b?"ui-icon-check ui-state-checked":"ui-icon-blank",this._removeClass(this.icon,null,b?"ui-icon-blank":"ui-icon-check")):c+="ui-icon-blank",this._addClass(this.icon,"ui-checkboxradio-icon",c),b||this._removeClass(this.icon,null,"ui-icon-check ui-state-checked"),this.icon.prependTo(this.label).after(this.iconSpace)):void 0!==this.icon&&(this.icon.remove(),this.iconSpace.remove(),delete this.icon)},_updateLabel:function(){var a=this.label.contents().not(this.element[0]);this.icon&&(a=a.not(this.icon[0])),this.iconSpace&&(a=a.not(this.iconSpace[0])),a.remove(),this.label.append(this.options.label)},refresh:function(){var a=this.element[0].checked,b=this.element[0].disabled;this._updateIcon(a),this._toggleClass(this.label,"ui-checkboxradio-checked","ui-state-active",a),null!==this.options.label&&this._updateLabel(),b!==this.options.disabled&&this._setOptions({disabled:b})}}]),a.ui.checkboxradio});;
/**
 * @file
 * JavaScript behaviors for jQuery UI buttons (checkboxradio) element integration.
 */

(function ($, Drupal) {

  'use strict';

  Drupal.webform = Drupal.webform || {};
  Drupal.webform.buttons = Drupal.webform.buttons || {};
  Drupal.webform.buttons.selector = Drupal.webform.buttons.selector || [
    // Applies to Classy, Bartik, and Seven themes.
    '.js-webform-buttons .form-radios',
    // Applies to Stable and Bootstrap themes.
    '.js-webform-buttons .js-form-type-radio'
  ].join(',');

  /**
   * Create jQuery UI buttons (checkboxradio) element.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.webformButtonsCheckboxRadio = {
    attach: function (context) {
      $(context).find(Drupal.webform.buttons.selector).once('webform-buttons').each(function () {
        var $buttons = $(this);

        // Remove classes around radios and labels and move to main element.
        $buttons.find('input[type="radio"], label').each(function() {
          $buttons.append($(this).removeAttr('class'));
        });

        // Remove all empty div wrappers.
        $buttons.find('div').remove();

        // Must reset $buttons since the contents have changed.
        $buttons = $(this);

        // Get radios.
        var $input = $buttons.find('input[type="radio"]');

        // Create checkboxradio.
        $input.checkboxradio({'icon': false});

        // Disable checkboxradio.
        $input.checkboxradio('option', 'disabled', $input.is(':disabled'));

        // Turn checkboxradio off/on when the input is disabled/enabled.
        // @see webform.states.js
        $input.on('webform:disabled', function () {
          $input.checkboxradio('option', 'disabled', $input.is(':disabled'));
        });
      });
    }
  };

})(jQuery, Drupal);
;
/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

(function ($, Drupal) {
  Drupal.behaviors.tableSelect = {
    attach: function attach(context, settings) {
      $(context).find('th.select-all').closest('table').once('table-select').each(Drupal.tableSelect);
    }
  };

  Drupal.tableSelect = function () {
    if ($(this).find('td input[type="checkbox"]').length === 0) {
      return;
    }

    var table = this;
    var checkboxes = void 0;
    var lastChecked = void 0;
    var $table = $(table);
    var strings = {
      selectAll: Drupal.t('Select all rows in this table'),
      selectNone: Drupal.t('Deselect all rows in this table')
    };
    var updateSelectAll = function updateSelectAll(state) {
      $table.prev('table.sticky-header').addBack().find('th.select-all input[type="checkbox"]').each(function () {
        var $checkbox = $(this);
        var stateChanged = $checkbox.prop('checked') !== state;

        $checkbox.attr('title', state ? strings.selectNone : strings.selectAll);

        if (stateChanged) {
          $checkbox.prop('checked', state).trigger('change');
        }
      });
    };

    $table.find('th.select-all').prepend($('<input type="checkbox" class="form-checkbox" />').attr('title', strings.selectAll)).on('click', function (event) {
      if ($(event.target).is('input[type="checkbox"]')) {
        checkboxes.each(function () {
          var $checkbox = $(this);
          var stateChanged = $checkbox.prop('checked') !== event.target.checked;

          if (stateChanged) {
            $checkbox.prop('checked', event.target.checked).trigger('change');
          }

          $checkbox.closest('tr').toggleClass('selected', this.checked);
        });

        updateSelectAll(event.target.checked);
      }
    });

    checkboxes = $table.find('td input[type="checkbox"]:enabled').on('click', function (e) {
      $(this).closest('tr').toggleClass('selected', this.checked);

      if (e.shiftKey && lastChecked && lastChecked !== e.target) {
        Drupal.tableSelectRange($(e.target).closest('tr')[0], $(lastChecked).closest('tr')[0], e.target.checked);
      }

      updateSelectAll(checkboxes.length === checkboxes.filter(':checked').length);

      lastChecked = e.target;
    });

    updateSelectAll(checkboxes.length === checkboxes.filter(':checked').length);
  };

  Drupal.tableSelectRange = function (from, to, state) {
    var mode = from.rowIndex > to.rowIndex ? 'previousSibling' : 'nextSibling';

    for (var i = from[mode]; i; i = i[mode]) {
      var $i = $(i);

      if (i.nodeType !== 1) {
        continue;
      }

      $i.toggleClass('selected', state);
      $i.find('input[type="checkbox"]').prop('checked', state);

      if (to.nodeType) {
        if (i === to) {
          break;
        }
      } else if ($.filter(to, [i]).r.length) {
          break;
        }
    }
  };
})(jQuery, Drupal);;
/**
 * @file
 * JavaScript behaviors for Text format integration.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Enhance text format element.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.webformTextFormat = {
    attach: function (context) {
      $(context).find('.js-text-format-wrapper textarea').once('webform-text-format').each(function () {
        if (!window.CKEDITOR) {
          return;
        }

        var $textarea = $(this);
        // Update the CKEDITOR when the textarea's value has changed.
        // @see webform.states.js
        $textarea.on('change', function () {
          if (CKEDITOR.instances[$textarea.attr('id')]) {
            var editor = CKEDITOR.instances[$textarea.attr('id')];
            editor.setData($textarea.val());
          }
        });

        // Set CKEDITOR to be readonly when the textarea is disabled.
        // @see webform.states.js
        $textarea.on('webform:disabled', function () {
          if (CKEDITOR.instances[$textarea.attr('id')]) {
            var editor = CKEDITOR.instances[$textarea.attr('id')];
            editor.setReadOnly($textarea.is(':disabled'));
          }
        });

      });
    }
  };

})(jQuery, Drupal);
;
/**
* DO NOT EDIT THIS FILE.
* See the following change record for more information,
* https://www.drupal.org/node/2815083
* @preserve
**/

(function ($, Drupal) {
  Drupal.behaviors.filterGuidelines = {
    attach: function attach(context) {
      function updateFilterGuidelines(event) {
        var $this = $(event.target);
        var value = $this.val();
        $this.closest('.filter-wrapper').find('.filter-guidelines-item').hide().filter('.filter-guidelines-' + value).show();
      }

      $(context).find('.filter-guidelines').once('filter-guidelines').find(':header').hide().closest('.filter-wrapper').find('select.filter-list').on('change.filterGuidelines', updateFilterGuidelines).trigger('change.filterGuidelines');
    }
  };
})(jQuery, Drupal);;
/**
 * @file
 * JavaScript behaviors for color element integration.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Enhance HTML5 color element.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.webformColor = {
    attach: function (context) {
      $(context).find('.form-color:not(.form-color-output)').once('webform-color').each(function () {
        var $element = $(this);
        // Handle browser that don't support the HTML5 color input.
        if (Modernizr.inputtypes.color === false) {
          // Remove swatch sizes.
          $element.removeClass('form-color-small')
            .removeClass('form-color-medium')
            .removeClass('form-color-large');
        }
        else {
          // Display color input's output to the end user.
          var $output = $('<input class="form-color-output ' + $element.attr('class') + ' js-webform-input-mask" data-inputmask-mask="\\#######" />');
          if ($.fn.inputmask) {
            $output.inputmask();
          }
          $output[0].value = $element[0].value;
          $element
            .after($output)
            .css({float: 'left'});

          // Sync $element and $output.
          $element.on('input', function () {
            $output[0].value = $element[0].value;
          });
          $output.on('input', function () {
            $element[0].value = $output[0].value;
          });
        }
      });
    }
  };

})(jQuery, Drupal);
;
/**
 * @file
 * JavaScript behaviors for range element integration.
 */

(function ($, Drupal) {

  'use strict';

  /**
   * Display HTML5 range output in a left/right aligned number input.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.webformRangeOutputNumber = {
    attach: function (context) {
      $(context).find('.js-form-type-range').once('webform-range-output-number').each(function () {
        // Handle browser that don't support the HTML5 range input.
        if (Modernizr.inputtypes.range === false) {
          return;
        }

        var $element = $(this);
        var $input = $element.find('input[type="range"]');
        var $output = $element.find('input[type="number"]');
        if (!$output.length) {
          return;
        }

        // Set output value.
        $output.val($input.val());

        // Sync input and output values.
        $input.on('input', function () {
          $output.val($input.val());
        });
        $output.on('input', function () {
          $input.val($output.val());
        });
      });
    }
  }

  /**
   * Display HTML5 range output in a floating bubble.
   *
   * @type {Drupal~behavior}
   *
   * @see https://css-tricks.com/value-bubbles-for-range-inputs/
   * @see https://stackoverflow.com/questions/33794123/absolute-positioning-in-relation-to-a-inputtype-range
   */
  Drupal.behaviors.webformRangeOutputBubble = {
    attach: function (context) {
      $(context).find('.js-form-type-range').once('webform-range-output-bubble').each(function () {
        // Handle browser that don't support the HTML5 range input.
        if (Modernizr.inputtypes.range === false) {
          return;
        }

        var $element = $(this);
        var $input = $element.find('input[type="range"]');
        var $output = $element.find('output');
        var display = $output.attr('data-display');

        if (!$output.length) {
          return;
        }


        $element.css('position', 'relative');

        $input.on('input', function () {
          var inputValue = $input.val();

          // Set output text with prefix and suffix.
          var text = ($output.attr('data-field-prefix') || '') +
            inputValue +
            ($output.attr('data-field-suffix') || '');
          $output.text(text);

          // Set output top position.
          var top;
          if (display === 'above') {
            top = $input.position().top - $output.outerHeight() + 2;
          }
          else {
            top = $input.position().top + $input.outerHeight() + 2;
          }

          // It is impossible to accurately calculate the exact position of the
          // range's buttons so we only incrementally move the output bubble.
          var inputWidth = $input.outerWidth();
          var buttonPosition = Math.floor(inputWidth * (inputValue - $input.attr('min')) / ($input.attr('max') - $input.attr('min')));
          var increment = Math.floor(inputWidth/5);
          var outputWidth = $output.outerWidth();

          // Set output left position.
          var left;
          if (buttonPosition <= increment) {
            left = 0;
          }
          else if (buttonPosition <= increment * 2) {
            left = (increment * 1.5) - outputWidth;
            if (left < 0) {
              left = 0;
            }
          }
          else if (buttonPosition <= increment * 3) {
            left = (increment * 2.5) - (outputWidth/2);

          }
          else if (buttonPosition <= increment * 4) {
            left = (increment * 4) - outputWidth;
            if (left > (increment * 5) - outputWidth) {
              left = (increment * 5) - outputWidth;
            }
          }
          else if (buttonPosition <= inputWidth) {
            left = (increment * 5) - outputWidth;
          }
          // Also make sure to include the input's left position.
          left = Math.floor($input.position().left + left);

          // Finally, position the output.
          $output.css({top: top, left: left})
        })
        // Fake a change to position output at page load.
        .trigger('input');

        // Add fade in/out event handlers if opacity is defined.
        var defaultOpacity = $output.css('opacity');
        if (defaultOpacity < 1) {
          // Fade in/out on focus/blur of the input.
          $input.on('focus mouseover', function() {
            $output.stop().fadeTo('slow', 1);
          });
          $input.on('blur mouseout', function() {
            $output.stop().fadeTo('slow', defaultOpacity);
          });
          // Also fade in when focusing the output.
          $output.on('touchstart mouseover', function() {
            $output.stop().fadeTo('slow', 1);
          });
        }
      });
    }
  }


})(jQuery, Drupal);
;
