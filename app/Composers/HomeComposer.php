<?php

namespace App\Composers;

class HomeComposer
{

    public function compose($view)
    {
        $captchaSitekey = config('captcha.sitekey');

        $withCoffee = 1;//rand(0,1) == 1 ? true : false;
        $currentBackground = asset('/img/bg_' . ($withCoffee ? 'with' : 'without') . '_coffee.jpeg');

        $logoTop = asset('/img/logo.png');
        $logoTopMobile = asset('/img/logo-full-mobile.png');

        $view->with('captchaSitekey', $captchaSitekey)
                ->with('currentBackground', $currentBackground)
                ->with('logoTop', $logoTop)
                ->with('logoTopMobile', $logoTopMobile);
    }
}
