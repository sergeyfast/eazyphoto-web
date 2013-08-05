( function( $ ) {

    var defaultOptions = {
        cancelText: 'Cancel'
        , okText: 'OK'
        , okCallback: function() {}
    };



    $.fn.confirmdialog = function ( userOptions ) {

        var options = $.extend( defaultOptions, userOptions );
        var template = $('<div class="confirm-dialog"><div class="confirm-content"></div><ul class="confirm-buttons"><li class="confirm-ok"><a class="button green" href="#">' + options.okText + '</a></li><li class="confirm-cancel"><a class="button" href="#">' + options.cancelText + '</a></li></ul></div>');

        this.each(function(){
            var dialogContents = $( template );
            var dialog;
            dialogContents.children( '.confirm-content' ).html('').append( this );
            dialogContents.attr('title', this.title)

            dialogContents.find('.confirm-ok').children().click( function () {
                if ( options.okCallback.call( this ) ) {
                    dialog.dialog('close');
                }
                return false;
            });

            dialogContents.find('.confirm-cancel').children().click( function () {
                dialog.dialog( 'close' );
                return false;
            });

            dialog = dialogContents.dialog({
                'modal': true
                , closeOnEscape: true
                , open: function(event, ui) {
                    //bing key presses
                    dialogContents.keypress(function ( event ) {
                        var code = ( event.keyCode ) ? event.keyCode : event.which;
                        if( code == 32 && options.okCallback.call( this ) ) {
                            dialog.dialog('close');
                        }
                        return false;
                    });
                }
            });
        })

    };
} )( jQuery );