<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class WorkCenter extends Page
{
    protected string $view = 'filament.pages.work-center';

    protected static ?int $navigationSort = 1;
    public static function getNavigationGroup(): string
    {
        return __('navigation.work');
    }

    public static function getNavigationLabel(): string
    {
        return __('dashboard.work_center');
    }

    public function getTitle(): string
    {
        return __('dashboard.work_center');
    }
}
