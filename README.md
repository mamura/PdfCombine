# PdfCombine

Combine PDF files in PHP and Laravel with optional page numbering.

---

## Installation

```bash
composer require mamura/pdf-combine
```

---

## Basic Usage (PHP)

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
        addPageNumbers: true
    )
);

echo $output;
```

---

## Laravel Usage

This package supports Laravel auto-discovery.

```php
use Mamura\PdfCombine\DTO\CombinePdfData;
use Mamura\PdfCombine\Services\CombineDocuments;

$path = app(CombineDocuments::class)->handle(
    new CombinePdfData(
        files: [
            storage_path('app/a.pdf'),
            storage_path('app/b.pdf'),
        ],
        addPageNumbers: true
    )
);

return response()->json([
    'file' => $path,
]);
```

---

## CombinePdfData

| Field          | Type    | Description                       |
| -------------- | ------- | --------------------------------- |
| files          | array   | List of PDF file paths            |
| outputPath     | ?string | Output file path (optional)       |
| addPageNumbers | bool    | Add page numbers to the final PDF |

### Notes

* `files` must contain valid paths to readable PDF files.
* If `outputPath` is not provided, a temporary file will be generated automatically.
* Page numbers are rendered at the bottom center of each page when enabled.

---

## Exceptions

The package may throw:

* `Mamura\PdfCombine\Exceptions\FileNotFoundException`
* `Mamura\PdfCombine\Exceptions\InvalidPdfException`
* `Mamura\PdfCombine\Exceptions\PdfCombineException`

Example:

```php
try {
    $service->handle($data);
} catch (\Mamura\PdfCombine\Exceptions\PdfCombineException $e) {
    // handle error
}
```

---

## Testing

Run tests with:

```bash
vendor/bin/phpunit
```

---

## Current Limitations

* Supports only local PDF files
* Uses FPDI as the current driver
* Does not support remote files or cloud storage (S3, etc.)
* Does not perform OCR or PDF validation beyond basic checks

---

## Roadmap

* Configurable page number position
* Custom font support
* Multiple drivers (external APIs, fallback strategies)
* Stream output support (without saving to disk)

---

## License

MIT
