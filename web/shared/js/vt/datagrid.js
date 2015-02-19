var __token;

jQuery.fn.reset = function () {
    $(this).find('input:text, input:password, input:file, select, textarea').val('');
    $(this).find('input:radio, input:checkbox').removeAttr('checked').removeAttr('selected');
};

$(function () {
    //datepicker
    $('.dtpicker').datetimepicker();
    $('.contentToggle').click(function (e) {
        e.preventDefault();
        $($(this).data('target')).toggle();
    });

    $('table.tableData th.tableRowCheckbox').closest('table').each(function () {
        siteTableRowCheckbox = new TableRowCheckbox();
        siteTableRowCheckbox.init($(this));
    });

    //sort table
    $(".tableData thead:not(.tableSorter) th.headerSort").click(function () {
        var $th = $(this),
            field = $th.attr('data-field'),
            sort = '';

        if ($th.hasClass('headerSortUp')) {
            $th.removeClass('headerSortUp').addClass('headerSortDown');
            sort = 'ASC';
        } else if ($th.hasClass('headerSortDown')) {
            $th.removeClass('headerSortDown').addClass('headerSortUp');
            sort = 'DESC';
        } else {
            $th.addClass('headerSortDown');
            sort = 'ASC';
        }

        $('#sortField').val(field);
        $('#sortType').val(sort);
        $('#searchForm').submit();
    });

    //pages
    $('.page-changer a').click(function (e) {
        var page = parseInt($('.page-changer input').val());
        if ($(this).hasClass('prev')) {
            page -= 2;
        }

        gotoPage(page);
        e.preventDefault()
    });

    //manual page input
    $('.page-changer input').keypress(function (event) {
        var code = ( event.keyCode ) ? event.keyCode : event.which;
        if (code == 13) {
            gotoPage($(this));
        }
    }).bind('change', function () {
        gotoPage($(this));
    });

    //page sizes
    $("#pageSize-changer").find("a").click(function (e) {
        $("#pageSize").val($(this).attr("data-value"));

        gotoPage(0);
        e.preventDefault()
    });

    $('select.select2').select2({allowClear: true});

    $('#ExtendedSearch').find('input, select').each(function() {
        var $this = $(this);
        if ( $this.val() != '' ) {
            $this.closest( 'div.row').detach().insertBefore( '#ExtendedSearch' )
        }
    });

    // delete dialog
    $('.delete-object').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        var objectId = $(this).parents('tr').attr('data-object-id');
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
                            $('[data-object-id="' + objectId + '"]').fadeOut();
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

    // table with checkboxes
    $('table#tableWithCheckboxes').each(function () {
        siteTableRowCheckbox = new TableRowCheckbox();
        siteTableRowCheckbox.init($(this));
    });

    var listButtons = $('ul.listButtons > li');
    listButtons.children('a, span').click(function (e) {
        var thisButton = $(this).parent('li');
        if (thisButton.hasClass('_popup')) {
            e.preventDefault();
            e.stopPropagation();
            if (thisButton.hasClass('_active')) {
                listButtons.removeClass('_active');
            } else {
                listButtons.removeClass('_active');
                thisButton.addClass('_active');
            }
        } else {
            listButtons.removeClass('_active');
        }
    });
    $('html').click(function () {
        listButtons.removeClass('_active');
    });
    //eof
});

function gotoPage(obj) {
    var page = ( obj instanceof $ ) ? parseInt(obj.val()) - 1 : obj;
    $('#pageId').val(page);
    $('#searchForm').submit();
}

// Table Row Checkbox
var siteTableRowCheckbox;
var TableRowCheckbox = function () {
    var elem,
        thControl,
        thCheck,
        tdChecks,

        disableThControl = function () {
            thControl.addClass('_disabled');
        },

        enableThControl = function () {
            thControl.removeClass('_disabled');
        },

        deselectAllTdChecks = function () {
            tdChecks.prop('checked', false);
        },

        selectAllTdChecks = function () {
            tdChecks.prop('checked', true);
        },

        init = function (el) {
            elem = el;
            thControl = $('ul#controlsForTableCheckboxes');
            thCheck = elem.find('th.tableRowCheckbox input');
            tdChecks = elem.find('td.tableRowCheckbox input');
            thCheck.click(function () {
                if ($(this).is(':checked')) {
                    enableThControl();
                    selectAllTdChecks();
                } else {
                    disableThControl();
                    deselectAllTdChecks();
                }
            });
            tdChecks.click(function () {
                if (tdChecks.is(':checked')) {
                    enableThControl();
                } else {
                    disableThControl();
                    thCheck.prop('checked', false);
                }
            });
            disableThControl();
        };

    return {
        init: init
    };

};