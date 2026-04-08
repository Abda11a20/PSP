<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'company' => new CompanyResource($this->resource),
            'stats' => [
                'total_campaigns' => $this->campaigns_count ?? 0,
                'active_campaigns' => $this->active_campaigns_count ?? 0,
                'total_targets' => $this->total_targets ?? 0,
                'total_users' => $this->users_count ?? 0,
                'total_interactions' => $this->interactions_count ?? 0,
                'recent_campaigns' => CampaignResource::collection($this->recent_campaigns ?? collect()),
            ],
        ];
    }
}
