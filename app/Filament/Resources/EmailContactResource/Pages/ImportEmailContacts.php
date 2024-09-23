<?php

namespace App\Filament\Resources\EmailContactResource\Pages;

use App\Models\EmailList;
use App\Filament\Resources\EmailContactResource;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class ImportEmailContacts extends Page
{
    use WithFileUploads;


    protected static string $resource = EmailContactResource::class;

    protected static string $view = 'filament.resources.email-contact-resource.pages.import-email-contacts';

    public $csvFile;
    public $selectedList;

    public function mount()
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('csvFile')
                    ->label('CSV File')
                    ->acceptedFileTypes(['text/csv'])
                    ->required(),
                Select::make('selectedList')
                    ->label('Email List')
                    ->options(EmailList::pluck('name', 'id'))
                    ->required(),
            ]);
    }

    public function import()
    {
        $data = $this->form->getState();

        $csvFile = $data['csvFile'];
        $selectedListId = $data['selectedList'];

        if (!Storage::disk('public')->exists($csvFile)) {
            $this->addError('csvFile', 'CSV dosyası bulunamadı.');
            return;
        }

        $csvContent = Storage::disk('public')->get($csvFile);
        $csvData = array_map(function($row) {
            return str_getcsv($row, ';'); // Noktalı virgül ayracını kullan
        }, explode("\n", $csvContent));

        \Log::info('CSV Data:', ['data' => $csvData]); // CSV içeriğini logla

        $headers = array_shift($csvData); // Başlık satırını al
        $importedCount = 0; // Eklenen kişi sayısını takip et

        // Custom fields'ları veritabanından oku
        $fields = \App\Models\Field::all()->pluck('id', 'name')->toArray();
$fieldNamesToIds = array_change_key_case($fields, CASE_LOWER);
\Log::info('Mevcut fields:', ['fields' => $fieldNamesToIds]);

        foreach ($csvData as $row) {
            if (count($row) < 2 || empty($row[0]) || empty($row[1])) {
                \Log::warning('Geçersiz veya eksik veri içeren satır:', ['row' => $row]);
                continue;
            }

            $email = $row[0];
            $name = $row[1];


            \Log::info('İşlenen veri:', ['email' => $email, 'name' => $name, 'row' => $row]);

            $contact = \App\Models\EmailContact::updateOrCreate(
                ['email' => $email],
                ['name' => $name]
            );

            $customFieldData = [];
            for ($i = 2; $i < count($headers); $i++) {
                $fieldName = strtolower($headers[$i]);
                $fieldValue = $row[$i] ?? null;

                \Log::info('İşlenen alan:', ['name' => $fieldName, 'value' => $fieldValue]);

                if (isset($fieldNamesToIds[$fieldName])) {
                    $fieldId = $fieldNamesToIds[$fieldName];
                    $customFieldData[$fieldId] = ['value' => $fieldValue];
                    \Log::info('Alan eşleşti:', ['id' => $fieldId, 'name' => $fieldName, 'value' => $fieldValue]);
                } else {
                    \Log::warning('Alan bulunamadı:', ['name' => $fieldName]);
                }
            }

            \Log::info('Custom fields data before sync:', ['data' => $customFieldData]);

            // Custom fields'ları sync et
            if (!empty($customFieldData)) {
                $contact->fields()->syncWithoutDetaching($customFieldData);
                \Log::info('Custom fields synced for contact:', ['email' => $contact->email, 'fields' => $customFieldData]);
            }

            $contact->lists()->syncWithoutDetaching([$selectedListId]);

            $importedCount++;
        }

        \Log::info('Import tamamlandı', [
            'imported_count' => $importedCount,
            'total_rows' => count($csvData) - 1, // Başlık satırını çıkar
        ]);


        \Log::info('Custom fields data:', ['data' => $customFieldData]); // Eklenen log

        session()->flash('message', "{$importedCount} kişi başarıyla içe aktarıldı.");
        return redirect(EmailContactResource::getUrl('index'));
    }
}
