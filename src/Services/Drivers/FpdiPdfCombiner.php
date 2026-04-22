<?php

declare(strict_types=1);

namespace Mamura\PdfCombine\Services\Drivers;

use Mamura\PdfCombine\Contracts\PdfCombinerInterface;
use Mamura\PdfCombine\DTO\CombinePdfData;
use Mamura\PdfCombine\Exceptions\FileNotFoundException;
use Mamura\PdfCombine\Exceptions\InvalidPdfException;
use setasign\Fpdi\Fpdi;

final class FpdiPdfCombiner implements PdfCombinerInterface
{
    public function combine(CombinePdfData $data): string
    {
        $this->validateFiles($data->files);

        $pdf = new Fpdi();

        foreach ($data->files as $file) {
            try {
                $pageCount = $pdf->setSourceFile($file);
            } catch (\Throwable $exception) {
                throw new InvalidPdfException(
                    sprintf('Invalid PDF file: %s', $file),
                    previous: $exception
                );
            }

            for ($page = 1; $page <= $pageCount; $page++) {
                $template = $pdf->importPage($page);
                $size = $pdf->getTemplateSize($template);

                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';

                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($template);
            }
        }

        $directory = dirname($data->outputPath);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $pdf->Output('F', $data->outputPath);

        return $data->outputPath;
    }

    /**
     * @param array<int, string> $files
     */
    private function validateFiles(array $files): void
    {
        if ($files === []) {
            throw new FileNotFoundException('No PDF files provided.');
        }

        foreach ($files as $file) {
            if (! file_exists($file)) {
                throw new FileNotFoundException(
                    sprintf('File not found: %s', $file)
                );
            }

            if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'pdf') {
                throw new InvalidPdfException(
                    sprintf('File is not a PDF: %s', $file)
                );
            }
        }
    }
}