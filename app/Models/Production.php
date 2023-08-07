<?php

namespace App\Models;

use App\Statistic\TripleExponentialSmoothing;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Production extends Model
{
    use HasFactory;

    protected $guarded = [''];
    public $timestamps = false;
    protected $primaryKey = 'date';

    protected $casts = [
        'date' => 'date',
    ];

    private static function forecast()
    {
        Prediction::query()->delete();

        $productions = Production::orderBy('date', 'ASC')->get(['date', 'weight']);
        $data = [];
        foreach ($productions as $production) {
            $formattedProduction = null;
            $formattedProduction['weight'] = $production->weight + 100;
            $formattedProduction['date'] = Carbon::parse($production->date)->format('Y-m-d');
            array_push($data, $formattedProduction);
        }

        // $model = new TripleExponentialSmoothing(
        //     data: $data,
        //     alpha: 0.2,
        //     beta: 0.3,
        //     gamma: 0.5,
        //     seasonalPeriod: 24
        // );

        Prediction::insert($data);
    }


    protected static function booted(): void
    {
        static::saved(function (Production $production) {
            static::forecast();
        });

        static::deleted(function (Production $production) {
            static::forecast();
        });
    }
}
