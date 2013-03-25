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

    $(document).on("click", "section.folder, section.file ", function(event) {
    /* Click on item changes the selection */
    //$("section.folder,section.file").click(function(event) {
	event.preventDefault();
	$(this).find('input[type=checkbox]').click();
    });
    $(document).on("click", "section.folder a, section.file a,section.folder input, section.file input", function(e) {
    //find("a,input").click(function(e) {
	e.stopPropagation();
    });
});

function computeSizes() {

    $("#outer-box").height($(window).height() - 1);
    $("#inner-box").height($(window).height() - $("#header").height() - $("#footer").height() - 2 * parseInt($("#inner-box").css('padding')) - 5)

    $("#data").height($("#inner-box").height() - ($("#data").offset().top - $("#inner-box").offset().top));
}

function nameWidthResize() {
    var names = $("#data .name");
    var child = $(names).children('span');
    var namesWidth = names.width();

    if (child.width() > (namesWidth - 30) || child.width() < (namesWidth - 40))
	$(names).children('span').width(namesWidth - 20);

}
