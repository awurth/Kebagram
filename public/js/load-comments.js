
$(document).ready(function () {

    $('.display-comments').click(function (e) {
        e.preventDefault();
        var $this = $(this);

        $.ajax({
            url: $(this).data('url'),
            success: function (data) {
                $this.replaceWith(data);
            }
        });
    });

});
