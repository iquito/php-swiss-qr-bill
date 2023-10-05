<?php declare(strict_types=1);

namespace Sprain\Tests\SwissQrBill\QrCode;

use PHPUnit\Framework\TestCase;
use Sprain\SwissQrBill\QrCode\Exception\UnsupportedFileExtensionException;
use Sprain\SwissQrBill\QrCode\QrCode;

final class QrCodeTest extends TestCase
{
    /**
     * @dataProvider supportedExtensionsProvider
     */
    public function testSupportedFileExtensions(string $extension): void
    {
        $qrCode = QrCode::create('This is a test code');
        $testfile = __DIR__ . '/../TestData/testfile.' . $extension;

        if (!is_writable(dirname($testfile))) {
            $this->markTestSkipped();
            return;
        }

        $qrCode->writeFile($testfile);
        $this->assertTrue(file_exists($testfile));
        unlink($testfile);
    }

    public function supportedExtensionsProvider(): array
    {
        return [
            ['svg'],
            ['png']
        ];
    }

    /**
     * @dataProvider unsupportedExtensionsProvider
     */
    public function testUnsupportedFileExtensions(?string $extension): void
    {
        $this->expectException(UnsupportedFileExtensionException::class);

        $qrCode = QrCode::create('This is a test code');
        $qrCode->writeFile(__DIR__ . '/../TestData/testfile.' . $extension);
    }

    public function unsupportedExtensionsProvider(): array
    {
        return [
            ['eps'],
            ['jpg'],
            ['gif'],
            [''],
            [null]
        ];
    }

    /**
     * @dataProvider dataUriProvider
     */
    public function testDataUri(string $code, string $dataUri, string $format): void
    {
        $qrCode = QrCode::create($code);
        $this->assertEquals(
            $dataUri,
            $qrCode->getDataUri($format)
        );
    }

    public function dataUriProvider()
    {
        return [
            # PNGs do not create the same output in all environments
            # [
            #     'code' => 'This is a test code',
            #     'dataUri' => 'data:image/png;base64,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',
            #     QrCode::FILE_FORMAT_PNG
            # ],
            [
                'code' => 'This is a test code',
                'dataUri' => 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIj8+CjxzdmcgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgdmVyc2lvbj0iMS4xIiB3aWR0aD0iNTUwcHgiIGhlaWdodD0iNTUwcHgiIHZpZXdCb3g9IjAgMCA1NTAgNTUwIj48ZGVmcz48cmVjdCBpZD0iYmxvY2siIHdpZHRoPSIyMi4wMDAwMDAwMDAwIiBoZWlnaHQ9IjIyLjAwMDAwMDAwMDAiIGZpbGw9IiMwMDAwMDAiIGZpbGwtb3BhY2l0eT0iMSIvPjwvZGVmcz48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0iNTUwIiBoZWlnaHQ9IjU1MCIgZmlsbD0iI2ZmZmZmZiIgZmlsbC1vcGFjaXR5PSIxIi8+PHVzZSB4PSIwLjAwMDAwMDAwMDAiIHk9IjAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyMi4wMDAwMDAwMDAwIiB5PSIwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDQuMDAwMDAwMDAwMCIgeT0iMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjY2LjAwMDAwMDAwMDAiIHk9IjAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI4OC4wMDAwMDAwMDAwIiB5PSIwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTEwLjAwMDAwMDAwMDAiIHk9IjAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIxMzIuMDAwMDAwMDAwMCIgeT0iMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjE3Ni4wMDAwMDAwMDAwIiB5PSIwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTk4LjAwMDAwMDAwMDAiIHk9IjAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyMjAuMDAwMDAwMDAwMCIgeT0iMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjI4Ni4wMDAwMDAwMDAwIiB5PSIwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzUyLjAwMDAwMDAwMDAiIHk9IjAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzOTYuMDAwMDAwMDAwMCIgeT0iMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQxOC4wMDAwMDAwMDAwIiB5PSIwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDQwLjAwMDAwMDAwMDAiIHk9IjAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0NjIuMDAwMDAwMDAwMCIgeT0iMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQ4NC4wMDAwMDAwMDAwIiB5PSIwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNTA2LjAwMDAwMDAwMDAiIHk9IjAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI1MjguMDAwMDAwMDAwMCIgeT0iMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjAuMDAwMDAwMDAwMCIgeT0iMjIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIxMzIuMDAwMDAwMDAwMCIgeT0iMjIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyMjAuMDAwMDAwMDAwMCIgeT0iMjIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyNDIuMDAwMDAwMDAwMCIgeT0iMjIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyODYuMDAwMDAwMDAwMCIgeT0iMjIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzMDguMDAwMDAwMDAwMCIgeT0iMjIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzOTYuMDAwMDAwMDAwMCIgeT0iMjIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI1MjguMDAwMDAwMDAwMCIgeT0iMjIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIwLjAwMDAwMDAwMDAiIHk9IjQ0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDQuMDAwMDAwMDAwMCIgeT0iNDQuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI2Ni4wMDAwMDAwMDAwIiB5PSI0NC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9Ijg4LjAwMDAwMDAwMDAiIHk9IjQ0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTMyLjAwMDAwMDAwMDAiIHk9IjQ0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTc2LjAwMDAwMDAwMDAiIHk9IjQ0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTk4LjAwMDAwMDAwMDAiIHk9IjQ0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjIwLjAwMDAwMDAwMDAiIHk9IjQ0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjQyLjAwMDAwMDAwMDAiIHk9IjQ0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzA4LjAwMDAwMDAwMDAiIHk9IjQ0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzk2LjAwMDAwMDAwMDAiIHk9IjQ0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDQwLjAwMDAwMDAwMDAiIHk9IjQ0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDYyLjAwMDAwMDAwMDAiIHk9IjQ0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDg0LjAwMDAwMDAwMDAiIHk9IjQ0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNTI4LjAwMDAwMDAwMDAiIHk9IjQ0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMC4wMDAwMDAwMDAwIiB5PSI2Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQ0LjAwMDAwMDAwMDAiIHk9IjY2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNjYuMDAwMDAwMDAwMCIgeT0iNjYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI4OC4wMDAwMDAwMDAwIiB5PSI2Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjEzMi4wMDAwMDAwMDAwIiB5PSI2Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjE5OC4wMDAwMDAwMDAwIiB5PSI2Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjI0Mi4wMDAwMDAwMDAwIiB5PSI2Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjI2NC4wMDAwMDAwMDAwIiB5PSI2Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjI4Ni4wMDAwMDAwMDAwIiB5PSI2Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjMwOC4wMDAwMDAwMDAwIiB5PSI2Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjM5Ni4wMDAwMDAwMDAwIiB5PSI2Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQ0MC4wMDAwMDAwMDAwIiB5PSI2Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQ2Mi4wMDAwMDAwMDAwIiB5PSI2Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQ4NC4wMDAwMDAwMDAwIiB5PSI2Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjUyOC4wMDAwMDAwMDAwIiB5PSI2Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjAuMDAwMDAwMDAwMCIgeT0iODguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0NC4wMDAwMDAwMDAwIiB5PSI4OC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjY2LjAwMDAwMDAwMDAiIHk9Ijg4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iODguMDAwMDAwMDAwMCIgeT0iODguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIxMzIuMDAwMDAwMDAwMCIgeT0iODguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyMjAuMDAwMDAwMDAwMCIgeT0iODguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyODYuMDAwMDAwMDAwMCIgeT0iODguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzMzAuMDAwMDAwMDAwMCIgeT0iODguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzNTIuMDAwMDAwMDAwMCIgeT0iODguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzOTYuMDAwMDAwMDAwMCIgeT0iODguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0NDAuMDAwMDAwMDAwMCIgeT0iODguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0NjIuMDAwMDAwMDAwMCIgeT0iODguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0ODQuMDAwMDAwMDAwMCIgeT0iODguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI1MjguMDAwMDAwMDAwMCIgeT0iODguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIwLjAwMDAwMDAwMDAiIHk9IjExMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjEzMi4wMDAwMDAwMDAwIiB5PSIxMTAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIxNzYuMDAwMDAwMDAwMCIgeT0iMTEwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjY0LjAwMDAwMDAwMDAiIHk9IjExMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjI4Ni4wMDAwMDAwMDAwIiB5PSIxMTAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzMDguMDAwMDAwMDAwMCIgeT0iMTEwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzk2LjAwMDAwMDAwMDAiIHk9IjExMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjUyOC4wMDAwMDAwMDAwIiB5PSIxMTAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIwLjAwMDAwMDAwMDAiIHk9IjEzMi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjIyLjAwMDAwMDAwMDAiIHk9IjEzMi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQ0LjAwMDAwMDAwMDAiIHk9IjEzMi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjY2LjAwMDAwMDAwMDAiIHk9IjEzMi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9Ijg4LjAwMDAwMDAwMDAiIHk9IjEzMi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjExMC4wMDAwMDAwMDAwIiB5PSIxMzIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIxMzIuMDAwMDAwMDAwMCIgeT0iMTMyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTc2LjAwMDAwMDAwMDAiIHk9IjEzMi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjIyMC4wMDAwMDAwMDAwIiB5PSIxMzIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyNjQuMDAwMDAwMDAwMCIgeT0iMTMyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzA4LjAwMDAwMDAwMDAiIHk9IjEzMi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjM1Mi4wMDAwMDAwMDAwIiB5PSIxMzIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzOTYuMDAwMDAwMDAwMCIgeT0iMTMyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDE4LjAwMDAwMDAwMDAiIHk9IjEzMi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQ0MC4wMDAwMDAwMDAwIiB5PSIxMzIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0NjIuMDAwMDAwMDAwMCIgeT0iMTMyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDg0LjAwMDAwMDAwMDAiIHk9IjEzMi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjUwNi4wMDAwMDAwMDAwIiB5PSIxMzIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI1MjguMDAwMDAwMDAwMCIgeT0iMTMyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjQyLjAwMDAwMDAwMDAiIHk9IjE1NC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjMzMC4wMDAwMDAwMDAwIiB5PSIxNTQuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIwLjAwMDAwMDAwMDAiIHk9IjE3Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQ0LjAwMDAwMDAwMDAiIHk9IjE3Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjEzMi4wMDAwMDAwMDAwIiB5PSIxNzYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIxNTQuMDAwMDAwMDAwMCIgeT0iMTc2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjIwLjAwMDAwMDAwMDAiIHk9IjE3Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjI4Ni4wMDAwMDAwMDAwIiB5PSIxNzYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzMzAuMDAwMDAwMDAwMCIgeT0iMTc2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzUyLjAwMDAwMDAwMDAiIHk9IjE3Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQxOC4wMDAwMDAwMDAwIiB5PSIxNzYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0ODQuMDAwMDAwMDAwMCIgeT0iMTc2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNTI4LjAwMDAwMDAwMDAiIHk9IjE3Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjAuMDAwMDAwMDAwMCIgeT0iMTk4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjIuMDAwMDAwMDAwMCIgeT0iMTk4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDQuMDAwMDAwMDAwMCIgeT0iMTk4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iODguMDAwMDAwMDAwMCIgeT0iMTk4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTEwLjAwMDAwMDAwMDAiIHk9IjE5OC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjE3Ni4wMDAwMDAwMDAwIiB5PSIxOTguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyMjAuMDAwMDAwMDAwMCIgeT0iMTk4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjQyLjAwMDAwMDAwMDAiIHk9IjE5OC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjI2NC4wMDAwMDAwMDAwIiB5PSIxOTguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzNTIuMDAwMDAwMDAwMCIgeT0iMTk4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDYyLjAwMDAwMDAwMDAiIHk9IjE5OC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjIyLjAwMDAwMDAwMDAiIHk9IjIyMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQ0LjAwMDAwMDAwMDAiIHk9IjIyMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjY2LjAwMDAwMDAwMDAiIHk9IjIyMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9Ijg4LjAwMDAwMDAwMDAiIHk9IjIyMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjEzMi4wMDAwMDAwMDAwIiB5PSIyMjAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIxNzYuMDAwMDAwMDAwMCIgeT0iMjIwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzMwLjAwMDAwMDAwMDAiIHk9IjIyMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjM5Ni4wMDAwMDAwMDAwIiB5PSIyMjAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0MTguMDAwMDAwMDAwMCIgeT0iMjIwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDYyLjAwMDAwMDAwMDAiIHk9IjIyMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjUyOC4wMDAwMDAwMDAwIiB5PSIyMjAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIwLjAwMDAwMDAwMDAiIHk9IjI0Mi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjIyLjAwMDAwMDAwMDAiIHk9IjI0Mi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQ0LjAwMDAwMDAwMDAiIHk9IjI0Mi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjY2LjAwMDAwMDAwMDAiIHk9IjI0Mi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjExMC4wMDAwMDAwMDAwIiB5PSIyNDIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIxNTQuMDAwMDAwMDAwMCIgeT0iMjQyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjIwLjAwMDAwMDAwMDAiIHk9IjI0Mi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjI0Mi4wMDAwMDAwMDAwIiB5PSIyNDIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzMzAuMDAwMDAwMDAwMCIgeT0iMjQyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzUyLjAwMDAwMDAwMDAiIHk9IjI0Mi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjM5Ni4wMDAwMDAwMDAwIiB5PSIyNDIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0ODQuMDAwMDAwMDAwMCIgeT0iMjQyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNTA2LjAwMDAwMDAwMDAiIHk9IjI0Mi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjAuMDAwMDAwMDAwMCIgeT0iMjY0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDQuMDAwMDAwMDAwMCIgeT0iMjY0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNjYuMDAwMDAwMDAwMCIgeT0iMjY0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTEwLjAwMDAwMDAwMDAiIHk9IjI2NC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjEzMi4wMDAwMDAwMDAwIiB5PSIyNjQuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyMjAuMDAwMDAwMDAwMCIgeT0iMjY0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjQyLjAwMDAwMDAwMDAiIHk9IjI2NC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjI2NC4wMDAwMDAwMDAwIiB5PSIyNjQuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyODYuMDAwMDAwMDAwMCIgeT0iMjY0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzMwLjAwMDAwMDAwMDAiIHk9IjI2NC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjM3NC4wMDAwMDAwMDAwIiB5PSIyNjQuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzOTYuMDAwMDAwMDAwMCIgeT0iMjY0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDYyLjAwMDAwMDAwMDAiIHk9IjI2NC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQ4NC4wMDAwMDAwMDAwIiB5PSIyNjQuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI1MjguMDAwMDAwMDAwMCIgeT0iMjY0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjIuMDAwMDAwMDAwMCIgeT0iMjg2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNjYuMDAwMDAwMDAwMCIgeT0iMjg2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iODguMDAwMDAwMDAwMCIgeT0iMjg2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTEwLjAwMDAwMDAwMDAiIHk9IjI4Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjE1NC4wMDAwMDAwMDAwIiB5PSIyODYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIxOTguMDAwMDAwMDAwMCIgeT0iMjg2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjY0LjAwMDAwMDAwMDAiIHk9IjI4Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjMwOC4wMDAwMDAwMDAwIiB5PSIyODYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzMzAuMDAwMDAwMDAwMCIgeT0iMjg2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzc0LjAwMDAwMDAwMDAiIHk9IjI4Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQ2Mi4wMDAwMDAwMDAwIiB5PSIyODYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI1MDYuMDAwMDAwMDAwMCIgeT0iMjg2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNTI4LjAwMDAwMDAwMDAiIHk9IjI4Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjAuMDAwMDAwMDAwMCIgeT0iMzA4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjIuMDAwMDAwMDAwMCIgeT0iMzA4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDQuMDAwMDAwMDAwMCIgeT0iMzA4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTMyLjAwMDAwMDAwMDAiIHk9IjMwOC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjE3Ni4wMDAwMDAwMDAwIiB5PSIzMDguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIxOTguMDAwMDAwMDAwMCIgeT0iMzA4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjY0LjAwMDAwMDAwMDAiIHk9IjMwOC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjI4Ni4wMDAwMDAwMDAwIiB5PSIzMDguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzMDguMDAwMDAwMDAwMCIgeT0iMzA4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzMwLjAwMDAwMDAwMDAiIHk9IjMwOC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjM3NC4wMDAwMDAwMDAwIiB5PSIzMDguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzOTYuMDAwMDAwMDAwMCIgeT0iMzA4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDYyLjAwMDAwMDAwMDAiIHk9IjMwOC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjUwNi4wMDAwMDAwMDAwIiB5PSIzMDguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI1MjguMDAwMDAwMDAwMCIgeT0iMzA4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNjYuMDAwMDAwMDAwMCIgeT0iMzMwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTU0LjAwMDAwMDAwMDAiIHk9IjMzMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjE5OC4wMDAwMDAwMDAwIiB5PSIzMzAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyNDIuMDAwMDAwMDAwMCIgeT0iMzMwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjY0LjAwMDAwMDAwMDAiIHk9IjMzMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjMzMC4wMDAwMDAwMDAwIiB5PSIzMzAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzNTIuMDAwMDAwMDAwMCIgeT0iMzMwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzk2LjAwMDAwMDAwMDAiIHk9IjMzMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQxOC4wMDAwMDAwMDAwIiB5PSIzMzAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0ODQuMDAwMDAwMDAwMCIgeT0iMzMwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNTA2LjAwMDAwMDAwMDAiIHk9IjMzMC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjAuMDAwMDAwMDAwMCIgeT0iMzUyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjIuMDAwMDAwMDAwMCIgeT0iMzUyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iODguMDAwMDAwMDAwMCIgeT0iMzUyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTEwLjAwMDAwMDAwMDAiIHk9IjM1Mi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjEzMi4wMDAwMDAwMDAwIiB5PSIzNTIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIxNTQuMDAwMDAwMDAwMCIgeT0iMzUyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTk4LjAwMDAwMDAwMDAiIHk9IjM1Mi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjI4Ni4wMDAwMDAwMDAwIiB5PSIzNTIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzMDguMDAwMDAwMDAwMCIgeT0iMzUyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzMwLjAwMDAwMDAwMDAiIHk9IjM1Mi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjM1Mi4wMDAwMDAwMDAwIiB5PSIzNTIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzNzQuMDAwMDAwMDAwMCIgeT0iMzUyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzk2LjAwMDAwMDAwMDAiIHk9IjM1Mi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQxOC4wMDAwMDAwMDAwIiB5PSIzNTIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0NDAuMDAwMDAwMDAwMCIgeT0iMzUyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDg0LjAwMDAwMDAwMDAiIHk9IjM1Mi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjUwNi4wMDAwMDAwMDAwIiB5PSIzNTIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI1MjguMDAwMDAwMDAwMCIgeT0iMzUyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTc2LjAwMDAwMDAwMDAiIHk9IjM3NC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjI4Ni4wMDAwMDAwMDAwIiB5PSIzNzQuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzMzAuMDAwMDAwMDAwMCIgeT0iMzc0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzUyLjAwMDAwMDAwMDAiIHk9IjM3NC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQ0MC4wMDAwMDAwMDAwIiB5PSIzNzQuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0NjIuMDAwMDAwMDAwMCIgeT0iMzc0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMC4wMDAwMDAwMDAwIiB5PSIzOTYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyMi4wMDAwMDAwMDAwIiB5PSIzOTYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0NC4wMDAwMDAwMDAwIiB5PSIzOTYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI2Ni4wMDAwMDAwMDAwIiB5PSIzOTYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI4OC4wMDAwMDAwMDAwIiB5PSIzOTYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIxMTAuMDAwMDAwMDAwMCIgeT0iMzk2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTMyLjAwMDAwMDAwMDAiIHk9IjM5Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjE3Ni4wMDAwMDAwMDAwIiB5PSIzOTYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIxOTguMDAwMDAwMDAwMCIgeT0iMzk2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjIwLjAwMDAwMDAwMDAiIHk9IjM5Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjI0Mi4wMDAwMDAwMDAwIiB5PSIzOTYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzMzAuMDAwMDAwMDAwMCIgeT0iMzk2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzUyLjAwMDAwMDAwMDAiIHk9IjM5Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjM5Ni4wMDAwMDAwMDAwIiB5PSIzOTYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0NDAuMDAwMDAwMDAwMCIgeT0iMzk2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNTI4LjAwMDAwMDAwMDAiIHk9IjM5Ni4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjAuMDAwMDAwMDAwMCIgeT0iNDE4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTMyLjAwMDAwMDAwMDAiIHk9IjQxOC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjE5OC4wMDAwMDAwMDAwIiB5PSI0MTguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyMjAuMDAwMDAwMDAwMCIgeT0iNDE4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjg2LjAwMDAwMDAwMDAiIHk9IjQxOC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjMzMC4wMDAwMDAwMDAwIiB5PSI0MTguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzNTIuMDAwMDAwMDAwMCIgeT0iNDE4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDQwLjAwMDAwMDAwMDAiIHk9IjQxOC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjUyOC4wMDAwMDAwMDAwIiB5PSI0MTguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIwLjAwMDAwMDAwMDAiIHk9IjQ0MC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQ0LjAwMDAwMDAwMDAiIHk9IjQ0MC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjY2LjAwMDAwMDAwMDAiIHk9IjQ0MC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9Ijg4LjAwMDAwMDAwMDAiIHk9IjQ0MC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjEzMi4wMDAwMDAwMDAwIiB5PSI0NDAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyMjAuMDAwMDAwMDAwMCIgeT0iNDQwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjQyLjAwMDAwMDAwMDAiIHk9IjQ0MC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjI2NC4wMDAwMDAwMDAwIiB5PSI0NDAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyODYuMDAwMDAwMDAwMCIgeT0iNDQwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzMwLjAwMDAwMDAwMDAiIHk9IjQ0MC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjM1Mi4wMDAwMDAwMDAwIiB5PSI0NDAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzNzQuMDAwMDAwMDAwMCIgeT0iNDQwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzk2LjAwMDAwMDAwMDAiIHk9IjQ0MC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQxOC4wMDAwMDAwMDAwIiB5PSI0NDAuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0NDAuMDAwMDAwMDAwMCIgeT0iNDQwLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNTA2LjAwMDAwMDAwMDAiIHk9IjQ0MC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjAuMDAwMDAwMDAwMCIgeT0iNDYyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDQuMDAwMDAwMDAwMCIgeT0iNDYyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNjYuMDAwMDAwMDAwMCIgeT0iNDYyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iODguMDAwMDAwMDAwMCIgeT0iNDYyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTMyLjAwMDAwMDAwMDAiIHk9IjQ2Mi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjE5OC4wMDAwMDAwMDAwIiB5PSI0NjIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyNjQuMDAwMDAwMDAwMCIgeT0iNDYyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzUyLjAwMDAwMDAwMDAiIHk9IjQ2Mi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQxOC4wMDAwMDAwMDAwIiB5PSI0NjIuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI1MDYuMDAwMDAwMDAwMCIgeT0iNDYyLjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMC4wMDAwMDAwMDAwIiB5PSI0ODQuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0NC4wMDAwMDAwMDAwIiB5PSI0ODQuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI2Ni4wMDAwMDAwMDAwIiB5PSI0ODQuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI4OC4wMDAwMDAwMDAwIiB5PSI0ODQuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIxMzIuMDAwMDAwMDAwMCIgeT0iNDg0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTc2LjAwMDAwMDAwMDAiIHk9IjQ4NC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjI2NC4wMDAwMDAwMDAwIiB5PSI0ODQuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyODYuMDAwMDAwMDAwMCIgeT0iNDg0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzA4LjAwMDAwMDAwMDAiIHk9IjQ4NC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjM1Mi4wMDAwMDAwMDAwIiB5PSI0ODQuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzNzQuMDAwMDAwMDAwMCIgeT0iNDg0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzk2LjAwMDAwMDAwMDAiIHk9IjQ4NC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQxOC4wMDAwMDAwMDAwIiB5PSI0ODQuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0NjIuMDAwMDAwMDAwMCIgeT0iNDg0LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNTA2LjAwMDAwMDAwMDAiIHk9IjQ4NC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjUyOC4wMDAwMDAwMDAwIiB5PSI0ODQuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIwLjAwMDAwMDAwMDAiIHk9IjUwNi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjEzMi4wMDAwMDAwMDAwIiB5PSI1MDYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyMjAuMDAwMDAwMDAwMCIgeT0iNTA2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjQyLjAwMDAwMDAwMDAiIHk9IjUwNi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjI2NC4wMDAwMDAwMDAwIiB5PSI1MDYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIzMDguMDAwMDAwMDAwMCIgeT0iNTA2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzc0LjAwMDAwMDAwMDAiIHk9IjUwNi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQ0MC4wMDAwMDAwMDAwIiB5PSI1MDYuMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI1MDYuMDAwMDAwMDAwMCIgeT0iNTA2LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNTI4LjAwMDAwMDAwMDAiIHk9IjUwNi4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjAuMDAwMDAwMDAwMCIgeT0iNTI4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMjIuMDAwMDAwMDAwMCIgeT0iNTI4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDQuMDAwMDAwMDAwMCIgeT0iNTI4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNjYuMDAwMDAwMDAwMCIgeT0iNTI4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iODguMDAwMDAwMDAwMCIgeT0iNTI4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTEwLjAwMDAwMDAwMDAiIHk9IjUyOC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjEzMi4wMDAwMDAwMDAwIiB5PSI1MjguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIxNzYuMDAwMDAwMDAwMCIgeT0iNTI4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMTk4LjAwMDAwMDAwMDAiIHk9IjUyOC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjIyMC4wMDAwMDAwMDAwIiB5PSI1MjguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSIyODYuMDAwMDAwMDAwMCIgeT0iNTI4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iMzA4LjAwMDAwMDAwMDAiIHk9IjUyOC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjM1Mi4wMDAwMDAwMDAwIiB5PSI1MjguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI0NDAuMDAwMDAwMDAwMCIgeT0iNTI4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjx1c2UgeD0iNDYyLjAwMDAwMDAwMDAiIHk9IjUyOC4wMDAwMDAwMDAwIiB4bGluazpocmVmPSIjYmxvY2siLz48dXNlIHg9IjQ4NC4wMDAwMDAwMDAwIiB5PSI1MjguMDAwMDAwMDAwMCIgeGxpbms6aHJlZj0iI2Jsb2NrIi8+PHVzZSB4PSI1MjguMDAwMDAwMDAwMCIgeT0iNTI4LjAwMDAwMDAwMDAiIHhsaW5rOmhyZWY9IiNibG9jayIvPjxpbWFnZSB4PSIyMzMuNSIgeT0iMjMzLjUiIHdpZHRoPSI4MyIgaGVpZ2h0PSI4MyIgcHJlc2VydmVBc3BlY3RSYXRpbz0ibm9uZSIgeGxpbms6aHJlZj0iZGF0YTppbWFnZS9wbmc7YmFzZTY0LGlWQk9SdzBLR2dvQUFBQU5TVWhFVWdBQUFLWUFBQUNtQVFBQUFBQjQ4OG5hQUFBQVJrbEVRVlJJeDJQNGp3WDhZUmlCb2g4WTBJSDlxT2p3Rk9VSHh2aUJVZEZSMFNFb3lvOVVhaDBZRlIwVnBaSG9ZRWpybzZLam9wU0lJb0ZSMFZGUjZvdGlBU05RRkFDZHEvUEkwVXVnTVFBQUFBQkpSVTVFcmtKZ2dnPT0iLz48L3N2Zz4K',
                QrCode::FILE_FORMAT_SVG
            ]
        ];
    }

    /**
     * @dataProvider stringProvider
     */
    public function testString(string $code, string $string, string $format): void
    {
        $qrCode = QrCode::create($code);

        $this->assertEquals(
            $string,
            $qrCode->getAsString($format)
        );
    }

    public function stringProvider()
    {
        return [
            # PNGs do not create the same output in all environments
            # [
            #     'code' => 'This is a test code',
            #     'string' => file_get_contents(__DIR__ . '/../TestData/QrCode/string.png'),
            #     'format' => QrCode::FILE_FORMAT_PNG
            # ],
            [
                'code' => 'This is a test code',
                'string' => file_get_contents(__DIR__ . '/../TestData/QrCode/string.svg'),
                'format' => QrCode::FILE_FORMAT_SVG
            ]
        ];
    }
}
