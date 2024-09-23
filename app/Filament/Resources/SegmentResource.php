<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SegmentResource\Pages;
use App\Filament\Resources\SegmentResource\RelationManagers;
use App\Models\Segment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;

class SegmentResource extends Resource
{
    protected static ?string $model = Segment::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Segments';
    protected static ?string $navigationGroup = 'Email Management';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\Repeater::make('conditions')
                ->schema([
                    Forms\Components\Select::make('field')
                        ->options(function () {
                            return array_merge(
                                ['name' => 'Name', 'email' => 'Email'],
                                static::getCustomFields()
                            );
                        })
                        ->required(),
                    Forms\Components\Select::make('operator')
                        ->options([
                            'equals' => 'Equals',
                            'not_equals' => 'Does not equal',
                            'contains' => 'Contains',
                            'not_contains' => 'Does not contain',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('value')
                        ->required(),
                ])
                ->columns(3),
        ]);
}

public static function infolist(Infolist $infolist): Infolist
{
    return $infolist
        ->schema([
            TextEntry::make('name'),
            TextEntry::make('emailContacts.email')
                ->label('Segmented Emails')
                ->listWithLineBreaks()
                ->limitList(50)
                ->expandableLimitedList(),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('email_contacts_count')
                ->label('Contacts Count')
                ->getStateUsing(function (Segment $record): int {
                    return $record->emailContacts()->count();
                })
                ->sortable(),
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
            RelationManagers\EmailContactsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSegments::route('/'),
            'create' => Pages\CreateSegment::route('/create'),
            'edit' => Pages\EditSegment::route('/{record}/edit'),
        ];
    }

    protected static function getCustomFields()
{
    return \App\Models\Field::pluck('name', 'name')->toArray();
}

public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withCount('emailContacts');
    }
}
