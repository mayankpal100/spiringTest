<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    /** @use HasFactory<\Database\Factories\WinnerFactory> */
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = ['user_id', 'points', 'declared_at'];
}
