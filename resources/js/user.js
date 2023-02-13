$(window).on('load', function () {
    $('#isChange').on('click', function () {
        if ($(this).prop('checked')) {
            $('#userPassword, #userPassConfirm').prop('disabled', false);
        } else {
            $('#userPassword, #userPassConfirm').prop('disabled', true);
        }
    });
});
