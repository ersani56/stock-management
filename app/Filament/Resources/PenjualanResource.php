<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Barang;
use Filament\Forms\Form;
use App\Models\Penjualan;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PenjualanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PenjualanResource\RelationManagers;

class PenjualanResource extends Resource
{
    protected static ?string $model = Penjualan::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Scan QR Code')
                ->schema([
                    Forms\Components\ViewField::make('qr_scanner')
                        ->view('filament.forms.components.qr-scanner')
                        ->live(),
                ]),

                Forms\Components\Select::make('barang_id')
                    ->label('Barang')
                    ->options(Barang::all()->pluck('nama_barang', 'id'))
                    ->searchable()
                    ->required()
                    ->reactive(),

                Forms\Components\TextInput::make('jumlah')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->minValue(1),

                Forms\Components\TextInput::make('total_harga')
                    ->readOnly()
                    ->numeric()
                    ->prefix('Rp')
                    ->dehydrated()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $barang = Barang::find($get('barang_id'));
                        if ($barang) {
                            $set('total_harga', $barang->harga_jual * $get('jumlah'));
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('barang.nama_barang')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('jumlah')
                ->numeric()
                ->sortable(),
            Tables\Columns\TextColumn::make('total_harga')
                ->money('IDR')
                ->sortable(),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            Tables\Columns\TextColumn::make('updated_at')
                ->dateTime()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenjualans::route('/'),
            'create' => Pages\CreatePenjualan::route('/create'),
            'edit' => Pages\EditPenjualan::route('/{record}/edit'),
        ];
    }
}
