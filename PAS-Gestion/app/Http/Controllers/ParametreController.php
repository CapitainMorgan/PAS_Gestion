<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    public function index()
    {
        $settings = [
            'tva' => config('app_settings.tva'),
            'conditionsGenerales' => config('app_settings.conditions_generales'),
            'newsletterEmail' => config('app_settings.newsletter_email'),
        ];

        $users = User::all();

        if (!auth()->check() || auth()->user()->role !== 'admin') {
            $users = NULL;
        }

        return inertia('Parametre/Index', [
            'settings' => $settings,
            'users' => $users,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request;

        $settings = [
            'app_tva' => $data['tva'],
            'app_conditions_generales' => $data['conditionsGenerales'],
            'app_newsletter_email' => $data['newsletterEmail'],
        ];

        $path = base_path('.env');

        if (file_exists($path)) {
            $env = file_get_contents($path);

            foreach ($settings as $key => $value) {
                $envKey = strtoupper($key);
                $envValue = env($envKey);

                if ($envValue) {
                    $env = str_replace("{$envKey}={$envValue}", "{$envKey}={$value}", $env);
                } else {
                    $env .= "\n{$envKey}={$value}";
                }
            }

            file_put_contents($path, $env);
        }

        return redirect()->back();
    }
}
