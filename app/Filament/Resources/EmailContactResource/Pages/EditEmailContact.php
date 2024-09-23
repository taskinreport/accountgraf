<?php

namespace App\Filament\Resources\EmailContactResource\Pages;

use App\Filament\Resources\EmailContactResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailContact extends EditRecord
{
    protected static string $resource = EmailContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $fields = $data['fields'] ?? [];
        unset($data['fields']);

        $fieldData = [];
        foreach ($fields as $fieldId => $value) {
            if ($value !== null) {
                $fieldData[$fieldId] = ['value' => $value];
            }
        }

        $this->record->fields()->sync($fieldData);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->load('fields');
    }
}
