$(function() {
    // previous/next buttons highlight by mouse move

$(document).on("mouseenter","#image-content ",function(){
    $("#image-content").addClass('showPager');
});
$(document).on("mouseleave","#image-content ",function(){
    $("#image-content").removeClass('showPager');
    $(".pager").animate({opacity: "0"}, {queue: false, duration: 100});
});
$(document).on("mousemove","#image-content ",$.throttle( 100,  function(event) {
    if ($("#image-content").hasClass('showPager')) {
	// get mouse position
	var mouse = event.pageX - $(this).offset().left;
	var left = $(".pager.prev").width() * 2;
	highlightPagerSide(mouse,left);
    }
}));
    /*$("#image-content").hover(function() {
	// show buttons
	$("#image-content").addClass('showPager');

    }, function() {
	// hide the buttons
	$("#image-content").removeClass('showPager');
	$(".pager").animate({opacity: "0"}, {queue: false, duration: 100});
    }).mousemove($.throttle( 100,  function(event) {
	// highlight buttons

	if ($("#image-content").hasClass('showPager')) {
	    // get mouse position
	    var mouse = event.pageX - $(this).offset().left;
	    var left = $(".pager.prev").width() * 2;
	    highlightPagerSide(mouse,left);
	}
	//console.log(mouse);
    }));*/
    $(document).on("click","#image",function(){
    //$("#image").click(function(){
	var mouse = event.pageX - $(this).offset().left;
	var left = $(".pager.prev").width() * 2;
	if (mouse < left) {
	    if($(".pager.prev").hasClass('ajax')){
		$(".pager.prev").click();
	    }else{
		window.location = $(".pager.prev").attr('href');
	    }
	    //
	}else{
	    if($(".pager.next").hasClass('ajax')){
		$(".pager.next").click();
	    }else{
		window.location = $(".pager.next").attr('href');
	    }
	}
    })
});

/**
* Do the highlighting

 * @param {type} mouse
 * @param {type} left
 * @returns {undefined} */
function highlightPagerSide(mouse,left) {
    if (mouse < left) {
	// highlight previous
	$(".pager.prev").animate({opacity: "1"}, {queue: false, duration: 100});
	$(".pager.next").animate({opacity: "0.5"}, {queue: false, duration: 100});
    } else {
	//highlight next
	$(".pager.next").animate({opacity: "1"}, {queue: false, duration: 100});
	$(".pager.prev").animate({opacity: "0.5"}, {queue: false, duration: 100});

    }
}