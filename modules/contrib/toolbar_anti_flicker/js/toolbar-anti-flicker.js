(function ($) {
  $(document).on('drupalViewportOffsetChange.toolbar', function () {
    $('html').removeClass('toolbar-no-flickering');
  });
})(jQuery);
