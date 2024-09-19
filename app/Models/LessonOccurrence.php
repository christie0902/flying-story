<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Lesson;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonOccurrence extends Model
{
    use HasFactory;

    protected $fillable = [
        'lesson_id',
        'scheduled_at',
    ];

   
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
