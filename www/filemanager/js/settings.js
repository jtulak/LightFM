$(function() {


    /* toggler for enabling/disabling part of the form */
    // for each checkbox with class .form-toggler set and event
    $(document).on("change", ".form-toggler input[type=checkbox] ", function() {
	formToggle(this);

    });


    /* Apply to subdirs confirm */
    // bind event

    $(document).on("click", "[data-confirm-settings]", function() {
	if ($(this).attr('data-confirmed')) {
	    console.log("confirmed");
	    // if data confirmed is set, then remove the confirmation
	    // and continue with uninterupted click
	    $(this).removeAttr('data-confirmed');
	} else {
	    // if confirmation is not set, then set waiting and raise confirm dialog
	    $(this).attr('data-confirm-settings-waiting', 'true');
	    $("#dialog-confirm-apply-settings").dialog('open');

	    event.preventDefault();
	}
    });
    // create dialog

    $("#dialog-confirm-apply-settings").dialog({
	resizable: false,
	modal: true,
	autoOpen: false,
	close: function() {
	    // remove sign of waiting for confirm
	    $('[data-confirm-settings-waiting]')
		    .removeAttr('data-confirm-settings-waiting');
	},
	buttons: {
	    "Apply settings": function() {
		// set confirmation sign and make a click
		$('[data-confirm-settings-waiting]')
			.attr('data-confirmed', 'true').click();

		$(this).dialog("close");
	    },
	    Cancel: function() {
		$(this).dialog("close");
	    }
	}
    });


    $.nette.ext('formToggler', {
	init: function() {
	    formToggle();
	},
	complete: function() {
	    formToggle();
	}
    });
    // do it also on startup
    setTimeout(function() {
	formToggle()
    }, 1);

});

/**
 * Toggle form elements in reaction on checkbox change
 * @returns {undefined}
 */
function formToggle() {
    $(".form-toggler input[type=checkbox]").each(function() {
	formToggle_one(this);
    });
}
function formToggle_one(el) {
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