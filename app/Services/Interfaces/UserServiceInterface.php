<?php

namespace App\Services\Interfaces;

use App\Models\User;
use Illuminate\Http\UploadedFile;

interface UserServiceInterface
{
    /**
     * Get all users with their roles
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllUsers();

    /**
     * Update user status
     *
     * @param User $user
     * @param string $status
     * @return bool
     */
    public function updateUserStatus(User $user, string $status): bool;

    /**
     * Update user profile picture
     *
     * @param User $user
     * @param UploadedFile $file
     * @return string
     */
    public function updateProfilePicture(User $user, UploadedFile $file): string;

    /**
     * Check if a field value is unique
     *
     * @param string $field
     * @param string $value
     * @param int|null $userId
     * @return bool
     */
    public function checkUniqueField(string $field, string $value, ?int $userId = null): bool;
} 