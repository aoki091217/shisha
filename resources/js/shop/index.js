import ajaxClass from '../common/ajax';

$(window).on('load', function () {
    // $('#shopForm .btn-group button').on('click', function () {
    //     let method = $(this).data('method');
    //     $('#shopForm [name=_method]').prop('disabled', true);
    //     $(`#shopForm [value=${method}]`).prop('disabled', false);
    // });

    $('.table-shop .list-group-item-action').on('click', function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            $(this).addClass('active');
            $('.table-shop .list-group-item-action').not($(this)).removeClass('active');
        }
    });

    // $('#shopForm button[type=button]').on('click', function () {
    //     let method = $(this).data('method');
    //     let route = $(this).data('route');
    //     let url = $('.table-shop .list-group-item-action.active').data(route);
    //     let shopId = $('.table-shop .list-group-item-action.active').data('shop-id');
    //     let name = $('#shopName').val()
    //     let csrf = $('meta[name="csrf-token"]').attr('content');

    //     let requests = {
    //         method: method,
    //         url: url,
    //         csrf: csrf,
    //         shop: {
    //             name: name,
    //             shop_id: shopId
    //         }
    //     }
    //     console.log(requests);
    //     ajaxClass.ajax(requests).done(function (response) {
    //         console.log(response);
    //     }).fail(function (response) {
    //         console.log(response);
    //     });
    // });

    $('.table-shop .btn-outline-danger, #createButton').on('click', function () {
        let url = $(this).data('route');
        if ($(this).attr('id') === 'createButton') {
            $('#shopForm [name=_method]').prop('disabled', true);
        } else {
            $('#shopForm [name=_method]').prop('disabled', false);
        }

        $('#shopForm').attr('action', url).trigger('submit');
    });
});
