<?php

namespace App\Services;

use Spatie\PdfToImage\Pdf;
use Exception;

class ConvertFileService
{
    /**
     * Convert a PDF file to a single JPG image containing all pages.
     *
     * @param string $pdfPath
     * @param string|null $outputPath
     * @param int $dpi
     * @return string|bool
     */
    public function convert(string $pdfPath, ?string $outputPath = null, int $dpi = 600)
    {
        try {
            // Create an instance of Pdf
            $pdf = new Pdf($pdfPath);

            // Set the resolution (DPI)
            $pdf->resolution($dpi);

            // Get the directory of the PDF file
            $directory = dirname($pdfPath);

            // Get the filename without the extension
            $filename = pathinfo($pdfPath, PATHINFO_FILENAME);

            // Construct the output path if not provided
            if (!$outputPath) {
                $outputPath = $directory . '/' . $filename . '.jpg';
            }

            // Get the number of pages in the PDF
            $numberOfPages = $pdf->pageCount();

            // Initialize variables for tracking dimensions
            $totalWidth = 0;
            $totalHeight = 0;
            $images = [];

            // Loop through each page and store dimensions and image resource
            for ($page = 1; $page <= $numberOfPages; $page++) {
                // Render the page as an image
                $imagePath = $directory . '/' . $filename . '-page-' . $page . '.jpg';
                $pdf->selectPage($page)->save($imagePath);

                // Open the rendered page image
                $pageImage = imagecreatefromjpeg($imagePath);

                // Store dimensions and image resource
                $images[] = [
                    'width' => imagesx($pageImage),
                    'height' => imagesy($pageImage),
                    'resource' => $pageImage,
                ];

                // Update total dimensions
                $totalWidth = max($totalWidth, imagesx($pageImage));
                $totalHeight += imagesy($pageImage);

                // Clean up: delete the temporary page image
                unlink($imagePath);
            }

            // Create a blank canvas for merged image
            $mergedImage = imagecreatetruecolor($totalWidth, $totalHeight);

            // Set initial y-coordinate for placing images on canvas
            $y = 0;

            // Merge each page image into the canvas
            foreach ($images as $imageData) {
                // Get dimensions and image resource
                $width = $imageData['width'];
                $height = $imageData['height'];
                $resource = $imageData['resource'];

                // Merge page image into canvas
                imagecopy($mergedImage, $resource, 0, $y, 0, 0, $width, $height);

                // Update y-coordinate for next image
                $y += $height;

                // Free memory allocated for page image resource
                imagedestroy($resource);
            }

            // Save the merged image as a single JPEG file
            imagejpeg($mergedImage, $outputPath);

            // Free memory allocated for merged image
            imagedestroy($mergedImage);

            // Optionally delete the PDF file after conversion
            unlink($pdfPath);

            return $outputPath;
        } catch (Exception $e) {
            // Handle any exceptions
            return false;
        }
    }
}
