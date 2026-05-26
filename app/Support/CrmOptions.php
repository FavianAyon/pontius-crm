<?php

namespace App\Support;

class CrmOptions
{
    public static function leadStatuses(): array
    {
        return [
            'new' => __('leads.status_new'),
            'contacted' => __('leads.status_contacted'),
            'qualified' => __('leads.status_qualified'),
            'proposal' => __('leads.status_proposal'),
            'negotiation' => __('leads.status_negotiation'),
            'won' => __('leads.status_won'),
            'lost' => __('leads.status_lost'),
            'converted' => __('leads.status_converted'),
        ];
    }

    public static function priorities(): array
    {
        return [
            'low' => __('leads.priority_low'),
            'normal' => __('leads.priority_normal'),
            'high' => __('leads.priority_high'),
            'urgent' => __('leads.priority_urgent'),
        ];
    }

    public static function leadSources(): array
    {
        return [
            'website' => __('leads.website'),
            'facebook' => 'Facebook',
            'instagram' => 'Instagram',
            'whatsapp' => 'WhatsApp',
            'referral' => __('leads.referral'),
            'walk_in' => __('leads.walk_in'),
            'other' => __('leads.other'),
        ];
    }

    public static function leadIntents(): array
    {
        return [
            'buy' => __('leads.buy'),
            'sell' => __('leads.sell'),
            'both' => __('leads.both'),
        ];
    }

    public static function interestTargetTypes(): array
    {
        return [
            'general' => __('leads.general'),
            'development' => __('leads.development'),
            'development_unit' => __('leads.development_unit'),
            'listing' => __('leads.listing'),
        ];
    }

    public static function caseFileTypes(): array
    {
        return [
            'lead' => __('case-files.lead_type'),
            'buyer' => __('case-files.buyer'),
            'seller' => __('case-files.seller'),
            'listing' => __('case-files.listing_file'),
        ];
    }

    public static function caseFileStatuses(): array
    {
        return [
            'open' => __('case-files.open'),
            'in_review' => __('case-files.in_review'),
            'approved' => __('case-files.approved'),
            'closed' => __('case-files.closed'),
            'cancelled' => __('case-files.cancelled'),
        ];
    }

    public static function documentStatuses(): array
    {
        return [
            'pending' => __('case-file-documents.pending'),
            'requested' => __('case-file-documents.requested'),
            'uploaded' => __('case-file-documents.uploaded'),
            'in_review' => __('case-file-documents.in_review'),
            'approved' => __('case-file-documents.approved'),
            'rejected' => __('case-file-documents.rejected'),
        ];
    }

    public static function documentTypes(): array
    {
        return [
            'id' => __('case-file-documents.id'),
            'proof_of_address' => __('case-file-documents.proof_of_address'),
            'tax_document' => __('case-file-documents.tax_document'),
            'property_deed' => __('case-file-documents.property_deed'),
            'bank_statement' => __('case-file-documents.bank_statement'),
            'other' => __('case-file-documents.other'),
        ];
    }

    public static function unitStatuses(): array
    {
        return [
            'available' => __('development-units.available'),
            'reserved' => __('development-units.reserved'),
            'sold' => __('development-units.sold'),
            'blocked' => __('development-units.blocked'),
            'inactive' => __('development-units.inactive'),
        ];
    }
}
