<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Auth\Access\Response;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\User;
use App\Models\Exam;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('view-all-exams', fn (User $user) => 
            Str::of($user->email)->endsWith('@v3.admin') ? Response::allow() : Response::deny()
        );

        Gate::define('create-exam', fn (User $user) => 
            Str::of($user->email)->endsWith('@v3.admin') ? Response::allow() : Response::deny()
        );

        Gate::define('update-exam', fn (User $user, Exam $exam) => 
            $user->id === $exam->candidate_id ? Response::allow() : Response::deny()
        );
    }
}
