<?php

namespace App\Repositories\Interfaces;

use App\Models\User;
use App\Models\EmailTemplates;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Interface EmailTemplatesInterface
 *
 * Represents an interface for managing email templates.
 */
interface EmailTemplatesInterface
{
    /**
     * Get email templates based on various criteria.
     *
     * @param EmailTemplates $loggedUser The logged-in user.
     * @param string $search The search keyword to filter templates (optional, default is an empty string).
     * @param int $page The page number for pagination (optional, default is 1).
     * @param string $sortField The field to sort templates by (optional, default is 'id').
     * @param string $sortDirection The direction for sorting templates ('asc' or 'desc', optional, default is 'asc').
     * @return LengthAwarePaginator A paginated collection of email templates.
     */
    public function getEmailTemplates(User $loggedUser, string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator;

    /**
     * Get an email template by its ID.
     *
     * @param int $templateId The ID of the email template to retrieve.
     * @return EmailTemplates|null The email template instance associated with the specified ID, or null if not found.
     */
    public function getEmailTemplateById(int $templateId): ?EmailTemplates;

    /**
     * Add a new email template.
     *
     * @param array $data The data for creating the email template.
     * @return EmailTemplates The newly created email template instance.
     */
    public function addTemplate(array $data): EmailTemplates;

    /**
     * Update an email template's information.
     *
     * @param EmailTemplates $template The email template instance to update.
     * @param array $data The data for updating the email template.
     * @return bool True if the email template is successfully updated, false otherwise.
     */
    public function updateTemplate(EmailTemplates $template, array $data): bool;

    /**
     * Delete multiple email templates.
     *
     * @param array $templateIds The IDs of the email templates to delete.
     * @return bool True if the templates are successfully deleted, false otherwise.
     */
    public function deleteTemplate(array $templateIds): bool;

    /**
     * Update the status of multiple email templates.
     *
     * @param array $templateIds The IDs of the email templates to update.
     * @param string $status The new status for the templates.
     * @param int $loggedUserId The ID of the logged-in user performing the action.
     * @return bool True if the bulk status update is successful, false otherwise.
     */
    public function updateBulkStatus(array $templateIds, string $status, int $loggedUserId): bool;

    /**
     * Check if a template name is unique.
     *
     * @param string $templateName The template name to check for uniqueness.
     * @param int|null $templateId The ID of the template to exclude from the uniqueness check (optional).
     * @return EmailTemplates|null The email template instance with the specified name, or null if not found.
     */
    public function checkUniqueTemplateName(string $templateName, int $templateId = null): ?EmailTemplates;

    /**
     * Retrieves a collection of email templates based on the given array of IDs and an optional status.
     *
     * This function queries the database to fetch all templates whose IDs are in the provided array.
     * Optionally, it filters the templates by their status if a status is provided.
     *
     * @param array $templateIds An array of template IDs to retrieve.
     * @param string|null $status An optional status to filter the templates by. Defaults to null.
     * 
     * @return Collection A collection of email templates that match the given IDs and status.
     */
    public function getTemplateByIds(array $templateIds, string $status = null): Collection;
}
