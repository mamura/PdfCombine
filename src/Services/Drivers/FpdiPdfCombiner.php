<?php

declare(strict_types=1);

namespace Mamura\PdfCombine\Services\Drivers;

use Mamura\PdfCombine\Contracts\PdfCombinerInterface;
use Mamura\PdfCombine\DTO\CombinePdfData;
use Mamura\PdfCombine\Exceptions\FileNotFoundException;
use Mamura\PdfCombine\Exceptions\InvalidPdfException;
use Mamura\PdfCombine\Exceptions\PdfCombineException;
use setasign\Fpdi\Fpdi;

final class FpdiPdfCombiner implements PdfCombinerInterface
{
    public function combine(CombinePdfData $data): string
    {
        $this->validateFiles($data->files);

        $outputPath = $data->outputPath
            ?? sys_get_temp_dir() . '/pdf-combine-' . uniqid('', true) . '.pdf';

        $this->ensureOutputDirectoryExists(dirname($outputPath));

        $pdf = new Fpdi();

        $pageMap = $this->buildPageMap($data->files);
        $totalPages = count($pageMap);
        $currentPage = 1;
        $currentSourceFile = null;

        foreach ($pageMap as $pageData) {
            try {
                if ($currentSourceFile !== $pageData['file']) {
                    $pdf->setSourceFile($pageData['file']);
                    $currentSourceFile = $pageData['file'];
                }

                $template = $pdf->importPage($pageData['page']);
                $size = $pdf->getTemplateSize($template);
            } catch (\Throwable $exception) {
                throw new InvalidPdfException(
                    sprintf('Invalid PDF file: %s', $pageData['file']),
                    previous: $exception
                );
            }

            $orientation = $size['width'] > $size['height'] ? 'L' : 'P';

            $pdf->AddPage($orientation, [$size['width'], $size['height']]);
            $pdf->useTemplate($template);

            if ($data->addPageNumbers) {
                $this->addPageNumber(
                    $pdf,
                    $currentPage,
                    $totalPages,
                    (float) $size['width'],
                    (float) $size['height']
                );
            }

            $currentPage++;
        }

        $pdf->Output('F', $outputPath);

        return $outputPath;
    }

    /**
     * @param array<int, string> $files
     * @return array<int, array{file: string, page: int}>
     */
    private function buildPageMap(array $files): array
    {
        $probe = new Fpdi();
        $pages = [];

        foreach ($files as $file) {
            try {
                $pageCount = $probe->setSourceFile($file);
            } catch (\Throwable $exception) {
                throw new InvalidPdfException(
                    sprintf('Invalid PDF file: %s', $file),
                    previous: $exception
                );
            }

            for ($page = 1; $page <= $pageCount; $page++) {
                $pages[] = [
                    'file' => $file,
                    'page' => $page,
                ];
            }
        }

        return $pages;
    }

    private function addPageNumber(
        Fpdi $pdf,
        int $currentPage,
        int $totalPages,
        float $pageWidth,
        float $pageHeight
    ): void {
        $text = sprintf('Página %d de %d', $currentPage, $totalPages);

        $pdf->SetFont('Arial', '', 9);
        $pdf->SetTextColor(80, 80, 80);

        $textWidth = $pdf->GetStringWidth($text);
        $x = ($pageWidth - $textWidth) / 2;
        $y = $pageHeight - 10;

        $pdf->SetXY($x, $y);
        $pdf->Cell($textWidth, 5, $text);
    }

    private function ensureOutputDirectoryExists(string $directory): void
    {
        if (is_dir($directory)) {
            return;
        }

        if (! mkdir($directory, 0777, true) && ! is_dir($directory)) {
            throw new PdfCombineException(
                sprintf('Could not create output directory: %s', $directory)
            );
        }
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
                throw new FileNotFoundException(sprintf('File not found: %s', $file));
            }

            if (! is_readable($file)) {
                throw new FileNotFoundException(sprintf('File is not readable: %s', $file));
            }

            if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) !== 'pdf') {
                throw new InvalidPdfException(sprintf('File is not a PDF: %s', $file));
            }
        }
    }
}