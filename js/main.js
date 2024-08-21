$(document).ready(function () {

    /* Browser fullscreen experience on double click */
    if (self == top) {
        $('body').on('dblclick', function (e) {

            if (!document.fullscreenElement && // alternative standard method
                !document.mozFullScreenElement && !document.webkitFullscreenElement && !document.msFullscreenElement) { // current working methods
                if (document.documentElement.requestFullscreen) {
                    document.documentElement.requestFullscreen();
                } else if (document.documentElement.msRequestFullscreen) {
                    document.documentElement.msRequestFullscreen();
                } else if (document.documentElement.mozRequestFullScreen) {
                    document.documentElement.mozRequestFullScreen();
                } else if (document.documentElement.webkitRequestFullscreen) {
                    document.documentElement.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }
            }

        });
    } else {
        
    }


    /* float label checking input is not empty */
    $('.float-label .form-control').on('blur', function () {
        if ($(this).val() || $(this).val().length != 0) {
            $(this).closest('.float-label').addClass('active');
        } else {
            $(this).closest('.float-label').removeClass('active');
        }
    })

    /* menu open close wrapper screen click close menu */
    $('.menu-btn').on('click', function (e) {
        e.stopPropagation();
        if ($('body').hasClass('sidemenu-open') == true) {
            $('body, html').removeClass('sidemenu-open');
            setTimeout(function () {
                $('body, html').removeClass('menuactive');
            }, 500);
        } else {
            $('body, html').addClass('sidemenu-open menuactive');
        }
    });
    $('.wrapper').on('click', function () {

        if ($('body').hasClass('sidemenu-open') == true) {

            $('body, html').removeClass('sidemenu-open');
            setTimeout(function () {
                $('body, html').removeClass('menuactive');
            }, 500);
        }
    });

    /* filter click open filter */
    if ($('body').hasClass('filtermenu-open') == true) {
        $('.filter-btn').find('i').html('close');
    }
    $('.filter-btn').on('click', function () {
        if ($('body').hasClass('filtermenu-open') == true) {
            $('body').removeClass('filtermenu-open');
            $(this).find('i').html('filter_list')

        } else {
            $('body').addClass('filtermenu-open');
            $(this).find('i').html('close')
        }
    });


    /* background image to cover */
    $('.background').each(function () {
        var imagewrap = $(this);
        var imagecurrent = $(this).find('img').attr('src');
        imagewrap.css('background-image', 'url("' + imagecurrent + '")');
        $(this).find('img').remove();
    });


    /* drag and scroll like mobile remove while creating mobile app */
    (function ($) {
        $.dragScroll = function (options) {
            var settings = $.extend({
                scrollVertical: true,
                scrollHorizontal: true,
                cursor: null
            }, options);

            var clicked = false,
                clickY, clickX;

            var getCursor = function () {
                if (settings.cursor) return settings.cursor;
                if (settings.scrollVertical && settings.scrollHorizontal) return 'url(img/touch.png), move';
                if (settings.scrollVertical) return 'row-resize';
                if (settings.scrollHorizontal) return 'col-resize';
            }

            var updateScrollPos = function (e, el) {
                $('html').css('cursor', getCursor());
                var $el = $(el);
                settings.scrollVertical && $el.scrollTop($el.scrollTop() + (clickY - e.pageY));
                settings.scrollHorizontal && $el.scrollLeft($el.scrollLeft() + (clickX - e.pageX));
            }

            $(document).on({
                'mousemove': function (e) {
                    clicked && updateScrollPos(e, this);
                },
                'mousedown': function (e) {
                    clicked = true;
                    clickY = e.pageY;
                    clickX = e.pageX;
                },
                'mouseup': function () {
                    clicked = false;
                    $('html').css('cursor', 'url(img/logo-cursor.png), auto');
                }
            });
        }
    }(jQuery))

    $.dragScroll();
    /* End of drag and scroll like mobile remove while creating mobile app */
});


$(window).on('load', function () {
    $('.loader-screen').fadeOut('slow');

    /* header active on scroll more than 50 px*/
    if ($(this).scrollTop() >= 30) {
        $('.header').addClass('active')
    } else {
        $('.header').removeClass('active')
    }

    $(window).on('scroll', function () {
        /* header active on scroll more than 50 px*/
        if ($(this).scrollTop() >= 30) {
            $('.header').addClass('active')
        } else {
            $('.header').removeClass('active')
        }
    });


});

document.addEventListener('DOMContentLoaded', function () {
    var swiper = new Swiper('.swiper-container', {
        slidesPerView: 'auto',
        spaceBetween: 0,
        freeMode: true,
        breakpoints: {
            640: {
                slidesPerView: 2,
                spaceBetween: 10
            },
            768: {
                slidesPerView: 3,
                spaceBetween: 10
            }
        }
    });
});


$(document).ready(function() {
    let total = 0;

    function updateTotal() {
        total = 0;
        $('.quantity-input').each(function() {
            let quantity = parseInt($(this).val()) || 0;
            let price = parseFloat($(this).siblings('.input-group-append').find('.quantity-btn').data('price')) || 0;
            total += quantity * price;
        });
        $('#total-price').text(total.toFixed(2));
    }

    $('.quantity-btn').on('click', function() {
        let $input = $(this).closest('.input-group').find('.quantity-input');
        let action = $(this).data('action');
        let currentValue = parseInt($input.val()) || 0;

        if (action === 'increase') {
            currentValue++;
        } else if (action === 'decrease') {
            if (currentValue > 0) currentValue--;
        }

        $input.val(currentValue);
        updateTotal();
    });

    $('#save-order').on('click', function() {
        let items = [];
        $('.quantity-input').each(function() {
            let quantity = parseInt($(this).val()) || 0;
            if (quantity > 0) {
                let productId = $(this).closest('.input-group').find('.quantity-btn').data('product-id');
                let price = parseFloat($(this).closest('.input-group').find('.quantity-btn').data('price'));
                items.push({
                    product_id: productId,
                    quantity: quantity,
                    price: price
                });
            }
        });

        $.ajax({
            url: 'save_order.php',
            type: 'POST',
            data: { items: items, total: total },
            success: function(response) {
                alert('Order saved successfully!');
                location.reload(); 
            },
            error: function(error) {
                console.error('Error saving order:', error);
            }
        });
    });

    updateTotal();
});

