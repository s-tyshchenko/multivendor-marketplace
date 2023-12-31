@php
    Theme::layout('full-width');
@endphp

<div class="page-content pt-150 pb-150">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-10 col-md-12 m-auto">
                <div class="row">
                    <div class="col-lg-6 col-md-8">
                        <div class="login_wrap widget-taber-content background-white">
                            <div class="padding_eight_all bg-white">
                                <div class="padding_eight_all bg-white">
                                    <h3 class="mb-20">{{ __('Register') }}</h3>
                                    <p>{{ __('Please fill in the information below') }}</p>
                                    <br>
                                </div>

                                <form method="POST" action="{{ route('customer.register.post') }}">
                                    @csrf
                                    <div class="form__content">
                                        <div class="form-group">
                                            <input class="form-control" name="email" id="txt-email" type="email" value="{{ old('email') }}" placeholder="{{ __('Your email address') }}">
                                            @if ($errors->has('email'))
                                                <span class="text-danger">{{ $errors->first('email') }}</span>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <input class="form-control" type="password" name="password" id="txt-password" placeholder="{{ __('Your password') }}">
                                            @if ($errors->has('password'))
                                                <span class="text-danger">{{ $errors->first('password') }}</span>
                                            @endif
                                        </div>

                                        <div class="form-group">
                                            <p>{{ __('Your personal data will be used to support your experience throughout this website, to manage access to your account, and for other purposes described in our privacy policy.') }}</p>
                                        </div>

                                        @if (is_plugin_active('captcha'))
                                            @if(Captcha::isEnabled() && get_ecommerce_setting('enable_recaptcha_in_register_page', 0))
                                                <div class="form-group">
                                                    {!! Captcha::display() !!}
                                                </div>
                                            @endif

                                            @if (get_ecommerce_setting('enable_math_captcha_in_register_page', 0))
                                                <div class="form-group">
                                                    <label class="form-label" for="math-group">{{ app('math-captcha')->label() }}</label>
                                                    {!! app('math-captcha')->input(['class' => 'form-control', 'id' => 'math-group', 'placeholder' => app('math-captcha')->getMathLabelOnly()]) !!}
                                                </div>
                                            @endif
                                        @endif

                                        <div class="login_footer form-group">
                                            <div class="chek-form">
                                                <div class="custome-checkbox">
                                                    <input type="hidden" name="agree_terms_and_policy" value="0">
                                                    <input class="form-check-input" type="checkbox" name="agree_terms_and_policy" id="agree-terms-and-policy" value="1" @if (old('agree_terms_and_policy') == 1) checked @endif>
                                                    <label class="form-check-label" for="agree-terms-and-policy"><span>{!! BaseHelper::clean(__('I agree to terms & Policy.')) !!}</span></label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-fill-out btn-block hover-up">{{ __('Register') }}</button>
                                        </div>

                                        <br>
                                        <p>{{ __('Have an account already?') }} <a href="{{ route('customer.login') }}" class="d-inline-block">{{ __('Login') }}</a></p>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 pr-30">
                        {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\Ecommerce\Models\Customer::class) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
