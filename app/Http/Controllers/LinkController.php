<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveLinkRequest;
use App\Http\Resources\LinkResource;
use App\Models\DailyClick;
use App\Models\Shortlink;
use App\Services\LinkService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LinkController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate(['search' => ['nullable', 'string', 'max:120'], 'status' => ['nullable', Rule::in(['active', 'paused', 'expired', 'archived'])], 'page' => ['nullable', 'integer', 'min:1']]);
        $query = $request->user()->shortlinks()->with('shortstring', 'destination')->withSum('clicks', 'count');
        $query->when($data['search'] ?? null, function ($q, $search) {
            $term = '%'.str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $search).'%';
            $q->where(fn ($q) => $q->whereRaw("title LIKE ? ESCAPE '!'", [$term])->orWhereHas('shortstring', fn ($q) => $q->whereRaw("shortstring LIKE ? ESCAPE '!'", [$term])));
        });
        $status = $data['status'] ?? null;
        if ($status === 'archived') {
            $query->where('status_id', Shortlink::ARCHIVED);
        } elseif ($status === 'paused') {
            $query->where('status_id', Shortlink::PAUSED);
        } elseif ($status === 'expired') {
            $query->where('status_id', Shortlink::ACTIVE)->where('expires_at', '<=', now());
        } elseif ($status === 'active') {
            $query->where('status_id', Shortlink::ACTIVE)->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()));
        } else {
            $query->where('status_id', '!=', Shortlink::ARCHIVED);
        }

        return LinkResource::collection($query->orderByDesc('id')->paginate(8));
    }

    public function show(Shortlink $link)
    {
        $this->authorize('view', $link);

        return new LinkResource($link->load('shortstring', 'destination', 'history')->loadSum('clicks', 'count'));
    }

    public function store(SaveLinkRequest $request, LinkService $service)
    {
        $link = $service->create($request->user(), $request->validated());

        return (new LinkResource($link->load('shortstring', 'destination')->loadSum('clicks', 'count')))->response()->setStatusCode(201);
    }

    public function update(SaveLinkRequest $request, Shortlink $link, LinkService $service)
    {
        $this->authorize('update', $link);
        $service->update($link, $request->validated());

        return $this->show($link->fresh());
    }

    public function status(Request $request, Shortlink $link, LinkService $service)
    {
        $this->authorize('update', $link);
        $data = $request->validate(['status' => ['required', Rule::in(['active', 'paused', 'archived'])], 'version' => ['required', 'integer', 'min:1']]);
        $service->status($link, $data['version'], ['active' => 1, 'archived' => 2, 'paused' => 3][$data['status']]);

        return $this->show($link->fresh());
    }

    public function stats(Request $request)
    {
        $ids = $request->user()->shortlinks()->select('id');
        $series = DailyClick::whereIn('shortlink_id', $ids)->where('day', '>=', today()->subDays(13)->toDateString())->select('day')->selectRaw('SUM(count) as total')->groupBy('day')->pluck('total', 'day');
        $days = collect(range(13, 0))->map(function ($ago) use ($series) {
            $day = today()->subDays($ago)->toDateString();

            return ['day' => $day, 'count' => (int) ($series[$day] ?? 0)];
        });

        return ['total_links' => $request->user()->shortlinks()->where('status_id', '!=', Shortlink::ARCHIVED)->count(),
            'active_links' => $request->user()->shortlinks()->where('status_id', Shortlink::ACTIVE)->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))->count(),
            'total_clicks' => (int) DailyClick::whereIn('shortlink_id', $ids)->sum('count'), 'recent_clicks' => $days->sum('count'), 'days' => $days];
    }
}
