<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lesson_id',
        'registration_date',
        'payment_status',
    ];

    /**
     * Get the user that owns the registration.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the class that the registration is for.
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
