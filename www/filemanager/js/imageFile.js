

lightFM.image = new function() {

    this.imageFile = {
	size: {x: 0, y: 0},
	mouse: 0,
	left: 0,
	placeholder: null // wil hold previous image
    };



    /**
     * This function change the sizes of #image-content and the old image
     * according the size of the new image AFTER it is loaded.
     */
    this.changeImageSize = function() {
	var img = $("#image");
	var t = this;
	img.load(function()   {
	    $("#image-content").animate({
		width: img.width(),
		height: img.height()
	    }, 100);
	    if (t.imageFile.placeholder !== null) {
		t.imageFile.placeholder.animate({
		    width: img.width(),
		    height: img.height()
		}, 100).fadeOut(100, function() {
		    t.imageFile.placeholder.remove()
		})
	    }
	});



    }

    /**
     * Do the highlighting
     
     * @param {type} mouse
     * @param {type} left
     * @returns {undefined} */
    this.highlightPagerSide = function(mouse, left) {
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
    
    
}

/**
 * scroll to image
 */
lightFM.addOnLoadCallback(function() {
    if(window.location.hash == "#title") return;
    $('html, body').animate({
         scrollTop: $("#title").offset().top
     }, 500);
});


/**
 * Image 
 */
lightFM.addOnLoadCallback(function() {
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
	    lightFM.image.imageFile.mouse = event.pageX - $(this).offset().left;
	    lightFM.image.imageFile.left = $(".pager.prev").width() * 2;
	    lightFM.image.highlightPagerSide(lightFM.image.imageFile.mouse, lightFM.image.imageFile.left);
	}
    }));

    $(document).on("click", "#image", function(event) {
	//$("#image").click(function(){
	lightFM.image.imageFile.mouse = event.pageX - $(this).offset().left;
	lightFM.image.imageFile.left = $(".pager.prev").width() * 2;
	if (lightFM.image.imageFile.mouse < lightFM.image.imageFile.left) {
	    if ($(".pager.next").hasClass('ajax') && lightFM.ajaxEnabled) {
		$(".pager.prev").click();
	    } else {
		window.location = $(".pager.prev").attr('href');
	    }
	    //
	} else {
	    if ($(".pager.next").hasClass('ajax') && lightFM.ajaxEnabled) {
		$(".pager.next").click();
	    } else {
		window.location = $(".pager.next").attr('href');
	    }
	}
    });

    lightFM.image.changeImageSize();


});

/**
 * 
 */
lightFM.addOnLoadCallback(function() {
    if(!lightFM.ajaxEnabled){ return; }
    /* changer - it will keep the previous image until the new one is loaded 
     * and then make a transition
     * 
     */
    $.nette.ext('imageChange', {
	before: function() {
	    lightFM.image.imageFile.size.x = $("#image").width();
	    lightFM.image.imageFile.size.y = $("#image").height();
	    $("#image-content").css({width: lightFM.image.imageFile.size.x, height: lightFM.image.imageFile.size.y});
	    lightFM.image.imageFile.placeholder = $("#image").clone();
	    lightFM.image.imageFile.placeholder.css({
		position: 'absolute',
		'zIndex': 1,
		top: 0
	    }).removeAttr('id');
	    lightFM.image.imageFile.placeholder.appendTo($("#image-content"));
	},
	complete: function() {
	    lightFM.image.changeImageSize();
	    //formToggle();
	    lightFM.image.highlightPagerSide(lightFM.image.imageFile.mouse, lightFM.image.imageFile.left);

	}
    });
});

