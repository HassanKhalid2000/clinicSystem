<?php

namespace App\Models;

class UserModel extends BaseModel
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = [
        'username',
        'email',
        'password',
        'role',
        'first_name',
        'last_name',
        'phone',
        'status'
    ];

    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[255]|is_unique[users.username,id,{id}]',
        'email' => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password' => 'required|min_length[8]',
        'role' => 'required|in_list[admin,doctor,receptionist]',
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name' => 'required|min_length[2]|max_length[100]',
        'phone' => 'permit_empty|min_length[10]|max_length[20]',
        'status' => 'required|in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'username' => [
            'is_unique' => 'This username is already taken'
        ],
        'email' => [
            'is_unique' => 'This email is already registered'
        ]
    ];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /**
     * Attempt to authenticate a user
     */
    public function authenticate(string $username, string $password): ?array
    {
        $user = $this->where('username', $username)
                    ->where('status', 'active')
                    ->first();

        if (is_null($user)) {
            return null;
        }

        if (!password_verify($password, $user['password'])) {
            return null;
        }

        unset($user['password']);
        return $user;
    }
} 