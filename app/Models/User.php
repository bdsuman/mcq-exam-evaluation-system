<?php

namespace App\Models;

use App\Enums\LoginTypeEnum;
use App\Builders\UserBuilder;
use App\Enums\UserGenderEnum;
use App\Enums\AppLanguageEnum;
use App\Traits\FirebaseNotifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory;

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
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if ($user->isProfileSetupCompleted()) {
                // $user->profile_setup_completed = true;
            }
        });

        static::updating(function ($user) {
            if ($user->isProfileSetupCompleted()) {
                // $user->profile_setup_completed = true;
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

    protected $appends = ['avatar_url', 'voice_url', 'voice_cover_image_url', 'voice_description', 'cloned_voice_url'];

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

    public function getVoiceUrlAttribute()
    {
        if (is_null($this->voice)) {
            return null;
        }

        if (preg_match('@http@', $this->voice)) {
            return $this->voice;
        }

        if (config('filesystems.default') == 'exoscale') {
            return Storage::disk('exoscale')->publicUrl($this->voice);
        }

        return Storage::disk(config('filesystems.default'))->url($this->voice);
    }

    public function getClonedVoiceUrlAttribute()
    {
        if (is_null($this->cloned_voice)) {
            return null;
        }

        if (preg_match('@http@', $this->cloned_voice)) {
            return $this->cloned_voice;
        }

        if (config('filesystems.default') == 'exoscale') {
            return Storage::disk('exoscale')->publicUrl($this->cloned_voice);
        }

        return Storage::disk(config('filesystems.default'))->url($this->cloned_voice);
    }

    public function voiceDescription(): Attribute
    {
        return Attribute::get(fn() => 'Own Voice');
    }

    public function voiceCoverImageUrl(): Attribute
    {
        return Attribute::get(fn() => asset('images/background_cover_image.png'));
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
            // && UserProfileQuestionSubmission::where('user_id', $this->id)->exists();

        // && filled($this->voice)
        // && filled($this->voice_text);

        // && filled($this->cloned_voice) // Removed cloned voice as a requirement for profile setup completion
    }

}
