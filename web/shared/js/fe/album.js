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
});