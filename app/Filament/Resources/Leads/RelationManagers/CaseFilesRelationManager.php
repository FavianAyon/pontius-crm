<?php

namespace App\Filament\Resources\Leads\RelationManagers;

use App\Filament\Resources\CaseFiles\Schemas\CaseFileForm;
use App\Filament\Resources\CaseFiles\Tables\CaseFilesTable;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class CaseFilesRelationManager extends RelationManager
{
    protected static string $relationship = 'caseFiles';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('case-files.case_files');
    }

    public function form(Schema $schema): Schema
    {
        return CaseFileForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return CaseFilesTable::configure($table)
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['lead_id'] = $this->getOwnerRecord()->id;

                        return $data;
                    }),
            ]);
    }
}
