<?php

namespace App\Filament\Resources\CaseFiles;

use App\Filament\Resources\CaseFiles\Pages\CreateCaseFile;
use App\Filament\Resources\CaseFiles\Pages\EditCaseFile;
use App\Filament\Resources\CaseFiles\Pages\ListCaseFiles;
use App\Filament\Resources\CaseFiles\Pages\ViewCaseFile;
use App\Filament\Resources\CaseFiles\Schemas\CaseFileForm;
use App\Filament\Resources\CaseFiles\Schemas\CaseFileInfolist;
use App\Filament\Resources\CaseFiles\Tables\CaseFilesTable;
use App\Models\CaseFile;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;use Illuminate\Database\Eloquent\Builder;

class CaseFileResource extends Resource
{
    protected static ?string $model = CaseFile::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;
    public static function getNavigationGroup(): string
    {
        return __('navigation.files');
    }

    public static function form(Schema $schema): Schema
    {
        return CaseFileForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CaseFileInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CaseFilesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\DocumentsRelationManager::class,
            RelationManagers\ActivitiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCaseFiles::route('/'),
            'create' => CreateCaseFile::route('/create'),
            'view' => ViewCaseFile::route('/{record}'),
            'edit' => EditCaseFile::route('/{record}/edit'),
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->can('view_all_leads')) {
            return $query;
        }

        return $query->where('assigned_to_user_id', auth()->id());
    }
}
