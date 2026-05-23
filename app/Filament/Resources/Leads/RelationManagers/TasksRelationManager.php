<?php

namespace App\Filament\Resources\Leads\RelationManagers;

use App\Filament\Resources\Tasks\Schemas\TaskForm;
use App\Filament\Resources\Tasks\Tables\TasksTable;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TasksRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    public static function getTitle($ownerRecord, string $pageClass): string
    {
        return __('tasks.tasks');
    }

    public function form(Schema $schema): Schema
    {
        return TaskForm::configure($schema);
    }

    public function table(Table $table): Table
    {
        return TasksTable::configure($table)
            ->headerActions([
                CreateAction::make()
                    ->mutateDataUsing(function (array $data): array {
                        $data['lead_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    }),
            ]);
    }
}
