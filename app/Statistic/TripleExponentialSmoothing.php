<?php

namespace App\Statistic;

use Illuminate\Support\Carbon;

final class TripleExponentialSmoothing
{
    private array $data;
    private float $alpha;
    private float $beta;
    private float $gamma;
    private int $seasonalPeriod;

    private $level;
    private $trend;
    private $seasonal;

    public function __construct(array $data,  float $alpha, float $beta, float $gamma, int $seasonalPeriod)
    {
        $this->data = $data;
        $this->alpha = $alpha;
        $this->beta = $beta;
        $this->gamma = $gamma;
        $this->seasonalPeriod = $seasonalPeriod;
    }

    private function initialize()
    {
        $weights = array_column($this->data, 'weight');

        $this->level = $weights[0];
        $this->trend = $weights[1] - $weights[0];

        $seasonalSum = 0;

        for ($i = 0; $i < $this->seasonalPeriod; $i++) {
            $seasonalSum += $weights[$i];
        }
        $this->seasonal = array_fill(0, $this->seasonalPeriod, $seasonalSum / $this->seasonalPeriod);
    }

    private function forecast($index, $seasonalIndex)
    {
        return ($this->level + $this->trend * $index) * $this->seasonal[$seasonalIndex % $this->seasonalPeriod];
    }

    public function calculateForecast($periodsToForecast)
    {
        $this->initialize();

        $weights = array_column($this->data, 'weight');
        $n = count($weights);
        $forecastedData = array();

        $nReached = false;
        $dayGap = 0;

        for ($i = 0; $i < $n + $periodsToForecast; $i++) {
            $forecast = $this->forecast($i, $i % $this->seasonalPeriod);

            if ($i < $n) {
                $weight = $weights[$i];

                $prevLevel = $this->level;
                $this->level = $this->alpha * ($weight / $this->seasonal[$i % $this->seasonalPeriod]) + (1 - $this->alpha) * ($this->level + $this->trend);
                $this->trend = $this->beta * ($this->level - $prevLevel) + (1 - $this->beta) * $this->trend;
                $this->seasonal[$i % $this->seasonalPeriod] = $this->gamma * ($weight / ($this->level + $this->trend)) + (1 - $this->gamma) * $this->seasonal[$i % $this->seasonalPeriod];
            }

            if (!$this->data[$i]['date']) {
                $nReached = true;
                $dayGap++;
            }

            $forecastedData[] = [
                'date' =>  $nReached ? Carbon::parse(end($this->data)['date'])->addDays($dayGap)->format('Y-m-d') : $this->data[$i]['date'], // Wrap around for forecasting
                'forecasted_weight' => $forecast,
            ];
        }

        return $forecastedData;
    }
}
