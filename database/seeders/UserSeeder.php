<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminUsers = [

            [
                'id'=>'1',
                'name'=>'Developer',
                'email'=>'dev@lms.com',
                'phone'=>'01710000000',
                'user_role'=>User::DEVELOPER,
                'status'=>User::APPROVED,
                'password'=>bcrypt('12345678'),
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],
            [
                'id'=>'2',
                'name'=>'Super Admin',
                'email'=>'superadmin@lms.com',
                'phone'=>'01720000000',
                'user_role'=>User::SUPERADMIN,
                'status'=>User::APPROVED,
                'password'=>bcrypt('12345678'),
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],
            [
                'id'=>'3',
                'name'=>'Admin',
                'email'=>'admin@lms.com',
                'phone'=>'01730000000',
                'user_role'=>User::ADMIN,
                'status'=>User::APPROVED,
                'password'=>bcrypt('12345678'),
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],

            [
                'id'=>'4',
                'name'=>'Librarian',
                'email'=>'librarian@lms.com',
                'phone'=>'01740000000',
                'user_role'=>User::LIBRARIAN,
                'status'=>User::APPROVED,
                'password'=>bcrypt('12345678'),
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ],

            [
                'id'=>'5',
                'name'=>'General User ',
                'email'=>'user@lms.com',
                'phone'=>'01750000000',
                'user_role'=>User::GENERALUSER,
                'status'=>User::APPROVED,
                'password'=>bcrypt('12345678'),
                'created_at'=>Date('Y-m-d h:i:s'),
                'updated_at'=>Date('Y-m-d h:i:s')
            ]

        ];

        $adminUser=\App\Models\User::first();

        if (empty($adminUser)){
            \App\Models\User::insert($adminUsers);
        }
    }
}
