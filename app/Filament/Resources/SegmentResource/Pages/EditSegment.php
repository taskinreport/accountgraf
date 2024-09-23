<?php

namespace App\Filament\Resources\SegmentResource\Pages;

use App\Filament\Resources\SegmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Models\EmailContact;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;

class EditSegment extends EditRecord
{
    protected static string $resource = SegmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
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

    protected function afterSave(): void
{
    $segment = $this->record;
    $contacts = EmailContact::query();

    foreach ($segment->conditions as $condition) {
        $contacts->where(function ($query) use ($condition) {
            $field = $condition['field'];
            $operator = $condition['operator'];
            $value = $condition['value'];

            if (in_array($field, ['name', 'email'])) {
                $query->where($field, $this->getOperatorCondition($operator), $operator === 'contains' || $operator === 'not_contains' ? "%{$value}%" : $value);
            } else {
                $query->whereHas('fields', function ($q) use ($field, $operator, $value) {
                    $q->where('fields.name', $field)
                      ->where('email_contact_field.value', $this->getOperatorCondition($operator), $operator === 'contains' || $operator === 'not_contains' ? "%{$value}%" : $value);
                });
            }
        });
    }

    $matchingContacts = $contacts->get();

    // Segment'in mevcut email contactlarını temizle ve yeni eşleşenleri ekle
    $segment->emailContacts()->sync($matchingContacts->pluck('id'));

    // Filament'in kayıt görünümünü yenile
    $this->fillForm();
}


    private function getOperatorCondition($operator)
    {
        switch ($operator) {
            case 'equals':
                return '=';
            case 'not_equals':
                return '!=';
            case 'contains':
            case 'not_contains':
                return 'like';
            default:
                return '=';
        }
    }
}
