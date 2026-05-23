<?php

namespace App\Filament\Resources\CaseFileDocuments;

use App\Filament\Resources\CaseFileDocuments\Pages\CreateCaseFileDocument;
use App\Filament\Resources\CaseFileDocuments\Pages\EditCaseFileDocument;
use App\Filament\Resources\CaseFileDocuments\Pages\ListCaseFileDocuments;
use App\Filament\Resources\CaseFileDocuments\Pages\ViewCaseFileDocument;
use App\Filament\Resources\CaseFileDocuments\Schemas\CaseFileDocumentForm;
use App\Filament\Resources\CaseFileDocuments\Schemas\CaseFileDocumentInfolist;
use App\Filament\Resources\CaseFileDocuments\Tables\CaseFileDocumentsTable;
use App\Models\CaseFileDocument;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class CaseFileDocumentResource extends Resource
{
    protected static ?string $model = CaseFileDocument::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return CaseFileDocumentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CaseFileDocumentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CaseFileDocumentsTable::configure($table);
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
            'index' => ListCaseFileDocuments::route('/'),
            'create' => CreateCaseFileDocument::route('/create'),
            'view' => ViewCaseFileDocument::route('/{record}'),
            'edit' => EditCaseFileDocument::route('/{record}/edit'),
        ];
    }
}
