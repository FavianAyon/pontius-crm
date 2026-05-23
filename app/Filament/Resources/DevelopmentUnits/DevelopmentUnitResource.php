<?php

namespace App\Filament\Resources\DevelopmentUnits;

use App\Filament\Resources\DevelopmentUnits\Pages\CreateDevelopmentUnit;
use App\Filament\Resources\DevelopmentUnits\Pages\EditDevelopmentUnit;
use App\Filament\Resources\DevelopmentUnits\Pages\ListDevelopmentUnits;
use App\Filament\Resources\DevelopmentUnits\Pages\ViewDevelopmentUnit;
use App\Filament\Resources\DevelopmentUnits\Schemas\DevelopmentUnitForm;
use App\Filament\Resources\DevelopmentUnits\Schemas\DevelopmentUnitInfolist;
use App\Filament\Resources\DevelopmentUnits\Tables\DevelopmentUnitsTable;
use App\Models\DevelopmentUnit;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;use App\Filament\Resources\DevelopmentUnits\RelationManagers;

class DevelopmentUnitResource extends Resource
{
    protected static ?string $model = DevelopmentUnit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return DevelopmentUnitForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DevelopmentUnitInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DevelopmentUnitsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDevelopmentUnits::route('/'),
            'create' => CreateDevelopmentUnit::route('/create'),
            'view' => ViewDevelopmentUnit::route('/{record}'),
            'edit' => EditDevelopmentUnit::route('/{record}/edit'),
        ];
    }
}
