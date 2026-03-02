@extends('backend.admin-master')
@section('site-title')
    {{__('SMTP Settings')}}
@endsection
@section('content')
    <div class="row g-4 mt-0">
        <x-validation.error/>
        <div class="col-xl-6 col-lg-6">
            <div class="dashboard__card bg__white padding-20 radius-10">
                <h2 class="dashboard__card__header__title">{{__('SMTP Settings')}}</h2>
                <form action="{{route('admin.email.smtp.update.settings')}}" method="POST">
                    @csrf
                    <div class="form__input__flex">
                        <div class="form__input__single">
                            <label for="site_global_email" class="form__input__single__label">{{__('Global Email')}}</label>
                            <input type="text" class="form__control radius-5" name="site_global_email" value="{{get_static_option('site_global_email')}}">
                            <small class="form-text text-muted">{{__('use your web mail here')}}</small>
                        </div>

                        <div class="form__input__single">
                            <label for="site_smtp_mail_mailer" class="form__input__single__label">{{__('SMTP Mailer')}}</label>
                            <select name="site_smtp_mail_mailer" class="form__control radius-5">
                                <option value="smtp" @if(get_static_option('site_smtp_mail_mailer') == 'smtp') selected @endif>{{__('SMTP')}}</option>
                                <option value="sendmail" @if(get_static_option('site_smtp_mail_mailer') == 'sendmail') selected @endif>{{__('SendMail')}}</option>
                                <option value="mailgun" @if(get_static_option('site_smtp_mail_mailer') == 'mailgun') selected @endif>{{__('Mailgun')}}</option>
                                <option value="postmark" @if(get_static_option('site_smtp_mail_mailer') == 'postmark') selected @endif>{{__('Postmark')}}</option>
                            </select>
                        </div>

                        <div class="form__input__single">
                            <label for="site_smtp_mail_host" class="form__input__single__label">{{ __('SMTP Mail Host') }}</label>
                            <input type="text" class="form__control" name="site_smtp_mail_host" value="{{get_static_option('site_smtp_mail_host')}}">
                        </div>

                        <div class="form__input__single">
                            <label for="site_smtp_mail_port" class="form__input__single__label">{{__('SMTP Mail Port')}}</label>
                            <select name="site_smtp_mail_port" class="form__control">
                                <option value="587" @if(get_static_option('site_smtp_mail_port') == '587') selected @endif>{{__('587')}}</option>
                                <option value="465" @if(get_static_option('site_smtp_mail_port') == '465') selected @endif>{{__('465')}}</option>
                                <option value="25" @if(get_static_option('site_smtp_mail_port') == '25') selected @endif>{{__('25')}}</option>
                                <option value="2525" @if(get_static_option('site_smtp_mail_port') == '2525') selected @endif>{{__('2525')}}</option>
                            </select>
                        </div>

                        <div class="form__input__single">
                            <label for="site_smtp_mail_username" class="form__input__single__label">{{__('SMTP Mail Username')}}</label>
                            <input type="text" class="form__control" name="site_smtp_mail_username" value="{{get_static_option('site_smtp_mail_username')}}">
                        </div>

                        <div class="form__input__single">
                            <label for="site_smtp_mail_password" class="form__input__single__label">{{__('SMTP Mail Password')}}</label>
                            <input type="text" class="form__control" name="site_smtp_mail_password"  value="{{get_static_option('site_smtp_mail_password')}}">
                        </div>

                        <div class="form__input__single">
                            <label for="site_smtp_mail_encryption" class="form__input__single__label">{{__('SMTP Mail Encryption')}}</label>
                            <select name="site_smtp_mail_encryption" class="form__control">
                                <option value="ssl" @if(get_static_option('site_smtp_mail_encryption') == 'ssl') selected @endif>{{__('SSL')}}</option>
                                <option value="tls" @if(get_static_option('site_smtp_mail_encryption') == 'tls') selected @endif>{{__('TLS')}}</option>
                                <option value="none" @if(get_static_option('site_smtp_mail_encryption') == 'none') selected @endif>{{__('None')}}</option>
                            </select>
                        </div>
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="update" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Update Changes') }}</button>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-xl-6 col-lg-6">
            <div class="dashboard__card padding-20 radius-10">
                <h2 class="dashboard__card__header__title mb-3">{{__('SMTP Test')}}</h2>
                <form action="{{route('admin.email.smtp.settings.test')}}" method="POST">
                    @csrf
                    <div class="form__input__flex">
                        <div class="form__input__single">
                            <label for="Email" class="form__input__single__label">{{__('Email')}}</label>
                            <input type="text" class="form__control radius-5" name="email">
                        </div>
                    </div>
                    <div class="btn_wrapper mt-4">
                        <button type="submit" id="send" class="cmnBtn btn_5 btn_bg_blue radius-5">{{ __('Send') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        <x-btn.send/>
    </script>
@endsection
