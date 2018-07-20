$(function () {
    $('[data-btn="paid"]').on('click', function(e) {
        e.preventDefault();

        let $link = $(this);

        $.get($link.attr('href'), function (datas) {
            let text = datas.is_paid ? 'Oui' : 'Non';

            $link.toggleClass('btn-success btn-danger').text(text);
        });

        return false;
    });
});