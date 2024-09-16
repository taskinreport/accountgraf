<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Invoice;
use App\Models\Payment;
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
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Ysfkaya\FilamentPhoneInput\Tables\PhoneColumn;
use Ysfkaya\FilamentPhoneInput\Infolists\PhoneEntry;
use Ysfkaya\FilamentPhoneInput\PhoneInputNumberType;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            TextInput::make('company_name')
                ->required()
                ->maxLength(255),
            TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            PhoneInput::make('phone')
                ->required()
                ->defaultCountry('TR')
                ->focusNumberFormat(PhoneInputNumberType::E164)
                ->countryStatePath('phone_country'),
            TextInput::make('account_name')
                ->required()
                ->maxLength(255),
            // Forms\Components\DatePicker::make('account_start_date')
            //     ->required(),
            Forms\Components\DatePicker::make('notification_date')
                ->required(),
            Forms\Components\DatePicker::make('account_start_date')
                ->required()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state) {
                        $date = \Carbon\Carbon::parse($state);
                        $set('account_start_month', $date->month);
                        $set('account_start_year', $date->year);
                    }
                }),
            Forms\Components\Hidden::make('account_start_month'),
            Forms\Components\Hidden::make('account_start_year'),



            Select::make('status')
                ->options([
                    'active' => 'Active',
                    'renewal' => 'Renewal',
                    'archived' => 'Archived',
                ])
                ->required(),
            Select::make('product_id')
                ->relationship('product', 'name')
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company_name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone'),
                TextColumn::make('account_name'),
                TextColumn::make('account_start_date')->date(),
                TextColumn::make('account_start_month')->label('Ay'),
                TextColumn::make('account_start_year')->label('Yıl'),
                TextColumn::make('notification_date')->date(),
                TextColumn::make('status'),
                TextColumn::make('product.name')->label('Product'),
            ])
            ->filters([
                // Filtreleme işlemleri
                Tables\Filters\SelectFilter::make('account_start_month')
                ->options([
                    1 => 'Ocak', 2 => 'Şubat', 3 => 'Mart', 4 => 'Nisan',
                    5 => 'Mayıs', 6 => 'Haziran', 7 => 'Temmuz', 8 => 'Ağustos',
                    9 => 'Eylül', 10 => 'Ekim', 11 => 'Kasım', 12 => 'Aralık'
                ])
                ->attribute('account_start_month'),
            Tables\Filters\SelectFilter::make('account_start_year')
                ->options(function () {
                    $currentYear = date('Y');
                    $years = range($currentYear - 7, $currentYear);
                    return array_combine($years, $years);
                })
                ->attribute('account_start_year'),
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
            RelationManagers\InvoicesRelationManager::class,
            RelationManagers\PaymentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
}
