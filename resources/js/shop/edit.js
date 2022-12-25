$(window).on('load', function () {
    $('#updateButton').on('click', function () {
        $('#shopForm').trigger('submit');
    });
});
