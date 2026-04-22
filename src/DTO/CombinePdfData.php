<?php
declare(strict_types=1);

namespace Mamura\PdfCombine\DTO;

final class CombinePdfData
{
    /**
     * @param array<int, string> $files
     */
    public function __construct(
        public readonly array $files,
        public readonly ?string $outputPath = null,
        public readonly bool $addPageNumbers = false,
    ) {}
}