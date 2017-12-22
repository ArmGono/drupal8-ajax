(function ($, Drupal) {
  /**
   * Add new command for ScrollTo element.
   */
  Drupal.AjaxCommands.prototype.scrollTo = function (ajax, response, status) {
    if (!response.selector) {
      return;
    }
    setTimeout(function () {
      var $wrapper = response.selector ? $(response.selector) : $(ajax.wrapper);
      var top = $wrapper.offset().top;
      var offset = response.offset ? response.offset : 0;
      var speed = drupalSettings.scrollTopSpeed ? drupalSettings.scrollTopSpeed : 500;
      $('html,body').stop().animate({
        scrollTop: top + offset
      }, speed);
    }, 500);

  }
})(jQuery, Drupal);