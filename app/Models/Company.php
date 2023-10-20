<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'companies';

    public function users(){
        return $this->belongsToMany(User::class, 'company_has_permission', 'company_id', 'user_id');
    }
}