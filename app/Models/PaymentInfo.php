<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'price',
        'bank_info',
        'payment_QR_url',
    ];

    /**
     * Get the transactions associated with the payment info.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
