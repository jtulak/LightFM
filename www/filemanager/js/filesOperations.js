$(function() {
    // disable/enable file manipulations on change state of select boxes
    //$(".fileSelector").change(function(){
    
    $(document).on("change", ".fileSelector", function(event) {
	if($(".fileSelector:checked").length){
	    // if at least one select box is checked
	    $(".filesManipulation").removeClass('disabled');
	}else{
	    // no select box is checked
	    $(".filesManipulation").addClass('disabled');
	}
    });
    // and also check initial state on loading (back button and so)
    if($(".fileSelector:checked").length){
	// if at least one select box is checked
	$(".filesManipulation").removeClass('disabled');
    }else{
	// no select box is checked
	$(".filesManipulation").addClass('disabled');
    }
    
    

    /* prepare for download */
    $(document).on("click", "#filesDownload", function(event) {
    //$("#filesDownload").click(function(event) {
	event.preventDefault();
	filesDownload(this);
    });
    // download preparing message
    $("#dialog-message-zip-preparing").dialog({
	modal: true,
	closeOnEscape: false,
	autoOpen: false,
	open: function(event, ui) {
	    $(".ui-dialog-titlebar-close").hide()
	}
    });
    // download ready message
    $("#dialog-message-zip").dialog({
	modal: true,
	autoOpen: false,
	buttons: {
	    Close: function() {
		$(this).dialog("close");
	    }
	}

    });


    //$("#filesMove").click(function(event) {
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

});

/** 
 * Functions for creating a request for file manipulation
 * 
 */
function filesDownload(link) {
    var list = getSelectedItems();
    if(list.length === 0) return;
    
    // open waiting dialog
    $("#dialog-message-zip-preparing").dialog("open");

    var jqxhr = $.post($(link).attr("href"), {"list": list}, function(data) {
	//alert("success");
	// change dialogs
	$("#dialog-message-zip-preparing").dialog("close");
	$("#dialog-message-zip-download-link").attr({'href': data.path});
	$("#dialog-message-zip").dialog("open");
	window.location = data.path;
	//console.log(data);
    }, 'json')
	    .fail(function(data) {
	$("#dialog-message-zip-preparing").dialog("close");
	alert("An error occured. You can try it later, or download files one-by-one.");
	console.log(data.error);
    });

}

function filesMove(link) {

}

function filesRename(link) {

}

function filesDelete(link) {

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



/** functions for selecting checkboxes */
function selectAll() {
    $("#data").find("input").prop('checked', true);
}
function selectNone() {
    $("#data").find("input").prop('checked', false);
}
function selectInvert() {
    $("#data").find("input").each(function() {
	if ($(this).prop('checked'))
	    $(this).prop('checked', false);
	else
	    $(this).prop('checked', true);
    });
}
