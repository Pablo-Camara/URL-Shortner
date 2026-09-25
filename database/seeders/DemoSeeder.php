<?php

namespace Database\Seeders;

use App\Models\DailyClick;
use App\Models\Shortlink;
use App\Models\User;
use App\Services\LinkService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local', 'testing']) || User::exists()) {
            throw new \RuntimeException('Demo data requires a local/testing environment and an empty users table.');
        }
        DB::transaction(function () {
            $user = User::create(['name' => 'Alex Morgan', 'email' => 'alex@example.test', 'password' => 'hello-there-demo', 'guest' => false]);
            $items = [
                ['Design system essentials', 'design-notes', 'https://developer.mozilla.org/en-US/docs/Web/CSS'],
                ['The next chapter', 'next-chapter', 'https://vuejs.org/guide/introduction.html'],
                ['A better development workflow', 'dev-workflow', 'https://laravel.com/docs'],
                ['Notes from the workshop', 'workshop-notes', 'https://docs.docker.com/'],
                ['The launch collection', 'launch-collection', 'https://www.php.net/docs.php'],
                ['A little inspiration', 'inspiration', 'https://www.w3.org/WAI/'],
            ];
            foreach (array_reverse($items) as $i => $item) {
                $link = app(LinkService::class)->create($user, ['title' => $item[0], 'alias' => $item[1], 'url' => $item[2]]);
                $link->update(['created_at' => now()->subDays(17 - $i * 2), 'status_id' => $i === 1 ? Shortlink::PAUSED : Shortlink::ACTIVE]);
                foreach (range(13, 0) as $ago) {
                    // Deterministic fictional activity makes screenshots and local exploration reproducible.
                    $count = max(0, (14 - $ago) * ($i + 2) + (($ago * 7 + $i * 3) % 19) - 9);
                    DailyClick::create(['shortlink_id' => $link->id, 'day' => today()->subDays($ago)->toDateString(), 'count' => $count]);
                }
            }
        });
    }
}
