<?php

namespace App\Filament\Resources;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use App\Filament\Resources\EmailListResource\RelationManagers\ContactsRelationManager;

use App\Filament\Resources\EmailListResource\Pages;
use App\Filament\Resources\EmailListResource\RelationManagers;
use App\Models\EmailList;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EmailListResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Components\Select;
use App\Models\EmailContact;
use Filament\Forms\Components\Placeholder;

class EmailListResource extends Resource
{
    protected static ?string $model = EmailList::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Lists';
    protected static ?string $navigationGroup = 'Email Management';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),


                Select::make('segments')
                    ->multiple()
                    ->relationship('segments', 'name')
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function ($state, $set) {
                        $set('contacts', EmailContact::whereHas('segments', function ($query) use ($state) {
                            $query->whereIn('segments.id', $state);
                        })->distinct()->pluck('id')->toArray());
                    }),

            Forms\Components\Actions::make([
                Action::make('refresh')
                    ->label('Refresh Contacts')
                    ->action(function ($livewire) {
                        $livewire->refreshContactsList();
                    })
                    ->color('primary')
                    ->icon('heroicon-o-arrow-path')
                    ->button(),

            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TextColumn::make('contacts_count')->counts('contacts'),
            Tables\Columns\TextColumn::make('segments.name')
                ->badge()
                ->separator(', ')
                ->label('Segments'),
        ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                ->after(function ($record) {
                    $record->updateContacts();
                }),
                Tables\Actions\DeleteAction::make(),
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
            RelationManagers\ContactsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailLists::route('/'),
            'create' => Pages\CreateEmailList::route('/create'),
            'edit' => Pages\EditEmailList::route('/{record}/edit'),
        ];
    }
}
