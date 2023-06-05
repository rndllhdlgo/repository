<?php

namespace App\Models;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLogs extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'user_logs';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}