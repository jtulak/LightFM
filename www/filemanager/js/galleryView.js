
lightFM.gallery = new function() {

    /**
     * Get sizes of images and #data width and compute how many of them can be on one line and set margins.
     * @returns {undefined}
     */
    this.dataResize = function() {
	wrapper = $("#data");
	minMargin = $("#one-em").width();
	width = $(wrapper).innerWidth();
	itemWidth = $("section.image").width() + minMargin ;
console.log(itemWidth+" - "+itemWidth*4+" - "+width);
	// switch the width
	if (width < 2 * itemWidth) {
	    $(wrapper).addClass("col1");
	    $(wrapper).removeClass("col2").removeClass("col3").removeClass("col4");

	} else if (width < 3 * itemWidth) {
	    $(wrapper).addClass("col2");
	    $(wrapper).removeClass("col1").removeClass("col3").removeClass("col4");

	} else if (width < 4 * itemWidth) {
	    $(wrapper).addClass("col3");
	    $(wrapper).removeClass("col1").removeClass("col2").removeClass("col4");

	} else {
	    $(wrapper).addClass("col4");
	    $(wrapper).removeClass("col1").removeClass("col2").removeClass("col3");

	}
    }
}
/**
 * changing of image margins
 */
lightFM.addOnResizeCallback(function() {
    lightFM.gallery.dataResize();
});
