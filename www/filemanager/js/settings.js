$(function() {
    /*$("input[name=inheritViews]").change(function() {
	if(this.checked) {
	    $("#viewsSetting").find("input").attr("disabled","disabled");
	}else{
	    $("#viewsSetting").find("input").removeAttr("disabled");
	}
    });*/
    
    /* toggler for enabling/disabling part of the form */
    // for each checkbox with class .form-toggler set and event
    $(".form-toggler").find("input[type=checkbox]").change(function() {
	formToggle(this);

    });
    // do it also on startup
    $(".form-toggler").find("input[type=checkbox]").each(function() {
	formToggle(this);

    });

});

function formToggle(el){
    // that do something on change
	if(el.checked) {
	    // find closest parent with class and find all inputs in it
	    $(el).closest('.form-toggled').find("input").each(function(){
		// if the input is the toggler, then do nothing
		if($(this).parent(".form-toggler").length){ return;}
		// else disable it
		$(this).attr("disabled","disabled");
	    });
	}else{
	    // find closest parent with class and find all inputs in it
	    $(el).closest('.form-toggled').find("input").each(function(){
		// if the input is the toggler, then do nothing
		if($(this).parent(".form-toggler").length) return;
		// else enable it
		$(this).removeAttr("disabled");
	    });
	}
}