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
$(function(){
    /** 
     * showing/hiding the sidebar
     */
    $("#sidebar-controll ").click(function(){
	// test if it is hidden or not
	var width = $(".sidebar-content-width").width();
	var sidebar = $("#sidebar-content");
	
	// at first call callbacks Before
	forEachCallback(sidebarOnChangeBefore);
	// and during
	sidebarOnChangeDuringTimer = setInterval(function(){
	    forEachCallback(sidebarOnChangeDuring);
	},50);
	
	
	if(sidebar.width() != 0){
	    // if the sidebar is hidden show it
	    sidebar.animate({'width':'0'},200);
	    sidebar.children().animate({'width':'0'},200).hide(200,function(){
		// call callbacks After and disable during
		forEachCallback(sidebarOnChangeAfter);
		// delay for ensuring that the callbacks will be called also after
		// finishing
		setTimeout(function(){clearInterval(sidebarOnChangeDuringTimer)},50);
	    });
	}else{
	    // else hide it
	    sidebar.animate({width:width},200)
	    sidebar.children().css({'width':width}).show(200,function(){
		// call callbacks After and disable during
		forEachCallback(sidebarOnChangeAfter);
		// delay for ensuring that the callbacks will be called also after
		// finishing
		setTimeout(function(){clearInterval(sidebarOnChangeDuringTimer)},50);
	    });
	    
	}
    }).hover(function(e) {
	// http://www.2meter3.de/code/hoverFlow/
	$(this).hoverFlow(e.type, { 'opacity': 1 }, 'fast');
      }, function(e) {
	$(this).hoverFlow(e.type, { opacity: 0.6 }, 'fast');
      });
    /*
     * scrolling sidebar
     */
    $(".for-fixed").addClass("fixed");
    sidebarFixing();
    $(window).scroll(function(){
	sidebarFixing();
    });
    
    
    
});

function sidebarFixing(){
    var fixed = $(".fixed");
	var sidebar = $("#sidebar");
    var offset = sidebar.offset().top;
    if($(window).height() > fixed.height()+offset){
	// protection for small screens where some buttons could become inaccesible
	fixed.css({"position":"fixed","top":Math.max(offset-$(this).scrollTop(),0)});

    }else{
	fixed.css({"position":"relative", "top":0});
    }
    var middle = Math.max(
	    $(this).scrollTop()+$(window).height()/2-offset,
	    80
	);
    $("#sidebar-controll").height(sidebar.height()).css({'background-position':'100% '+middle+'px'})
    .find(".gradient").height(sidebar.height());
    
}



/** functions for selecting checkboxes */
function selectAll(){
    $("#data").find("input").prop('checked', true);
}
function selectNone(){
    $("#data").find("input").prop('checked', false);
}
function selectInvert(){
    $("#data").find("input").each(function(){
	if($(this).prop('checked')) $(this).prop('checked', false);
	else $(this).prop('checked', true);
    });
}


function forEachCallback(arr){
    if(typeof arr != 'object') return;
    var l=arr.length;
    for(var i=0; i < l; i++){
	if(typeof arr[i] == 'function'){
	    arr[i]();
	}
    }
}