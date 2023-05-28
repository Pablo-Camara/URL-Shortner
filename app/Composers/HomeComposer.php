<?php

namespace App\Composers;

class HomeComposer
{

    public function compose($view)
    {
        $enableCaptcha = config('captcha.enable');
        $captchaSitekey = config('captcha.sitekey');

        $withCoffee = 1;//rand(0,1) == 1 ? true : false;
        $currentBackground = asset('/img/bg_' . ($withCoffee ? 'with' : 'without') . '_coffee.jpeg');

        $logoTop = asset('/img/logo.png');
        $logoTopMobile = asset('/img/logo.png');

        $enableLoginWithGoogleBtn = config('services.google.enable_login_btn');
        $enableLoginWithFacebookBtn = config('services.facebook.enable_login_btn');
        $enableLoginWithTwitterBtn = config('services.twitter.enable_login_btn');
        $enableLoginWithLinkedinBtn = config('services.linkedin.enable_login_btn');
        $enableLoginWithGithubBtn = config('services.github.enable_login_btn');

        $view->with('captchaSitekey', $captchaSitekey)
                ->with('enableCaptcha', $enableCaptcha)
                ->with('enableLoginWithGoogleBtn', $enableLoginWithGoogleBtn)
                ->with('enableLoginWithFacebookBtn', $enableLoginWithFacebookBtn)
                ->with('enableLoginWithTwitterBtn', $enableLoginWithTwitterBtn)
                ->with('enableLoginWithLinkedinBtn', $enableLoginWithLinkedinBtn)
                ->with('enableLoginWithGithubBtn', $enableLoginWithGithubBtn)
                ->with('currentBackground', $currentBackground)
                ->with('logoTop', $logoTop)
                ->with('logoTopMobile', $logoTopMobile)
                ->with('domain', preg_replace("(^https?://)", "", url('/')));
    }
}
