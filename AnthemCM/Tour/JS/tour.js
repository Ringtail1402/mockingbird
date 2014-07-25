/**
 * Tour module JS.
 */
var TourCommon = {
  shownScreens: {},

  init: function () {
    TourCommon.pollTourState();
    setInterval(TourCommon.pollTourState, 1750);
    // Dismiss screens
    $(document).on('click', '.popover', {}, TourCommon.dismissScreens);
  },

  pollTourState: function () {
    for (var screen in TourScreens)
      TourScreens[screen]();
  },

  showScreen: function(tour, screen, options) {
    var $anchor = $(options.anchor);
    if (!$anchor.length) return;
    TourCommon.shownScreens[screen] = $anchor;
    $.post(baseURL + '/tour/' + tour + '/' + screen, {}, function (response) {
      $anchor.popover({
        placement: options.position ? options.position : 'right',
        trigger:   'manual',
        title:     options.title + '<button type="button" class="close">×</button>',
        content:   '<a class="tour-popover"></a>' + response
      }).popover('show');
      $('.tour-popover').parents('.popover').css('z-index', 10000);
      $('#disable-layer').css('height', $(document).height()).show().click(TourCommon.dismissScreens);
    });
  },

  dismissScreens: function () {
    $('.popover').removeClass('in').on($.support.transition.end, function () { $(this).remove(); });
    $('#disable-layer').unbind('click').hide();
  }
};

$(document).ready(TourCommon.init);