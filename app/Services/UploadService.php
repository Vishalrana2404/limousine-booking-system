<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Auth\Guard as Auth;

/**
 * This class provides file upload functionality.
 */
class UploadService
{

    /**
     * @var string|null The storage path for uploaded files.
     */
    protected $storagePath = null;

    /**
     * Constructs a new UploadService object.
     */
    public function __construct(
        private  Auth $auth,
    ) {
    }

    /**
     * Set the storage path for uploaded files.
     *
     * @param string $path The path where files will be stored.
     * @return void
     */
    public function setPath($path)
    {
        $this->storagePath = $path;
    }

    /**
     * Upload a file to the specified storage path.
     *
     * @param \Illuminate\Http\UploadedFile $file The file to upload.
     * @param string $filename
     * @return void
     */
    public function upload($file, $fileName)
    {
        return Storage::disk('public')->putFileAs($this->storagePath, $file, $fileName);
    }

    /**
     * Delete the specified storage directory.
     *
     * @return bool True if the directory is deleted successfully, false otherwise.
     */
    public function delete()
    {
        // Check if the folder exists in the storage
        return (Storage::exists($this->storagePath)) ?  Storage::delete($this->storagePath) : false;
    }

    /**
     * Create the specified storage directory if it doesn't exist.
     *
     * @return bool True if the directory is created successfully or already exists, false otherwise.
     */
    public function createDirectory()
    {

        return (!Storage::exists($this->storagePath))
            ? Storage::makeDirectory($this->storagePath)
            : false;
    }

    /**
     * Download a file from s3 or local disk.
     */
    public function download($path)
    {
        return Storage::download($path);
    }
}
