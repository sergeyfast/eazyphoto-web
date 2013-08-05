<?php
    if ( !empty($__useEditor) ) {
?>
<script language="javascript" type="text/javascript" src="{web:js://ext/tiny_mce/tiny_mce_gzip.js}"></script>
<script type="text/javascript">
    tinyMCE_GZ.init({
    	plugins : "advimage,advlink,contextmenu,fullscreen,inlinepopups," +
    	          "nonbreaking,paste,style,visualchars,table",
    	themes : 'advanced',
    	languages : 'ru',
    	disk_cache : true,
    	debug : false
    });

    function vfsBrowser(field_name, url, type, win) {
        var cmsURL = '{web:vt://vfs/mce}';
        var searchString = window.location.search;

        if (searchString.length < 1) {
            searchString = "?";
        }

        tinyMCE.activeEditor.windowManager.open({
            file : cmsURL + searchString + "&type=" + type + "&file=" + url, // PHP session ID is now included if there is one at all
            title: "File Browser",
            width : 900,  // Your dimensions may differ - toy around with them!
            height : 730,
            resizable : "yes",
            close_previous : "no"
        }, {
            window : win,
            input : field_name,
            resizable : "yes",
            inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
            editor_id : tinyMCE.selectedInstance.editorId
        });
        return false;
    }


    if ( $(".mceEditor").length > 0 )  {
        $(".mceEditor").wrap('<div style="display: inline-block; //display: inline;"></div>')
                tinyMCE.init({
                mode : "textareas",
                editor_selector : "mceEditor",
                language : "ru",
                theme : "advanced",
                plugins : "advhr,advimage,advlink,contextmenu,fullscreen,inlinepopups,nonbreaking,paste,style,table,visualchars",

                // Theme options
                theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,|,fullscreen,|,removeformat,charmap,|,tablecontrols",
                theme_advanced_buttons2 : "cut,copy,paste,pastetext,|,undo,redo,|,link,unlink,anchor,image,cleanup,code,|,styleprops,|,visualchars,nonbreaking,|,bullist,numlist",
                theme_advanced_buttons3 : "",
                theme_advanced_toolbar_location : "top",
                theme_advanced_toolbar_align : "left",
                theme_advanced_statusbar_location : "bottom",
                theme_advanced_resizing : true,
        	    plugi2n_insertdate_dateFormat : "%d.%m.%Y",
        	    plugi2n_insertdate_timeFormat : "%H:%M:%S",
        		file_browser_callback : 'vfsBrowser',
        		theme_advanced_resize_horizontal : true,
                extended_valid_elements : "iframe[src|width|height|name|align|frameborder|scrolling|marginheight|marginwidth]",
                paste_auto_cleanup_on_paste : true,
        		paste_convert_headers_to_strong : false,
        		paste_strip_class_attributes : "all",
        		paste_remove_spans : true,
        		paste_remove_styles : true,
                relative_urls : false,
                remove_script_host : true,
                verify_html : true,
                cleanup : true,
                <?php if ( !empty( $__editorDisableP ) ) { ?>
					forced_root_block : '',
					force_br_newlines : true,
					force_p_newlines : false,
                <?php } ?>
                document_base_url : "{web:/}"
            });
    }
</script>
<?php
    }
?>
</body>
</html>