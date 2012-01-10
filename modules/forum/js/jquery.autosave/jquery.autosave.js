;(function($) {
	$.autosave = function(form, button, draftMarker, time) {
		var autosave = {			
			/*
				Autosave form.
			*/
			form: null,
			
			/*
				Autosave save button.
			*/
			button: null,
			
			/*
				Timer id.
			*/
			timer: null,
			
			/*
				Autosave delay time.
			*/
			time: null,
			
			init: function(form, button, draftMarker, time) {
				autosave.form = form;
				autosave.draftMarker = draftMarker;
				autosave.button = button; 
				autosave.time = time;
			},
			
			/*
				Callback function that performs the saving call.
			*/
			save: function() {
				$.jGrowl('Saving using auto-saving.');
				$(autosave.draftMarker).val(1);
				$(autosave.form).submit();
			},
			
			/*
				Callback function called before saving.
			*/
			beforeSave: function() {
				$(autosave.button).attr('disabled','disabled');
				clearTimeout(autosave.timer)
				$(autosave.form).bind('keypress',autosave.detect);
			},
			
			/*
				Callback function called when the form changes.
			*/
			detect: function(event) {
				$(autosave.form).unbind('keypress');
				$(autosave.button).removeAttr('disabled');
				autosave.timer = setTimeout(autosave.save, autosave.time);
				$.jGrowl('Form changed!');
			}
		};
		
		this.beforeSave = function() {
			autosave.beforeSave();
		};
		
		autosave.init(form,button,draftMarker,time);
		$(form).bind('keypress',autosave.detect);
		
		return this;
	};
})(jQuery);