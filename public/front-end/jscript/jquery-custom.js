// JavaScript Document
$(document).scroll(function() {
    $('#menu').toggle($(this).scrollTop() > 100);
});

$(document).ready(function(){
  $('a[href*="#"]:not([href="#"])').click(function() {
    if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) +']');
      if (target.length) {
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 1000);
        return false;
      }
    }
  });
  
}); 

var main = function() {
  /* Push the body and the nav over by 285px over */
  $('.burger-menu').click(function() {
    $('.menu').animate({
      right: "0px"
    }, 200);

    $('body').animate({
      right: "275px"
    }, 200);
  });

  /* Then push them back */
  $('.icon-close').click(function() {
    $('.menu').animate({
      right: "-275px"
    }, 200);

    $('body').animate({
      right: "0px"
    }, 200);
  });
};


$(document).ready(main);           


$(window, document, undefined).ready(function() {

  $('.group input').blur(function() {
    var $this = $(this);
    if ($this.val())
      $this.addClass('used');
    else
      $this.removeClass('used');
  });

  var $ripples = $('.ripples');

  $ripples.on('click.Ripples', function(e) {

    var $this = $(this);
    var $offset = $this.parent().offset();
    var $circle = $this.find('.ripplesCircle');

    var x = e.pageX - $offset.left;
    var y = e.pageY - $offset.top;

    $circle.css({
      top: y + 'px',
      left: x + 'px'
    });

    $this.addClass('is-active');

  });

  $ripples.on('animationend webkitAnimationEnd mozAnimationEnd oanimationend MSAnimationEnd', function(e) {
  	$(this).removeClass('is-active');
  });

});

