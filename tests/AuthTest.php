<?php

use App\Models\User;

class AuthTest {
    public function run(): array {
        $results = [];

        // 1. Password hashing test
        $pwd = 'Secret@123';
        $hash = password_hash($pwd, PASSWORD_BCRYPT);
        $results['password_hash_verifies'] = password_verify($pwd, $hash);

        // 2. User creation and retrieval
        $testPhone = '99999' . rand(10000, 99999);
        $userId = User::create([
            'name' => 'Unit Test User',
            'phone' => $testPhone,
            'email' => "test_{$testPhone}@example.com",
            'password' => $pwd
        ]);
        $results['user_created'] = ($userId > 0);

        $user = User::findById($userId);
        $results['user_found_by_id'] = ($user !== null && $user['name'] === 'Unit Test User');

        $userByPhone = User::findByPhone($testPhone);
        $results['user_found_by_phone'] = ($userByPhone !== null && (int)$userByPhone['id'] === $userId);

        return $results;
    }
}
