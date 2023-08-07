<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PredictionResource\Pages;
use App\Filament\Resources\PredictionResource\RelationManagers;
use App\Models\Prediction;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PredictionResource extends Resource
{
    protected static ?string $model = Prediction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'data prediksi';

    protected static ?string $pluralModelLabel = 'data prediksi';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    TextColumn::make('date')
                        ->label('Tanggal')
                        ->sortable()
                        ->date('Y-m-d'),
                    TextColumn::make('weight')
                        ->label('Berat')
                        ->sortable()
                        ->formatStateUsing(fn (string $state): string =>  $state . ' kg')
                ])->from('md')
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Dari')
                            ->minDate(Carbon::parse(Prediction::orderBy('date', 'ASC')->first()['date']))
                            ->maxDate(fn (Get $get) => $get('created_until'))
                            ->native(false)
                            ->displayFormat('Y-m-d')
                            ->reactive(),
                        DatePicker::make('created_until')
                            ->label('Sampai')
                            ->displayFormat('Y-m-d')
                            ->minDate(fn (Get $get) => $get('created_from'))
                            ->reactive()
                            ->maxDate(Carbon::parse(Prediction::orderBy('date', 'DESC')->first()['date']))
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date', '<=', $date),
                            );
                    })
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManagePredictions::route('/'),
        ];
    }
}
