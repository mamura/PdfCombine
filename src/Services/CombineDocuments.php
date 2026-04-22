<?php

namespace Mamura\PdfCombine\Services;

use Mamura\PdfCombine\Contracts\PdfCombinerInterface;
use Mamura\PdfCombine\DTO\CombinePdfData;

final class CombineDocuments
{
    public function __construct(
        private PdfCombinerInterface $driver
    ) {}

    public function handle(CombinePdfData $data): string
    {
        return $this->driver->combine($data);
    }
}