$(function(){
    $('#year').change(function(){
        var $this = $(this);
        document.location = $this.data('url') + '&year=' + $this.val()
    });

    $('#story').change(function(){
        var $this = $(this);
        document.location = $this.data('url') + '&story=' + $this.is(':checked')
    });
});