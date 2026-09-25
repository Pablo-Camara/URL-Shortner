<?php

namespace App\Services;

use App\Http\Requests\SaveLinkRequest;
use App\Models\Shortlink;
use App\Models\Shortstring;
use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LinkService
{
    public function create(User $owner, array $data): Shortlink
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $alias = $data['alias'] ?? strtolower(Str::random(8));
            if (in_array($alias, SaveLinkRequest::RESERVED, true)) {
                continue;
            }
            try {
                return DB::transaction(function () use ($owner, $data, $alias) {
                    // A conditional update also safely claims unused, pre-generated legacy aliases.
                    $shortstring = Shortstring::firstOrCreate(['shortstring' => $alias], ['is_available' => true, 'is_custom' => ! empty($data['alias']), 'length' => strlen($alias)]);
                    if (! Shortstring::whereKey($shortstring->id)->where('is_available', true)->update(['is_available' => false])) {
                        throw ValidationException::withMessages(['alias' => 'That short link is already taken.']);
                    }
                    $link = $owner->shortlinks()->create(['shortstring_id' => $shortstring->id, 'title' => $data['title'], 'status_id' => Shortlink::ACTIVE, 'expires_at' => $data['expires_at'] ?? null]);
                    $link->history()->create(['url' => $data['url'], 'is_redirect_url' => true]);

                    return $link->refresh();
                }, 3);
            } catch (UniqueConstraintViolationException|ValidationException $e) {
                if (! empty($data['alias'])) {
                    throw ValidationException::withMessages(['alias' => 'That short link is already taken.']);
                }
            }
        }
        abort(503, 'Could not allocate a short link. Please try again.');
    }

    public function update(Shortlink $link, array $data): void
    {
        DB::transaction(function () use ($link, $data) {
            // Compare-and-swap prevents stale browser tabs from replacing a newer edit.
            $changed = Shortlink::whereKey($link->id)->where('version', $data['version'])->update([
                'title' => $data['title'], 'expires_at' => $data['expires_at'] ?? null, 'version' => DB::raw('version + 1'),
            ]);
            abort_unless($changed, 409, 'This link changed in another tab. Close and reopen it before saving.');
            if ($link->destination()->value('url') !== $data['url']) {
                $link->history()->where('is_redirect_url', true)->update(['is_redirect_url' => false]);
                $link->history()->create(['url' => $data['url'], 'is_redirect_url' => true]);
            }
        }, 3);
    }

    public function status(Shortlink $link, int $version, int $status): void
    {
        $changed = Shortlink::whereKey($link->id)->where('version', $version)->update(['status_id' => $status, 'version' => DB::raw('version + 1')]);
        abort_unless($changed, 409, 'This link changed in another tab. Refresh the list and try again.');
    }
}
