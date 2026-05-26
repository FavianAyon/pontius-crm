<?php

namespace App\Filament\Resources\Developments;

use App\Filament\Resources\Developments\Pages\CreateDevelopment;
use App\Filament\Resources\Developments\Pages\EditDevelopment;
use App\Filament\Resources\Developments\Pages\ListDevelopments;
use App\Filament\Resources\Developments\Pages\ViewDevelopment;
use App\Filament\Resources\Developments\Schemas\DevelopmentForm;
use App\Filament\Resources\Developments\Schemas\DevelopmentInfolist;
use App\Filament\Resources\Developments\Tables\DevelopmentsTable;
use App\Models\Development;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use App\Filament\Resources\Developments\RelationManagers;

class DevelopmentResource extends Resource
{
    protected static ?string $model = Development::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view_any_development') ?? false;
    }
    public static function getNavigationGroup(): string
    {
        return __('navigation.inventory');
    }

    public static function form(Schema $schema): Schema
    {
        return DevelopmentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return DevelopmentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DevelopmentsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\UnitsRelationManager::class,
            RelationManagers\ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDevelopments::route('/'),
            'create' => CreateDevelopment::route('/create'),
            'view' => ViewDevelopment::route('/{record}'),
            'edit' => EditDevelopment::route('/{record}/edit'),
        ];
    }
}
