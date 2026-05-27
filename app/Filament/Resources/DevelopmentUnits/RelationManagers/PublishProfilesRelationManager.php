<?php

namespace App\Filament\Resources\DevelopmentUnits\RelationManagers;

use App\Filament\Resources\PublishProfiles\Schemas\PublishProfileForm;
use App\Filament\Resources\PublishProfiles\Tables\PublishProfilesTable;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class PublishProfilesRelationManager extends RelationManager
{
    protected static string $relationship = 'publishProfiles';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('publish-profiles.publish_profiles');
    }

    public function form(Schema $schema): Schema
    {
        return PublishProfileForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return PublishProfilesTable::configure($table)
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
