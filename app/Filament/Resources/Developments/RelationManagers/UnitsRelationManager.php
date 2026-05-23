<?php

namespace App\Filament\Resources\Developments\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use App\Filament\Resources\DevelopmentUnits\Schemas\DevelopmentUnitForm;
use App\Filament\Resources\DevelopmentUnits\Tables\DevelopmentUnitsTable;

class UnitsRelationManager extends RelationManager
{
    protected static string $relationship = 'units';

    protected static ?string $title = null;

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('development-units.units');
    }

    public function form(Schema $schema): Schema
    {
        return DevelopmentUnitForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return DevelopmentUnitsTable::configure($table)
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['development_id'] = $this->getOwnerRecord()->id;

                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
