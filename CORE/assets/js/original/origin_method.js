/*
	Dexa JQuery additional method library

	-by Dexa
*/

(function ($) {

	// Set attribute to multiple elements at once
	$.fn.attrs = function (attribute, value = '') {

		if (typeof attribute == 'object') {

			$.each(this, function () {

				$(this).attr(attribute);
			});
		} else {

			$.each(this, function () {

				$(this).attr(attribute, value);
			});
		}
	}

	// Remove attribute from multiple elements at once
	$.fn.removeAttrs = function (attribute) {

		$.each(this, function () {

			$(this).removeAttr(attribute);
		});
	}

	// Get element class list
	$.fn.classList = function () {

		return this[0].classList;
	}

	// Get element class exist
	$.fn.classExists = function (attribute) {

		let returnValue = $.inArray(attribute, this[0].classList);

		return returnValue >= 0 ? true : false;
	}

    // Remove Element with fade
    $.fn.removeFading = function(duration = 500) {
        
        $.each(this, function () {

            this.style.transition = duration + 'ms';
            this.style.opacity = '0';

            setTimeout((elem) => {
                
                elem.remove();
            }, (duration + 1), this);

        });
    }
})(jQuery);