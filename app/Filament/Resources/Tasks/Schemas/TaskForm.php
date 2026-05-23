<?php

namespace App\Filament\Resources\Tasks\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Hidden::make('created_by_user_id')
                ->default(fn () => auth()->id()),

            Section::make(__('tasks.task'))
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('lead_id')
                            ->label(__('tasks.lead'))
                            ->relationship('lead', 'full_name')
                            ->searchable()
                            ->preload(),

                        Select::make('assigned_to_user_id')
                            ->label(__('tasks.assigned_to'))
                            ->relationship('assignedTo', 'name')
                            ->searchable()
                            ->preload()
                            ->default(fn () => auth()->id())
                            ->required(),

                        TextInput::make('title')
                            ->label(__('tasks.title'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        Select::make('type')
                            ->label(__('tasks.type'))
                            ->options([
                                'general' => __('tasks.general'),
                                'follow_up' => __('tasks.follow_up'),
                                'documents' => __('tasks.documents'),
                                'listing' => __('tasks.listing'),
                                'contract' => __('tasks.contract'),
                            ])
                            ->default('general')
                            ->required(),

                        Select::make('status')
                            ->label(__('tasks.status'))
                            ->options([
                                'open' => __('tasks.open'),
                                'in_progress' => __('tasks.in_progress'),
                                'completed' => __('tasks.completed'),
                                'cancelled' => __('tasks.cancelled'),
                            ])
                            ->default('open')
                            ->required(),

                        Select::make('priority')
                            ->label(__('tasks.priority'))
                            ->options([
                                'low' => __('tasks.low'),
                                'normal' => __('tasks.normal'),
                                'high' => __('tasks.high'),
                                'urgent' => __('tasks.urgent'),
                            ])
                            ->default('normal')
                            ->required(),

                        DateTimePicker::make('due_at')
                            ->label(__('tasks.due_at')),

                        DateTimePicker::make('completed_at')
                            ->label(__('tasks.completed_at')),
                    ]),

                    Textarea::make('description')
                        ->label(__('tasks.description'))
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
