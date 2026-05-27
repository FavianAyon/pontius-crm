<?php

namespace App\Filament\Resources\DevelopmentUnits\RelationManagers;

use App\Filament\Resources\MediaAssets\Schemas\MediaAssetForm;
use App\Filament\Resources\MediaAssets\Tables\MediaAssetsTable;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MediaAssetsRelationManager extends RelationManager
{
    protected static string $relationship = 'mediaAssets';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('media-assets.media_assets');
    }

    public function form(Schema $schema): Schema
    {
        return MediaAssetForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return MediaAssetsTable::configure($table)
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
