<?php

namespace App\Services;

use App\Models\Lead;

class LeadTimelineService
{
    public static function build(Lead $lead): array
    {
        $timeline = [];

        foreach ($lead->leadActivities as $activity) {
            $timeline[] = [
                'date' => $activity->created_at,
                'type' => 'activity',
                'title' => $activity->title ?: __('leads.activity'),
                'description' => $activity->description,
            ];
        }

        foreach ($lead->tasks as $task) {
            $timeline[] = [
                'date' => $task->created_at,
                'type' => 'task',
                'title' => $task->title,
                'description' => __("tasks.{$task->status}") . ' · ' . ($task->due_at?->format('d/m/Y H:i') ?: '—'),
            ];
        }

        foreach ($lead->assignments as $assignment) {
            $timeline[] = [
                'date' => $assignment->created_at,
                'type' => 'assignment',
                'title' => __('leads.lead_reassigned'),
                'description' =>
                    ($assignment->fromUser?->name ?? '—')
                    . ' → ' .
                    ($assignment->toUser?->name ?? '—')
                    . ($assignment->changedBy ? ' · ' . $assignment->changedBy->name : ''),
            ];
        }

        foreach ($lead->caseFiles as $caseFile) {
            $timeline[] = [
                'date' => $caseFile->created_at,
                'type' => 'case_file',
                'title' => __('case-files.case_file') . ': ' . $caseFile->folio,
                'description' => $caseFile->title . ' · ' . __("case-files.{$caseFile->status}"),
                'url' => \App\Filament\Resources\CaseFiles\CaseFileResource::getUrl('view', [
                    'record' => $caseFile,
                ]),
            ];

            foreach ($caseFile->documents as $document) {
                $timeline[] = [
                    'date' => $document->updated_at,
                    'type' => 'document',
                    'title' => __('case-file-documents.document') . ': ' . $document->name,
                    'description' => __("case-file-documents.{$document->status}"),
                    'url' => $document->file_url,
                ];
            }
        }

        usort($timeline, fn ($a, $b) => $b['date'] <=> $a['date']);

        return $timeline;
    }
}
