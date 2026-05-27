<?php

namespace App\Filament\Resources\Listings;

use App\Filament\Resources\Listings\Pages\CreateListing;
use App\Filament\Resources\Listings\Pages\EditListing;
use App\Filament\Resources\Listings\Pages\ListListings;
use App\Filament\Resources\Listings\Pages\ViewListing;
use App\Filament\Resources\Listings\Schemas\ListingForm;
use App\Filament\Resources\Listings\Schemas\ListingInfolist;
use App\Filament\Resources\Listings\Tables\ListingsTable;
use App\Models\Listing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ListingResource extends Resource
{
    protected static ?string $model = Listing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view_any_listing') ?? false;
    }
    public static function getNavigationGroup(): string
    {
        return __('navigation.inventory');
    }

    public static function form(Schema $schema): Schema
    {
        return ListingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ListingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ListingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ActivitiesRelationManager::class,
            RelationManagers\MediaAssetsRelationManager::class,
            RelationManagers\PublishProfilesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListListings::route('/'),
            'create' => CreateListing::route('/create'),
            'view' => ViewListing::route('/{record}'),
            'edit' => EditListing::route('/{record}/edit'),
        ];
    }
}
