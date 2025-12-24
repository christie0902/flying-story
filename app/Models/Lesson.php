<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category_id',
        'description',
        'schedule',
        'payment_type',
        'duration',
        'capacity',
        'registered_students',
        'status',
        'recurrence_option',
        'recurrence_id',
        'level',
    ];

     // cast schedule to datetime
     protected $casts = [
        'schedule' => 'datetime',
    ];

    /**
     * Define the relationship with Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    /**
     * Get the registrations for the class.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(LessonRegistration::class);
    }

    public function paymentInfo(): BelongsTo
    {
        return $this->belongsTo(PaymentInfo::class, 'payment_type', 'type');
    }

    //Accessors
    // Format price
    public function getFormattedPriceAttribute()
    {
        return intval($this->price) == $this->price
            ? number_format($this->price, 0) . ' CZK'
            : number_format($this->price, 2) . ' CZK';
    }

    // Format duration
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

    //Format schedule
    public function getFormattedScheduleAttribute()
    {
        return Carbon::parse($this->schedule)->format('ga M j, Y');
    }

     // ISO 8601 string with timezone (for JS / FullCalendar)
     public function getScheduleIsoAttribute(): string
     {
         return $this->schedule
             ->copy()
             ->timezone(config('app.timezone'))
             ->toIso8601String();
     }

    //Student counts
    public function confirmedStudentsCount()
    {
        return $this->hasMany(LessonRegistration::class)
                    ->where('confirmation_status', 'Confirmed')
                    ->count();
    }

    public function pendingStudentsCount()
    {
        return $this->hasMany(LessonRegistration::class)
                    ->where('confirmation_status', 'Pending')
                    ->count();
    }

    public function totalRegisteredStudentsCount()
    {
        return $this->hasMany(LessonRegistration::class)->count();
    }
}
