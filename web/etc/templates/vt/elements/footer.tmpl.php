<?php
    use Eaze\Helpers\AssetHelper;

    if ( !empty( $__useEditor ) ) {
?>
        <script type="text/javascript" src="{web:js://ext/tinymce/tinymce.gzip.js}"></script>
        <script>
            tinymce.init({
                plugins : "advlist autolink lists link image charmap print preview hr anchor pagebreak searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking save table contextmenu directionality template paste textpattern spellchecker",
                languages : 'ru',
                disk_cache : true,
                debug : false
            });

            function vfsBrowser(field_name, url, type, win) {
                var cmsURL = '{web:vt://vfs/mce}',
                    searchString = window.location.search;

                if (searchString.length < 1) {
                    searchString = "?";
                }

                tinymce.activeEditor.windowManager.open({
                    file : cmsURL + searchString + "&type=" + type + "&file=" + url,
                    title: "VFS",
                    width : 900,
                    height : 730,
                    inline: true,
                    resizable : "yes",
                    close_previous : "no"
                }, {
                    window : win,
                    input : field_name,
                });
                return false;
            }

            if ( $(".mceEditor").length > 0 )  {
                tinymce.init({
                    mode : "textareas",
                    selector : ".mceEditor",
                    language : "ru",
                    plugins: [
                        "advlist autolink lists link image charmap print preview hr anchor pagebreak",
                        "searchreplace wordcount visualblocks visualchars code fullscreen",
                        "insertdatetime media nonbreaking save table contextmenu directionality",
                        "template paste textpattern spellchecker",
                    ],
                    toolbar1: "code | insertfile undo redo | styleselect | bold italic | bullist numlist outdent indent | link unlink image",
                    image_advtab: true,
                    toolbar_items_size: 'small',
                    file_browser_callback : vfsBrowser,
                    extended_valid_elements : "iframe[src|width|height|name|align|frameborder|scrolling|marginheight|marginwidth]",
                    spellchecker_languages: "Russian=ru,Ukrainian=uk,English=en",
                    spellchecker_rpc_url: "http://speller.yandex.net/services/tinyspell",
                    resize: 'both',
                    relative_urls : false,
                    remove_script_host : true,
                    document_base_url : "{web:/}",
                    content_css: "{web:css://tinymce.css}?<?= AssetHelper::GetRevision() ?>",
                    <?php if ( !empty( $__editorDisableP ) ) { ?>
                    forced_root_block : '',
                    force_p_newlines : false,
                    <?php } ?>
                    menu : {
                        file   : {title : 'File'  , items : 'newdocument fullscreen | spellchecker code'},
                        edit   : {title : 'Edit'  , items : 'undo redo | cut copy paste pastetext | selectall'},
                        insert : {title : 'Insert', items : 'link image media | template | hr charmap anchor pagebreak nonbreaking'},
                        view   : {title : 'View'  , items : 'visualaid visualblocks visualchars'},
                        format : {title : 'Format', items : 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
                        table  : {title : 'Table' , items : 'inserttable tableprops deletetable | cell row column'},
                    }
                });
            }
        </script>
<? } ?>
    </div>
</body>
</html>