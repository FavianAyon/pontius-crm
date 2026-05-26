<?php

namespace App\Filament\Pages;

use App\Filament\Resources\CaseFiles\CaseFileResource;
use App\Filament\Resources\Leads\LeadResource;
use App\Models\CaseFile;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ActiveClients extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.active-clients';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('case-files.active_clients');
    }

    public function getTitle(): string
    {
        return __('case-files.active_clients');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getClientsQuery())
            ->columns([
                TextColumn::make('folio')
                    ->label(__('case-files.folio'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label(__('case-files.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('case-files.client_type'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('case-files.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('lead.full_name')
                    ->label(__('case-files.lead'))
                    ->searchable(),

                TextColumn::make('documents_progress_percent')
                    ->label(__('case-files.progress'))
                    ->suffix('%')
                    ->sortable(),

                TextColumn::make('pending_documents_count')
                    ->label(__('case-files.pending_documents'))
                    ->sortable(),

                TextColumn::make('assignedTo.name')
                    ->label(__('case-files.assigned_to')),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('case-files.client_type'))
                    ->options([
                        'buyer' => __('case-files.buyer'),
                        'seller' => __('case-files.seller'),
                    ]),

                SelectFilter::make('status')
                    ->label(__('case-files.status'))
                    ->options([
                        'open' => __('case-files.open'),
                        'in_review' => __('case-files.in_review'),
                        'approved' => __('case-files.approved'),
                        'closed' => __('case-files.closed'),
                        'cancelled' => __('case-files.cancelled'),
                    ]),
            ])
            ->recordActions([
                Action::make('openCaseFile')
                    ->label(__('case-files.case_file'))
                    ->icon('heroicon-o-folder-open')
                    ->url(fn (CaseFile $record) => CaseFileResource::getUrl('view', ['record' => $record])),

                Action::make('openLead360')
                    ->label(__('leads.lead_overview'))
                    ->icon('heroicon-o-squares-2x2')
                    ->visible(fn (CaseFile $record) => filled($record->lead_id))
                    ->url(fn (CaseFile $record) => LeadResource::getUrl('overview', ['record' => $record->lead_id])),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    protected function getClientsQuery(): Builder
    {
        $query = CaseFile::query()
            ->whereIn('type', ['buyer', 'seller'])
            ->with(['lead', 'assignedTo']);

        if (! auth()->user()?->can('view_all_leads')) {
            $query->where('assigned_to_user_id', auth()->id());
        }

        return $query;
    }
}
