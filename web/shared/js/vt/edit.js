$(function(){
    $('div.tabs').each(function () {
        var selectedTabIndex = $('#selectedTab').val();
        var heads, conts;
        heads = $(this).find('div.tabs_head > ul > li');
        conts = $(this).find('div.tabs_cont');

        heads.eq(selectedTabIndex).addClass('_active');
        conts.eq(selectedTabIndex).addClass('_active');

        heads.click(function () {
            if (!$(this).hasClass('_active')) {
                heads.add(conts).removeClass('_active');
                $(this).add(conts.eq($(this).index())).addClass('_active');
                $('#selectedTab').val($(this).index())
            }
        });
    });

    // errors
    if( typeof( jsonErrors ) === 'undefined' ) {
        jsonErrors = '';
    }

    $.each( jsonErrors, function( i, item ) {
        var $row = $('[data-row="' + item.title + '"]');
        $row.find("input,select,textarea").addClass( "_error" );
        $.each( item.errors, function( j, error ) {
            $row.find("input:last,select,textarea").after( ' <span class="blockLabel fsSmall cWarn">' + error + '</span>' );
        });
    });

    // add and update buttons handler
    var $editForm = $('#data-form');
    $editForm.find('button[type=submit]').click(function(e){
        var $button = $(this);
        e.preventDefault();
        if( $button.hasClass('edit-preview') ) {
            var r = $button.data('redirect');
            $('#redirect').val( r ? r : 'view' );
        }

        $editForm.find("button[type=submit]").prop('disabled', true).addClass('cFade');
        $editForm.submit();
    });

    // add span to required elements
    $editForm.find('div.required > label').append( ' <span class="cWarn">*</span>' );

    // initialize select2
    $('select.select2').select2({allowClear: true});

    // delete dialog
    $('.delete-object-return').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        var objectId = $(this).parents('form').attr('data-object-id');
        $('<div title="' + Lang.common.deleteDialogHeader + '">' + objectDeleteStr + '</div>').dialog({
            resizable: false,
            height: 150,
            modal: true,
            buttons: [
                {
                    class: '_del',
                    text: Lang.common.deleteBtn,
                    click: function () {
                        $.post(objectBasePath + 'delete/' + objectId, {'__token': __token}, function (data) {
                            document.location = objectBasePath;
                        });
                        $(this).dialog('close');
                    }
                },
                {
                    text: Lang.common.cancelBtn,
                    click: function () {
                        $(this).dialog('close');
                    }
                }
            ]
        });
    });
});