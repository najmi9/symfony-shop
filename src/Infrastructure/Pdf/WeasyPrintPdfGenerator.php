<?php

declare(strict_types=1);

namespace App\Infrastructure\Pdf;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mime\FileinfoMimeTypeGuesser;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class WeasyPrintPdfGenerator implements PdfGeneratorInerface
{
    /**
     * Rendered Pdf file to the user.
     */
    private string $output;

    /**
     * Binary path of WeasyPrint.
     */
    private string $binary;

    public function __construct(string $binary)
    {
        $this->binary = $binary;
    }

    public function getOutputFromHtml(string $html, string $header = null, string $footer = null): Response
    {
        $inputFile = tmpfile();
        $input = stream_get_meta_data($inputFile)['uri'];
        if ($header) {
            file_put_contents($input, $header, LOCK_EX);
        }

        // using the FILE_APPEND flag to append the content to the end of the file
        // and the LOCK_EX flag to prevent anyone else writing to the file at the same time
        file_put_contents($input, $html, FILE_APPEND | LOCK_EX);

        if ($footer) {
            file_put_contents($input, $footer, FILE_APPEND | LOCK_EX);
        }

        $this->output = $input.'.pdf';

        $process = new Process([$this->binary, $input, $this->output, '-o', '-f=pdf']);
        $process->run();

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $response = new BinaryFileResponse($this->output);

        // To generate a file download, you need the mimetype of the file
        $mimeTypeGuesser = new FileinfoMimeTypeGuesser();

        // Set the mimetype with the guesser or manually
        if ($mimeTypeGuesser->isGuesserSupported()) {
            // Guess the mimetype of the file according to the extension of the file
            $response->headers->set('Content-Type', $mimeTypeGuesser->guessMimeType($this->output));
        } else {
            // Set the mimetype of the file manually, in this case for a text file is text/plain
            $response->headers->set('Content-Type', 'text/plain');
        }

        // Set content disposition inline of the file
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

        $response->deleteFileAfterSend(true);

        return $response;
    }
}
