<?php

namespace App\Http\Controllers;

use App\Models\Shortstring;
use App\Rules\DestinationUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class RedirectController extends Controller
{
    public function __invoke(Request $request, string $alias)
    {
        $link = Shortstring::where('shortstring', $alias)->first()?->shortlink()->with('destination')->first();
        abort_unless($link, 404);
        abort_unless($link->status() === 'active' && $link->destination, 410);
        $url = $link->destination->url;
        abort_if(Validator::make(['url' => $url], ['url' => ['required', 'max:2048', new DestinationUrl]])->fails(), 410);
        // HEAD probes do not count as clicks. No IP, user agent, referrer or tracking cookie is stored.
        if (! $request->isMethod('HEAD')) {
            try {
                DB::transaction(function () use ($link) {
                    $key = ['shortlink_id' => $link->id, 'day' => today()->toDateString()];
                    DB::table('daily_clicks')->insertOrIgnore([...$key, 'count' => 0]);
                    DB::table('daily_clicks')->where($key)->increment('count');
                }, 3);
            } catch (\Throwable $e) {
                report($e);
            } // Analytics failure must not break a valid redirect.
        }

        return redirect()->away($url, 302)->withHeaders(['Cache-Control' => 'no-store, private', 'Referrer-Policy' => 'no-referrer']);
    }
}
