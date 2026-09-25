<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id, 'title' => $this->title ?: $this->shortstring->shortstring,
            'alias' => $this->shortstring->shortstring,
            'short_url' => rtrim(config('app.url'), '/').'/'.$this->shortstring->shortstring,
            'url' => $this->destination?->url, 'status' => $this->status(), 'version' => $this->version,
            'clicks' => (int) ($this->clicks_sum_count ?? 0),
            'expires_at' => $this->expires_at?->toIso8601String(), 'created_at' => $this->created_at->toIso8601String(),
            'history' => $this->whenLoaded('history', fn () => $this->history->map(fn ($h) => ['url' => $h->url, 'current' => (bool) $h->is_redirect_url, 'created_at' => $h->created_at->toIso8601String()])),
        ];
    }
}
