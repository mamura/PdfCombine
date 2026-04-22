<?php

declare(strict_types=1);

namespace Mamura\PdfCombine\Laravel;

use Illuminate\Support\ServiceProvider;
use Mamura\PdfCombine\Contracts\PdfCombinerInterface;
use Mamura\PdfCombine\Services\CombineDocuments;
use Mamura\PdfCombine\Services\Drivers\FpdiPdfCombiner;

final class PdfCombineServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/config/pdf-combine.php', 'pdf-combine');

        $this->app->singleton(PdfCombinerInterface::class, function () {
            return new FpdiPdfCombiner();
        });

        $this->app->singleton(CombineDocuments::class, function ($app) {
            return new CombineDocuments(
                $app->make(PdfCombinerInterface::class)
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/config/pdf-combine.php' => config_path('pdf-combine.php'),
        ], 'pdf-combine-config');
    }
}