<?php

namespace App\Filament\Widgets;

use App\Models\Prediction;
use App\Models\Production;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Get;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class PredictionChart extends ApexChartWidget
{
    protected static string $chartId = 'predictionChart';
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Grafik Hasil Produksi';

    protected function getFormSchema(): array
    {

        $dateEnd = Carbon::parse(Prediction::orderBy('date', 'desc')->first()['date']);
        $dateStart = Carbon::parse($dateEnd)->subDays(14);

        return [
            DatePicker::make('date_start')
                ->label('Tanggal Awal')
                ->native(false)
                ->maxDate(fn (Get $get) => $get('date_end'))
                ->default($dateStart)
                ->afterStateUpdated(function () {
                    $this->updateOptions();
                })
                ->reactive(),
            DatePicker::make('date_end')
                ->label('Tanggal Akhir')
                ->minDate(fn (Get $get) => $get('date_start'))
                ->native(false)
                ->reactive()
                ->afterStateUpdated(function () {
                    $this->updateOptions();
                })
                ->default($dateEnd)

        ];
    }

    protected function getOptions(): array
    {
        $dateStart = $this->filterFormData['date_start'];
        $dateEnd = $this->filterFormData['date_end'];
        $productions = Production::whereBetween('date', [$dateStart, $dateEnd])->get();
        $forecastedProductions = Prediction::whereBetween('date', [$dateStart, $dateEnd])->get();

        return [
            'chart' => [
                'type' => 'line',
                'height' => 400,
            ],
            'series' => [
                [
                    'name' => 'Total Produksi',
                    'data' => $productions->map(fn ($value) => $value->weight),
                ],

                [
                    'name' => 'Prediksi Hasil Produksi',
                    'data' => $forecastedProductions->map(fn ($value) => $value->weight),
                ],
            ],
            'xaxis' => [
                'categories' => $forecastedProductions->map(fn ($value) => Carbon::parse($value->date)->format('m/d')),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b', '#f70e0b'],
            // 'stroke' => [
            //     'curve' => 'smooth',
            // ],
        ];
    }
}
