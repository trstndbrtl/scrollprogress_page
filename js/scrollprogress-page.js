/**
* Function to preprend new ScrollProgress bar to page by content type
* This function retrieve the variable scrollprogress settings - div and color-stored 
* in drupalsetting from the hook_scrollprogress_page_preprocess_node(&$variables)
* 
**/

(function ($, window, Drupal, drupalSettings) {
  Drupal.behaviors.scrollprogresspage = {
    attach: function (context, settings) {
      // attach js process
      $("body", context).once('SetupStrucureMenu').each(this.progressPage);
    },

    progressPage: function (idx, column) {
      // Prepare variable
      var div = drupalSettings.scrollprogress.div,
        color = drupalSettings.scrollprogress.color;
      var divWrapper = $(div);
      // Prepend progress bar and color it
      divWrapper.prepend('<div class="progress-bar"></div>');
      progress = $('.progress-bar');
      progress.css("background-color", color);
      // Run new ScrollProgress();
      window.addEventListener('load', function () {
        setTimeout(function () {
          var progressBar = document.querySelector('.progress-bar');
          function onProgress(x, y) {
            progressBar.style.width = y * 100 + '%';
          }
          self.progressObserver = new ScrollProgress(onProgress);
        }, 100)
      });

    },
  };

})(jQuery, window, Drupal, drupalSettings);
