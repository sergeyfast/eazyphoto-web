var sessionData, $objectImages, objectImageErrors, objectImages, objectImageType;

/**
 * Counters for image index
 */
var Counter = function () {
    this.index = -1;
};

Counter.prototype.nextIndex = function () {
    this.index++;
    return this.index;
};

var imageCounter = new Counter();

/**
 * Image Controls
 */
$(function () {
    $objectImages = $('#objectImages');

    // draw images
    if ( objectImages != null ) {
        $.each( objectImages, function( k, oi ) {
            oi.c = imageCounter;
            $objectImages.append(ich.tmplObjectImage(oi));
        });

        displayErrors( objectImageErrors );
        rebindImageControls();
    }

    // add image
    $('#add-image').click(function (e) {
        e.preventDefault();
        var data = { c : imageCounter };
        $objectImages.append( ich.tmplObjectImage(data) );
        rebindImageControls();
    });

    // remove image
    $objectImages.on("click", "._del", function (e) {
        e.preventDefault();
        $(this).parent().parent().remove();
    });

    if ( !objectImageType) {
        objectImageType = 'objects';
    }


    // assign callback
    $('#objectImages_upload').uploadify({
        'uploader': root + 'int/controls/image-upload/' + objectImageType + '/',
        'swf': root + 'shared/js/ext/uploadify/uploadify.swf',
        'langFile': root + 'shared/js/ext/uploadify/uploadifyLang_en.js',
        'cancelImage': root + 'shared/js/ext/uploadify/uploadify-cancel.png',
        'method': 'post',
        'auto': true,
        'debug': false,
        'multi': true,
        'buttonText': 'Загрузить файлы',
        'width': 130,
        'formData': sessionData,
        'checkExisting': false,
        'onUploadSuccess': function (file, data, response) {
            var result = JSON.parse(data);
            if (result) {
                uploadCallback(file, result);
            }
        }
    });
});


/** Image Upload Callback from images-queue */
function uploadCallback(file, data) {
    if (data.error) {
        alert(data.error);
        return false;
    }

    data.c = imageCounter;
    $objectImages.append(ich.tmplObjectImage(data));
    rebindImageControls();
}

/** Display Errors */
function displayErrors(errors) {
    if (typeof( errors ) == 'undefined') {
        return;
    }

    $.each(errors, function (index, fields) {
        $.each(fields, function (field) {
            $("input[name$='[images][" + index + "][" + field + "]']").parent().addClass('bcWarn');
        });
    });
}

/** reinit vfs & sortable */
function rebindImageControls() {
    vfsSelector.Init();
    $objectImages.sortable({
        'items': '.objectImage'
        , 'forceHelperSize': true
        , 'forcePlaceholderSize': true
        , 'handle': '.handle'
    });
}
