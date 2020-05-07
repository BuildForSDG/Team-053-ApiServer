<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\FileBag;

if (!function_exists('getUrlSlug')) {
    function getUrlSlug()
    {
        $url = request()->path();

        dd($url);
    }
}

if (!function_exists('api_response')) {
    function api_response($message, $success = false, $data = null)
    {
        return response()->json(
            [
                'success' => $success,
                'error' => !$success,
                'message' => $message,
                'data' => $data
            ]
        );
    }
}

if (!function_exists('getPageLimit')) {
    function getPageLimit($default = 15)
    {
        return (int) request()->query('per_page', $default);
    }
}


if (!function_exists('get_file_name')) {
    function get_file_name($name)
    {
        preg_match('/(_)([0-9])+$/', $name, $matches);
        if (count($matches) == 3) {
            return Str::replaceLast($matches[0], '', $name) . '_' . (intval($matches[2]) + 1);
        } else {
            return $name . '_1';
        }
    }
}

if (!function_exists('deleteFileIfExists')) {
    function deleteFileIfExists($path): bool
    {
        if (Storage::disk(config('appconfig.default_storage_disk'))->exists($path)) {
            Storage::disk(config('appconfig.default_storage_disk'))->delete($path);
            return true;
        }

        return false;
    }
}

if (!function_exists('generatePath')) {
    function generatePath($prefix = null)
    {
        return $prefix . DIRECTORY_SEPARATOR . date('FY') . DIRECTORY_SEPARATOR;
    }
}

if (!function_exists('generateFileName')) {
    function generateFileName($file, $path, $preserveFileUploadName = true)
    {
        if (isset($preserveFileUploadName) && $preserveFileUploadName) {
            $filename = basename($file->getClientOriginalName(), '.' . $file->getClientOriginalExtension());
            $filename_counter = 1;

            // Make sure the filename does not exist, if it does make sure to add a number to the end 1, 2, 3, etc...
            while (Storage::disk(config('appconfig.default_storage_disk'))->exists($path . $filename . '.' .
            $file->getClientOriginalExtension())) {
                $filename = basename($file->getClientOriginalName(), '.' . $file->getClientOriginalExtension()) .
                (string) ($filename_counter++);
            }
        } else {
            $filename = Str::random(20);

            // Make sure the filename does not exist, if it does, just regenerate
            while (Storage::disk(config('appconfig.default_storage_disk'))->exists($path . $filename .
            '.' . $file->getClientOriginalExtension())) {
                $filename = Str::random(20);
            }
        }

        return $filename;
    }
}

if (!function_exists('handle_file_upload')) {
    function handle_file_upload($field, $prefix, $preserve_name = true, FileBag $filesBag = null)
    {
        if (!request()->hasFile($field) && ($filesBag && !$filesBag->has($field))) {
            return;
        }

        $files = !$filesBag ? Arr::wrap(request()->file($field)) :
            Arr::wrap($filesBag->get($field));

        $filesPath = [];
        $path = generatePath($prefix);

        foreach ($files as $file) {
            $filename = generateFileName($file, $path, $preserve_name);
            $file->storeAs(
                $path,
                $filename . '.' . $file->getClientOriginalExtension(),
                config('voyager.storage.disk', 'public')
            );

            array_push($filesPath, [
                'download_link' => $path . $filename . '.' . $file->getClientOriginalExtension(),
                'original_name' => $file->getClientOriginalName(),
            ]);
        }

        return json_encode($filesPath);
    }
}

if (!function_exists('delete_file')) {
    function delete_file($file)
    {
        $paths = $file->getFilePaths(true);
        // print_r($paths);die;
        foreach ($paths as $path) {
            Storage::disk(config('appconfig.appconfig.default_storage_disk'))->delete($path['download_link']);
        }
    }
}

if (!function_exists('generate_code')) {
    function generate_code($length = 8, $prefix = null, $table = 'applications', $column = 'id')
    {
        $rnd = str_pad('1', $length, '0', STR_PAD_RIGHT);

        $qr = DB::select(
            "SELECT FLOOR(RAND()*{$rnd}) AS x FROM $table WHERE \"x\" NOT IN
            (SELECT $column FROM $table) limit 1"
        );

        // $qr = DB::table($table)
        //     ->select(
        //         DB::raw("FLOOR(RAND()*{$rnd}) AS x")
        //     )
        //     ->whereNotIn(
        //         'x',
        //         DB::table($table)
        //             ->select($column)
        //     )
        //     ->limit(1)
        //     ->get();

        return $qr[0]->x;
    }
}

if (!function_exists('fix_date')) {
    function fix_date($val)
    {
        return $val ? new Carbon(strtotime($val)) : null;
    }
}

if (!function_exists('setting')) {
    /**
     * Setting Helper
     *
     * @param string $key // Key to retrieve
     * @param mixed  $default // Value if empty
     * @param string $field // Field to retrieve
     *
     * @return void
     */
    function setting($key, $default = null, $field = null)
    {
        $settings = Cache::rememberForever(
            'users',
            function () {
                return DB::table('settings')->get();
            }
        );
    }
}


if (!function_exists('passport_routes')) {
    /**
     * Register Passport Routes
     *
     * @return void
     */
    function passport_routes($options = [])
    {
        foreach ($options as $option) {
            Passport::routes(
                null,
                [
                    'prefix' => $option['name'] . '/oauth',
                    'middleware' => [
                        'passport.provider:' . $option['guard']
                    ],
                    'as' => $option['name'] . '.'
                ]
            );
        }
    }
}
