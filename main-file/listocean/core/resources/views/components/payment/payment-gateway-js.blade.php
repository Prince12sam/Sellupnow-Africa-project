<script>
    (function ($){
        $(document).ready(function (){
            var defaulGateway = $('#site_global_payment_gateway').val();
            $('.payment-gateway-wrapper ul li[data-gateway="' + defaulGateway + '"]').addClass('selected');
            let customFormParent = $('.payment_gateway_extra_field_information_wrap');
            customFormParent.children().hide();
            // for manual payment gateway
            $(document).on('click', '.payment-gateway-wrapper > ul > li', function (e) {
                e.preventDefault();
                let gateway = $(this).data('gateway');
                let manual_transaction_div = $('.manual_transaction_id');
                let summernot_wrap_div = $('.summernot_wrap');

                customFormParent.children().hide();
                if (gateway === 'manual_payment') {
                    manual_transaction_div.fadeIn();
                    summernot_wrap_div.fadeIn();
                    manual_transaction_div.removeClass('d-none');
                } else {
                    manual_transaction_div.addClass('d-none');
                    summernot_wrap_div.fadeOut();
                    manual_transaction_div.fadeOut();

                    let wrapper = customFormParent.find('#'+gateway+'-parent-wrapper');
                    if (wrapper.length > 0)
                    {
                        wrapper.fadeIn();
                    }
                }
                $(this).addClass('selected').siblings().removeClass('selected');
                $('.payment-gateway-wrapper').find(('input')).val($(this).data('gateway'));
                $('.payment_gateway_passing_clicking_name').val($(this).data('gateway'));
            });
        });
    })(jQuery);
</script>
<script>
    (function ($){
        $(document).ready(function (){
            //if the wallet checkbox is checked need to show this value as current seleted payment gateway
            $(document).on('click', '.current_balance_selected_gateway',function(){
                $('.payment-gateway-wrapper li').removeClass('active');
                $('.payment-gateway-wrapper li').removeClass('selected');
                $('.current-balance-wrapper .current_balance_selected_gateway').addClass('selected');
                $('.payment-gateway-wrapper #order_from_user_wallet').val('current_balance');
            });
            // if the wallet checkbox is checked need to show this value as current selected payment gateway
            $(document).on('click', '.wallet_selected_payment_gateway',function(){
                let wallet_value = $(this).val();
                $('.payment-gateway-wrapper li').removeClass('active');
                $('.payment-gateway-wrapper li').removeClass('selected');
                $('.wallet-payment-gateway-wrapper .wallet_selected_payment_gateway').addClass('selected');
                if ($('.wallet-payment-gateway-wrapper input[type="checkbox"]').prop('checked')) {
                    $('.payment-gateway-wrapper #order_from_user_wallet').val('wallet');
                    $('.wallet-payment-gateway-wrapper input[type="checkbox"]').prop('checked', true)
                } else {
                    $('.payment-gateway-wrapper #order_from_user_wallet').val('');
                    $('.wallet-payment-gateway-wrapper input[type="checkbox"]').removeAttr('checked');
                }
            });

            //select payment gateway
            $(document).on('click', '.payment_getway_image ul li',function(){
                //wallet start
                $('.wallet_selected_payment_gateway').removeClass('selected')
                $( ".wallet_selected_payment_gateway" ).prop( "checked", false );
                //wallet end
                //current balance start
                $('.current_balance_selected_gateway').addClass('selected');
                $( ".current_balance_selected_gateway" ).prop( "checked", false );
                //current balance end
                $(this).siblings().removeClass('active');
                $(this).addClass('active');
                let payment_gateway_name = $(this).data('gateway');
                $('#msform input[name=selected_payment_gateway]').val();

                $('.payment_gateway_extra_field_information_wrap > div').hide();
                $('.payment_gateway_extra_field_information_wrap div.'+payment_gateway_name+'_gateway_extra_field').show();
                $(this).addClass('selected').siblings().removeClass('selected');
                $('.payment-gateway-wrapper').find(('input')).val(payment_gateway_name);
            });
            $('.payment_getway_image ul li.selected.active').trigger('click');
        });
    })(jQuery);
</script>
