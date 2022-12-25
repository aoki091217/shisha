export default {
    ajax
};

function ajax(requests)
{
    return $.ajax({
        headers: {
            'X-CSRF-TOKEN': requests.csrf
        },
        url: requests.url,
        method: requests.method,
        dataType: 'json',
        data: requests.shop
    })
}
