<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Segment extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'conditions'];

    protected $casts = [
        'conditions' => 'array',
    ];

    public function emailContacts()
{
    return $this->belongsToMany(EmailContact::class, 'email_contact_segment')->withTimestamps();
}
}
