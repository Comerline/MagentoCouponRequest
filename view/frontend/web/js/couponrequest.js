define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";

    function main(config, element) {
        var $element = $(element);
        var $dataForm = $('#coupon-request-form');
        var ajaxUrl = $dataForm.attr('action');
        $dataForm.mage('validation', {});
        $(document).on('click', '.submit-coupon-request', function () {
            if ($dataForm.valid()) {
                event.preventDefault();
                var param = $dataForm.serialize();
                $.ajax({
                    showLoader: true,
                    url: ajaxUrl,
                    data: param,
                    type: "POST"
                }).done(function (data) {
                    $('.info-success').hide();
                    $('.info-error').hide();
                    if (data.result) {
                        $('.info-success').show();
                    } else {
                        $('.info-error').show();
                    }
                    return true;
                });
            }
        });
    }
    ;
    return main;
});


