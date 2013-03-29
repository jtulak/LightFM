$(function() {
    // disable/enable file manipulations on change state of select boxes

    filesDownload($("#download-link"));


    /* prepare for download */
    /*$(document).on("click", "#filesDownload", function(event) {
    //$("#filesDownload").click(function(event) {
	event.preventDefault();
	filesDownload(this);
    });*/

    /*/$("#filesMove").click(function(event) {
    $(document).on("click", "#filesMove", function(event) {
	event.preventDefault();
	filesMove(this);
    });

   // $("#filesRename").click(function(event) {
    $(document).on("click", "#filesRename", function(event) {
	event.preventDefault();
	filesRename(this);
    });

    //$("#filesDelete").click(function(event) {
    $(document).on("click", "#filesDelete", function(event) {
	event.preventDefault();
	filesDelete(this);
    });
*/
});

/** 
 * Functions for creating a request for file manipulation
 * 
 */
function filesDownload(link) {
    var list = JSON.parse($(link).attr('data-list'));
    if(list.length === 0) return;
    
    var jqxhr = $.post($(link).attr("href"), {"list": list}, function(data) {
	//alert("success");
	// change dialogs
	$("#download-preparing").hide();
	$("#download-link-ready").attr({'href': data.path});
	$("#download-ready").show();
	//$("#dialog-message-zip").dialog("open");
	//console.log(data);
	window.location = data.path;
    }, 'json')
	    .fail(function(data) {
	$("#download-preparing").hide();
	$("#download-error-message").html(jQuery.parseJSON(data.responseText).error);
	$("#download-error").show();
    });

}



/**
 * Return array of selected items
 * @returns {Array}
 */
function getSelectedItems() {
    var list = new Array();
    // for all files and dirs
    $(".fileSelector").each(function() {
	if ($(this).prop('checked')) {
	    // if checked
	    list.push($(this).attr('data-name'));

	}
    });
    return list;
}

