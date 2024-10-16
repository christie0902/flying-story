<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'bg_color', 'img_url'];

    /**
     * Relationship with lessons.
     */
    public function lessons()
    {
        return $this->hasMany(Lesson::class);
    }
}
