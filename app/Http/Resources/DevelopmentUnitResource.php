<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DevelopmentUnitResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $profile = $request->get('lang') === 'en'
            ? $this->publishProfileEn
            : $this->publishProfileEs;

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'price' => $this->price,
            'currency' => $this->currency,
            'location' => $this->location,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'area_m2' => $this->area_m2,
            'description' => $profile?->public_description,
            'seo' => [
                'title' => $profile?->seo_title,
                'description' => $profile?->seo_description,
            ],
            'structured_data' => $profile?->structured_data_json,
            'images' => $this->mediaAssets->map(fn ($media) => [
                'url' => $media->url,
                'title' => $media->title,
                'alt' => $media->alt_text,
                'featured' => $media->is_featured,
            ]),
            'development' => [
                'id' => $this->development?->id,
                'name' => $this->development?->name,
                'slug' => $this->development?->slug,
            ],

            'open_graph' => [
                'title' => $profile?->og_title,
                'description' => $profile?->og_description,
                'image' => $this->featuredImage?->url,
            ],

            'api_payload' => $profile?->api_payload,
        ];
    }
}
