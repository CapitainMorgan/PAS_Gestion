<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
            'APP_TVA' => $data['tva'],
            'APP_CONDITIONS_GENERALES' => $data['conditionsGenerales'],
            'MAIL_USERNAME' => $data['newsletterEmail'],
            'MAIL_FROM_ADDRESS' => $data['newsletterEmail'],
        ];

        $path = base_path('.env');

        if (file_exists($path)) {
            $env = file_get_contents($path);

            foreach ($settings as $key => $value) {
                $envKey = strtoupper($key);
                $envValue = env($envKey);

                // Échapper les caractères spéciaux
                $escapedValue = preg_match('/[\s=]/', $value) ? "\"$value\"" : $value;

                if ($envValue) {
                    $env = str_replace("{$envKey}={$envValue}", "{$envKey}={$escapedValue}", $env);
                } else {
                    $env .= "\n{$envKey}={$escapedValue}";
                }
            }

            file_put_contents($path, $env);
        }

        return response()->route('parametre.index')->with('success', 'Paramètres mis à jour avec succès');
    }
}
