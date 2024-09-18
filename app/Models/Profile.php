<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'first_name', 'last_name', 'phone', 'email', 'credits', 'credits_purchased_date', 'valid_date',
    ];

    public function user()
    {
        return $this->belongTo(User::class);
    }
}
