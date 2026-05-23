<?php

namespace App\Filament\Resources\CaseFileDocuments\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CaseFileDocumentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Hidden::make('uploaded_by_user_id')
                ->default(fn () => auth()->id()),

            Section::make(__('case-file-documents.document'))
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('case_file_id')
                            ->label(__('case-file-documents.case_file'))
                            ->relationship('caseFile', 'folio')
                            ->searchable()
                            ->preload()
                            ->required(),

                        TextInput::make('name')
                            ->label(__('case-file-documents.name'))
                            ->required()
                            ->maxLength(255),

                        Select::make('document_type')
                            ->label(__('case-file-documents.document_type'))
                            ->options([
                                'id' => __('case-file-documents.id'),
                                'proof_of_address' => __('case-file-documents.proof_of_address'),
                                'tax_document' => __('case-file-documents.tax_document'),
                                'property_deed' => __('case-file-documents.property_deed'),
                                'bank_statement' => __('case-file-documents.bank_statement'),
                                'other' => __('case-file-documents.other'),
                            ])
                            ->required(),

                        Select::make('status')
                            ->label(__('case-file-documents.status'))
                            ->options([
                                'pending' => __('case-file-documents.pending'),
                                'requested' => __('case-file-documents.requested'),
                                'uploaded' => __('case-file-documents.uploaded'),
                                'in_review' => __('case-file-documents.in_review'),
                                'approved' => __('case-file-documents.approved'),
                                'rejected' => __('case-file-documents.rejected'),
                            ])
                            ->default('pending')
                            ->required(),

                        FileUpload::make('file_path')
                            ->label(__('case-file-documents.file'))
                            ->disk('public')
                            ->directory('case-file-documents')
                            ->visibility('private')
                            ->downloadable()
                            ->openable()
                            ->acceptedFileTypes([
                                'application/pdf',
                                'image/jpeg',
                                'image/png',
                                'image/webp',
                            ])
                            ->maxSize(10240)
                            ->columnSpanFull(),
                    ]),

                    Textarea::make('notes')
                        ->label(__('case-file-documents.notes'))
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
