var __token;
$(document).ready( function () {
    //errors
    if( typeof( jsonErrors ) === 'undefined' ) {
        jsonErrors = '';
    }
    $.each( jsonErrors, function( i, item ) {
		$('[data-row="' + item.title + '"]').addClass( "error");
		$.each( item.errors, function( j, error ) {
			$('[data-row="' + item.title + '"]').append( '<p class="error">' + error + '</p>' );
		});
	});

	//menu
    $('.header-menu').superfish();

	//search form hide trigger
    $('.search').each( function() {
        var self = $(this);
        var closed = self.hasClass( 'closed' );
        var isAnimating = false;
        self.children('.search-close, .search-open').click( function (){
            if ( isAnimating ) {
                return false;
            }
            isAnimating = true;
            var oldHeight = self.height();
            if ( closed ) {
                self.removeClass('closed');
                $.cookie('hideSearch', 'false');
            }
            else {
                self.addClass('closed');
                $.cookie('hideSearch', 'true');
            }
            var newHeight = self.height();
            self.css( 'height', oldHeight + 'px' );
            self.animate(
                { 'height': newHeight + 'px' }
                , 150
                , function() {
                    self.css('height', '');
                    isAnimating = false;
                }
            );
            closed = !closed;
            return false;
        } );
    } );
	
	//hints
    $('.hint-icon').click(function () {
        var self = $(this);
        var text = self.parent().children('.hint-text');
        text.fadeIn('fast', function () {

        });
        text.mouseleave( function () {
            text.fadeOut('fast');
        });
        return false;
    });

	//objects table
    var objects = $( '.objects' );
	
	//even rows
	objects.find("tbody tr:even").addClass('even');

    //sotr table
    objects.find("thead th.sorted").click(function(){
        field   = $(this).attr('data-field');
        sort    = '';
        if( $(this).hasClass('headerSortUp') ) {
            $(this).removeClass('headerSortUp');
            $(this).addClass('headerSortDown');
            sort = 'ASC';
        } else if( $(this).hasClass('headerSortDown') ) {
            $(this).removeClass('headerSortDown');
            $(this).addClass('headerSortUp');
            sort = 'DESC';
        } else {
            $(this).addClass('headerSortDown');
            sort = 'ASC';
        }
        $('#sortField').val( field );
        $('#sortType').val( sort );
        $('#searchForm').submit();
    });

	//tabs
    $('.tabs').tabs({
        create: function ( event, ui ) {
            var self = $(this);
            var errors = new Array();
            self.children('.ui-tabs-panel').each( function( index ) {
                if ( $(this).find('.error').length > 0 ) {
                    errors.push( index );
                }
            });
            var lis = self.children('.ui-tabs-nav').children();
            for ( var i = 0; i < errors.length; i ++ ) {
                lis.eq( errors[i] ).addClass('error');
            }
        }
        , select: function(event, ui) {
            $("#selectedTab").val(ui.index);
        }
        , selected: $("#selectedTab").val()
    });
    
	//required labels fix
    if ( $.browser.msie && parseInt($.browser.version) < 8 ) {
        $('.required label').append( "<span>*</span>" );
    }

    //pages
    $('.paginator-pages a').click( function(){
        page = parseInt( $('.paginator-pages input').val() );
        if( $(this).hasClass('prev') ) {
            page-=2;
        }
        $('#pageId').val( page );
	    $('#searchForm').submit();
        return false;
    });

    //manual page input
    var paginatorInput = $('.paginator-pages input');
    function manualPageChange( obj ) {
        page = parseInt( obj.val() ) - 1;
        $('#pageId').val( page );
        $('#searchForm').submit();
    }
    paginatorInput.keypress(function ( event ) {
        var code = ( event.keyCode ) ? event.keyCode : event.which;
        if( code == 13 ) {
            manualPageChange( $(this) );
        }
    });
    paginatorInput.bind( 'change', function(){
        manualPageChange( $(this) );
    });

    //page sizes
    $('ul.paginator-sizes li a').click( function(){
        $('#pageId').val(0);
        $('#pageSize').val( $(this).attr('data-value') );
        $( "#searchForm" ).submit();
        return false;
    });

	//datepicker
    $('.dtpicker').datetimepicker();
	
	//clearable inputs
    $('.search-form').find('input:text').clearable();

    $('form#data-form').append( $('<input>', { 'name': '__token', 'type': 'hidden', 'value': __token } ) );

    $('.delete-object-return').click(function(){
        objectId = $(this).parents('form').attr('data-object-id');
        $('<div title="' + Lang.common.deleteDialogHeader + '">' + objectDeleteStr + '</div>').confirmdialog( {
            okCallback: function () {
                $.post( objectBasePath + 'delete/' + objectId, { '__token' : __token  }, function(data) {
                    document.location = objectBasePath;
                });
            }
            , okText: Lang.common.deleteBtn
            , cancelText: Lang.common.cancelBtn
        } );
        return false;
    });

    $('.delete-object').click(function(){
        objectId = $(this).parents('tr').attr('data-object-id');
        $('<div title="' + Lang.common.deleteDialogHeader + '">' + objectDeleteStr + '</div>').confirmdialog( {
            okCallback: function () {
                $.post( objectBasePath + 'delete/' + objectId, { '__token' : __token }, function(data) {
                    $('[data-object-id="' + objectId + '"]').fadeOut();
                });
                return true;
            }
            , okText: Lang.common.deleteBtn
            , cancelText: Lang.common.cancelBtn
        } );
        return false;
    });

    //add and update
    $('form#data-form div.buttons div.buttons-inner input').click(function(){
        if( $(this).hasClass('edit-preview') ) {
            $('#redirect').val('view');
        }
        $('form#data-form div.buttons a.back').remove();
        $('form#data-form div.buttons div.buttons-inner input').attr('disabled', 'disabled').addClass('disabled');
        $('form#data-form').submit();
        return false;
    });

    //fancy
    $('a.fancy').fancybox();

    //autocomplete layout
    $('input.autocomplete').after( '<span class="autocomplete-hint" title="' + Lang.common.autocompleteHint + '"></span>' );
});

//base selector
function BaseSelector( path ) {
    this.path = path;

    this.title = '';

    this.height = 760;

    this.width = 860;

    this.left = (screen.width/2)-(this.width/2);

    this.top  = (screen.height/2)-(this.height/2);

    this.controlName = "selector";

    this.Unblock = function() {
         $.unblockUI();
    }

    this.Block  = function() {
        if($.blockUI){
         $.blockUI({
          message: Lang.common.windowOpened + '<br /> <a style="color:#FFFFFF" href="javascript: ' + this.controlName + '.Unblock();">' + Lang.common.windowClose + '</a>',
          css: {
           border:'none', padding:'15px', size:'12.0pt',
           backgroundColor:'#900', color:'#fff',
           opacity:'.8','-webkit-border-radius': '10px','-moz-border-radius': '10px'
          }
         });
        }
    }
}

//autocomplete patch
function monkeyPatchAutocomplete() {
    $.ui.autocomplete.prototype._renderItem = function( ul, item) {
        var t = item.label.replace(new RegExp("(?![^&;]+;)(?!<[^<>]*)(" + $.ui.autocomplete.escapeRegex(this.term) + ")(?![^<>]*>)(?![^&;]+;)", "gi"), "<span style='font-weight:bold;color:#000000;'>$1</span>");
        return $( "<li></li>" ).data( "item.autocomplete", item ).append( "<a>" + t + "</a>" ).appendTo( ul );
    };
}

//init autocomplete
function initAutocomplete( textSelector, hiddenSelector, url ) {
    monkeyPatchAutocomplete();
    $(textSelector).autocomplete({
        source: function( request, response ) {
            $.ajax({
                url: url,
                dataType: "json",
                data: {
                    q: request.term
                },
                success: function( data ) {
                    response( $.map( data, function( item ) {
                        return {
                            label: item.label,
                            value: item.title,
                            id:    item.id
                        }
                    }));
                }
            });
        }
        , select: function(event, ui) {
            $(hiddenSelector).val( ui.item.id );
        }
        , change: function(event, ui) {
            if( ui.item == null ) {
                $(hiddenSelector).val('');
            }
        }
    });

    $(textSelector).bind('keypress keydown', function(event){
        var code = ( event.keyCode ) ? event.keyCode : event.which;
        if( ( code >= 48 ) || ( code == 46 ) || ( code == 8 ) ) {
            $(hiddenSelector).val('');
        }
    });

    $(textSelector).bind('change', function(event){
        if( $(this).val() == '' ) {
            $(hiddenSelector).val('');
        }
    });
}