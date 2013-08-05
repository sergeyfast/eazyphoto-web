<? if ( $isMce ) { ?>
<script language="javascript" type="text/javascript" src="{web:js://ext/tiny_mce/tiny_mce_popup.js}"></script>
<script type="text/javascript">
    InitFunction = function () {
        // patch TinyMCEPopup.close
        tinyMCEPopup.close_original = tinyMCEPopup.close;
        tinyMCEPopup.close = function () {
            tinyMCE.selectedInstance.fileBrowserAlreadyOpen = false;
            tinyMCEPopup.close_original();
        };

        var allLinks = document.getElementsByTagName("link");
        allLinks[allLinks.length-1].parentNode.removeChild(allLinks[allLinks.length-1]);
    }

    $(function(){
        tinyMCEPopup.executeOnLoad('InitFunction();');
    });

    function Feedback( file ) {
        var URL = file.path;
        var win = tinyMCEPopup.getWindowArg("window");
        win.document.getElementById(tinyMCEPopup.getWindowArg("input")).value = URL;

        if ( file.width ) {
            win.document.getElementById('width').value = file.width;
            win.document.getElementById('height').value = file.height;
        }

        tinyMCEPopup.close();
        self.close();
    }
</script>
<? } else { ?>
<script type="text/javascript">
    function Feedback( file ) {
        if ( window.parent ) {
            window.parent.vfsSelector.Feedback( file );
            window.parent.vfsDialog.dialog('close');
        }
    }
</script>
<? } ?>
</body>
</html>