$(function() {
    // disable/enable file manipulations on change state of select boxes
    
    $(document).on("change", ".fileSelector", function(event) {

	if(this.checked){
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
    // try to remove before adding to prevent duplicities
    removeA(obj,data);
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
    obj=removeA(obj,data);
    $("#itemsList").attr('value',(JSON.stringify(obj)));  
}



/** functions for selecting checkboxes */
function selectAll() {
    //$("#data").find("input").prop('checked', true);
    $("#data").find("input").each(function(){
	if(!this.checked){ 
	    $(this).click();
	}
    });
}
function selectNone() {
    //$("#data").find("input").prop('checked', false);
    $("#data").find("input").each(function(){
	if(this.checked){ 
	    $(this).click();
	}
    });
}
function selectInvert() {
    //$("#data").find("input").prop('checked', false);
    $("#data").find("input").click();
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

function removeA(arr,val) {
    /*var what, a = arguments, L = a.length, ax;
    while (L > 1 && arr.length) {
        what = a[--L];
        while ((ax= arr.indexOf(what)) !== -1) {
            arr.splice(ax, 1);
        }
    }*/
    var pos;
    while((pos = arr.indexOf(val)) != -1){
	arr.splice(pos,1);
    }
    return arr;
}