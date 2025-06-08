<?php

namespace App\Repositories;

use App\Models\EmailTemplates;
use App\Models\User;
use App\Repositories\Interfaces\EmailTemplatesInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class EmailTemplatesRepository
 * 
 * @package App\Repositories
 */
class EmailTemplatesRepository implements EmailTemplatesInterface
{
    /**
     * EmailTemplatesRepository constructor.
     *
     * @param EmailTemplates $model The EmailTemplates model instance.
     */
    public function __construct(
        protected EmailTemplates $model
    ) {
    }

    /**
     * Get paginated list of templates based on specified parameters.
     *
     * Retrieves a paginated list of templates based on the provided logged-in template, search criteria,
     * template type filter, pagination, sorting field, and sorting direction.
     *
     * @param User $loggedUser The logged-in template instance.
     * @param string $search The search criteria for filtering templates (optional).
     * @param int $page The page number for pagination (optional, default is 1).
     * @param string $sortField The field to sort templates by (optional, default is 'id').
     * @param string $sortDirection The direction for sorting templates ('asc' or 'desc', optional, default is 'asc').
     * @return LengthAwarePaginator A paginator for the retrieved templates.
     */
    public function getEmailTemplates(User $loggedUser, string $search = '', int $page = 1, string $sortField = 'id', string $sortDirection = 'asc'): LengthAwarePaginator
    {
        // Filter templates based on the provided parameters
        $templates = $this->filterEmailTemplatesResult($search)->get();

        // Sort the templates based on the specified field and direction
        $sortedCollection = $this->sortEmailTemplates($templates, $sortField, $sortDirection);

        $pageSize = config('constants.paginationSize');

        // Paginate the sorted collection
        return $this->paginateResults($sortedCollection, $pageSize, $page);
    }

    /**
     * Filter template query result based on specified parameters.
     *
     * Builds and returns a query builder instance for filtering templates based on the provided logged-in template,
     * search criteria, and optional template type filter.
     *
     * @param User $loggedUser The logged-in template instance.
     * @param string $search The search criteria for filtering templates (optional).
     * @return \Illuminate\Database\Eloquent\Builder The query builder instance for filtering templates.
     */
    private function filterEmailTemplatesResult(string $search = '')
    {
        // Start building the query with eager loading relationships
        $query = $this->model->newQuery();

        // Apply search query filters
        if (!empty($search)) {
            $search = strtolower($search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . $search . '%'])
                ->orWhereRaw('LOWER(subject) LIKE ?', ['%' . $search . '%']);
            });
        }

        return $query;
    }



    /**
     * Sort a collection of templates based on specified field and direction.
     *
     * Sorts the provided collection of templates based on the specified field and direction.
     *
     * @param Collection $templates The collection of templates to be sorted.
     * @param string $sortField The field to sort templates by (optional, default is 'id').
     * @param string $sortDirection The direction for sorting templates ('asc' or 'desc', optional, default is 'asc').
     * @return Collection The sorted collection of templates.
     */
    private function sortEmailTemplates(Collection $emailTemplates, string $sortField = 'id', string $sortDirection = 'asc')
    {
        $sortFunction = $sortDirection == 'asc' ? 'sortBy' : 'sortByDesc';
        return $emailTemplates->$sortFunction(function ($innerQuery) use ($sortField) {
            switch ($sortField) {
                case 'sortName':
                    $value = strtolower($innerQuery->name ?? 'zzzz');
                    break;
                case 'sortSubject':
                    $value = strtolower($innerQuery->subject ?? 'zzzz');
                    break;
                case 'sortStatus':
                    $value = strtolower($innerQuery->status ?? 'zzzz');
                    break;
                default:
                    $value = $innerQuery->id;
                    break;
            }
            return $value;
        });
    }
    /**
     * Paginate a collection of results.
     *
     * Paginates the provided collection based on the specified page size and page number.
     *
     * @param mixed $collection The collection of results to be paginated.
     * @param int $pageSize The number of items per page.
     * @param int $page The current page number (optional, default is 1).
     * @return LengthAwarePaginator The paginated collection of results.
     */
    private function paginateResults($collection, $pageSize, $page = 1): LengthAwarePaginator
    {
        return new LengthAwarePaginator(
            $collection->values()->forPage($page, $pageSize), // Paginate the collection for the specified page and page size
            $collection->count(), // Total count of items in the collection
            $pageSize, // Number of items per page
            $page, // Current page number
            ['path' => LengthAwarePaginator::resolveCurrentPath()] // Path for generating pagination links
        );
    }

    /**
     * Get data for a specific template by ID.
     *
     * @param int $templateId The ID of the template.
     * @return User|null The template instance or null if not found.
     */
    public function getEmailTemplateById(int $templateId): ?EmailTemplates
    {
        return $this->model->find($templateId);
    }
    /**
     * Add a new template.
     *
     * @param array $data The data for creating the template.
     * @return Template The newly created template.
     */
    public function addTemplate(array $data): EmailTemplates
    {
        return $this->model->create($data);
    }

    /**
     * Update a template with new data.
     *
     * Updates the provided template instance with the specified data.
     *
     * @param Template $template The template instance to update.
     * @param array $data The new data for updating the template.
     * @return bool True if the template is successfully updated, false otherwise.
     */
    public function updateTemplate(EmailTemplates $template, array $data): bool
    {
        return $template->update($data);
    }

    /**
     * Delete bulk template.
     *
     * @param array $templateIds The ID of the templates to delete.
     * @return bool True if the template is deleted successfully, false otherwise.
     */
    public function deleteTemplate(array $templateIds): bool
    {
        return $this->model->whereIn('id', $templateIds)->delete();
    }
    /**
     * update bulk template status.
     *
     * @param array $templateIds The ID of the templates to update status.
     * @param string $status The status of the templates to update status.
     * @param int $loggedUserId The id of logged template.
     * @return bool True if the template is updated successfully, false otherwise.
     */
    public function updateBulkStatus(array $templateIds, string $status, int $loggedUserId): bool
    {
        return $this->model->whereIn('id', $templateIds)->update(['status' => $status, 'updated_by_id' => $loggedUserId]);
    }

    /**
     * Check if an name is unique in the database.
     *
     * Checks whether the provided name address is unique in the database.
     * Optionally, it excludes the template with the given templateId from the check.
     *
     * @param string $template The name address to check for uniqueness.
     * @param int|null $templateId The ID of the template to exclude from the check (optional).
     * @return Template|null The template instance with the specified name if found, null otherwise.
     */
    public function checkUniqueTemplateName(string $templateName, int $templateId = null): ?EmailTemplates
    {
        // Check if a templateId is provided
        if ($templateId) {
            // If templateId is provided, check for unique name excluding the current template's name
            return $this->model->where('name', $templateName)->where('id', '!=', $templateId)->first();
        } else {
            // If templateId is not provided, simply check for unique name
            return $this->model->where('name', $templateName)->first();
        }
    }
    /**
     * Retrieves a collection of templates based on the given array of template IDs and an optional status.
     *
     * This function queries the database to fetch all templates whose IDs are in the provided array.
     * Optionally, it filters the templates by their status if a status is provided.
     *
     * @param array $templateIds An array of template IDs to retrieve.
     * @param string|null $status An optional status to filter the templates by. Defaults to null.
     * 
     * @return \Illuminate\Support\Collection A collection of templates that match the given IDs and status.
     *
     */
    public function getTemplateByIds(array $templateIds, string $status = null): Collection
    {
        // Start the query with the base condition
        $query = $this->model->whereIn('id', $templateIds);
        // Add the status condition if it's provided
        if (!empty($status)) {
            $query->where('status', $status);
        }
        // Execute the query and return the result
        return $query->get();
    }

    public function getAllTemplates()
    {
        return $this->model->where('status', 'ACTIVE')->get();
    }
}
