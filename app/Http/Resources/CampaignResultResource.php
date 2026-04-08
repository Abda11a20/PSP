<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResultResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'campaign_id' => $this->campaign_id,
            'target_email' => $this->target_email,
            'opened_at' => $this->opened_at?->toISOString(),
            'clicked_at' => $this->clicked_at?->toISOString(),
            'submitted_data' => $this->submitted_data,
            'risk_level' => $this->risk_level,
            'notes' => $this->notes,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'campaign' => new CampaignResource($this->whenLoaded('campaign')),
        ];
    }
}
