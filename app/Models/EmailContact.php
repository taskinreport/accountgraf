<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailContact extends Model
{
    use HasFactory;

    protected $fillable = ['email', 'name'];

    // public function emailLists()
    // {
    //     return $this->belongsToMany(EmailList::class);
    // }

    public function lists()
    {
        return $this->belongsToMany(EmailList::class, 'email_contact_email_list');
    }

    public function fields()
    {
        return $this->belongsToMany(Field::class, 'email_contact_field')->withPivot('value')->withTimestamps();
    }

public function segments()
{
    return $this->belongsToMany(Segment::class, 'email_contact_segment');
}
}
