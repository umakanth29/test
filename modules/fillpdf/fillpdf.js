(function ($) {
  Drupal.behaviors.fillpdf = {
    attach: function (context, settings) {
      $('#edit-backend input').change(function () {
        if (this.value === 'fillpdf_service') {
          $('#edit-fillpdf-service').removeClass('collapsed');
        }
        else {
          $('#edit-fillpdf-service').addClass('collapsed');
        }
      });
    }
  };
}(jQuery));
