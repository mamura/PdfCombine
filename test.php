<?php

require 'vendor/autoload.php';

use Mamura\PdfCombine\DTO\CombinePdfData;
use Mamura\PdfCombine\Services\CombineDocuments;
use Mamura\PdfCombine\Services\Drivers\FpdiPdfCombiner;

$service = new CombineDocuments(new FpdiPdfCombiner());

$output = $service->handle(
    new CombinePdfData(
        files: [
            __DIR__ . '/tests/Fixtures/pdfs/a.pdf',
            __DIR__ . '/tests/Fixtures/pdfs/b.pdf',
        ],
        outputPath: __DIR__ . '/merged.pdf'
    )
);

echo "Arquivo gerado em: {$output}\n";