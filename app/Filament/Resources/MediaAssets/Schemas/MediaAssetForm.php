<?php

namespace App\Filament\Resources\MediaAssets\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MediaAssetForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('media-assets.media_asset'))
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('collection')
                            ->label(__('media-assets.collection'))
                            ->options([
                                'gallery' => __('media-assets.gallery'),
                                'hero' => __('media-assets.hero'),
                                'logo' => __('media-assets.logo'),
                                'floorplan' => __('media-assets.floorplan'),
                            ])
                            ->default('gallery')
                            ->required(),

                        TextInput::make('sort_order')
                            ->label(__('media-assets.sort_order'))
                            ->numeric()
                            ->default(0),

                        FileUpload::make('file_path')
                            ->label(__('media-assets.file'))
                            ->disk('public')
                            ->directory('media-assets')
                            ->visibility('public')
                            ->image()
                            ->imageEditor()
                            ->downloadable()
                            ->openable()
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('title')
                            ->label(__('media-assets.title'))
                            ->maxLength(255),

                        TextInput::make('alt_text')
                            ->label(__('media-assets.alt_text'))
                            ->maxLength(255),

                        Textarea::make('caption')
                            ->label(__('media-assets.caption'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Toggle::make('is_featured')
                            ->label(__('media-assets.is_featured')),

                        Toggle::make('is_public')
                            ->label(__('media-assets.is_public'))
                            ->default(true),
                    ]),
                ]),
        ]);
    }
}
