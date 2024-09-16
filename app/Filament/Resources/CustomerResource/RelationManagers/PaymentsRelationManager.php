<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use App\Models\Payment;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Forms\Components\DatePicker::make('payment_date')
                    ->required(),
                Forms\Components\TextInput::make('invoice_number')
                    ->required(),
                Forms\Components\Select::make('payment_method')
                    ->options([
                        'card' => 'Card',
                        'transfer' => 'Transfer',
                    ])
                    ->required(),
                Select::make('payment_status')
                    ->options([
                        'paid' => 'Paid',
                        'unpaid' => 'Unpaid',
                        'pending' => 'Pending',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('payment_description')
                    ->required(),
                Select::make('payment_currency')
                    ->options([
                        'TRY' => 'TRY',
                        'GBP' => 'GBP',
                        'USD' => 'USD',
                        'EUR' => 'EUR',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('payment_exchange_rate')
                    ->numeric()
                    ->step(0.00000001)
                ->required(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('payments relation')
            ->columns([
                Tables\Columns\TextColumn::make('amount')
                ->money(fn (Payment $record): string => $record->payment_currency)
                ->sortable(),
                Tables\Columns\TextColumn::make('payment_date')->date(),
                Tables\Columns\TextColumn::make('invoice_number'),
                Tables\Columns\TextColumn::make('payment_method'),
                Tables\Columns\TextColumn::make('payment_status'),
                Tables\Columns\TextColumn::make('payment_currency'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
