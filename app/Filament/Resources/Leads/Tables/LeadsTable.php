<?php

namespace App\Filament\Resources\Leads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable(),

                TextColumn::make('phone')
                    ->label('Teléfono')
                    ->searchable(),

                TextColumn::make('source')
                    ->label('Fuente')
                    ->badge(),

                TextColumn::make('interest_type')
                    ->label('Interés')
                    ->badge(),

                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->sortable(),

                TextColumn::make('priority')
                    ->label('Prioridad')
                    ->badge()
                    ->sortable(),

                TextColumn::make('next_follow_up_at')
                    ->label('Seguimiento')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'new' => 'Nuevo',
                        'contacted' => 'Contactado',
                        'qualified' => 'Calificado',
                        'proposal' => 'Propuesta',
                        'negotiation' => 'Negociación',
                        'won' => 'Ganado',
                        'lost' => 'Perdido',
                    ]),

                SelectFilter::make('source')
                    ->label('Fuente')
                    ->options([
                        'website' => 'Sitio web',
                        'facebook' => 'Facebook',
                        'instagram' => 'Instagram',
                        'whatsapp' => 'WhatsApp',
                        'referral' => 'Referido',
                        'walk_in' => 'Visita directa',
                        'other' => 'Otro',
                    ]),
            ]);
    }
}
