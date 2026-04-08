<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'campaign' => [
                'id' => $this->resource['campaign']['id'],
                'type' => $this->resource['campaign']['type'],
                'status' => $this->resource['campaign']['status'],
                'start_date' => $this->resource['campaign']['start_date'],
                'end_date' => $this->resource['campaign']['end_date'],
                'created_at' => $this->resource['campaign']['created_at'],
            ],
            'summary' => $this->resource['summary'],
            'interaction_details' => $this->resource['interaction_details'],
            'time_analytics' => $this->resource['time_analytics'],
            'target_analytics' => $this->resource['target_analytics'],
            'charts_data' => $this->resource['charts_data'],
        ];
    }
}
