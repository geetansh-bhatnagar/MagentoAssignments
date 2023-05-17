define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    return function () {
        var loading = false;

        var infiniteScroll = function () {
            var windowHeight = $(window).height();
            var documentHeight = $(document).height();
            var scrollTop = $(window).scrollTop();
            var loadMoreThreshold = 200;

            if (!loading && documentHeight - (scrollTop + windowHeight) < loadMoreThreshold) {
                loading = true;
                loadMoreProducts();
            }
        };

        var loadMoreProducts = function () {
            var nextUrl = $('.toolbar-products .pages .action.next').attr('href');

            if (nextUrl && nextUrl !== '#') {
                $('.toolbar-products .pages .action.next').addClass('disabled');

                $.ajax({
                    url: nextUrl,
                    dataType: 'html',
                    beforeSend: function () {
                        $('.toolbar-products .loading-mask').show();
                    },
                    success: function (response) {
                        var productsHtml = $(response).find('.products-grid .product-item');
                        $('.products-grid .product-items').append(productsHtml);

                        var nextLink = $(response).find('.toolbar-products .pages .action.next');
                        if (nextLink.length > 0) {
                            $('.toolbar-products .pages .action.next').attr('href', nextLink.attr('href')).removeClass('disabled');
                        } else {
                            $('.toolbar-products .pages .action.next').remove();
                        }

                        loading = false;
                        $('.toolbar-products .loading-mask').hide();
                    },
                    error: function () {
                        loading = false;
                        $('.toolbar-products .loading-mask').hide();
                    }
                });
            }
        };

        $(window).on('scroll', infiniteScroll);
    };
});
