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
