$(window).on('load', function () {
    $('.bland-select').on('change', function () {
        let blandIndex = $('.bland-select').index($(this));
        let blandId = $(this).val();

        getFlavors(blandId).done(function (flavors) {
            let flavorSelect = $('.select-wrapper').eq(blandIndex).find('.flavor-select');
            flavorSelect.children('option:not(:first-of-type)').remove();

            $.each(flavors, function (index, flavor) {
                let option = $('<option>', {});
                option.val(flavor.flavor_id).text(flavor.name);
                flavorSelect.append(option);
            })
        });
    });

    function getFlavors(blandId)
    {
        return $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: window.getFlavors,
            method: 'POST',
            dataType: 'json',
            data: {
                bland_id: blandId
            }
        })
    }
});
