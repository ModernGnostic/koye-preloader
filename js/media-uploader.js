(function ($) {
  'use strict';

  $(function () {
    // Color picker
    if ($('.koye-color-picker').length) {
      $('.koye-color-picker').wpColorPicker();
    }

    // Media uploader
    let frame;
    $('#koye_logo_button').on('click', function (e) {
      e.preventDefault();

      if (frame) {
        frame.open();
        return;
      }

      frame = wp.media({
        title: 'Select or Upload Logo',
        button: { text: 'Use this logo' },
        multiple: false
      });

      frame.on('select', function () {
        const attachment = frame.state().get('selection').first().toJSON();
        $('#koye_logo').val(attachment.url).trigger('change');
        $('#koye-preloader-preview img').attr('src', attachment.url);
      });

      frame.open();
    });
  });
})(jQuery);
