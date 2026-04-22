# PdfCombine

Package for combining PDF files in PHP.

## Installation

```bash
composer require mamura/pdf-combine
```

# Basic usage
```php
use Mamura\PdfCombine\DTO\CombinePdfData;
use Mamura\PdfCombine\Services\CombineDocuments;
use Mamura\PdfCombine\Services\Drivers\FpdiPdfCombiner;

$service = new CombineDocuments(new FpdiPdfCombiner());

$output = $service->handle(
    new CombinePdfData(
        files: [
            '/path/one.pdf',
            '/path/two.pdf',
        ],
        outputPath: '/path/merged.pdf',
    )
);
```

# Laravel
The package supports Laravel auto-discovery.

