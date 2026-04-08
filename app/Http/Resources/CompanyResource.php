<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'plan_id' => $this->plan_id,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
            'plan' => new PlanResource($this->whenLoaded('plan')),
            'campaigns' => CampaignResource::collection($this->whenLoaded('campaigns')),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),
        ];
    }
}
