if (typeof lightFM.fileops === "undefined")
    lightFM.fileops = new function() {
    };
lightFM.fileops.sidebar = new function() {
    
    /**
     * Will add a filename to the list for file operations
     * @param {string} data
     */
    this.addToList = function(data) {
	var text = $("#itemsList").attr('value');
	if (text === "") {
	    text = "[]";
	}
	var obj = JSON.parse(text);
	// try to remove before adding to prevent duplicities
	removeA(obj, data);
	obj.push(data);
	$("#itemsList").attr('value', (JSON.stringify(obj)));
    }

    /**
     * Will remove a filename from the list for file operations
     * @param {string} data
     */
    this.delFromList = function(data) {
	var text = $("#itemsList").attr('value');
	if (text === "") {
	    // nothing to remove..
	    return;
	}
	var obj = JSON.parse(text);
	obj = removeA(obj, data);
	$("#itemsList").attr('value', (JSON.stringify(obj)));
    }



    /** functions for selecting checkboxes */
    this.selectAll = function() {
	//$("#data").find("input").prop('checked', true);
	$("#data").find("input").each(function() {
	    if (!this.checked) {
		$(this).click();
	    }
	});
    }
    this.selectNone = function() {
	//$("#data").find("input").prop('checked', false);
	$("#data").find("input").each(function() {
	    if (this.checked) {
		$(this).click();
	    }
	});
    }
    this.selectInvert = function() {
	//$("#data").find("input").prop('checked', false);
	$("#data").find("input").click();
    }


}


/*******************************************************************************
 * OnLoad adding
 ******************************************************************************/
/**
 * Handling select all/none/invert buttons
 * @param {type} param 
 */

lightFM.addOnLoadCallback(function() {
    $(document).on("click", "#select-all", function(event) {
	lightFM.fileops.sidebar.selectAll();
    });
    $(document).on("click", "#select-none", function(event) {
	lightFM.fileops.sidebar.selectNone();
    });
    $(document).on("click", "#select-invert", function(event) {
	lightFM.fileops.sidebar.selectInvert();
    });
});


/**
 * handling of checkbox changes
 * @param {type} param
 */
lightFM.addOnLoadCallback(function() {
    // disable/enable file manipulations on change state of select boxes

    $(document).on("change", ".fileSelector", function(event) {

	if (this.checked) {
	    // if at least one select box is checked
	    $(".filesManipulation").removeClass('disabled').removeAttr('disabled');
	    lightFM.fileops.sidebar.addToList($(this).attr('data-name'));
	} else {
	    // no select box is checked
	    $(".filesManipulation").addClass('disabled').attr('disabled', 'disabled');
	    lightFM.fileops.sidebar.delFromList($(this).attr('data-name'));
	}
    });
    // and also check initial state on loading (back button and so)
    if ($(".fileSelector:checked").length) {
	// if at least one select box is checked
	$(".filesManipulation").removeClass('disabled').removeAttr('disabled');
    } else {
	// no select box is checked
	$(".filesManipulation").addClass('disabled').attr('disabled', 'disabled');
    }

});


/**
 * Handling of clicking on a item
 */
lightFM.addOnLoadCallback(function() {

    $(document).on("click", "section.folder, section.file, section.image", function(event) {
	/* Click on item changes the selection */
	//$("section.folder,section.file").click(function(event) {
	event.preventDefault();
	$(this).find('input[type=checkbox]').click();
    });
    $(document).on("click", "section.folder a, section.file a, section.image a,\
			    section.folder input, section.file input,\
			    section.image input", function(e) {
	// on the checkbox and the link we want to act normaly without any js..
	e.stopPropagation();
    });


});

