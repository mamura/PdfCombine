<?php

declare(strict_types=1);

namespace Mamura\PdfCombine\Services;

use Mamura\PdfCombine\Contracts\PdfCombinerInterface;
use Mamura\PdfCombine\DTO\CombinePdfData;

final class CombineDocuments
{
    public function __construct(
        private readonly PdfCombinerInterface $combiner
    ) {
    }

    public function handle(CombinePdfData $data): string
    {
        return $this->combiner->combine($data);
    }
}