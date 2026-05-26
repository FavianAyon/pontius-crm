<?php

namespace App\Filament\Pages;

use App\Filament\Resources\CaseFiles\CaseFileResource;
use App\Models\CaseFile;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class ActiveListings extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'filament.pages.active-listings';

    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('case-files.active_listing_files');
    }

    public function getTitle(): string
    {
        return __('case-files.active_listing_files');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getListingFilesQuery())
            ->columns([
                TextColumn::make('folio')
                    ->label(__('case-files.folio'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label(__('case-files.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('case-files.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('lead.full_name')
                    ->label(__('case-files.lead'))
                    ->searchable(),

                TextColumn::make('listing.title')
                    ->label(__('case-files.listing'))
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
            ])
            ->defaultSort('updated_at', 'desc');
    }

    protected function getListingFilesQuery(): Builder
    {
        $query = CaseFile::query()
            ->where('type', 'listing')
            ->with(['lead', 'listing', 'assignedTo']);

        if (! auth()->user()?->can('view_all_leads')) {
            $query->where('assigned_to_user_id', auth()->id());
        }

        return $query;
    }
}
