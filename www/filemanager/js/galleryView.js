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


    /* Click on item changes the selection */
    $("section.folder,section.image").click(function(event) {
	event.preventDefault();
	$(this).find('input[type=checkbox]').click();
    }).find("a,input").click(function(e) {
	e.stopPropagation();
    });
});


function nameWidthResize() {
    var names = $("#data .name");
    var child = $(names).children('span');
    var namesWidth = names.width();


}
