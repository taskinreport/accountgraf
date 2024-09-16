<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Filament\Resources\PaymentResource\RelationManagers;
use App\Models\Payment;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('customer_id')
                ->relationship('customer', 'company_name')
                ->required(),
            Select::make('invoice_number')
                ->options(function (callable $get) {
                    $customerId = $get('customer_id');
                    if (!$customerId) return [];
                    return Invoice::where('customer_id', $customerId)
                        ->where('invoice_status', 'pending')
                        ->pluck('invoice_number', 'invoice_number')
                        ->toArray();
                })
                ->required()
                ->searchable()
                ->reactive(),
            TextInput::make('amount')
                ->numeric()
                ->required(),
            Forms\Components\DatePicker::make('payment_date')
                ->required(),
            TextInput::make('invoice_number')
                ->required(),
            Select::make('payment_method')
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
            TextInput::make('payment_description')
                ->required(),
            Select::make('payment_currency')
                ->options([
                    'TRY' => 'TRY',
                    'GBP' => 'GBP',
                    'EUR' => 'EUR',
                    'USD' => 'USD',
                ])
                ->required(),
            TextInput::make('payment_exchange_rate')
                ->numeric()
                ->step(0.0001)
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('customer.company_name')->searchable(),
            TextColumn::make('amount'),
            TextColumn::make('payment_date')->date(),
            TextColumn::make('invoice_number')->searchable(),
            TextColumn::make('payment_method'),
            TextColumn::make('payment_status'),
            TextColumn::make('payment_currency'),
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
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
