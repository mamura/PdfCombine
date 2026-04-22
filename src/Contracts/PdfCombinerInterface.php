<?php

namespace Mamura\PdfCombine\Contracts;

use Mamura\PdfCombine\DTO\CombinePdfData;

interface PdfCombinerInterface
{
    public function combine(CombinePdfData $data): string;
}
