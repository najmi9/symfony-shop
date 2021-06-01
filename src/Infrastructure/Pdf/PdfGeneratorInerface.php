<?php

declare(strict_types=1);

namespace App\Infrastructure\Pdf;

use Symfony\Component\HttpFoundation\Response;

/**
 * The Second Time when we want change the current pdf generator, we don't need to touch.
 * the pdf service or any other code, we need just to create a class that implement this interface.
 */
interface PdfGeneratorInerface
{
    public function getOutputFromHtml(string $html, string $header = null, string $footer = null): Response;
}
