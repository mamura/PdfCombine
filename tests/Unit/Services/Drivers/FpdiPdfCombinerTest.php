<?php

declare(strict_types=1);

namespace Mamura\PdfCombine\Tests\Unit\Services\Drivers;

use Mamura\PdfCombine\DTO\CombinePdfData;
use Mamura\PdfCombine\Exceptions\FileNotFoundException;
use Mamura\PdfCombine\Services\Drivers\FpdiPdfCombiner;
use PHPUnit\Framework\TestCase;

final class FpdiPdfCombinerTest extends TestCase
{
    public function test_it_throws_exception_when_no_files_are_provided(): void
    {
        $driver = new FpdiPdfCombiner();

        $this->expectException(FileNotFoundException::class);

        $driver->combine(
            new CombinePdfData([], sys_get_temp_dir() . '/merged.pdf')
        );
    }

    public function test_it_combines_two_pdfs(): void
    {
        $driver = new FpdiPdfCombiner();

        $output = sys_get_temp_dir() . '/merged-test.pdf';

        $result = $driver->combine(
            new CombinePdfData(
                files: [
                    __DIR__ . '/../../../Fixtures/pdfs/a.pdf',
                    __DIR__ . '/../../../Fixtures/pdfs/b.pdf',
                ],
                outputPath: $output
            )
        );

        $this->assertSame($output, $result);
        $this->assertFileExists($output);
        $this->assertGreaterThan(0, filesize($output));
    }

    public function test_it_throws_exception_when_file_does_not_exist(): void
    {
        $driver = new FpdiPdfCombiner();

        $this->expectException(FileNotFoundException::class);

        $driver->combine(
            new CombinePdfData(
                files: [__DIR__ . '/../../../Fixtures/pdfs/not-found.pdf'],
                outputPath: sys_get_temp_dir() . '/merged.pdf'
            )
        );
    }
}