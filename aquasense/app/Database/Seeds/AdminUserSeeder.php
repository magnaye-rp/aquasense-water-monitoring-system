<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $users = new UserModel();

        // Create admin user
        $user = new User([
            'username' => 'admin',
            'email'    => 'admin@example.com',
            'password' => 'password123',
        ]);
        $users->save($user);

        $user = $users->findById($users->getInsertID());
        $user->addGroup('admin');

    }
}