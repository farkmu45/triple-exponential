<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductionResource\Pages;
use App\Models\Production;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

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
                        ->formatStateUsing(fn (string $state): string => $state.' kg'),
                ])->from('md'),
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
