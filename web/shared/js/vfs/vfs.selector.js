function VfsWindow(url, title, width, height) {
    var dialog = $('<div style="position:relative;"><iframe id="vfsFrame" src="' + url + '" style="width:100%; border: 0; margin: 0; height: 100%;"></iframe></div>')
        .dialog({'height':height, 'width':width, 'title':title, modal:true, resizable: false});

    window.vfsDialog = dialog;
    $('#vfsFrame').css({'width':'100%'});
    return dialog;
}



function VfsSelector(path) {
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
    }


    this.OpenFile = function (fileId, currentElementId, mode) {
        this.currentElementId = currentElementId;
        if ( mode == 'path' ) {
            this.vfsDialog = VfsWindow(this.path + "?file=" + fileId, this.title, this.width, this.height);
        } else if ( mode == 'fileId' ) {
            this.vfsDialog = VfsWindow(this.path + "?fileId=" + fileId, this.title, this.width, this.height);
        }
    }


    this.Feedback = function (result) {
        this.lastFile = result;
        this.setFileObject(this.currentElementId);
        this.drawFile($("#" + this.currentElementId));
    }

    /**
     * Init Draw
     */
    this.Init = function () {
        this.controlName = "vfsSelector";
        $(".vfsFile").each(function () {
            vfsSelector.drawFile($(this))
        });
    }


    this.DeleteFile = function (fileId) {
        $("#" + fileId).val('-1');
        this.drawFile($("#" + fileId));
    }


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
    }


    /**
     * Draw File
     */
    this.drawFile = function (node) {
        var current = node;
        var areaId = "vfsArea_" + current.attr('id');
        $("#" + areaId).remove();

        var mode = current.data( 'mode' ); // fileId or path
        var xhtml = '<div id="' + areaId + '" class="fileinput">';

        if (current.val() == '' || current.val() == '-1') {
            xhtml = xhtml + '<a href="javascript:vfsSelector.Open( null, \'' + current.attr('id') + '\' );">' + vfsConstants.langOpen + '</a>';
        } else {
            if (current.attr('vfs:previewType') == vfsSelector.ImagePreviewType) {
                xhtml += '<a href="' + current.attr('vfs:src') + '" class="fancy"><img class="image" width="50" height="50" src="' + current.attr('vfs:src') + '"/></a>';
                xhtml += '<div class="info">';
            } else {
                xhtml = xhtml + '<div class="info-short">';
            }

            xhtml += '<p><a class="filename" href="' + current.attr('vfs:src') + '" target="_blank">' + current.attr('vfs:name') + '</a>&nbsp;&nbsp;';
            xhtml += '<a class="delete" href="javascript:vfsSelector.DeleteFile( \'' + current.attr('id') + '\' );" title="' + vfsConstants.langDelete + '">' + vfsConstants.langDelete + '</a>';
            xhtml += '</p>';
            xhtml += '<a class="edit" href="javascript:vfsSelector.OpenFile( \'' + current.val() + '\',\'' + current.attr('id') + "','" + mode + "' );\">" + vfsConstants.langEdit + '</a> ';
            xhtml += '</div>';
        }

        xhtml = xhtml + '</div>';

        current.after(xhtml);
        $('a.fancy').fancybox();
    }
}

VfsSelector.prototype = new BaseSelector;

var vfsConstants = new VFSConstants('');