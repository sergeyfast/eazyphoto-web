<? if ( $isMce ) { ?>
    <script type="text/javascript">
        function Feedback(file) {
            var win = top.tinymce.activeEditor.windowManager,
                args = win.getParams();
            args.window.document.getElementById(args.input).value = file.path;
            win.close();
            self.close();
        }
    </script>
<? } else { ?>
    <script type="text/javascript">
        function Feedback(file) {
            if (window.parent) {
                window.parent.vfsSelector.Feedback(file);
                window.parent.vfsDialog.dialog('close');
            }
        }
    </script>
<? } ?>
</body>
</html>