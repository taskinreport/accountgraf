<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailList extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function contacts()
    {
        return $this->belongsToMany(EmailContact::class);
    }

    public function segments()
{
return $this->belongsToMany(Segment::class);
}

public function updateContacts($selectedSegmentIds = null)
{
    if ($selectedSegmentIds === null) {
        $selectedSegmentIds = $this->segments()->pluck('segments.id')->toArray();
    }

    $emailContacts = EmailContact::whereHas('segments', function ($query) use ($selectedSegmentIds) {
        $query->whereIn('segments.id', $selectedSegmentIds);
    })->distinct()->get();

    $this->contacts()->sync($emailContacts->pluck('id'));

    $this->load('contacts');
}
}

