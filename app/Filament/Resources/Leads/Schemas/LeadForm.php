<?php

namespace App\Filament\Resources\Leads\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información principal')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('first_name')
                                ->label('Nombre'),

                            TextInput::make('last_name')
                                ->label('Apellido'),

                            TextInput::make('email')
                                ->label('Correo')
                                ->email(),

                            TextInput::make('phone')
                                ->label('Teléfono'),

                            TextInput::make('whatsapp')
                                ->label('WhatsApp'),
                        ]),
                    ]),

                Section::make('Origen e interés')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('source')
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

                            Select::make('interest_type')
                                ->label('Interés')
                                ->options([
                                    'buy' => 'Comprar',
                                    'rent' => 'Rentar',
                                    'sell' => 'Vender',
                                    'property_management' => 'Property Management',
                                    'storage' => 'Bodega / Storage',
                                    'other' => 'Otro',
                                ]),

                            Select::make('preferred_language')
                                ->label('Idioma')
                                ->options([
                                    'es' => 'Español',
                                    'en' => 'Inglés',
                                ])
                                ->default('es'),
                        ]),

                        Grid::make(2)->schema([
                            TextInput::make('budget_min')
                                ->label('Presupuesto mínimo')
                                ->numeric(),

                            TextInput::make('budget_max')
                                ->label('Presupuesto máximo')
                                ->numeric(),
                        ]),

                        TextInput::make('preferred_location')
                            ->label('Zona de interés'),
                    ]),

                Section::make('Pipeline')
                    ->schema([
                        Grid::make(3)->schema([
                            Select::make('status')
                                ->label('Estado')
                                ->options([
                                    'new' => 'Nuevo',
                                    'contacted' => 'Contactado',
                                    'qualified' => 'Calificado',
                                    'proposal' => 'Propuesta',
                                    'negotiation' => 'Negociación',
                                    'won' => 'Ganado',
                                    'lost' => 'Perdido',
                                ])
                                ->default('new'),

                            Select::make('priority')
                                ->label('Prioridad')
                                ->options([
                                    'low' => 'Baja',
                                    'normal' => 'Normal',
                                    'high' => 'Alta',
                                    'urgent' => 'Urgente',
                                ])
                                ->default('normal'),

                            DateTimePicker::make('next_follow_up_at')
                                ->label('Próximo seguimiento'),
                        ]),
                    ]),

                Section::make('Notas')
                    ->schema([
                        Textarea::make('notes')
                            ->label('Notas')
                            ->rows(4)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
