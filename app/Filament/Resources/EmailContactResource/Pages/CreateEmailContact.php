<?php

namespace App\Filament\Resources\EmailContactResource\Pages;

use App\Filament\Resources\EmailContactResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEmailContact extends CreateRecord
{
    protected static string $resource = EmailContactResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    // protected function getCreatedNotificationTitle(): ?string
    // {
    //     return 'Created successfully';
    // }

    protected function mutateFormDataBeforeSave(array $data): array
{
    $fields = $data['fields'] ?? [];
    unset($data['fields']);

    $fieldData = [];
    foreach ($fields as $fieldId => $value) {
        $fieldData[$fieldId] = ['value' => $value];
    }

    $this->record->fields()->sync($fieldData);

    return $data;
}

protected function mutateFormDataBeforeCreate(array $data): array
{
    $fields = $data['fields'] ?? [];
    unset($data['fields']);

    $contact = EmailContact::create($data);

    $fieldData = [];
    foreach ($fields as $fieldId => $value) {
        $fieldData[$fieldId] = ['value' => $value];
    }

    $contact->fields()->sync($fieldData);

    return $data;
}

protected function afterSave(): void
{
    $this->record->load('fields');
}

protected function afterCreate(): void
{
    $this->record->load('fields');
}
}
