<?php

namespace App\Http\Controllers\Api\Auth;

use App\Actions\Auth\ChangePasswordAction;
use App\Actions\Auth\LoginAction;
use App\Actions\Auth\RegisterAction;
use App\Actions\Auth\UpdateProfileAction;
use App\DataTransferObjects\Auth\ChangePasswordDTO;
use App\DataTransferObjects\Auth\LoginDTO;
use App\DataTransferObjects\Auth\RegisterDTO;
use App\DataTransferObjects\Auth\UpdateProfileDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\{LoginRequest, ChangePasswordRequest, UpdateProfileRequest};
use App\Http\Resources\Api\User\UserResource;
use App\Models\User;
use App\Traits\UploadAble;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/**
 * @group Auth
 */
class AuthController extends Controller
{
    use UploadAble;

    private const UPLOAD_OPTIONS = [
        'resize' => ['width' => 400, 'height' => 400],
        'quality' => 90
    ];
    /**
     * Login
     * 
     * Authenticate user with email and password, returns access token.
     * 
     * @group Auth
     * @unauthenticated
     * 
     * @response 200 scenario="Successful login" {
     *   "success": true,
     *   "message": "success",
     *   "data": {
     *     "message": "login_successful",
     *     "access_token": "1|abcdefghijklmnopqrstuvwxyz1234567890",
     *     "token_type": "Bearer",
     *     "expires_in": null,
     *     "user": {
     *       "id": 1,
     *       "full_name": "John Doe",
     *       "email": "user@example.com",
     *       "avatar": "avatars/avatar.jpg",
     *       "language": "en",
     *       "gender": "male",
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   }
     * }
     * 
     * @response 401 scenario="Invalid credentials" {
     *   "success": false,
     *   "message": "invalid_credentials",
     *   "data": []
     * }
     * 
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request, LoginAction $action): JsonResponse
    {
        $dto = new LoginDTO(
            email: $request->input('email'),
            password: $request->input('password'),
        );

        $result = $action->execute($dto);

        if (!$result['success']) {
            return error_response($result['message'], 401);
        }

        return $this->respondWithToken($result['token'], $result['user']);
    }

    /**
     * Register
     * 
     * Create a new user account and return access token.
     * 
     * @group Auth
     * @unauthenticated
     *
     * @response 201 scenario="Successful registration" {
     *   "success": true,
     *   "message": "success",
     *   "data": {
     *     "message": "registration_successful",
     *     "access_token": "2|abcdefghijklmnopqrstuvwxyz1234567890",
     *     "token_type": "Bearer",
     *     "expires_in": null,
     *     "user": {
     *       "id": 2,
     *       "full_name": "John Doe",
     *       "email": "user@example.com",
     *       "avatar": null,
     *       "language": "en",
     *       "gender": null,
     *       "created_at": "2024-01-01T00:00:00.000000Z",
     *       "updated_at": "2024-01-01T00:00:00.000000Z"
     *     }
     *   }
     * }
     * 
     * @response 409 scenario="Email already exists" {
     *   "success": false,
     *   "message": "email_already_exists",
     *   "data": []
     * }
     * 
     * @response 422 scenario="Validation error" {
     *   "success": false,
     *   "message": "The given data was invalid.",
     *   "data": {
     *     "email": ["The email has already been taken."],
     *     "password": ["The password confirmation does not match."]
     *   }
     * }
     *
     * @param \App\Http\Requests\Api\User\RegisterRequest $request
     * @param RegisterAction $action
     * @return JsonResponse
     */
    public function register(\App\Http\Requests\Api\Auth\RegisterRequest $request, RegisterAction $action): JsonResponse
    {
        $credentials = $request->validated();

        $dto = new RegisterDTO(
            full_name: $credentials['full_name'],
            email: $credentials['email'],
            password: $credentials['password'],
            language: $credentials['language'] ?? 'en',
        );

        $result = $action->execute($dto);

        if (!$result['success']) {
            return error_response($result['message'], 409);
        }

        return response()->json([
            'success' => true,
            'message' => 'success',
            'data' => [
                'message' => 'registration_successful',
                'access_token' => $result['token'],
                'token_type' => 'Bearer',
                'expires_in' => null,
                'user' => new UserResource($result['user']),
            ],
        ], 201);
    }

    /**
     * User Profile
     * 
     * Get the authenticated user's profile information.
     * 
     * @group Auth
     * @authenticated
     * 
     * @response 200 scenario="Profile retrieved" {
     *   "success": true,
     *   "message": "success",
     *   "data": {
     *     "id": 1,
     *     "full_name": "John Doe",
     *     "email": "user@example.com",
     *     "avatar": "avatars/avatar.jpg",
     *     "language": "en",
     *     "gender": "male",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     * 
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        return success_response(new UserResource($request->user()));
    }

    /**
     * Logout
     * 
     * Invalidate the current access token and logout the user.
     * 
     * @group Auth
     * @authenticated
     * 
     * @response 200 scenario="Successful logout" {
     *   "success": true,
     *   "message": "logged_out",
     *   "data": []
     * }
     * 
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }

        return success_response([], false, 'logged_out');
    }

    /**
     * Refresh a token.
     * 
     * @group Auth
     * @authenticated
     * 
     * @return JsonResponse
     */
    public function refresh(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return error_response('unauthenticated', 401);
        }

        optional($user->currentAccessToken())->delete();

        $token = $user->createToken('admin')->plainTextToken;

        return $this->respondWithToken($token, $user);
    }

    protected function respondWithToken(string $token, User $user)
    {
        $ttl = config('sanctum.expiration');

        return success_response([
            'message' => 'login_successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $ttl ? $ttl * 60 : null,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Update Profile
     * 
     * Update the authenticated user's profile information including avatar upload.
     * 
     * @group Auth
     * @authenticated
     * 
     * @response 200 scenario="Profile updated successfully" {
     *   "success": true,
     *   "message": "profile_updated",
     *   "data": {
     *     "id": 1,
     *     "full_name": "John Doe Updated",
     *     "email": "user@example.com",
     *     "avatar": "avatars/new-avatar.jpg",
     *     "language": "es",
     *     "gender": "male",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-02T00:00:00.000000Z"
     *   }
     * }
     * 
     * @response 422 scenario="Validation error" {
     *   "success": false,
     *   "message": "The given data was invalid.",
     *   "data": {
     *     "full_name": ["The full name field is required."],
     *     "avatar": ["The avatar must be an image."]
     *   }
     * }
     * 
     * @param UpdateProfileRequest $request
     * @return JsonResponse
     */
    public function updateProfile(UpdateProfileRequest $request, UpdateProfileAction $action): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Upload new avatar
            $avatarPath = $this->uploadFile(
                $request->file('avatar'),
                'avatars',
                'public',
                null,
                self::UPLOAD_OPTIONS
            );
        }

        $dto = new UpdateProfileDTO(
            full_name: $validated['full_name'],
            language: $validated['language'] ?? null,
            gender: $validated['gender'] ?? null,
            password: $validated['password'] ?? null,
            avatar: $avatarPath,
        );

        $updatedUser = $action->execute($user, $dto);

        return success_response(new UserResource($updatedUser), false, 'profile_updated');
    }

    /**
     * Change Password
     * 
     * Change the authenticated user's password.
     * 
     * @group Auth
     * @authenticated
     * 
     * @response 200 scenario="Password changed successfully" {
     *   "success": true,
     *   "message": "password_changed",
     *   "data": {
     *     "id": 1,
     *     "full_name": "John Doe",
     *     "email": "user@example.com",
     *     "avatar": "avatars/avatar.jpg",
     *     "language": "en",
     *     "gender": "male",
     *     "created_at": "2024-01-01T00:00:00.000000Z",
     *     "updated_at": "2024-01-01T00:00:00.000000Z"
     *   }
     * }
     * 
     * @response 422 scenario="Current password incorrect" {
     *   "success": false,
     *   "message": "current_password_incorrect",
     *   "data": []
     * }
     * 
     * @response 422 scenario="Validation error" {
     *   "success": false,
     *   "message": "The given data was invalid.",
     *   "data": {
     *     "password": ["The password confirmation does not match."]
     *   }
     * }
     * 
     * @param ChangePasswordRequest $request
     * @return JsonResponse
     */
    public function changePassword(ChangePasswordRequest $request, ChangePasswordAction $action): JsonResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $dto = new ChangePasswordDTO(
            current_password: $validated['current_password'],
            password: $validated['password'],
        );

        $result = $action->execute($user, $dto);

        if (!$result['success']) {
            return error_response($result['message'], 422);
        }

        return success_response(new UserResource($result['user']), false, 'password_changed');
    }
}
