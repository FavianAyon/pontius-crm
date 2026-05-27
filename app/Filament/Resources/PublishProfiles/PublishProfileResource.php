<?php

namespace App\Filament\Resources\PublishProfiles;

use App\Filament\Resources\PublishProfiles\Pages\CreatePublishProfile;
use App\Filament\Resources\PublishProfiles\Pages\EditPublishProfile;
use App\Filament\Resources\PublishProfiles\Pages\ListPublishProfiles;
use App\Filament\Resources\PublishProfiles\Pages\ViewPublishProfile;
use App\Filament\Resources\PublishProfiles\Schemas\PublishProfileForm;
use App\Filament\Resources\PublishProfiles\Schemas\PublishProfileInfolist;
use App\Filament\Resources\PublishProfiles\Tables\PublishProfilesTable;
use App\Models\PublishProfile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PublishProfileResource extends Resource
{
    protected static ?string $model = PublishProfile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return PublishProfileForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PublishProfileInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PublishProfilesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPublishProfiles::route('/'),
            'create' => CreatePublishProfile::route('/create'),
            'view' => ViewPublishProfile::route('/{record}'),
            'edit' => EditPublishProfile::route('/{record}/edit'),
        ];
    }
}
