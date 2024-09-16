<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use Filament\Resources\Pages\Page;
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

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Select::make('customer_id')
                ->relationship('customer', 'company_name')
                ->required(),
            TextInput::make('total_amount')
                ->numeric()
                ->required()
                ->afterStateUpdated(function ($state, $record) {
                    if ($record) {
                        $record->total_amount = $state;
                        $record->save();
                    }
                }),
            Forms\Components\DatePicker::make('invoice_date')
                ->required(),
            TextInput::make('invoice_number')
                ->required()
                ->unique(ignoreRecord: true),
            Select::make('invoice_currency')
                ->options([
                    'TRY' => 'TRY',
                    'GBP' => 'GBP',
                    'USD' => 'USD',
                    'EUR' => 'EUR',
                ])
                ->required(),
            TextInput::make('invoice_exchange_rate')
                ->numeric()
                ->step(0.0001)
                ->required(),
            TextInput::make('invoice_description')
                ->required(),
            Select::make('invoice_status')
                ->options([
                    'pending' => 'Pending',
                    'unpaid' => 'Unpaid',
                    'paid' => 'Paid',
                ])
                ->required(),
            Forms\Components\DatePicker::make('invoice_due_date')
                ->required(),
            Forms\Components\DatePicker::make('invoice_payment_date')
                ->nullable(),

            // TextInput::make('invoice_payment_status')
            //     ->required(),
            // TextInput::make('invoice_payment_method')
            //     ->required(),
            // TextInput::make('invoice_payment_currency')
            //     ->required(),
            // TextInput::make('invoice_payment_exchange_rate')
            //     ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('customer.company_name')->searchable(),
            TextColumn::make('total_amount')
            ->money(fn (Invoice $record): string => $record->invoice_currency)
            ->sortable(),
            TextColumn::make('invoice_date')->date(),
            TextColumn::make('invoice_number')->searchable(),
            TextColumn::make('invoice_currency'),
            TextColumn::make('invoice_exchange_rate'),
            TextColumn::make('invoice_description'),
            TextColumn::make('invoice_status'),
            TextColumn::make('invoice_due_date')->date(),
            TextColumn::make('invoice_payment_status'),
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }

    public static function afterSave(Page $page): void
{
    $invoice = $page->getRecord();
    $payment = Payment::where('invoice_number', $invoice->invoice_number)->first();
    if ($payment) {
        $payment->amount = $invoice->total_amount;
        $payment->save();
    }
}
}
