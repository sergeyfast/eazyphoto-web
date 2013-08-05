(function ( $ ) {
    var defaults = {
        'template': '<a href="#" class="clearable">clear</a>'
    };

    var clearables = new Array();

    function setPosition( input, link ) {
        var position = input.position();
        link.css( {
            'left': position.left + input.width() - link.width() + 'px'
            , 'top': position.top+ input.height()/2 - link.height()/2 + 'px'
            , 'position': 'absolute'
        });
    };

    $.fn.clearable = function ( userOptions ) {
        var options = $.extend( defaults, userOptions );
        this.each( function () {

            function checkLinkVisibility() {
                if ( self.val() == '' && linkVisible ) {
                    linkVisible = false;
                    link.fadeOut( 200 );
                }
                if ( self.val() != '' && !linkVisible ) {
                    setPosition( self, link );
                    linkVisible = true;
                    link.fadeIn( 200 );
                }
            }

            var self = $(this);
            var link = $(options.template);
            var linkVisible = true;
            self.parent().append( link );
            if ( self.val() == '' ) {
                linkVisible = false;
                link.hide();
            }
            clearables.push( { 'input': self, 'link': link } );
            
            setPosition( self, link );
            link.click( function() {
                self.val( '' );
                self.trigger('change');
                checkLinkVisibility();
                return false;
            });
            self.bind('click keydown', checkLinkVisibility );
        });
    }

    $(window).bind('resize.clearable', function () {
        for ( var i = 0; i < clearables.length; i ++ ) {
            setPosition( clearables[i].input, clearables[i].link );
        }
    });

})( jQuery );