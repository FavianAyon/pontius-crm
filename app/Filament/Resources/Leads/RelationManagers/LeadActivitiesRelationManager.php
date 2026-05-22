<?php

namespace App\Filament\Resources\Leads\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class LeadActivitiesRelationManager extends RelationManager
{
    protected static string $relationship = 'leadActivities';

    protected static ?string $title = 'Seguimientos';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(2)->schema([
                    Select::make('type')
                        ->label('Tipo')
                        ->options([
                            'note' => 'Nota',
                            'call' => 'Llamada',
                            'whatsapp' => 'WhatsApp',
                            'email' => 'Correo',
                            'meeting' => 'Reunión',
                            'task' => 'Tarea',
                            'follow_up' => 'Seguimiento',
                        ])
                        ->default('note')
                        ->required(),

                    Select::make('status')
                        ->label('Estado')
                        ->options([
                            'open' => 'Abierto',
                            'completed' => 'Completado',
                            'cancelled' => 'Cancelado',
                        ])
                        ->default('open')
                        ->required(),

                    TextInput::make('title')
                        ->label('Título')
                        ->maxLength(255)
                        ->columnSpanFull(),

                    DateTimePicker::make('scheduled_at')
                        ->label('Programado para'),

                    DateTimePicker::make('completed_at')
                        ->label('Completado en'),

                    Textarea::make('description')
                        ->label('Descripción')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge(),

                TextColumn::make('title')
                    ->label('Título')
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge(),

                TextColumn::make('scheduled_at')
                    ->label('Programado')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Usuario'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
