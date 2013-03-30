

if (typeof lightFM.fileops === "undefined")
    lightFM.fileops = new function() {
    };
lightFM.fileops.download = new function() {

    /** 
     * Functions for creating a request for file manipulation
     * 
     */
    this.filesDownload = function(link) {
	var list = JSON.parse($(link).attr('data-list'));
	if (list.length === 0)
	    return;

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

}


