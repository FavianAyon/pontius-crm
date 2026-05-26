<?php

namespace App\Filament\Resources\CaseFileDocuments\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;use App\Support\CrmOptions;

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
                            ->options(CrmOptions::documentTypes())
                            ->required(),

                        Select::make('status')
                            ->label(__('case-file-documents.status'))
                            ->options(CrmOptions::documentStatuses())
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
