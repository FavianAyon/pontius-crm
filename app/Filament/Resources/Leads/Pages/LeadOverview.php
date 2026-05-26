<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use App\Models\CaseFile;
use App\Models\Task;
use Filament\Actions;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Models\CaseFileDocument;
use Filament\Forms\Components\FileUpload;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\WithFileUploads;

class LeadOverview extends Page
{
    use InteractsWithRecord, WithFileUploads;
    public ?int $selectedDocumentId = null;
    public array $documentUploads = [];
    protected static string $resource = LeadResource::class;

    protected string $view = 'filament.resources.leads.pages.lead-overview';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->record = $this->resolveRecord($record);
        $this->refreshLeadOverview();
        $this->timeline = \App\Services\LeadTimelineService::build(
            $this->record
        );

    }
    public array $timeline = [];

    public function getTitle(): string
    {
        return __('leads.lead_overview');
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('createTask')
                ->label(__('tasks.task'))
                ->icon('heroicon-o-check-circle')
                ->schema([
                    TextInput::make('title')
                        ->label(__('tasks.title'))
                        ->required(),

                    Textarea::make('description')
                        ->label(__('tasks.description')),

                    Select::make('priority')
                        ->label(__('tasks.priority'))
                        ->options([
                            'low' => __('tasks.low'),
                            'normal' => __('tasks.normal'),
                            'high' => __('tasks.high'),
                            'urgent' => __('tasks.urgent'),
                        ])
                        ->default('normal'),

                    DateTimePicker::make('due_at')
                        ->label(__('tasks.due_at')),
                ])
                ->action(function (array $data): void {
                    $this->record->tasks()->create([
                        ...$data,
                        'assigned_to_user_id' => $this->record->assigned_to_user_id ?? auth()->id(),
                        'created_by_user_id' => auth()->id(),
                        'status' => 'open',
                        'type' => 'general',
                    ]);

                    Notification::make()
                        ->success()
                        ->title(__('tasks.task'))
                        ->send();

                    $this->refreshLeadOverview();
                }),

            Actions\Action::make('addFollowUp')
                ->label(__('leads.complete_followup'))
                ->icon('heroicon-o-chat-bubble-left-right')
                ->schema([
                    Textarea::make('description')
                        ->label(__('leads.complete_followup_notes'))
                        ->required(),

                    DateTimePicker::make('next_follow_up_at')
                        ->label(__('leads.next_followup')),
                ])
                ->action(function (array $data): void {
                    $this->record->leadActivities()->create([
                        'type' => 'follow_up',
                        'status' => 'completed',
                        'title' => __('leads.followup_completed'),
                        'description' => $data['description'],
                        'completed_at' => now(),
                        'user_id' => auth()->id(),
                    ]);

                    $this->record->update([
                        'last_contacted_at' => now(),
                        'next_follow_up_at' => $data['next_follow_up_at'] ?? null,
                    ]);

                    Notification::make()
                        ->success()
                        ->title(__('leads.followup_completed'))
                        ->send();

                    $this->refreshLeadOverview();
                }),

            Actions\Action::make('createCaseFile')
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
                        ->default(fn () => $this->record->full_name)
                        ->required(),

                    Textarea::make('description')
                        ->label(__('case-files.description')),
                ])
                ->action(function (array $data): void {
                    CaseFile::create([
                        ...$data,
                        'lead_id' => $this->record->id,
                        'assigned_to_user_id' => $this->record->assigned_to_user_id ?? auth()->id(),
                        'created_by_user_id' => auth()->id(),
                        'status' => 'open',
                    ]);

                    Notification::make()
                        ->success()
                        ->title(__('case-files.case_file'))
                        ->send();

                    $this->refreshLeadOverview();
                }),
            Actions\Action::make('uploadDocument')
                ->label(__('case-file-documents.upload_file'))
                ->icon('heroicon-o-arrow-up-tray')
                ->schema([
                    Select::make('document_id')
                        ->label(__('case-file-documents.document'))
                        ->options(fn () => $this->record->caseFiles
                            ->flatMap(fn ($caseFile) => $caseFile->documents)
                            ->filter(fn ($document) => ! $document->file_path || $document->status === 'rejected')
                            ->mapWithKeys(fn ($document) => [
                                $document->id => "{$document->caseFile->folio} - {$document->name}",
                            ]))
                        ->searchable()
                        ->required(),

                    FileUpload::make('file_path')
                        ->label(__('case-file-documents.file'))
                        ->disk('public')
                        ->directory('case-file-documents')
                        ->visibility('private')
                        ->acceptedFileTypes([
                            'application/pdf',
                            'image/jpeg',
                            'image/png',
                            'image/webp',
                        ])
                        ->maxSize(10240)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $document = CaseFileDocument::findOrFail($data['document_id']);

                    abort_unless(auth()->user()?->can('update', $document), 403);

                    $document->update([
                        'file_path' => $data['file_path'],
                        'uploaded_by_user_id' => auth()->id(),
                        'uploaded_at' => now(),
                        'status' => 'uploaded',
                    ]);

                    Notification::make()
                        ->success()
                        ->title(__('case-file-documents.uploaded'))
                        ->send();

                    $this->refreshLeadOverview();
                }),

            Actions\Action::make('approveDocument')
                ->label(__('case-file-documents.approve'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => auth()->user()?->can('approve_case_file_document') && $this->record->caseFiles->flatMap->documents->whereNotNull('file_path')->where('status', '!=', 'approved')->count())
                ->schema([
                    Select::make('document_id')
                        ->label(__('case-file-documents.document'))
                        ->options(fn () => $this->record->caseFiles
                            ->flatMap(fn ($caseFile) => $caseFile->documents)
                            ->filter(fn ($document) => $document->file_path && $document->status !== 'approved')
                            ->mapWithKeys(fn ($document) => [
                                $document->id => "{$document->caseFile->folio} - {$document->name}",
                            ]))
                        ->searchable()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $document = CaseFileDocument::findOrFail($data['document_id']);

                    abort_unless(auth()->user()?->can('approve', $document), 403);

                    $document->update([
                        'status' => 'approved',
                        'validated_at' => now(),
                    ]);

                    Notification::make()
                        ->success()
                        ->title(__('case-file-documents.approved'))
                        ->send();

                    $this->refreshLeadOverview();
                }),

            Actions\Action::make('rejectDocument')
                ->label(__('case-file-documents.reject'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => auth()->user()?->can('reject_case_file_document') && $this->record->caseFiles->flatMap->documents->whereNotNull('file_path')->where('status', '!=', 'rejected')->count())
                ->schema([
                    Select::make('document_id')
                        ->label(__('case-file-documents.document'))
                        ->options(fn () => $this->record->caseFiles
                            ->flatMap(fn ($caseFile) => $caseFile->documents)
                            ->filter(fn ($document) => $document->file_path && $document->status !== 'approved')
                            ->mapWithKeys(fn ($document) => [
                                $document->id => "{$document->caseFile->folio} - {$document->name}",
                            ]))
                        ->searchable()
                        ->required(),

                    Textarea::make('notes')
                        ->label(__('case-file-documents.notes')),
                ])
                ->action(function (array $data): void {
                    $document = CaseFileDocument::findOrFail($data['document_id']);

                    abort_unless(auth()->user()?->can('reject', $document), 403);

                    $document->update([
                        'status' => 'rejected',
                        'validated_at' => null,
                        'notes' => $data['notes'] ?? null,
                    ]);

                    Notification::make()
                        ->warning()
                        ->title(__('case-file-documents.rejected'))
                        ->send();

                    $this->refreshLeadOverview();
                }),
            Actions\Action::make('convertToBuyer')
                ->label(__('leads.convert_to_buyer'))
                ->icon('heroicon-o-user-plus')
                ->color('success')
                ->visible(fn () => ! $this->record->caseFiles->where('type', 'buyer')->count())
                ->requiresConfirmation()
                ->action(fn () => $this->createCaseFileFromLead('buyer')),

            Actions\Action::make('convertToSeller')
                ->label(__('leads.convert_to_seller'))
                ->icon('heroicon-o-home')
                ->color('warning')
                ->visible(fn () => ! $this->record->caseFiles->where('type', 'seller')->count())
                ->requiresConfirmation()
                ->action(fn () => $this->createCaseFileFromLead('seller')),
        ];
    }
    public function selectDocument(int $documentId): void
    {
        $this->selectedDocumentId = $documentId;
    }

    public function approveDocumentInline(int $documentId): void
    {
        $document = CaseFileDocument::findOrFail($documentId);

        abort_unless(auth()->user()?->can('approve', $document), 403);

        if (! $document->file_path) {
            Notification::make()
                ->danger()
                ->title(__('case-file-documents.file_required_before_approval'))
                ->send();

            return;
        }

        $document->update([
            'status' => 'approved',
            'validated_at' => now(),
        ]);

        $this->record->load('caseFiles.documents');

        Notification::make()
            ->success()
            ->title(__('case-file-documents.approved'))
            ->send();
    }

    public function rejectDocumentInline(int $documentId): void
    {
        $document = CaseFileDocument::findOrFail($documentId);

        abort_unless(auth()->user()?->can('reject', $document), 403);

        if (! $document->file_path) {
            Notification::make()
                ->danger()
                ->title(__('case-file-documents.file_required_before_rejection'))
                ->send();

            return;
        }

        $document->update([
            'status' => 'rejected',
            'validated_at' => null,
        ]);

        $this->record->load('caseFiles.documents');

        Notification::make()
            ->warning()
            ->title(__('case-file-documents.rejected'))
            ->send();
    }
    public function uploadDocumentInline(int $documentId): void
    {
        $document = CaseFileDocument::findOrFail($documentId);

        abort_unless(auth()->user()?->can('update', $document), 403);

        $file = $this->documentUploads[$documentId] ?? null;

        if (! $file instanceof TemporaryUploadedFile) {
            Notification::make()
                ->danger()
                ->title(__('case-file-documents.file_required'))
                ->send();

            return;
        }

        $path = $file->store('case-file-documents', 'public');

        $document->update([
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by_user_id' => auth()->id(),
            'uploaded_at' => now(),
            'status' => 'uploaded',
        ]);

        unset($this->documentUploads[$documentId]);

        $this->record->load('caseFiles.documents');

        Notification::make()
            ->success()
            ->title(__('case-file-documents.uploaded'))
            ->send();
    }
    protected function createCaseFileFromLead(string $type): void
    {
        \App\Models\CaseFile::firstOrCreate(
            [
                'lead_id' => $this->record->id,
                'type' => $type,
            ],
            [
                'title' => $this->record->full_name ?: $this->record->phone ?: $this->record->email,
                'status' => 'open',
                'assigned_to_user_id' => $this->record->assigned_to_user_id ?? auth()->id(),
                'created_by_user_id' => auth()->id(),
            ]
        );

        $this->record->update([
            'status' => 'converted',
        ]);

        $this->record->refresh();
        $this->record->load('caseFiles.documents');

        \Filament\Notifications\Notification::make()
            ->success()
            ->title(__('leads.converted_successfully'))
            ->send();
    }
    protected function refreshLeadOverview(): void
    {
        $this->record->refresh();

        $this->record->load([
            'tasks',
            'leadActivities',
            'caseFiles.documents',
            'assignedTo',
            'development',
            'developmentUnit',
            'listing',
            'assignments.fromUser',
            'assignments.toUser',
            'assignments.changedBy',
        ]);

        $this->timeline = \App\Services\LeadTimelineService::build($this->record);
    }
}
