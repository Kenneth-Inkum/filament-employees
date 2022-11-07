<?php

namespace App\Filament\Resources\EmployeeResource\Widgets;

use App\Models\Country;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class EmployeeStatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $us = Country::where('country_code','US')->withCount('employees')->first();
        $uk = Country::where('country_code','UK')->withCount('employees')->first();
        $gh = Country::where('country_code','GH')->withCount('employees')->first();



        return [
            Card::make('All Employees', Employee::all()->count())
                ->descriptionIcon('heroicon-s-trending-up')
                ->description('5% increase')
                ->color('success'),

            Card::make($gh->name. ' Employees', $gh->employees_count)
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('success'),

                // ->description('7% increase')
                // ->descriptionIcon('heroicon-s-trending-down')
                // ->extraAttributes([
                //     'class' => 'cursor-pointer',
                //     'wire:click' => '$emitUp("setStatusFilter", "processed")'])
                // ->color('danger'),

            Card::make($us->name. ' Employees', $us->employees_count)
                // ->description('32k increase')
                // ->descriptionIcon('heroicon-s-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->color('danger'),
        ];
    }
}
