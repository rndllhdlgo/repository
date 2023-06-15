<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingStatement extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'billing_statements';
}
