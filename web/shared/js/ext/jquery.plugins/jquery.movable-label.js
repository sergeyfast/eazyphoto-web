jQuery.fn.movableLabel = function () {
	return this.each( function (index) {
		if ( this.htmlFor ) {
			var label = jQuery ( this );
			var input = jQuery ( '#' + this.htmlFor);
			
			if ( !input ) {
				return false;	
			}

			label.hide ();

			inputPosition = input.position();
			
			positionX = inputPosition.left;
			positionY = inputPosition.top;

			plusX = 0;
			plusY = 0;

			label.css({
				'position': 'absolute',
				'left':     positionX + plusX + 'px',
				'top':      positionY + plusY + 'px',
				'cursor':  'text'
			});
			input.focus(function(){
				label.hide();
			});
			
			input.blur(function(){
				if ( this.value == '')
					label.show();
			});
			
			label.click(function() {
				label.hide();
			});
			
			if ( input.val() == '')
				label.show();

		}
	} );
};