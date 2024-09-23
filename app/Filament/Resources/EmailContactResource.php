<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmailContactResource\Pages;
use App\Filament\Resources\EmailContactResource\RelationManagers;
use App\Models\EmailContact;
use App\Models\Field;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EmailContactResource;
use Filament\Resources\Pages\CreateRecord;

class EmailContactResource extends Resource
{
    protected static ?string $navigationLabel = 'Contacts';
    protected static ?string $model = EmailContact::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Email Management';

    public static function getSlug(): string
{
    return 'email-contacts';
}

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('name')
                ->maxLength(255),
            Forms\Components\Select::make('lists')
                ->multiple()
                ->relationship('lists', 'name')
                ->preload(),
            ...static::getFields()->map(function ($field) {
                $componentClass = match ($field->type) {
                    'text' => Forms\Components\TextInput::class,
                    'number' => Forms\Components\TextInput::class,
                    'date' => Forms\Components\DatePicker::class,
                    default => Forms\Components\TextInput::class,
                };
                return $componentClass::make("fields.{$field->id}")
                    ->label($field->name)
                    ->afterStateHydrated(function ($component, $state, $record) use ($field) {
                        if ($record && $record->fields) {
                            $value = $record->fields->where('id', $field->id)->first()?->pivot->value ?? null;
                            $component->state($value);
                        }
                    });
            })->filter()->toArray(),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('email')->searchable(),
            Tables\Columns\TextColumn::make('name')->searchable(),
            Tables\Columns\TagsColumn::make('lists.name'),
            ...static::getFields()->map(function ($field) {
                return Tables\Columns\TextColumn::make("fields.{$field->id}")
                    ->label($field->name)
                    ->getStateUsing(function ($record) use ($field) {
                        return $record->fields && $record->fields->isNotEmpty()
                            ? $record->fields->where('id', $field->id)->first()?->pivot->value
                            : null;
                    });
            }),
        ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailContacts::route('/'),
            'create' => Pages\CreateEmailContact::route('/create'),
            'edit' => Pages\EditEmailContact::route('/{record}/edit'),
            'import' => Pages\ImportEmailContacts::route('/import'),
        ];
    }

    protected static function getFields()
    {
        return cache()->remember('email_contact_fields', now()->addMinutes(30), function () {
            $fields = Field::all();
            return $fields->isNotEmpty() ? $fields : collect();
        });
    }

public static function getUrl(string $name = 'index', array $parameters = [], bool $isAbsolute = true, ?string $panel = null, ?\Illuminate\Database\Eloquent\Model $tenant = null): string
{
    $slug = static::getSlug();
    return route("filament.admin.resources.{$slug}.{$name}", $parameters, $isAbsolute);
}


}
