( function ( $ ) {
    var dateTimeTemplate = '<div class="displayInlineBlock"><input type="text" class="dateInput dateInput-date" /><input type="text" class="timeInput dateInput-time" /></div>';
    var dateTemplate = '<div class="displayInlineBlock"><input type="text" class="dateInput dateInput-date" /></div>';
    var timeTemplate = '<div class="displayInlineBlock"><input type="text" class="dateInput dateInput-time" /></div>';

    $.fn.datetimepicker = function () {

        return this.each( function () {
            switch( $(this).attr('rel') ) {
                case 'dateTime':
                    template = dateTimeTemplate;
                    break;
                case 'date':
                    template = dateTemplate;
                    break;
                case 'time':
                    template = timeTemplate;
                    break;
            }

            function refresh() {
                if( typeof( dateText ) === 'undefined' ) {
                    dateText = '';
                }
                if( typeof( timeText ) === 'undefined' ) {
                    timeText = '';
                }

                //time fix
                if( timeText.length == 4 ) {
                    timeText = '0' + timeText;
                }

                $date.val( dateText );
                $time.val( timeText );
                $self.val( jQuery.trim( dateText + ' ' + timeText )).change();
                if ( !$self.hasClass( 'dateInput-initialized' ) ) {
                    $self.addClass( 'dateInput-initialized' )
                }
            }

            var $self = $( this );
            var $controls = $( template );
            $self.after( $controls );
            var $date = $controls.find( '.dateInput-date' );
            var $time = $controls.find( '.dateInput-time' );

            var val = $self.val();
            var dateText = '';
            var timeText = '';
            if ( val ) {
                var arr = val.split(' ');
                switch( $(this).attr('rel') ) {
                    case 'dateTime':
                        dateText = arr[0];
                        timeText = arr[1];
                        break;
                    case 'date':
                        dateText = arr[0];
                        timeText = '';
                        break;
                    case 'time':
                        dateText = '';
                        timeText = arr[0];
                        break;
                }
            }

            refresh();

            $date.datepicker( {
                'onSelect': function ( text ) {
                    dateText = text;
                    refresh();
                }
            });
            $date.change( function(){
                dateText = $(this).val();
                refresh();
            });
            $time.mask( '99:99' ).change( function () {
                timeText = $(this).val();
                refresh();
            });

        } );
    };

} ) ( jQuery );