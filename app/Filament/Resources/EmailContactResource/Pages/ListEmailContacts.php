<?php

namespace App\Filament\Resources\EmailContactResource\Pages;

use App\Filament\Resources\EmailContactResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmailContacts extends ListRecords
{
    protected static string $resource = EmailContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('importSubscribers')
    ->label('Add Subscriber')
    ->icon('heroicon-o-plus')
    ->url(fn (): string => EmailContactResource::getUrl('import'))
        ];
    }

    // protected function getActions(): array
    // {
    //     return [
    //         Actions\Action::make('importSubscribers')
    //             ->label('Add Subscriber')
    //             ->icon('heroicon-o-plus')
    //             ->action(fn () => redirect()->route('filament.resources.email-contacts.import'))
    //     ];
    // }
}
