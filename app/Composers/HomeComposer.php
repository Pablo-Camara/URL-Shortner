<?php

namespace App\Composers;

class HomeComposer
{

    public function compose($view)
    {
        $captchaSitekey = config('captcha.sitekey');

        $withCoffee = rand(0,1) == 1 ? true : false;
        $currentBackground = asset('/img/bg_' . ($withCoffee ? 'with' : 'without') . '_coffee.jpeg');

        $view->with('captchaSitekey', $captchaSitekey)
                ->with('currentBackground', $currentBackground);
    }
}
