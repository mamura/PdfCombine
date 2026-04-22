<?php

declare(strict_types=1);

namespace Mamura\PdfCombine\Laravel\Facades;

use Illuminate\Support\Facades\Facade;
use Mamura\PdfCombine\Services\CombineDocuments;

/**
 * @method static string handle(\Mamura\PdfCombine\DTO\CombinePdfData $data)
 */
final class PdfCombine extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CombineDocuments::class;
    }
}