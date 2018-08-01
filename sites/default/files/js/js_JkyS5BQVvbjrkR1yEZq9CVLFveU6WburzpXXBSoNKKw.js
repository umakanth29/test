/**
 * @file
 * JavaScript behaviors for toggle integration.
 */

(function ($, Drupal) {

  'use strict';

  // @see https://github.com/simontabor/jquery-toggles
  Drupal.webform = Drupal.webform || {};
  Drupal.webform.toggles = Drupal.webform.toggles || {};
  Drupal.webform.toggles.options = Drupal.webform.toggles.options || {};

  /**
   * Initialize toggle element using Toggles.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.webformToggle = {
    attach: function (context) {
      if (!$.fn.toggles) {
        return;
      }

      $(context).find('.js-webform-toggle').once('webform-toggle').each(function () {
        var $toggle = $(this);
        var $wrapper = $toggle.parent();
        var $checkbox = $wrapper.find('input[type="checkbox"]');
        var $label = $wrapper.find('label');

        var options = $.extend({
          checkbox: $checkbox,
          on: $checkbox.is(':checked'),
          clicker: $label,
          text: {
            on: $toggle.attr('data-toggle-text-on') || '',
            off: $toggle.attr('data-toggle-text-off') || ''
          }
        }, Drupal.webform.toggles.options);

        $toggle.toggles(options);

        // Trigger change event for #states API.
        // @see Drupal.states.Trigger.states.checked.change
        $toggle.on('toggle', function() {
          $checkbox.trigger("change");
        });
        
        // If checkbox is disabled then add the .disabled class to the toggle.
        if ($checkbox.attr('disabled') || $checkbox.attr('readonly')) {
          $toggle.addClass('disabled');
        }

        // Add .clearfix to the wrapper.
        $wrapper.addClass('clearfix');
      });
    }
  };

  // Track the disabling of a toggle's checkbox using states.
  if ($.fn.toggles) {
    $(document).on('state:disabled', function (event) {
      $('.js-webform-toggle').each(function () {
        var $toggle = $(this);
        var $wrapper = $toggle.parent();
        var $checkbox = $wrapper.find('input[type="checkbox"]');
        var isDisabled = ($checkbox.attr('disabled') || $checkbox.attr('readonly'));
        (isDisabled) ? $toggle.addClass('disabled') : $toggle.removeClass('disabled');
      });
    });
  }

})(jQuery, Drupal);
;
/**
 * @file
 * JavaScript behaviors for terms of service.
 */

(function ($, Drupal) {

  'use strict';

  // @see http://api.jqueryui.com/dialog/
  Drupal.webform = Drupal.webform || {};
  Drupal.webform.termsOfServiceModal = Drupal.webform.termsOfServiceModal || {};
  Drupal.webform.termsOfServiceModal.options = Drupal.webform.termsOfServiceModal.options || {};

  /**
   * Initialize terms of service element.
   *
   * @type {Drupal~behavior}
   */
  Drupal.behaviors.webformTermsOfService = {
    attach: function (context) {
      $(context).find('.js-form-type-webform-terms-of-service').once('webform-terms-of-service').each(function () {
        var $element = $(this);
        var type = $element.attr('data-webform-terms-of-service-type');

        var $details = $element.find('.webform-terms-of-service-details');

        // Initialize the modal.
        if (type === 'modal') {
          // Move details title to attribute.
          var $title = $element.find('.webform-terms-of-service-details--title');
          if ($title.length) {
            $details.attr('title', $title.text());
            $title.remove();
          }

          var options = $.extend({
            modal: true,
            autoOpen: false,
            minWidth: 600,
            maxWidth: 800
          }, Drupal.webform.termsOfServiceModal.options);
          $details.dialog(options);
        }

        $element.find('label a').click(function (event) {
          if (type === 'modal') {
            $details.dialog('open');
          }
          else {
            $details.slideToggle();
          }
          event.preventDefault();
        });
      });
    }
  };

})(jQuery, Drupal);
;
