<?php

declare(strict_types=1);

namespace Mamura\PdfCombine\Tests\Unit\Services;

use Mamura\PdfCombine\Contracts\PdfCombinerInterface;
use Mamura\PdfCombine\DTO\CombinePdfData;
use Mamura\PdfCombine\Services\CombineDocuments;
use PHPUnit\Framework\TestCase;

final class CombineDocumentsTest extends TestCase
{
    public function test_it_delegates_combination_to_driver(): void
    {
        $data = new CombinePdfData(
            files: ['/tmp/a.pdf', '/tmp/b.pdf'],
            outputPath: '/tmp/merged.pdf'
        );

        $driver = $this->createMock(PdfCombinerInterface::class);

        $driver->expects($this->once())
            ->method('combine')
            ->with($data)
            ->willReturn('/tmp/merged.pdf');

        $service = new CombineDocuments($driver);

        $result = $service->handle($data);

        $this->assertSame('/tmp/merged.pdf', $result);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $file = sys_get_temp_dir() . '/merged-test.pdf';

        if (file_exists($file)) {
            unlink($file);
        }
    }
}