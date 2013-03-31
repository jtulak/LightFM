
lightFM.settings = new function(){
    this.formToggle = function(){
	var t=this;
	$(".form-toggler input[type=checkbox]").each(function() {
	    t.formToggle_one(this);
	});
    };
    this.formToggle_one = function(el){
	// that do something on change
	if (el.checked) {
	    // find closest parent with class and find all inputs in it
	    $(el).closest('.form-toggled').find("input").each(function() {
		// if the input is the toggler, then do nothing
		if ($(this).parent(".form-toggler").length) {
		    return;
		}
		// else disable it
		$(this).attr("disabled", "disabled");
	    });
	} else {
	    // find closest parent with class and find all inputs in it
	    $(el).closest('.form-toggled').find("input").each(function() {
		// if the input is the toggler, then do nothing
		if ($(this).parent(".form-toggler").length)
		    return;
		// else enable it
		$(this).removeAttr("disabled");
	    });
	}
    }
};

/*******************************************************************************
 * OnLoad adding
 ******************************************************************************/

/**
 * Form toggler
 */
lightFM.addOnLoadCallback(function(){
    /* toggler for enabling/disabling part of the form */
    // for each checkbox with class .form-toggler set and event
    $(document).on("change", ".form-toggler input[type=checkbox] ", function() {
	lightFM.settings.formToggle(this);

    });
    $.nette.ext('formToggler', {
	init: function() {
	    lightFM.settings.formToggle();
	},
	complete: function() {
	    lightFM.settings.formToggle();
	}
    });
    // do it also on startup
    lightFM.settings.formToggle();
});

