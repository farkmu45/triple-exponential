<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

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
            $formattedProduction['weight'] = $production->weight;
            $formattedProduction['date'] = Carbon::parse($production->date)->format('Y-m-d');
            array_push($data, $formattedProduction);
        }

        $response = Http::post('https://farkmu45-triple-expo.hf.space/forecast?length=20', $data);
        Prediction::insert(json_decode($response->body(), true));
    }

    protected static function booted(): void
    {
        static::saved(
            fn () => static::forecast()
        );

        static::deleted(
            fn () => static::forecast()
        );
    }
}
