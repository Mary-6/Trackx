<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'tracking_number', 'subject', 'message', 'is_resolved',
    ];

    protected function casts(): array
    {
        return ['is_resolved' => 'boolean'];
    }
}
