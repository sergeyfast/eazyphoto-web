$(document).ready(function () {
    if (typeof $.fancybox == 'function') {
        $("a.fancybox").fancybox({
            padding: 0,
            margin: 5,
            nextEffect: "none",
            prevEffect: "none",
            helpers: {
                overlay: {
                    opacity: 1,
                    css: {'background-color': '#000000'}
                }
            }
        });
    }

    $('a.copy-to-clipboard').click(function(e) {
        e.preventDefault();
        window.prompt("Скопировать в буфер обмена: Ctrl+C, Enter", $(this).text());
    });
});