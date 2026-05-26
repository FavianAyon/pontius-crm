<?php

namespace App\Filament\Resources\Leads\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;use App\Models\User;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                IconColumn::make('is_duplicate')

                    ->label(__('leads.is_duplicate'))
                    ->boolean(),
                TextColumn::make('completeness_percent')
                    ->label(__('leads.completeness'))
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('full_name')
                    ->label(__('leads.full_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label(__('leads.phone'))
                    ->searchable(),

                TextColumn::make('email')
                    ->label(__('leads.email'))
                    ->searchable(),

                TextColumn::make('intent')
                    ->label(__('leads.intent'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('interest_target_type')
                    ->label(__('leads.interest_target_type'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('leads.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('priority')
                    ->label(__('leads.priority'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('assignedTo.name')
                    ->label(__('leads.assigned_to'))
                    ->sortable(),

                TextColumn::make('next_follow_up_at')
                    ->label(__('leads.next_follow_up_at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('duplicate_match_fields')
                    ->label(__('leads.duplicate_match_fields'))
                    ->badge()
                    ->separator(',')
                    ->visible(fn () => auth()->user()?->can('view_all_leads')),
                TextColumn::make('developmentUnit.unit_number')
                    ->label(__('leads.development_unit'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('source')
                    ->label(__('leads.source'))
                    ->badge()
                    ->sortable()
                    ->searchable(),
                TextColumn::make('campaign')
                    ->label(__('leads.campaign'))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

            ])
            ->filters([
                SelectFilter::make('intent')
                    ->label(__('leads.intent'))
                    ->options([
                        'buy' => __('leads.buy'),
                        'sell' => __('leads.sell'),
                        'both' => __('leads.both'),
                    ]),

                SelectFilter::make('status')
                    ->label(__('leads.status'))
                    ->options([
                        'new' => __('leads.status_new'),
                        'contacted' => __('leads.status_contacted'),
                        'qualified' => __('leads.status_qualified'),
                        'proposal' => __('leads.status_proposal'),
                        'negotiation' => __('leads.status_negotiation'),
                        'won' => __('leads.status_won'),
                        'lost' => __('leads.status_lost'),
                        'converted' => __('leads.status_converted'),
                    ]),
                TernaryFilter::make('is_duplicate')
                    ->label('Duplicados'),
                SelectFilter::make('lead_group')
                    ->label(__('leads.status'))
                    ->options([
                        'active' => __('leads.active_leads'),
                        'converted' => __('leads.converted_leads'),
                        'lost' => __('leads.lost_leads'),
                    ])
                    ->query(function ($query, array $data) {
                        return match ($data['value'] ?? null) {
                            'active' => $query->whereIn('status', [
                                'new',
                                'contacted',
                                'qualified',
                                'proposal',
                                'negotiation',
                            ]),
                            'converted' => $query->whereIn('status', [
                                'converted',
                                'won',
                            ]),
                            'lost' => $query->where('status', 'lost'),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                Action::make('overview')
                    ->label(__('leads.overview'))
                    ->icon('heroicon-o-squares-2x2')
                    ->url(fn ($record) => \App\Filament\Resources\Leads\LeadResource::getUrl('overview', ['record' => $record])),
                Action::make('createCaseFile')
                    ->label(__('case-files.case_file'))
                    ->icon('heroicon-o-folder-plus')
                    ->schema([
                        Select::make('type')
                            ->label(__('case-files.type'))
                            ->options([
                                'lead' => __('case-files.lead_type'),
                                'buyer' => __('case-files.buyer'),
                                'seller' => __('case-files.seller'),
                                'listing' => __('case-files.listing_file'),
                            ])
                            ->default('lead')
                            ->required(),

                        TextInput::make('title')
                            ->label(__('case-files.title'))
                            ->default(fn ($record) => $record->full_name ?: $record->phone ?: $record->email)
                            ->required(),
                    ])
                    ->action(function ($record, array $data): void {
                        \App\Models\CaseFile::create([
                            'lead_id' => $record->id,
                            'type' => $data['type'],
                            'title' => $data['title'],
                            'status' => 'open',
                            'assigned_to_user_id' => $record->assigned_to_user_id ?? auth()->id(),
                            'created_by_user_id' => auth()->id(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title(__('case-files.case_file_created'))
                            ->body(__('case-files.case_file_created_body'))
                            ->send();
                    }),
                ViewAction::make(),
                EditAction::make(),
                Action::make('reassignLead')
                    ->label(__('leads.reassign_lead'))
                    ->icon('heroicon-o-user-plus')
                    ->visible(fn () => auth()->user()?->can('assign_lead'))
                    ->schema([
                        Select::make('assigned_to_user_id')
                            ->label(__('leads.assigned_to'))
                            ->options(fn () => User::role('agent')
                                ->where('is_active', true)
                                ->orderBy('name')
                                ->pluck('name', 'id'))
                            ->searchable()
                            ->required(),
                        Textarea::make('reason')
                            ->label(__('leads.reassignment_reason')),
                    ])
                    ->action(function ($record, array $data): void {
                        $oldAssignedUserId = $record->assigned_to_user_id;
                        $record->update([
                            'assigned_to_user_id' => $data['assigned_to_user_id'],
                        ]);
                        $record->refresh();
                        $record->createReassignmentReviewTask();
                        if ($record->assignedTo) {
                            Notification::make()
                                ->title(__('leads.lead_assigned_title'))
                                ->body(__('leads.lead_assigned_body', [
                                    'name' => $record->full_name ?: $record->phone ?: $record->email,
                                ]))
                                ->success()
                                ->sendToDatabase($record->assignedTo);
                        }
                        $record->assignments()->create([
                            'from_user_id' => $oldAssignedUserId,
                            'to_user_id' => $data['assigned_to_user_id'],
                            'changed_by_user_id' => auth()->id(),
                            'reason' => $data['reason'] ?? null,
                        ]);


                        Notification::make()
                            ->success()
                            ->title(__('leads.lead_reassigned'))
                            ->send();
                    }),
                Action::make('mergeDuplicate')
                    ->label(__('leads.merge_duplicate'))
                    ->icon('heroicon-o-arrows-right-left')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(__('leads.merge_duplicate_heading'))
                    ->modalDescription(__('leads.merge_duplicate_description'))
                    ->visible(fn ($record) => $record->is_duplicate && $record->duplicate_of_lead_id)
                    ->action(function ($record) {
                        $mainLead = $record->duplicateOf;
                        if (! $mainLead) {
                            return;
                        }
                        $record->mergeInto($mainLead);

                        Notification::make()
                            ->success()
                            ->title(__('leads.merge_success_title'))
                            ->body(__('leads.merge_success_body'))
                            ->send();

                    }),

            ])
            ->toolbarActions([

                Action::make('openPipeline')
                    ->label(__('leads.open_pipeline'))
                    ->icon('heroicon-o-view-columns')
                    ->url(fn () => \App\Filament\Resources\Leads\LeadResource::getUrl('pipeline')),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);

    }
}
