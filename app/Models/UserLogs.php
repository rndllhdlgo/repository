<?php

namespace App\Models;
use App\Models\User;
use App\Events\NewLogs;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserLogs extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'user_logs';

    public static function boot()
    {
        parent::boot();

        static::created(function ($userLog) {
            event(new NewLogs('addLogs', $userLog));
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}