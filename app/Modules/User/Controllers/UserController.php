<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\Core\UserControllers;
use Illuminate\Http\Request;

class UserController extends UserControllers
{
    /**
     * Display the index page for User module.
     * Accessible via: /user or /User
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\View|string
     */
    public function index(Request $request)
    {
        $users = [
            ['id' => 1, 'name' => 'Daffa Pratama', 'email' => 'daffa@example.com', 'role' => 'Administrator', 'status' => 'Active'],
            ['id' => 2, 'name' => 'Sarah Amanda', 'email' => 'sarah@example.com', 'role' => 'Editor', 'status' => 'Active'],
            ['id' => 3, 'name' => 'Budi Santoso', 'email' => 'budi@example.com', 'role' => 'User', 'status' => 'Inactive'],
        ];

        return $this->moduleRender('index', [
            'title' => 'User Management',
            'users' => $users
        ]);
    }
}