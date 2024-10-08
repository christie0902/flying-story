<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PaymentInfo extends Model
{
    use HasFactory;
    protected $table = 'payment_info';

    protected $fillable = [
        'type',
        'amount_of_credits',
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

    public function getFormattedPriceAttribute()
    {
        return intval($this->price) == $this->price
            ? number_format($this->price, 0) . ' CZK'
            : number_format($this->price, 2) . ' CZK';
    }
}
