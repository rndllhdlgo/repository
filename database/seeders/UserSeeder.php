<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Role;

class UserSeeder extends Seeder
{
    public function run()
    {
        Role::create(['name' => 'ADMIN','guard_name' => 'web']);
        Role::create(['name' => 'ENCODER','guard_name' => 'web']);
        Role::create(['name' => 'VIEWER','guard_name' => 'web']);
        Role::create(['name' => 'BOSS','guard_name' => 'web']);

        $user = User::create([
            'name' => 'JEROME LOPEZ',
            'department' => 'ADMIN',
            'userlevel' => '1',
            'email' => 'emorej046@gmail.com',
            'password' => Hash::make('asdasdasd'),
        ]);

        $user->assignRole('1');

        $user = User::create([
            'name' => 'RENDELL HIDALGO',
            'department' => 'ADMIN',
            'userlevel' => '1',
            'email' => 'rendellhidalgo11@gmail.com',
            'password' => Hash::make('asdasdasd'),
        ]);
        $user->assignRole('1');

        $user = User::create([
                'name' => 'LANCE NACABUAN',
                'department' => 'ADMIN',
                'userlevel' => '1',
                'email' => 'c4lance@yahoo.com',
                'password' => Hash::make('asdasdasd'),
        ]);
        $user->assignRole('1');

    }
}
