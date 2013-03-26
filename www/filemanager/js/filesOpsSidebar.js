$(function() {
    // disable/enable file manipulations on change state of select boxes
    
    $(document).on("change", ".fileSelector", function(event) {
	if($(".fileSelector:checked").length){
	    // if at least one select box is checked
	    $(".filesManipulation").removeClass('disabled').removeAttr('disabled');
	    addToList($(this).attr('data-name'));
	}else{
	    // no select box is checked
	    $(".filesManipulation").addClass('disabled').attr('disabled','disabled');
	    delFromList($(this).attr('data-name'));
	}
    });
    // and also check initial state on loading (back button and so)
    if($(".fileSelector:checked").length){
	// if at least one select box is checked
	$(".filesManipulation").removeClass('disabled').removeAttr('disabled');
    }else{
	// no select box is checked
	$(".filesManipulation").addClass('disabled').attr('disabled','disabled');
    }
    
    
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


});


/**
 * Will add a filename to the list for file operations
 * @param {string} data
 */
function addToList(data){    
    var text = $("#itemsList").attr('value');
    if(text === "") {
        text="[]";
    } 
    var obj = JSON.parse(text);
    obj.push(data);
    $("#itemsList").attr('value',(JSON.stringify(obj)));  
}

/**
 * Will remove a filename from the list for file operations
 * @param {string} data
 */
function delFromList(data){    
    var text = $("#itemsList").attr('value');
    if(text === "") {
	// nothing to remove..
        return;
    } 
    var obj = JSON.parse(text);
    removeA(obj,data);
    $("#itemsList").attr('value',(JSON.stringify(obj)));  
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

function removeA(arr) {
    var what, a = arguments, L = a.length, ax;
    while (L > 1 && arr.length) {
        what = a[--L];
        while ((ax= arr.indexOf(what)) !== -1) {
            arr.splice(ax, 1);
        }
    }
    return arr;
}