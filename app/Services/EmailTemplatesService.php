<?php

namespace App\Services;

use App\Models\EmailTemplates;
use App\Repositories\Interfaces\EmailTemplatesInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmailTemplatesService
{
    public function __construct(
        private EmailTemplatesInterface $emailTemplatesRepository,
        private UploadService $uploadService,
        private ActivityLogService $activityLogService,
    ) {}

    /**
     * Get list of email templates with optional filters.
     */
    public function getEmailTemplatesData(array $requestData = [])
    {
        $loggedUser = Auth::user();
        $page = $requestData['page'] ?? 1;
        $search = $requestData['search'] ?? '';
        $sortField = $requestData['sortField'] ?? 'id';
        $sortDirection = $requestData['sortDirection'] ?? 'asc';

        return $this->emailTemplatesRepository->getEmailTemplates(
            $loggedUser, $search, $page, $sortField, $sortDirection
        );
    }

    /**
     * Get a single email template by ID.
     */
    public function getEmailTemplateData(int $id)
    {
        return $this->emailTemplatesRepository->getEmailTemplateById($id);
    }

    /**
     * Create a new email template.
     */
    public function createEmailTemplate(array $requestData, array $logHeaders)
    {
        DB::beginTransaction();
        try {
            $templateData = [
                'name' => $requestData['name'],
                'subject' => $requestData['subject'],
                'header' => $requestData['header'],
                'message' => $requestData['message'],
                'footer' => $requestData['footer'],
                'status' => $requestData['status'],
                'created_by_id' => Auth::id(),
            ];

            $template = $this->emailTemplatesRepository->addTemplate($templateData);

            $this->activityLogService->addActivityLog(
                'create', EmailTemplates::class, json_encode([]), json_encode($templateData),
                $logHeaders['headers']['Origin'], $logHeaders['headers']['User-Agent']
            );

            DB::commit();
            return $template;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update an existing email template.
     */
    public function updateTemplate(array $requestData, EmailTemplates $template, array $logHeaders)
    {
        DB::beginTransaction();
        try {
            $requestData['updated_by_id'] = Auth::id();
            $oldData = json_encode($template);

            $updatedTemplate = $this->emailTemplatesRepository->updateTemplate($template, $requestData);

            $this->activityLogService->addActivityLog(
                'update', EmailTemplates::class, $oldData, json_encode($requestData),
                $logHeaders['headers']['Origin'], $logHeaders['headers']['User-Agent']
            );

            DB::commit();
            return $updatedTemplate;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Delete one or more templates by ID.
     */
    public function deleteTemplate(array $requestData, array $logHeaders)
    {
        DB::beginTransaction();
        try {
            $oldData = $this->emailTemplatesRepository->getTemplateByIds($requestData['template_ids']);
            $result = $this->emailTemplatesRepository->deleteTemplate($requestData['template_ids']);

            $this->activityLogService->addActivityLog(
                'delete', EmailTemplates::class, json_encode($oldData), json_encode([]),
                $logHeaders['headers']['Origin'], $logHeaders['headers']['User-Agent']
            );

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Bulk status update for templates.
     */
    public function updateBulkStatus(array $requestData, array $logHeaders)
    {
        DB::beginTransaction();
        try {
            $templateIds = $requestData['template_ids'];
            $status = $requestData['status'];
            $loggedUserId = Auth::id();

            $oldData = $this->emailTemplatesRepository->getTemplateByIds($templateIds);

            $result = $this->emailTemplatesRepository->updateBulkStatus($templateIds, $status, $loggedUserId);

            $this->activityLogService->addActivityLog(
                'updateBulkStatus', EmailTemplates::class, json_encode($oldData), json_encode($requestData),
                $logHeaders['headers']['Origin'], $logHeaders['headers']['User-Agent']
            );

            DB::commit();
            return $result;
        } catch (\Exception $e) {
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Upload a QR code image and update the template for the logged-in user.
     */
    public function changeQrCodeImage($qrCodeImage)
    {
        DB::beginTransaction();
        try {
            $loggedUser = Auth::user();
            $folderName = 'qr_codes/';

            $this->uploadService->setPath($folderName);
            if (!Storage::exists($folderName)) {
                Storage::makeDirectory($folderName);
            }

            $fileName = time() . '.' . $qrCodeImage->getClientOriginalExtension();
            $uploadedPath = $this->uploadService->upload($qrCodeImage, $fileName);

            $updatedTemplate = $this->emailTemplatesRepository->updateTemplate($loggedUser->id, [
                'qr_code_image' => $uploadedPath
            ]);

            DB::commit();
            return $updatedTemplate;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception('Failed to update QR code image: ' . $e->getMessage());
        }
    }

    /**
     * Check if a template name is unique.
     */
    public function checkUniqueTemplateName(string $templateName, int $templateId = null)
    {
        try {
            // Call the UserRepository to check the uniqueness of the email
            $template = $this->emailTemplatesRepository->checkUniqueTemplateName($templateName, $templateId);
            // Return true if the email is unique, false otherwise
            return $template ? "false" : "true";
        } catch (\Exception $e) {
            // If an exception occurs, rollback and throw an exception
            DB::rollback();
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Remove profile image of the logged-in user (generic method, not specific to email template).
     */
    public function removeProfileImage(): bool
    {
        DB::beginTransaction();
        try {
            $loggedUser = Auth::user();
            $oldProfileImage = $loggedUser->profile_image;

            if ($oldProfileImage) {
                $filePath = storage_path('app/public/' . $oldProfileImage);
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                $this->uploadService->setPath('profile_image/' . $loggedUser->id);
                // This assumes updateUser is available (you had `$this->userRepository` before, which was undeclared)
                $this->emailTemplatesRepository->updateTemplate($loggedUser->id, ['profile_image' => null]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }
}
