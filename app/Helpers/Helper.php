<?php

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Convert Time (H:i) format into seconds
 * @param  string  $time
 * @return integer
 */
if (!function_exists('convertToSecond')) {
    function convertToSecond($time)
    {

        if (is_int($time)) return $time;

        if (empty($time)) return 0;

        [$hours, $minutes] = explode(':', $time) + [0, 0];
        return ($hours * 3600) + ($minutes * 60);
    }
}

/**
 * Get Translated Country name
 *
 * @param  string  $code  Country ISO2 code
 * @param  string  $local  en|de
 * @return string|null
 */
if (!function_exists('getLanguagesNameFromCode')) {

    function getLanguagesNameFromCode($code, $local = 'en')
    {
        $local = strtolower($local);

        $countries = Cache::rememberForever("languages-translations-$local", function () use ($local) {
            $countries = file_get_contents(base_path("lang/languages/$local.json"));

            return json_decode($countries, true);
        });

        return $countries[strtolower($code)] ?? $code;
    }
}

/**
 * Get audio duration
 *
 * @param  string  $audioFile  Audio file
 */
// if (!function_exists('getAudioDuration')) {

//     function getAudioDuration($audioFile)
//     {
//         $isUrl = Str::startsWith($audioFile, ['http://', 'https://']);

//         if ($isUrl) {
//             $fullPath = $audioFile;
//             $response = Http::get($fullPath);

//             if ($response->ok()) {
//                 $file = storage_path('app/temp_audio.mp3');
//                 file_put_contents($file, $response->body());
//             }
//         }else {
//             $file = storage_path('app/public/' . $audioFile);
//         }

//         if (!file_exists($file)) {
//             return error_response(__('file_not_found'), 404);
//         }

//         $getID3 = new getID3;
//         $info = $getID3->analyze($file);

//         return [
//             'playtime_seconds' => $info['playtime_seconds'] ?? 0,
//             'playtime_minutes' => $info['playtime_string'] ?? 0,
//         ];
//     }
// }
if (!function_exists('getAudioDuration')) {
    function getAudioDuration(string $audio): array
    {
        if (Str::startsWith($audio, ['http://', 'https://'])) {
            $appUrl = rtrim(config('app.url'), '/');
            if ($appUrl && Str::startsWith($audio, $appUrl . '/storage/')) {
                $relative = Str::after($audio, $appUrl . '/storage/');
                $file = storage_path('app/public/' . $relative);
            } elseif (Str::startsWith($audio, ['http://127.0.0.1', 'http://localhost'])) {
                $relative = Str::after($audio, '/storage/');
                $file = storage_path('app/public/' . $relative);
            } else {
                $tmp = storage_path('app/temp_audio_' . uniqid() . '.mp3');
                $response = Http::timeout(15)->retry(2, 200)->get($audio);
                if (!$response->ok()) {
                    return ['error' => 'Failed to fetch remote audio (HTTP ' . $response->status() . ')'];
                }
                file_put_contents($tmp, $response->body());
                $file = $tmp;
                $isTemp = true;
            }
        } else {
            $file = Storage::disk('public')->path($audio);
        }

        if (!isset($file) || !file_exists($file)) {
            return ['error' => 'file_not_found'];
        }

        $getID3 = new getID3;
        $info = $getID3->analyze($file);

        if (isset($isTemp) && $isTemp && file_exists($file)) {
            @unlink($file);
        }

        return [
            'playtime_seconds' => $info['playtime_seconds'] ?? 0,
            'playtime_minutes' => $info['playtime_string'] ?? '0:00',
        ];
    }
}
if (!function_exists('getFileUrl')) {
    function getFileUrl($filePath)
    {
        if (is_null($filePath)) {
            return null;
        }

        if (preg_match('@http@', $filePath)) {
            return $filePath;
        }

        if (config('filesystems.default') == 'exoscale') {
            return Storage::disk('exoscale')->publicUrl($filePath);
        }

        return Storage::disk(config('filesystems.default'))->url($filePath);
    }
}

if (!function_exists('generateUserSlug')) {
    function generateUserSlug(User $user): string
    {
        $clean = function ($str) {
            $str = strtolower($str);
            $str = preg_replace('/[^a-z0-9]+/', '_', $str);
            $str = trim($str, '_');
            return $str;
        };
        $id = $user->id;
        $full_name = $clean($user->full_name);
        $gender = $clean($user->gender?->value ?? 'male');
        $language = $clean($user->language->value);
        $time = date('YmdHis');

        return "{$id}_{$full_name}_{$gender}_{$language}_{$time}";
    }
}


if (!function_exists('downloadAndSaveFile')) {
    /**
     * Download a file from a given URL and save it locally.
     *
     * @param  string  $url         The file URL to download.
     * @param  string|null  $filename Optional custom filename.
     * @param  string  $disk        Storage disk (default: 'local').
     * @return string|false         Full saved path or false on failure.
     */
    function downloadAndSaveFile(string $url, string $filepath, string $disk = 'local')
    {
        try {
            // Try to get the remote file
            $response = Http::get($url);

            if (!$response->successful()) {
                throw new \Exception("Failed to download file: {$url}");
            }

            // Default filename if none provided
            $filename = uniqid('FILE_') . '_' . dechex(time());

            $fileFullPath = $filepath . $filename;
            Storage::disk($disk)->put($fileFullPath, $response->body());

            // Return absolute local path
            return Storage::disk($disk)->path($fileFullPath);
        } catch (\Throwable $e) {
            Log::error('File download failed: ' . $e->getMessage());
            return false;
        }
    }
}


if (! function_exists('default_cover_image_url')) {
    /**
     * Get the default cover image URL (cached).
     *
     * @return string
     */
    function default_cover_image_url(): string
    {
        return Cache::rememberForever('default_cover_image_url', function () {
            $appUrl = rtrim(config('app.url'), '/');
            return $appUrl . '/images/background_cover_image.png';
        });
    }
}


//text meannigfull word like faker generation helper by length

if (! function_exists('generateTextByLength')) {
    /**
     * Generate meaningful text of a specific length using Faker.
     * @param  int  $length
     * @return string
     */
    function generateTextByLength(int $length): string
    {
        $faker = \Faker\Factory::create();
        $text = '';
        
        while (strlen($text) < $length) {
            $text .= $faker->word() . ' ';
        }
        
        return trim(substr($text, 0, $length));
    }
}