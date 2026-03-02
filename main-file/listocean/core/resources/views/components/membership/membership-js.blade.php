    <script>
        (function ($){
            $(document).ready(function (){

                let site_default_currency_symbol = '{{ site_currency_symbol() }}';

                // Renew membership plan
                $(document).on('click', '.renew_current_membership', function(e){
                    // Change the class ID name
                    $('#paymentGatewayModal').find('#membership_id').attr('id', 'membership_id_stop');
                    let membership_id = $(this).data('renew_id');
                    $('#membership_id').val(membership_id);
                });

                // Buy now membership plan
                $(document).on('click', '.choose_membership_plan', function(e){
                    // Change the class ID name
                    $('#paymentGatewayModal').find('#membership_id_stop').attr('id', 'membership_id');

                    let membership_id = $(this).data('id');
                    let membership_price = $(this).data('price');
                    @if(moduleExists('Wallet'))
                        let balance = {{ Auth::check() ? optional(Auth::user()->user_wallet)->balance ?? 0 : 0 }};
                    @endif

                    $('#membership_id').val(membership_id);
                    $('#membership_price').val(membership_price);

                    if(membership_price > balance){
                        $('.display_balance').html('<span class="text-danger">{{__('Wallet Balance Shortage:')}}'+ site_default_currency_symbol + (membership_price-balance) +'</span>');
                        $('.deposit_link').html('<a href="#" target="_blank">{{ __('Deposit')}}</a>');
                    }
                });

                // login
                $(document).on('click', '.login_to_buy_a_membership', function(e){
                    e.preventDefault();
                    let username = $('#username').val();
                    let password = $('#password').val();
                    let membership_price = $('#membership_price').val();
                    let erContainer = $(".error-message");
                    erContainer.html('');
                    $.ajax({
                        url:"{{ route('membership.user.login')}}",
                        data:{username:username,password:password},
                        method:'POST',
                        error:function(res){
                            let errors = res.responseJSON;
                            erContainer.html('<div class="alert alert-danger"></div>');
                            $.each(errors.errors, function(index,value){
                                erContainer.find('.alert.alert-danger').append('<p>'+value+'</p>');
                            });
                        },
                        success: function(res){
                            if(res.status=='success'){
                                location.reload();
                                let balance = res.balance;
                                $('#loginModal').modal('hide');
                                if(membership_price > balance){
                                    $('.load_after_login').load(location.href + ' .load_after_login', function (){
                                        $('.display_balance').html('<span class="text-danger">{{__('Wallet Balance Shortage:')}}'+ site_default_currency_symbol + (membership_price-balance) +'</span>');
                                        $('.deposit_link').html('<a href="#" target="_blank">{{ __('Deposit')}}</a>');
                                    });
                                }
                            }
                            if(res.status == 'failed'){
                                erContainer.html('<div class="alert alert-danger">'+res.msg+'</div>');
                            }
                        }

                    });
                });

                //buy membership-load spinner
                $(document).on('click','#confirm_buy_membership_load_spinner',function(){
                    //Image validation
                    let manual_payment = $('#order_from_user_wallet').val();
                    if(manual_payment == 'manual_payment') {
                        let manual_payment_image = $('input[name="manual_payment_image"]').val();
                        if(manual_payment_image == '') {
                            toastr_warning_js("{{__('Image field is required')}}")
                            return false
                        }
                    }

                    $('#buy_membership_load_spinner').html('<i class="fas fa-spinner fa-pulse"></i>')
                    setTimeout(function () {
                        $('#buy_membership_load_spinner').html('');
                    }, 10000);
                });

            });
        }(jQuery));
    </script>
