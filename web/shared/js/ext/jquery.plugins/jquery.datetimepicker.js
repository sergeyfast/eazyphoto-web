( function ( $ ) {
    var dateTimeTemplate = '<div class="dateinput"><input type="text" class="dateinput-date" /><input type="text" class="dateinput-time" /></div>';
    var dateTemplate = '<div class="dateinput"><input type="text" class="dateinput-date" /></div>';
    var timeTemplate = '<div class="dateinput"><input type="text" class="dateinput-time" /></div>';

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
            //console.log( $(this).attr('rel') );
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
                
                date.val( dateText );
                time.val( timeText );
                self.val( jQuery.trim( dateText + ' ' + timeText ));
                if ( !self.hasClass( 'dateinput-initialized' ) ) {
                    self.addClass( 'dateinput-initialized' )
                }
            };

            var self = $( this );

            var controls = $( template );
            self.after( controls );
            var date = controls.find( '.dateinput-date' );
            var time = controls.find( '.dateinput-time' );

            var val = self.val();
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

            date.datepicker( {
                'onSelect': function ( text ) {
                    dateText = text;
                    refresh();
                }
            });
            date.change( function(){
                dateText = $(this).val();
                refresh();
            });
            time.mask( '99:99' ).change( function () {
                timeText = $(this).val();
                refresh();
            });
            
        } );
    };
    
} ) ( jQuery );