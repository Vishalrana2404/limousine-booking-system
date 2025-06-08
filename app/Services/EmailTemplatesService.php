<?php

namespace App\Services;

use App\Models\EmailTemplates;
use App\Repositories\Interfaces\EmailTemplatesInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


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
     * Get list of email templates with optional filters.
     */
    public function getAllTemplates()
    {
        return $this->emailTemplatesRepository->getAllTemplates();
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

            // Save the template first to get its ID
            $template = $this->emailTemplatesRepository->addTemplate($templateData);

            // Handle file upload if exists
            if (isset($requestData['qr_code']) && $requestData['qr_code']->isValid()) {
                $file = $requestData['qr_code'];
                $originalName = $file->getClientOriginalName();
                $newFileName = now()->format('YmdHis') . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

                $basePath = "email-templates/qr-codes/{$template->id}";

                // Create nested directories if not exist
                $pathsToCheck = [
                    'email-templates',
                    'email-templates/qr-codes',
                    "email-templates/qr-codes/{$template->id}",
                    $basePath
                ];

                foreach ($pathsToCheck as $path) {
                    if (!Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->makeDirectory($path);
                    }
                }

                // Store the file
                Storage::disk('public')->putFileAs($basePath, $file, $newFileName);

                // Update the template with image names
                $template->qr_code_image_name = $originalName;
                $template->qr_code_image = $newFileName;
                $template->save();
            }

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
            unset($requestData['qr_code_image']);
            $requestData['updated_by_id'] = Auth::id();
            $oldData = json_encode($template);

            // Handle file upload if exists
            if (isset($requestData['qr_code']) && $requestData['qr_code']->isValid()) {
                $file = $requestData['qr_code'];
                $originalName = $file->getClientOriginalName();
                $newFileName = now()->format('YmdHis') . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

                $basePath = "email-templates/qr-codes/{$template->id}";

                // Create nested directories if not exist
                $pathsToCheck = [
                    'email-templates',
                    'email-templates/qr-codes',
                    "email-templates/qr-codes/{$template->id}",
                    $basePath
                ];

                foreach ($pathsToCheck as $path) {
                    if (!Storage::disk('public')->exists($path)) {
                        Storage::disk('public')->makeDirectory($path);
                    }
                }

                // Store the file
                Storage::disk('public')->putFileAs($basePath, $file, $newFileName);

                // Update the template with image names
                $requestData['qr_code_image_name'] = $originalName;
                $requestData['qr_code_image'] = $newFileName;
            }

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
}
