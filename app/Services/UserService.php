<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Services\Interfaces\UserServiceInterface;
use App\Exceptions\UserServiceException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
use Throwable;

class UserService implements UserServiceInterface
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_TAG = 'users';
    private const ALLOWED_STATUSES = ['active', 'inactive', 'suspended'];
    private const ALLOWED_IMAGE_TYPES = ['image/jpeg', 'image/png', 'image/gif'];
    private const MAX_IMAGE_SIZE = 5120; // 5MB

    public function getAllUsers()
    {
        try {
            return Cache::tags([self::CACHE_TAG])->remember('all_users', self::CACHE_TTL, function () {
                return User::with('role')->get();
            });
        } catch (Throwable $e) {
            Log::error('Error fetching all users: ' . $e->getMessage(), [
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw new UserServiceException('Failed to fetch users', 0, $e);
        }
    }

    public function updateUserStatus(User $user, string $status): bool
    {
        try {
            $this->validateStatus($status);

            $user->status = $status;
            $result = $user->save();
            
            if ($result) {
                $this->clearUserCache();
            }
            
            return $result;
        } catch (Throwable $e) {
            Log::error('Error updating user status: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'status' => $status,
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw new UserServiceException('Failed to update user status', 0, $e);
        }
    }

    public function updateProfilePicture(User $user, UploadedFile $file): string
    {
        try {
            $this->validateImage($file);

            $fileName = time() . '_' . $file->getClientOriginalName();
            $path = Storage::disk('public')->putFileAs('uploads', $file, $fileName);
            
            if (!$path) {
                throw new UserServiceException('Failed to store profile picture');
            }

            // Delete old profile picture if exists
            if ($user->profile_picture) {
                Storage::disk('public')->delete('uploads/' . $user->profile_picture);
            }
            
            $user->profile_picture = $fileName;
            $user->save();
            
            $this->clearUserCache();
            
            return $fileName;
        } catch (Throwable $e) {
            Log::error('Error updating profile picture: ' . $e->getMessage(), [
                'user_id' => $user->id,
                'file_name' => $file->getClientOriginalName(),
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw new UserServiceException('Failed to update profile picture', 0, $e);
        }
    }

    public function checkUniqueField(string $field, string $value, ?int $userId = null): bool
    {
        try {
            $query = User::where($field, $value);
            
            if ($userId) {
                $query->where('id', '!=', $userId);
            }
            
            return $query->doesntExist();
        } catch (Throwable $e) {
            Log::error('Error checking unique field: ' . $e->getMessage(), [
                'field' => $field,
                'value' => $value,
                'user_id' => $userId,
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);
            throw new UserServiceException('Failed to check unique field', 0, $e);
        }
    }

    private function validateStatus(string $status): void
    {
        if (!in_array($status, self::ALLOWED_STATUSES)) {
            throw new UserServiceException('Invalid status value');
        }
    }

    private function validateImage(UploadedFile $file): void
    {
        $validator = Validator::make(['file' => $file], [
            'file' => [
                'required',
                'file',
                'mimes:jpeg,png,gif',
                'max:' . self::MAX_IMAGE_SIZE
            ]
        ]);

        if ($validator->fails()) {
            throw new UserServiceException('Invalid image file: ' . implode(', ', $validator->errors()->all()));
        }
    }

    private function clearUserCache(): void
    {
        Cache::tags([self::CACHE_TAG])->flush();
    }
} 