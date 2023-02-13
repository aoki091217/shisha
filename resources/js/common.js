$(window).on('load', function () {
    let containerHeight = $('.main-contents .container').height();
    let breadcrumbHeight = $('.breadcrumb').outerHeight();
    let searchFormHeight = $('#searchForm').outerHeight();
    let footerHeight = $('#footer').outerHeight();

    $('.table-wrapper').height(containerHeight - breadcrumbHeight - searchFormHeight - footerHeight - '50');

    $('.table-wrapper .list-group-item-action').on('click', function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            $(this).addClass('active');
            $('.table-wrapper .list-group-item-action').not($(this)).removeClass('active');
        }
    });

    $('.table-wrapper .btn-outline-danger, #createButton').on('click', function () {
        let url = $(this).data('route');
        if ($(this).attr('id') === 'createButton') {
            $('form [name=_method]').prop('disabled', true);
        } else {
            $('form [name=_method]').prop('disabled', false);
        }

        $('form').attr('action', url).trigger('submit');
    });

    $('.btn-delete').on('click', function () {
        $('#form').attr('method', 'POST');
    });

    $('[type=submit]').on('click', function () {
        $(this).addClass('btn-prevent');
    });
});
