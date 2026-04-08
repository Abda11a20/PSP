<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
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
            'company_id' => $this->company_id,
            'type' => $this->type,
            'status' => $this->status,
            'start_date' => $this->start_date?->toISOString(),
            'end_date' => $this->end_date?->toISOString(),
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'company' => new CompanyResource($this->whenLoaded('company')),
            'targets' => CampaignTargetResource::collection($this->whenLoaded('targets')),
            'interactions' => InteractionResource::collection($this->whenLoaded('interactions')),
            'targets_count' => $this->when(isset($this->targets_count), $this->targets_count),
            'interactions_count' => $this->when(isset($this->interactions_count), $this->interactions_count),
        ];
    }
}
