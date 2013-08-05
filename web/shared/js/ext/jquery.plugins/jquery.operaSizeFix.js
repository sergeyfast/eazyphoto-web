( function( $ ) {

    $.fn.operaSizeFix = function () {

        if ( !$.browser.opera ) return this;

        var params = [
            { 'name': 'left' , 'horisontal': true }
            , { 'name': 'right' , 'horisontal': true }
            , { 'name': 'width' , 'horisontal': true }
            , { 'name': 'top' , 'horisontal': false }
            , { 'name': 'bottom' , 'horisontal': false }
            , { 'name': 'height' , 'horisontal': false }
        ];

        var elements = new Array();

        function setBounds () {
            for ( var i = 0; i < elements.length; i ++ ) {
                var element = elements[i].element;
                var offsetParent = element.offsetParent();
                var w = offsetParent.width();
                var h = offsetParent.height();
                var v;
                for ( var k in params ) {
                    if ( elements[i].bounds[params[k].name].indexOf('%') != -1 ) {
                        v = params[k].horisontal?w:h;
                        elements[i].element.css( params[k].name, Math.floor(parseFloat(elements[i].bounds[params[k].name])/100*v) + 'px' );
                    }
                }
            }
        };

        this.each ( function () {
            var element = $( this );
            var item = { 'element': element, 'bounds': {} };

            for ( var k in params ) {
                item.bounds[params[k].name] = element.css(params[k].name);
            }
            elements.push( item );
        } );

        var resizeTmr;
        $(window).bind('resize.operaSizeFix', function() {
            clearTimeout( resizeTmr );
            setTimeout( function () {
                setBounds();
            }, 100);
        });

        setBounds();

        return this;
    };
} ) (jQuery);
