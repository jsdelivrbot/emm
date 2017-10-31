// Custom scripts file

(function ($) {

  // 'use strict';  // sigh

  // console.log("Hello world!");

 // Michah Godbolt http://codepen.io/micahgodbolt/pen/FgqLc 

  function equalheight(container){

        var currentTallest = 0,
        currentRowStart = 0,
        rowDivs = new Array(),
        $el,
        topPosition = 0;

        // console.log("I'm running!!");

    $(container).each(function() {
        $el = $(this);
        $($el).height('auto');
        topPosition = $el.position().top;


        if (currentRowStart != topPosition) {
          for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
              rowDivs[currentDiv].height(currentTallest);
            }

          rowDivs.length = 0; // empty the array
          currentRowStart = topPosition;
          currentTallest = $el.height();
          rowDivs.push($el);
          // console.log("If first thing does not equal topPosition!!");

        } else {
          rowDivs.push($el);
          currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
        }

        for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
          rowDivs[currentDiv].height(currentTallest);
        }

        // console.log("Hiiiiiii!!");

    });


  } // equalheight function 

  // Call equaliheight 
  // Only fire this JS if window if columns are not linearized 
  if ($(window).width() >= 700){
    $(window).load(function() {
      equalheight('a.col-content, .front .region-hero .block');
     // console.log("Hiiiiiii!!");
    });
  }

    $(window).resize(function(){
      equalheight('a.col-content, .front .region-hero .block');
    });

// DevCollab custom - sketch out how to append cloned footer blocks to mobile menu
 // console.log("Hiiiii!");
 // $( ".block--site-developed-with-support-and-funding-from" ).clone().appendTo('.mm-listview');

  // // Gesso generic functions
  // // Generic function that runs on window resize.
  // function resizeStuff() {
  //     equalheight('a.col-content');    
  // }

  // // // Runs function once on window resize.
  // var TO = false;
  // $(window).resize(function () {
  //   if (TO !== false) {
  //     clearTimeout(TO);
  //   }

  //   // 200 is time in miliseconds.
  //   TO = setTimeout(resizeStuff, 200);
  // }).resize();
  // // END Gesso generic functions

  // Show more resources when clicked
  // var allPanels = $('.block-collapsible .block__content').hide();
  var allPanels = $('.block-collapsible .block__content');
  $('.block-collapsible .block__title').click(function() {
    allPanels.slideToggle();
    $( this ).parent().toggleClass( 'expanded' );
    return false;
  });

})(jQuery);