<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * Upload Pictures.
 */
class FileUploader
{
    private string $productsDir;
    private SluggerInterface $slugger;

    public function __construct(string $projectDir, SluggerInterface $slugger)
    {
        // .../public/uploads/products/{product}
        $this->productsDir = $projectDir.'/public/uploads/products/{product}';
        $this->slugger = $slugger;
    }

    /**
     * upload a picture and move to the right dirctory.
     */
    public function uploadProductPicture(UploadedFile $file, int $id): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        // Move the file to the directory where brochures are stored
        try {
            $path = $this->getProductDir($id);

            if (!file_exists($path)) {
                mkdir($path, 0777, true);
            }

            $file->move(
                $path,
                $newFilename
            );
        } catch (FileException $e) {
        }

        return $newFilename;
    }

    /**
     * @parameter mixed[] $toDelete
     */
    public function deleteProductImage(int $productId, array $toDelete): void
    {
        $dir = str_replace(
            ['{product}'],
            [$productId],
            $this->productsDir
        );

        if (file_exists($dir.$toDelete[0]['file'])) {
            unlink($dir.$toDelete[0]['file']);
        }
    }

    private function getProductDir(int $id): string
    {
        return str_replace(
            ['{product}'],
            [$id],
            $this->productsDir
        );
    }
}
