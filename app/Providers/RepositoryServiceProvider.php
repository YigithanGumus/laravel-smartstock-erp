<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ProductRepository\ProductRepository;
use App\Repositories\ProductRepository\Interfaces\IProductRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // bindings
		$this->app->bind(IProductRepository::class, ProductRepository::class);
    }
}