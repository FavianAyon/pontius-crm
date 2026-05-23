<?php

namespace App\Filament\Resources\CaseFiles\RelationManagers;

use App\Filament\Resources\CaseFileDocuments\Schemas\CaseFileDocumentForm;
use App\Filament\Resources\CaseFileDocuments\Tables\CaseFileDocumentsTable;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class DocumentsRelationManager extends RelationManager
{
    protected static string $relationship = 'documents';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('case-file-documents.documents');
    }

    public function form(Schema $schema): Schema
    {
        return CaseFileDocumentForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return CaseFileDocumentsTable::configure($table)
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['case_file_id'] = $this->getOwnerRecord()->id;

                        return $data;
                    }),
            ]);
    }
}
