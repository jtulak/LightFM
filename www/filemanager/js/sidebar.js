
var sidebar = function (){
    /*******************
     * private variables
     */
    
    /**
     * This array of callbacks is called after finishing the animation of hiding/showing 
     * @type Array
     */
    this._sidebarOnChangeAfter = new Array();
    /**
     * This array of callbacks is called before calling the animation of hiding/showing 
     * @type Array
     */
    this._sidebarOnChangeBefore = new Array();
    /**
     * This array of callbacks is called during the animation of hiding/showing.
     * it is called each 50 ms;
     * @type Array
     */
    this._sidebarOnChangeDuring = new Array();
    this._sidebarOnChangeDuringTimer=null;


    /******************
     * public variables
     */
    this.sidebar = null;
    /****************
     * public methods
     */
    
    
}




/******************************************************************************/


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
    return;
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

    
});