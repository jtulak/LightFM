$(function() {
    // dynamic resize of name
    $(window).resize(function() {
	nameWidthResize();
	//computeSizes();
    });
    sidebarOnChangeDuring.push(function() {
	nameWidthResize();
    });
    
    //computeSizes();
});


function nameWidthResize() {
    var names = $("#data .name");
    var child = $(names).children('span');
    var namesWidth = names.width();

   /* if (child.width() > (namesWidth - 30) || child.width() < (namesWidth - 40))
	$(names).children('span').width(namesWidth - 20);*/

}
