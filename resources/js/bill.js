$(window).on('load', function () {
    if ($('#isPeriod').prop('checked')) {
        $('#endDate').prop('disabled', false);
    }

    $('#isPeriod').on('click', function () {
        if ($('#endDate').prop('disabled')) {
            $('#endDate').prop('disabled', false);
        } else {
            $('#endDate').prop('disabled', true);
        }
    });

    $('#draftButton').on('click', function () {
        $('[name=_method]').prop('disabled', true);
    });

    if ($('#shopId option').first().val() != $('#shopId option:selected').val()) {
        getMembers().done(function (response) {
            createMemberOptions(response);
        });
    }

    $('#shopId').on('change', function () {
        if ($(this).val() != null) {
            getMembers().done(function (response) {
                createMemberOptions(response);
            });
        }
    });

    $('.footer-buttons [type=submit]').on('click', function () {
        $('#orderTabContents .form-select option:selected[value=null]').prop('disabled', true);
    });


    $(document).on('click', '.btn-cancel', function () {
        $(this).parent().remove();
        const list = $('.list-group-item').not('.d-none');
        list.each(function (index, element) {
            $(element).find('input').attr('name', `bill[customers][${index}][customer_id]`);
        });
    });

    $(document).on('click', '.table-wrapper button', function () {
        let index = $('.list-group-item').not('.d-none').length;
        let cloned = $('.list-item-template.d-none').clone();
        cloned.children('span').text($(this).data('name'));
        cloned.children('input').attr('name', `bill[customers][${index}][customer_id]`).val($(this).data('customer-id'));
        cloned.removeClass('list-item-template d-none');
        $('.list-wrapper .list-group').append(cloned);
    });

    $('#searchButton, #reloadButton').on('click', function () {
        getCustomers().done(function (customerShops) {
            $('.table-wrapper tbody tr').remove();
            console.log(customerShops);
            $.each(customerShops, function (index, item) {
                console.log(item.customer);
                let selectButton = $('<button>', {
                    type: 'button',
                    class: 'btn btn-sm btn-outline-primary w-100',
                }).text('選択').data('name', item.customer.name).data('customer-id', item.customer_id);
                let selectElement = $('<td>').append(selectButton);

                let nameElement = $('<td>', {
                    class: 'name'
                }).text(item.customer.name);
                let checkinElement = $('<td>', {
                    class: 'checkin'
                }).text(item.visited_at);

                let tr = $('<tr>', {
                    class: `record_${index}`
                }).append(nameElement, checkinElement, selectElement);

                $('.table-wrapper tbody').append(tr);
            });
        });
    });

    function getMembers()
    {
        return $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: window.getMembers,
            method: 'POST',
            dataType: 'json',
            data: {
                shop_id: $('#shopId').val()
            }
        })
    }

    function getCustomers()
    {
        return $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: window.getCustomers,
            method: 'POST',
            dataType: 'json',
            data: {
                search: {
                    name: $('#searchName').val(),
                    shop_id: $('#searchShop').val()
                }
            }
        })
    }

    function createMemberOptions(response)
    {
        let requests = window.requests;

        $('#memberId option').remove();
        let nullOption = $('<option>', {
            value: ''
        });
        $('#memberId').append(nullOption);

        $.each(response, function (index, item) {
            let newOption = nullOption.clone().val(item.member_id).text(item.name);
            if (requests != null && item.member_id == requests.member_id) {
                newOption.prop('selected', true);
            }
            if (window.member != null && window.member.member_id == item.member_id) {
                newOption.prop('selected', true);
            }

            $('#memberId').append(newOption);
        });
    }
});
