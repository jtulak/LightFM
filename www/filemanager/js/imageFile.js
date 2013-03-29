var imageFile = {
    size : {x:0,y:0},
    mouse : 0,
    left : 0,
    placeholder:null // wil hold previous image
}
$(function() {
    // previous/next buttons highlight by mouse move

    $(document).on("mouseenter", "#image-content ", function() {
	$("#image-content").addClass('showPager');
    });
    $(document).on("mouseleave", "#image-content ", function() {
	$("#image-content").removeClass('showPager');
	$(".pager").animate({opacity: "0"}, {queue: false, duration: 100});
    });
    $(document).on("mousemove", "#image-content ", $.throttle(100, function(event) {
	if ($("#image-content").hasClass('showPager')) {
	    // get mouse position
	    imageFile.mouse = event.pageX - $(this).offset().left;
	    imageFile.left = $(".pager.prev").width() * 2;
	    highlightPagerSide(imageFile.mouse, imageFile.left);
	}
    }));

    $(document).on("click", "#image", function() {
	//$("#image").click(function(){
	imageFile.mouse = event.pageX - $(this).offset().left;
	imageFile.left = $(".pager.prev").width() * 2;
	if (imageFile.mouse < imageFile.left) {
	    if ($(".pager.prev").hasClass('ajax')) {
		$(".pager.prev").click();
	    } else {
		window.location = $(".pager.prev").attr('href');
	    }
	    //
	} else {
	    if ($(".pager.next").hasClass('ajax')) {
		$(".pager.next").click();
	    } else {
		window.location = $(".pager.next").attr('href');
	    }
	}
    });
    
    changeImageSize();
    /* changer - it will keep the previous image until the new one is loaded 
     * and then make a transition
     * 
     */
     $.nette.ext('imageChange', {
	before: function() {
	    imageFile.size.x = $("#image").width();
	    imageFile.size.y = $("#image").height();
	    $("#image-content").css({width:imageFile.size.x, height:imageFile.size.y});
	    imageFile.placeholder = $("#image").clone();
	    imageFile.placeholder.css({
		position:'absolute',
		'zIndex':1,
		top:0
	    }).removeAttr('id');
	    imageFile.placeholder.appendTo($("#image-content"));
	},
	complete: function() {
	    changeImageSize();
	    //formToggle();
	    highlightPagerSide(imageFile.mouse, imageFile.left);
	    
	}
    });
});


/**
 * This function change the sizes of #image-content and the old image
 * according the size of the new image AFTER it is loaded.
 */
function changeImageSize(){
    var img=$("#image");
    img.load(function() {
	$("#image-content").animate({
	    width:img.width(), 
	    height:img.height()
	},100);
	if(imageFile.placeholder !== null){
	    imageFile.placeholder.animate({
		width:img.width(),
		height:img.height()
	    },100).fadeOut(100,function(){this.remove()})
	}
    });
    
    
    
}

/**
 * Do the highlighting
 
 * @param {type} mouse
 * @param {type} left
 * @returns {undefined} */
function highlightPagerSide(mouse, left) {
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