$(window).on('load', function () {
    $(document).on('change', '#shopName', function () {
        const shopId = $(this).val();

        const date = new Date();
        const year = date.getFullYear();
        const month = date.getMonth() + 1;
        const day = date.getDate().toString().padStart(2, '0');
        const hour = date.getHours().toString().padStart(2, '0');
        const minute = date.getMinutes().toString().padStart(2, '0');
        const second = date.getMinutes().toString().padStart(2, '0');

        const datetime = `${year}-${month.toString().padStart(2, '0')}-${day}_${hour}:${minute}:${second}`;

        $('.kind_route_text').text(`sid=${shopId}&`);
        $('.kind_checkin_text').text(`shop_id=${shopId}&action=checkin&datetime=${datetime}`);
    });
});
