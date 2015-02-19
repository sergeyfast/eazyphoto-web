function VfsWindow(url, title, width, height) {
    var dialog = $('<div style="position:relative;"><iframe id="vfsFrame" src="' + url + '" style="width:100%; border: 0; margin: 0; height: 100%;"></iframe></div>')
        .dialog({'height':height, 'width':width, 'title':title, modal:true, resizable: false});

    window.vfsDialog = dialog;
    $('#vfsFrame').css({'width':'100%'});
    return dialog;
}



function VfsSelector(path) {
    this.title = '';
    this.height = 760;
    this.width = 860;
    this.left = (screen.width/2)-(this.width/2);
    this.top  = (screen.height/2)-(this.height/2);

    this.path = path;
    this.currentElementId = null;
    this.lastFile = null;
    this.ImagePreviewType = "image";
    this.DefaultFolderId = "";
    this.vfsDialog = null;

    this.Open = function( folderId, currentElementId ) {
        this.currentElementId = currentElementId;
        if ( folderId == null ) {
            folderId = this.DefaultFolderId;
        }

        this.vfsDialog = VfsWindow( this.path + "?folderId=" + folderId, this.title, this.width, this.height );
    };


    this.OpenFile = function (fileId, currentElementId, mode) {
        this.currentElementId = currentElementId;
        if ( mode == 'path' ) {
            this.vfsDialog = VfsWindow(this.path + "?file=" + fileId, this.title, this.width, this.height);
        } else if ( mode == 'fileId' ) {
            this.vfsDialog = VfsWindow(this.path + "?fileId=" + fileId, this.title, this.width, this.height);
        }
    };


    this.Feedback = function (result) {
        this.lastFile = result;
        this.setFileObject(this.currentElementId);
        this.drawFile($("#" + this.currentElementId));
    };

    /**
     * Init Draw
     */
    this.Init = function () {
        this.controlName = "vfsSelector";
        $(".vfsFile").each(function () {
            vfsSelector.drawFile($(this))
        });
    };


    this.DeleteFile = function (fileId) {
        $("#" + fileId).val('-1');
        this.drawFile($("#" + fileId));
    };


    /**
     * Set File Object
     */
    this.setFileObject = function (vfsFileId) {
        var $file = $("#" + vfsFileId);
        var mode = $file.data('mode');
        if ( !mode ) {
            mode = "fileId"
        }

        if ( mode == "fileId" ) {
            $("#" + vfsFileId).attr("vfs:name", this.lastFile.name).val(this.lastFile.id).attr("vfs:src", this.lastFile.path);
        } else if ( mode == "path" ) {
            $("#" + vfsFileId).attr("vfs:name", this.lastFile.shortPath).val(this.lastFile.shortPath).attr("vfs:src", this.lastFile.path);
        }
    };


    /**
     * Draw File
     */
    this.drawFile = function (node) {
        var current = node;
        var areaId = "vfsArea_" + current.attr('id');
        $("#" + areaId).remove();

        var mode = current.data( 'mode' ); // fileId or path
        var $xhtml = $('<div class="fileinput"/>').attr('id', areaId);

        if (current.val() == '' || current.val() == '-1') {
            $xhtml.append(
                $('<a class="_add"><i class="foundicon-plus fsSmall"></i> ' + vfsConstants.langOpen + '</a>').click(function (e) {
                    e.preventDefault();
                    vfsSelector.Open(null, current.attr('id'));
                }).addClass( current.attr('vfs:previewType') == vfsSelector.ImagePreviewType ? 'imgThumb' : 'theFile')
            );
        } else {
            var $a = $('<a/>').attr({href: current.attr('vfs:src'), title: current.attr('vfs:name')});

            if (current.attr('vfs:previewType') == vfsSelector.ImagePreviewType) {
                $a.css('background-image', 'url(' + current.attr('vfs:src') + ')').addClass('fancy imgThumb');
            } else {
                $a.addClass('theFile').attr('target', '_blank').append( current.attr('vfs:name'))
            }

            $a.append($('<span title="' + vfsConstants.langEdit + '" class="_edit"></span>')).find('span._edit').click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                vfsSelector.OpenFile(current.val(), current.attr('id'), mode);
            });

            $a.append($('<span title="' + vfsConstants.langDelete + '" class="_del"></span>')).find('span._del').click(function (e) {
                e.preventDefault();
                e.stopPropagation();
                vfsSelector.DeleteFile(current.attr('id'));
            });

            $xhtml.append( $a )
        }


        current.after($xhtml);
        $('a.fancy').fancybox();
    }
}

var vfsConstants = new VFSConstants('');
var vfsSelector;

$(function(){
    vfsSelector = new VfsSelector( root + "vt/vfs/" );
    vfsSelector.Init();
});