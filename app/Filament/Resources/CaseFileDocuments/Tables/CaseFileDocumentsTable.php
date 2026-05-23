<?php

namespace App\Filament\Resources\CaseFileDocuments\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CaseFileDocumentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('caseFile.folio')
                    ->label(__('case-file-documents.case_file'))
                    ->searchable(),

                TextColumn::make('name')
                    ->label(__('case-file-documents.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('document_type')
                    ->label(__('case-file-documents.document_type'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('case-file-documents.status'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('uploadedBy.name')
                    ->label(__('case-file-documents.uploaded_by')),

                TextColumn::make('uploaded_at')
                    ->label(__('case-file-documents.uploaded_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('case-file-documents.status'))
                    ->options([
                        'pending' => __('case-file-documents.pending'),
                        'requested' => __('case-file-documents.requested'),
                        'uploaded' => __('case-file-documents.uploaded'),
                        'in_review' => __('case-file-documents.in_review'),
                        'approved' => __('case-file-documents.approved'),
                        'rejected' => __('case-file-documents.rejected'),
                    ]),
            ])
            ->recordActions([
                Action::make('approve')
                    ->label(__('case-file-documents.approve'))
                    ->visible(fn ($record) => auth()->user()?->can('approve', $record)),

                Action::make('reject')
                    ->label(__('case-file-documents.reject'))
                    ->visible(fn ($record) => auth()->user()?->can('reject', $record)),

                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
