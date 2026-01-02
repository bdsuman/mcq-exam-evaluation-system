<?php

namespace App\Models;

use App\Builders\UserBuilder;
use App\Enums\UserGenderEnum;
use App\Enums\UserRoleEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'full_name',
        'gender',
        'date_of_birth',
        'email',
        'email_verify_token',
        'email_verified_at',
        'password',
        'avatar',
        'password_reset_token',
        'language',
        'role',
    ];

    protected $hidden = [
        'password',
        'password_reset_token',
        'email_verify_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_of_birth' => 'date',
        'deleted_at' => 'datetime',
        'gender' => UserGenderEnum::class,
        'role' => UserRoleEnum::class,
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if ($user->isProfileSetupCompleted()) {
                // Profile setup validation on create
            }
        });

        static::updating(function ($user) {
            if ($user->isProfileSetupCompleted()) {
                // Profile setup validation on update
            }
        });
    }

    static function getUserByEmail($email)
    {
        return self::where('email', $email)->first();
    }

    public function newEloquentBuilder($query)
    {
        return new UserBuilder($query);
    }

    public function hasRole(string|UserRoleEnum $role): bool
    {
        $value = $role instanceof UserRoleEnum ? $role->value : $role;

        return $this->role === $value;
    }

    protected $appends = ['avatar_url'];

    public function getAvatarUrlAttribute()
    {
        if (is_null($this->avatar)) {
            return null;
        }

        if (preg_match('@http@', $this->avatar)) {
            return $this->avatar;
        }

        if (config('filesystems.default') == 'exoscale') {
            return Storage::disk('exoscale')->publicUrl($this->avatar);
        }

        return Storage::disk(config('filesystems.default'))->url($this->avatar);
    }

    public function createdBy()
    {
        return $this->belongsTo(self::class, 'created_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(self::class, 'deleted_by');
    }

    private function isProfileSetupCompleted()
    {
        return filled($this->gender)
            && filled($this->date_of_birth)
            && filled($this->full_name);
    }

}
