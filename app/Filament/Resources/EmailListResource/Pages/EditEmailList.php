<?php

namespace App\Filament\Resources\EmailListResource\Pages;

use App\Filament\Resources\EmailListResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmailList extends EditRecord
{
    protected static string $resource = EmailListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        $this->refreshContactsList();
    }

    public function refreshContactsList(): void
    {
        $this->record->load('segments');
        $selectedSegments = $this->data['segments'] ?? [];
        $this->record->updateContacts($selectedSegments);
        $this->refreshFormData(['segments']);
        $this->fillForm();
    }
}
