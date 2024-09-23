<?php

namespace App\Filament\Resources\EmailListResource\Pages;

use App\Filament\Resources\EmailListResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\EmailContact;

class CreateEmailList extends CreateRecord
{
    protected static string $resource = EmailListResource::class;

    protected function afterCreate(): void
    {
        $emailList = $this->record;
        $emailList->updateContacts();
    }

    public function refreshContactsList(): void
    {
        $selectedSegments = $this->data['segments'] ?? [];
        $emailContacts = EmailContact::whereHas('segments', function ($query) use ($selectedSegments) {
            $query->whereIn('segments.id', $selectedSegments);
        })->distinct()->get();

        // Form verilerini güncelle
        $this->data['contacts'] = $emailContacts->pluck('id')->toArray();

        // Form alanlarını yeniden oluştur
        $this->form->fill($this->data);
    }
}
