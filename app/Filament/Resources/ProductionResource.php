<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PredictionResource\Pages\ManageProductions;
use App\Filament\Resources\ProductionResource\Pages;
use App\Models\Production;
use App\Models\Sale;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductionResource extends Resource
{
    protected static ?string $model = Production::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $modelLabel = 'data produksi';

    protected static ?string $pluralModelLabel = 'data produksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('date')
                    ->label('Tanggal')
                    ->unique(ignoreRecord: true)
                    ->native(false)
                    ->disabledOn('edit')
                    ->displayFormat('Y-m-d')
                    ->required(),
                TextInput::make('weight')
                    ->label('Bobot')
                    ->numeric()
                    ->suffix('kg')
                    ->gte(0.1, true)
                    ->required(),
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
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageProductions::route('/'),
        ];
    }
}
