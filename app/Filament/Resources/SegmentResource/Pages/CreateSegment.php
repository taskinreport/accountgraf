<?php

namespace App\Filament\Resources\SegmentResource\Pages;

use App\Filament\Resources\SegmentResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\EmailContact;

class CreateSegment extends CreateRecord
{
    protected static string $resource = SegmentResource::class;

    protected function afterCreate(): void
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
        \Log::info('Segment conditions: ' . json_encode($segment->conditions));
        \Log::info('Matching contacts SQL: ' . $contacts->toSql());
        \Log::info('Matching contacts bindings: ' . json_encode($contacts->getBindings()));
        \Log::info('Matching contacts count: ' . $matchingContacts->count());

        $segment->emailContacts()->sync($matchingContacts->pluck('id'));
    }
private function getOperatorCondition($operator)
{
    switch ($operator) {
        case 'equals':
            return '=';
        case 'not_equals':
            return '!=';
        case 'contains':
            return 'like';
        case 'not_contains':
            return 'not like';
        default:
            return '=';
    }
}
}
