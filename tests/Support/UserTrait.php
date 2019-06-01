<?php

namespace Tests\Support;

use Hash;
use App\User;

trait UserTrait
{

    private $admin;
    private $user;
    private $count;

    public function userTrait()
    {
        $this->admin = User::findOrFail(1);
        $this->user = User::findOrFail(2);
        $this->count = $this->getActualUsersCount();
    }

    public function getRegisterData(): array
    {
        return array_merge(
            $this->user->toArray(),
            [
                'password' => $this->getPassword(),
                'password_confirmation' => $this->getPassword()
            ]
        );
    }

    public function getUserByEmail(): User
    {
        return User::whereEmail($this->user->email)->first();
    }

    public function checkPasswords(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }

    public function getActualUsersCount(): int
    {
        return User::count();
    }

    public function getPassword(): string
    {
        return env('PASSWORD');
    }

    public function getAdmin(): User
    {
        return $this->admin;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getUsersCount(): int
    {
        return $this->count;
    }

}