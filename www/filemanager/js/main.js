


/** **********************************************************************
 *  LightFM Object
 ** **********************************************************************/
var lightFM = null;

function LightFM(){
	REVISION = "1";
	
	/*******************
	 * private variables
	 */
	this._onLoadCallbacks = new Array();
	this._onResizeCallbacks = new Array();
	
	/******************
	 * public variables
	 */
	this.sidebar = null;
	this.settings = null;
	
	this.ajaxEnabled = false;
	/****************
	 * public methods
	 */
	
	/**
	 * Add a function to be called once the page is loaded
	 * 
	 * @param {function} fn
	 * @returns {undefined}
	 */
	this.addOnLoadCallback = function (fn){
	    if(typeof fn != "function") throw "Error: Callback must be a function!";
	    
	    this._onLoadCallbacks.push(fn);
	}
	/**
	 * Add a function to be called when the page is resized
	 * 
	 * @param {function} fn
	 * @returns {undefined}
	 */
	this.addOnResizeCallback = function (fn){
	    if(typeof fn != "function") throw "Error: Callback must be a function!";
	    
	    this._onResizeCallbacks.push(fn);
	}
	
	/**
	 * Will call all onLoad callbacks
	 * @returns {LightFM}
	 */
	this.loaded = function(){
	    var length = this._onLoadCallbacks.length;
	    for (var i = 0; i < length; i++) {
	      this._onLoadCallbacks[i]();
	    }
	    return this;
	};
	
	
	/**
	 * Will call all onResize callbacks
	 * @returns {LightFM}
	 */
	this.resized = function(){
	    var length = this._onResizeCallbacks.length;
	    for (var i = 0; i < length; i++) {
	      this._onResizeCallbacks[i]();
	    }
	    return this;
	};
	
	
};


/**
 * Arrays special thing
 */

/** IE8 and below */
if(!Array.prototype.indexOf) {
    Array.prototype.indexOf = function(what, i) {
        i = i || 0;
        var L = this.length;
        while (i < L) {
            if(this[i] === what) return i;
            ++i;
        }
        return -1;
    };
}
function removeA(arr,val) {
    var pos;
    while((pos = arr.indexOf(val)) != -1){
	arr.splice(pos,1);
    }
    return arr;
}

/**
 * Initialization
 */

lightFM = new LightFM();

$(function() {
    // call callbacks
    lightFM.loaded();
});
/*******************************************************************************
 * OnLoad adding
 ******************************************************************************/

/*
 * Handling of page hight change
 */
lightFM.addOnResizeCallback(function(){

	    var minHeight = $("#one-em").height()*20;
	    var header=$("#data").offset().top;
	    var footer=$("#footer").height();
	    
	    var newHeight = $(window).height()-header-footer-$("#one-em").height()*2;
	    if(newHeight < minHeight) newHeight = minHeight;
	    $("#data").height(newHeight)

});
/**
 * scrolling for dir views and so
 */
lightFM.addOnLoadCallback(function(){
    $(window).resize($.throttle(200, function(){
	lightFM.resized();
    }));
    $(window).resize();
    
    setInterval(function(){
	if(window.document.hasFocus()){
	    $(window).resize();
	}
    },1000)
});


/**
 * Enabling AJAX
 */
lightFM.addOnLoadCallback(function(){
    if($('body').attr('data-no-ajax') || !lightFM.ajaxEnabled){
	console.log('no ajax');
    }else{
	console.log('ajax');
	
	// Unset on bad browsers
	if (!(window.history && history.pushState && window.history.replaceState && !navigator.userAgent.match(/((iPod|iPhone|iPad).+\bOS\s+[1-4]|WebApps\/.+CFNetwork)/))) {
	    $('.ajax').removeClass('ajax');
	}
	
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
});

/**
 * Disabling links with "disabled" class
 */
lightFM.addOnLoadCallback(function(){
    // disable disabled links
    $(document).on("click", "a.disabled ", function() {
	//$("a.disabled").click(function(event){
	event.preventDefault();
    });
});

/**
 * Enabling the javascript confirmation on elements.
 */
lightFM.addOnLoadCallback(function(){
    $.fn.extend({
        triggerAndReturn: function (name, data) {
            var event = new $.Event(name);
            this.trigger(event, data);
            return event.result !== false;
        }
    });
   //$('a[data-confirm], button[data-confirm], input[data-confirm]').live('click', function (e) {
   $(document).on('click','a[data-confirm], button[data-confirm], input[data-confirm]', function(e){
       console.log("a");
        var el = $(this);
        if (el.triggerAndReturn('confirm')) {
            if (!confirm(el.attr('data-confirm'))) {
                return false;
            }
        }
    });
});



