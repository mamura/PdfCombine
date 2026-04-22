<?php

declare(strict_types=1);

namespace Mamura\PdfCombine\Tests\Unit\Services\Drivers;

use Mamura\PdfCombine\DTO\CombinePdfData;
use Mamura\PdfCombine\Exceptions\FileNotFoundException;
use Mamura\PdfCombine\Exceptions\InvalidPdfException;
use Mamura\PdfCombine\Services\Drivers\FpdiPdfCombiner;
use PHPUnit\Framework\TestCase;

final class FpdiPdfCombinerTest extends TestCase
{
    private array $filesToDelete = [];
    private array $directoriesToDelete = [];

    protected function tearDown(): void
    {
        parent::tearDown();

        foreach ($this->filesToDelete as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        foreach ($this->directoriesToDelete as $directory) {
            if (is_dir($directory)) {
                @rmdir($directory);
            }
        }

        $this->filesToDelete = [];
        $this->directoriesToDelete = [];
    }

    public function test_it_throws_exception_when_no_files_are_provided(): void
    {
        $driver = new FpdiPdfCombiner();

        $this->expectException(FileNotFoundException::class);

        $driver->combine(
            new CombinePdfData([], sys_get_temp_dir() . '/merged.pdf')
        );
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

    public function test_it_throws_exception_when_file_is_not_a_pdf(): void
    {
        $driver = new FpdiPdfCombiner();

        $this->expectException(InvalidPdfException::class);

        $driver->combine(
            new CombinePdfData(
                files: [__DIR__ . '/../../../Fixtures/files/invalid.txt'],
                outputPath: sys_get_temp_dir() . '/merged.pdf'
            )
        );
    }

    public function test_it_combines_two_valid_pdfs(): void
    {
        $driver = new FpdiPdfCombiner();

        $output = sys_get_temp_dir() . '/merged-test.pdf';
        $this->filesToDelete[] = $output;

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

    public function test_it_creates_output_directory_when_it_does_not_exist(): void
    {
        $driver = new FpdiPdfCombiner();

        $directory = sys_get_temp_dir() . '/pdf-combine-tests-' . uniqid();
        $output = $directory . '/merged.pdf';

        $this->filesToDelete[] = $output;
        $this->directoriesToDelete[] = $directory;

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
        $this->assertDirectoryExists($directory);
        $this->assertFileExists($output);
        $this->assertGreaterThan(0, filesize($output));
    }

    public function test_it_combines_two_pdfs_with_page_numbers(): void
    {
        $driver = new FpdiPdfCombiner();

        $output = sys_get_temp_dir() . '/merged-with-page-numbers.pdf';
        $this->filesToDelete[] = $output;

        $result = $driver->combine(
            new CombinePdfData(
                files: [
                    __DIR__ . '/../../../Fixtures/pdfs/a.pdf',
                    __DIR__ . '/../../../Fixtures/pdfs/b.pdf',
                ],
                outputPath: $output,
                addPageNumbers: true
            )
        );

        $this->assertSame($output, $result);
        $this->assertFileExists($output);
        $this->assertGreaterThan(0, filesize($output));
    }

    public function test_it_combines_two_pdfs_without_page_numbers(): void
    {
        $driver = new FpdiPdfCombiner();

        $output = sys_get_temp_dir() . '/merged-without-page-numbers.pdf';
        $this->filesToDelete[] = $output;

        $result = $driver->combine(
            new CombinePdfData(
                files: [
                    __DIR__ . '/../../../Fixtures/pdfs/a.pdf',
                    __DIR__ . '/../../../Fixtures/pdfs/b.pdf',
                ],
                outputPath: $output,
                addPageNumbers: false
            )
        );

        $this->assertSame($output, $result);
        $this->assertFileExists($output);
        $this->assertGreaterThan(0, filesize($output));
    }

    public function test_it_generates_output_path_automatically_when_not_provided(): void
    {
        $driver = new FpdiPdfCombiner();

        $result = $driver->combine(
            new CombinePdfData(
                files: [
                    __DIR__ . '/../../../Fixtures/pdfs/a.pdf',
                    __DIR__ . '/../../../Fixtures/pdfs/b.pdf',
                ],
                outputPath: null
            )
        );

        $this->filesToDelete[] = $result;

        $this->assertFileExists($result);
        $this->assertStringEndsWith('.pdf', $result);
        $this->assertGreaterThan(0, filesize($result));
    }
}