<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DevelopmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $profile = $request->get('lang') === 'en'
            ? $this->publishProfileEn
            : $this->publishProfileEs;

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'status' => $this->status,
            'sales_status' => $this->sales_status,
            'location' => $this->location,
            'developer_name' => $this->developer_name,
            'delivery_date' => $this->delivery_date?->format('Y-m-d'),
            'construction_status' => $this->construction_status,
            'total_units' => $this->total_units,
            'available_units' => $this->available_units,

            'description' => $profile?->public_description,

            'seo' => [
                'title' => $profile?->seo_title,
                'description' => $profile?->seo_description,
            ],

            'open_graph' => [
                'title' => $profile?->og_title,
                'description' => $profile?->og_description,
                'image' => $this->featuredImage?->url,
            ],

            'structured_data' => $profile?->structured_data_json,

            'images' => $this->whenLoaded('mediaAssets', function () {
                return $this->mediaAssets->map(fn ($media) => [
                    'url' => $media->url,
                    'title' => $media->title,
                    'alt' => $media->alt_text,
                    'caption' => $media->caption,
                    'featured' => $media->is_featured,
                    'collection' => $media->collection,
                    'sort_order' => $media->sort_order,
                ])->values();
            }),

            'units' => DevelopmentUnitResource::collection(
                $this->whenLoaded('units')
            ),

            'api_payload' => $profile?->api_payload,
        ];
    }
}
