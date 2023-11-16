<?php

namespace App\Filament\Widgets;

use App\Models\Production;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestProduction extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Hasil Produksi Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(Production::query()->orderBy('date', 'desc')->limit(7))
            ->columns([
                TextColumn::make('date')
                    ->date('Y-m-d')
                    ->label('Tanggal'),
                TextColumn::make('weight')
                    ->label('Berat')
                    ->formatStateUsing(fn (string $state): string => $state.' kg'),
            ])
            ->paginated(false);
    }
}
