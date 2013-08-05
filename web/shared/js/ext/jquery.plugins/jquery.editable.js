/**
 * It's a very simple jQuery plugin a-la status editing in vkontakte.ru
 * But much cooler :)
 *
 * @author Bulyonov Antony
 * @version 1.0
 * 
 */

jQuery.fn.editable = function (userOptions) {
	var defaultOptions = {
        method: 'overlay',
        selector: false,
        tag: '<input type="text" value="" class="editable-input" />',
        submitEvents: 'blur keydown',
        editEvents: 'click',
        cancelEvents: false,
        minWidth: 100,
        minHeight: 0,
        onEdit: function( element, input ) {
            return element.html();
        },
        onSubmit: function( element, input ) {
            return;
        }
	};
	var options = jQuery.extend(defaultOptions, userOptions);
	
	$(this).each (function () {

        var _editable = $(this);

		$(this).bind (options.editEvents, function() {

            var element = $(this);
			var w = element.width();
			var h = element.height();
            var offset = element.offset();
			var x = offset.left;
			var y = offset.top;
			var inputContainer = $( options.tag );
            var input = inputContainer;
            if (options.selector) {
                var input = $(options.selector, inputContainer);
            }
            switch (options.method) {
                case 'overlay':
                    $(document.body).append(inputContainer);
                    input.css({
                        'width': ( w < options.minWidth ? options.minWidth : w ) + 'px'
                        , 'left': x - ( w < options.minWidth ? options.minWidth - w : 0 )  + 'px'
                        , 'top': y + 'px'
                        , 'position':'absolute'
                    });
                break;
                case 'replace':
                    input.css({
                        'width': ( w < options.minWidth ? options.minWidth : w ) + 'px'
                        , 'height': ( h < options.minHeight ? options.minHeight : h ) + 'px'
                    });
                    inputContainer.insertAfter(element);
                    element.hide();
                break;
            }

            if (!element.hasClass('empty')) {
				input.val( options.onEdit( element, input ) );
			}

			input.focus();

            if (options.cancelEvents) {
                input.bind( options.cancelEvents, function() {
                    inputContainer.hide();
                    inputContainer.remove();
                    element.show();
                });
            }


            input.bind( options.submitEvents, function (e) {
                var isReady;
                _editable.show();
                if ( e.type == 'keydown' ) {
                    isReady = 1 && input && e.keyCode == 13;
                }
                else {
                    isReady = 1 && input;
                }
                if ( isReady ) {

                    if (_editable.html() != input.val()) {
                        if (options.onSubmit)
                            options.onSubmit(_editable, input);
                        if (input.val() == '') {
                            _editable.addClass('empty');
                        }
                        else {
                            _editable.removeClass('empty');
                        }
                    }
					inputContainer.hide();
					inputContainer.remove();
					input = null;
				}
            } );

            
			return false;
		});
	});	
};