<?php

namespace App\Providers;

use Illuminate\Database\Schema\Builder;
use Illuminate\Support\ServiceProvider;
use App\Contracts\ConversationRepositoryInterface;
use App\Repositories\ConversationRepository;



class AppServiceProvider extends ServiceProvider
{
    public function register(): void
{
    $this->app->bind(
        ConversationRepositoryInterface::class,
        ConversationRepository::class,
    );
}

    public function boot(): void
    {
        // Corrige "Specified key was too long" no MySQL < 5.7.7
        Builder::defaultStringLength(191);
    }
}
