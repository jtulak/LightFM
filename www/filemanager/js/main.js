/**
 * This array of callbacks is called after finishing the animation of hiding/showing 
 * @type Array
 */
var sidebarOnChangeAfter = new Array();
/**
 * This array of callbacks is called before calling the animation of hiding/showing 
 * @type Array
 */
var sidebarOnChangeBefore = new Array();
/**
 * This array of callbacks is called during the animation of hiding/showing.
 * it is called each 50 ms;
 * @type Array
 */
var sidebarOnChangeDuring = new Array();
var sidebarOnChangeDuringTimer;
$(function() {
    
    if($('body').attr('data-no-ajax')){
	console.log('no ajax');
    }else{
	console.log('ajax');
	unsetAjaxOnBadBrowsers();
	//$.nette.init();
	$.nette.ext('history').cache = false;
	$('.no-ajax').off('click.nette');

	 $.nette.ext('customRedirect', {
	    success: function(payload) {
		if(payload.redirect){
		    window.location = payload.redirect;
		}

	    }
	});
    }

    /** 
     * showing/hiding the sidebar
     */
    $(document).on("click", "#sidebar-control ", function() {
	//$("#sidebar-control ").click(function(){
	// test if it is hidden or not
	var width = $(".sidebar-content-width").width();
	var sidebar = $("#sidebar-content");

	// at first call callbacks Before
	forEachCallback(sidebarOnChangeBefore);
	// and during
	sidebarOnChangeDuringTimer = setInterval(function() {
	    forEachCallback(sidebarOnChangeDuring);
	}, 50);


	if (sidebar.width() != 0) {
	    // if the sidebar is hidden show it
	    sidebar.animate({'width': '0'}, 200, function() {
		// call callbacks After and disable during
		forEachCallback(sidebarOnChangeAfter);
		// delay for ensuring that the callbacks will be called also after
		// finishing
		setTimeout(function() {
		    clearInterval(sidebarOnChangeDuringTimer)
		}, 50);
	    });
	    //sidebar.children().animate({'width':'0'},200).hide(200);
	} else {
	    // else hide it
	    sidebar.animate({width: width}, 200, function() {
		// call callbacks After and disable during
		forEachCallback(sidebarOnChangeAfter);
		// delay for ensuring that the callbacks will be called also after
		// finishing
		setTimeout(function() {
		    clearInterval(sidebarOnChangeDuringTimer)
		}, 50);
	    });
	    //sidebar.children().css({'width':width}).show(200);

	}
    }).hover(function(e) {
	// http://www.2meter3.de/code/hoverFlow/
	$(this).hoverFlow(e.type, {'opacity': 1}, 'fast');
    }, function(e) {
	$(this).hoverFlow(e.type, {opacity: 0.6}, 'fast');
    });



    /************************************************************************/




    /*
     * enabling scrolling sidebar
     */
    //$(".for-fixed").addClass("fixed");
    /* sidebarFixing();
     $(window).scroll(function(){
     sidebarFixing();
     });*/

    // disable disabled links
    $(document).on("click", "a.disabled ", function() {
	//$("a.disabled").click(function(event){
	event.preventDefault();
    });

});

function unsetAjaxOnBadBrowsers() {
    if (!(window.history && history.pushState && window.history.replaceState && !navigator.userAgent.match(/((iPod|iPhone|iPad).+\bOS\s+[1-4]|WebApps\/.+CFNetwork)/))) {
	$('.ajax').removeClass('ajax');
    }
}

/**
 * scrolling sidebar
 * 
 */
function sidebarFixing() {
//    var sidebar = $("#sidebar");
//    // break if sidebar has nothing inside
//    if(typeof sidebar.children('.border').offset() === 'undefined') return;
//    
//    var fixed = $(".fixed");
//	
//    var offset = sidebar.offset().top;
//    /** menu */
//    if($(window).height() > fixed.height()+offset){
//	// if there is enough of space, then we can have the bar fixed
//	fixed.css({"position":"fixed","top":Math.max(offset-$(this).scrollTop(),0)});
//
//    }else{
//	// protection for small screens where some buttons could become inaccesible
//	// so there keep the scrollbar static
//	fixed.css({"position":"relative", "top":0});
//    }
//    
//    
//    var middle = fixed.offset().top+offset;
//    //console.log(middle);
//    /** control (the arrow on the side) */
//    /*var middle = Math.max(
//	    $(this).scrollTop()+$(window).height()/2-offset,
//	    80
//	);
//    */
//    $("#sidebar-controll").height(sidebar.height()).css({'background-position':'100% '+middle+'px'})
//    .find(".gradient").height(sidebar.height());

}




function forEachCallback(arr) {
    if (typeof arr !== 'object')
	return;
    var l = arr.length;
    for (var i = 0; i < l; i++) {
	if (typeof arr[i] === 'function') {
	    arr[i]();
	}
    }
}