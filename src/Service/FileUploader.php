<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Filesystem\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    const USERS_FOLDER = '/public/uploads/users';
    const PRODUCTS_FOLDER = '/public/uploads/products/';

    private string $projectDir;
    private SluggerInterface $slugger;

    public function __construct(string $projectDir, SluggerInterface $slugger)
    {
        $this->projectDir = $projectDir;
        $this->slugger = $slugger;
    }

    /**
     * upload a picture and move to the right dirctory.
     */
    public function uploadProductPicture(UploadedFile $file, string $id): string
    {
        return $this->uploadImage($file, $this->projectDir.self::PRODUCTS_FOLDER.$id, $this->safeName($file));
    }

    public function deleteProductImage(string $id, string $image): void
    {
        $this->deleteImage($this->projectDir.self::PRODUCTS_FOLDER.$id.'/'.$image);
    }

    public function removeProductDir(string $id): void
    {
        try {
            $dirPath = $this->projectDir.self::PRODUCTS_FOLDER.$id;

            if (!is_dir($dirPath)) {
                throw new InvalidArgumentException("{$dirPath} must be a directory");
            }
            if ('/' !== substr($dirPath, \strlen($dirPath) - 1, 1)) {
                $dirPath .= '/';
            }
            $files = glob($dirPath.'*', GLOB_MARK);
            foreach ($files as $file) {
                if (is_dir($file)) {
                    $this->removeProductDir($file);
                } else {
                    unlink($file);
                }
            }

            rmdir($dirPath);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    private function uploadImage(UploadedFile $file, string $path, string $fileName): string
    {
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        try {
            $file->move($path, $fileName);
        } catch (FileException $e) {
            throw new FileException($e->getMessage());
        }

        return $fileName;
    }


    private function deleteImage(string $image): void
    {
        if (file_exists($image)) {
            unlink($image);
        }
    }

    private function safeName(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);

        return $safeFilename.'-'.uniqid().'.'.$file->guessExtension();
    }
}
