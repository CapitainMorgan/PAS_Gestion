<?php

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerPolicies();

        // Définitions de permissions pour Article
        Gate::define('view_article', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('create_article', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('update_article', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('delete_article', function ($user) {
            return $user->role === 'admin';
        });

        // Définitions de permissions pour Vente
        Gate::define('view_vente', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('create_vente', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('update_vente', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('delete_vente', function ($user) {
            return $user->role === 'admin';
        });

        // Définitions de permissions pour Fournisseur
        Gate::define('view_fournisseur', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('create_fournisseur', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('update_fournisseur', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('delete_fournisseur', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        // Définitions de permissions pour Frais

        Gate::define('view_frais', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('create_frais', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('update_frais', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('delete_frais', function ($user) {
            return $user->role === 'admin';
        });

        // Définitions de permissions pour User

        Gate::define('view_user', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('create_user', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('update_user', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('delete_user', function ($user) {
            return $user->role === 'admin';
        });

        // Définitions de permissions pour Frais_societe

        Gate::define('view_frais_societe', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('create_frais_societe', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('update_frais_societe', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('delete_frais_societe', function ($user) {
            return $user->role === 'admin';
        });

        // Définitions de permissions pour Depot

        Gate::define('view_depot', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('create_depot', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('update_depot', function ($user) {
            return $user->role === 'admin' || $user->role === 'user';
        });

        Gate::define('delete_depot', function ($user) {
            return $user->role === 'admin';
        });
    }
}
