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
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;use App\Filament\Resources\DevelopmentUnits\RelationManagers;
use Illuminate\Support\Facades\Artisan;

class DevelopmentUnitResource extends Resource
{
    protected static ?string $model = DevelopmentUnit::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->can('view_any_development_unit') ?? false;
    }
    public static function getNavigationGroup(): string
    {
        return __('navigation.inventory');
    }

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
            RelationManagers\MediaAssetsRelationManager::class,
            RelationManagers\PublishProfilesRelationManager::class,
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
    public static function toolbarActions(): array
    {
        return [
            Action::make('clearPublicCache')
                ->label(__('publish-profiles.clear_public_cache'))
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->visible(fn () => auth()->user()?->hasRole('admin'))
                ->action(function () {
                    Artisan::call('crm:clear-public-inventory-cache');

                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title(__('publish-profiles.public_cache_cleared'))
                        ->send();
                }),
        ];
    }
}
