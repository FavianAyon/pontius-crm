<?php

namespace App\Filament\Resources\PublishProfiles\Schemas;

use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PublishProfileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('publish-profiles.publish_profile'))
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('language')
                            ->label(__('publish-profiles.language'))
                            ->options([
                                'es' => 'Español',
                                'en' => 'English',
                            ])
                            ->required(),

                        TextInput::make('content_score')
                            ->label(__('publish-profiles.content_score'))
                            ->numeric()
                            ->disabled()
                            ->dehydrated(false),

                        TextInput::make('seo_title')
                            ->label(__('publish-profiles.seo_title'))
                            ->maxLength(255),

                        Textarea::make('seo_description')
                            ->label(__('publish-profiles.seo_description'))
                            ->rows(3),

                        TextInput::make('og_title')
                            ->label(__('publish-profiles.og_title'))
                            ->maxLength(255),

                        Textarea::make('og_description')
                            ->label(__('publish-profiles.og_description'))
                            ->rows(3),

                        Textarea::make('public_description')
                            ->label(__('publish-profiles.public_description'))
                            ->rows(6)
                            ->columnSpanFull(),

                        Textarea::make('ai_summary')
                            ->label(__('publish-profiles.ai_summary'))
                            ->rows(5)
                            ->columnSpanFull(),

                        KeyValue::make('keywords')
                            ->label(__('publish-profiles.keywords'))
                            ->columnSpanFull(),

                        KeyValue::make('metadata')
                            ->label('Metadata')
                            ->columnSpanFull(),
                    ]),
                ]),
        ]);
    }
}
