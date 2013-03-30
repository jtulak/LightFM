
lightFM.gallery = new function(){
    
    /**
     * Get sizes of images and #data width and compute how many of them can be on one line and set margins.
     * @returns {undefined}
     */
    this.dataResize = function(){
	/*var minMargin = $("#one-em").width()*0.1;
	var width = $("#data").innerWidth()-$("#one-em").width();
	var itemWidth = $('section.image').outerWidth()+minMargin*2;
	
	var space = (width%itemWidth);
	var count = Math.floor(width/itemWidth)
	var margin = (space/(count))*0.5;
	
	if(margin < minMargin) margin = minMargin;
	margin = Math.floor(margin);
	//console.log("space: "+space+", possible: "+count+", margin: "+margin);
	
	$('section.image').css({'margin-left':margin, 'margin-right':0});*/
    }
}
/**
 * changing of image margins
 */
lightFM.addOnResizeCallback(function() {
    lightFM.gallery.dataResize();
});
