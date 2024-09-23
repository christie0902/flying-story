<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'description',
        'schedule',
        'price',
        'duration',
        'capacity',
        'registered_students',
        'status',
        'recurrence_id',
        'level',
    ];

    /**
     * Get the recurrence associated with the class.
     */
    public function recurrence(): BelongsTo
    {
        return $this->belongsTo(Recurrence::class);
    }

    /**
     * Get the class occurrences.
     */
    public function occurrences(): HasMany
    {
        return $this->hasMany(LessonOccurrence::class);
    }

    /**
     * Get the registrations for the class.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(LessonRegistration::class);
    }

    //Accessors
    public function getFormattedPriceAttribute()
    {
        return intval($this->price) == $this->price
            ? number_format($this->price, 0) . ' CZK'
            : number_format($this->price, 2) . ' CZK';
    }

    public function getFormattedDurationAttribute()
    {
        $hours = intdiv($this->duration, 60);
        $minutes = $this->duration % 60;

        $formatted = '';

        if ($hours > 0) {
            $formatted .= $hours . 'h';
        }

        if ($minutes > 0) {
            $formatted .= $minutes . 'm';
        }

        return $formatted;
    }
}
