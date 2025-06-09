<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\EmailTemplatesService;
use App\Http\Requests\AddEmailTemplateRequest;
use App\Http\Requests\EditEmailTemplateRequest;
use App\Http\Requests\DeleteEmailTemplateRequest;
use App\Http\Requests\FilterEmailTemplateRequest;
use App\Http\Requests\BulkEmailTemplatesStatusUpdateRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;
use App\CustomHelper;
use App\Models\EmailTemplates;

class EmailTemplatesController extends Controller
{
    public function __construct(
        private EmailTemplatesService $emailTemplateService,
        private CustomHelper $helper
    ) {
    }

    public function index(Request $request)
    {
        try {
            $templates = $this->emailTemplateService->getEmailTemplatesData($request->query());
            return view('admin.email-templates.email-templates', compact('templates'));
        } catch (\Exception $e) {
            Log::error('Error fetching email templates: ' . $e->getMessage());
            return back()->withErrors(__('messages.fetch_error'));
        }
    }

    public function create()
    {
        return view('admin.email-templates.create-email-template');
    }

    public function store(Request $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Create a new email template using the provided request data
            $this->emailTemplateService->createEmailTemplate($request->all(), $log_headers);
            // Display a success message and redirect to the email-templates page
            $this->helper->alertResponse(__('message.email_template_created_successfully'), 'success');
            return redirect('email-templates');
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Display an error message and redirect back to the previous page
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }

    public function edit(EmailTemplates $emailTemplate)
    {
        return view('admin.email-templates.update-email-template', compact('emailTemplate'));
    }

    public function view(EmailTemplates $emailTemplate)
    {
        return view('admin.email-templates.view-email-template', compact('emailTemplate'));
    }

    public function update(EditEmailTemplateRequest $request, EmailTemplates $emailTemplate)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Update the user using the provided request data and user instance
            $this->emailTemplateService->updateTemplate($request->all(), $emailTemplate, $log_headers);
            // Display a success message and redirect to the users page
            $this->helper->alertResponse(__('message.email_template_updated_successfully'), 'success');
            return redirect('email-templates');
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Display an error message and redirect back to the previous page
            $this->helper->alertResponse(__('messages.something_went_wrong'), 'error');
            return redirect()->back();
        }
    }

    public function delete(DeleteEmailTemplateRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Delete the email template using the emailTemplateService
            $templates = $this->emailTemplateService->deleteTemplate($request->all(), $log_headers);
            // Generate and return a successful response with the result of the email template deletion operation
            return $this->handleResponse($templates, __("message.email_template_deleted_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function filterEmailTemplates(FilterEmailTemplateRequest $request)
    {
        try {
            // Retrieve email template data from the EmailTemplateService based on the provided criteria
            $templates = $this->emailTemplateService->getEmailTemplatesData($request->query());
            // Render the HTML for the email template listing view
            $data = ['html' => view('admin.email-templates.partials.email-templates-listing', compact('templates'))->render()];
            // Generate and return a successful response with the filtered email template data
            return $this->handleResponse($data, __("message.email_template_filtered_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function checkUniqueTemplateName(Request $request)
    {
        try {
            // Retrieve template ID and name from the request
            $templateId = $request->input('template_id', null);
            $templateName = $request->input('template_name');
            // Check if the template name is unique among email templates, excluding the template with the specified template ID (if provided)
            $result = $this->emailTemplateService->checkUniqueTemplateName($templateName, $templateId);
            // Generate and return a response indicating whether the name is unique
            return $this->handleResponse(['isvalid' => $result], '', Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse(['isvalid' => false], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function cloneTemplate(Request $request)
    {
        try {
            $log_headers = $this->getHttpData($request);

            // Retrieve template ID and name from the request
            $templateId = !empty($request->input('template_id')) ? $request->input('template_id') : '';
            $templateName = !empty($request->input('template_name')) ? $request->input('template_name') : '';

            if(!empty($templateId) && !empty($templateName))
            {
                $cloneTemplate = $this->emailTemplateService->cloneTemplate($templateName, $templateId, $log_headers);

                if ($cloneTemplate['success']) {
                    return response()->json([
                        'status' => [
                            'code' => 200,
                            'message' => $cloneTemplate['message']
                        ],
                    ], Response::HTTP_OK);
                }else{
                    return response()->json([
                        'status' => [
                            'code' => 400,
                            'message' => $cloneTemplate['message']
                        ]
                    ], Response::HTTP_BAD_REQUEST);
                }
            }else{
                return response()->json([
                    'status' => [
                        'code' => 400,
                        'message' => 'All required parameters are not provided.'
                    ],
                    'data' => []
                ], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse(['isvalid' => false], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function sendTestEmail(Request $request)
    {
        try {
            $log_headers = $this->getHttpData($request);

            // Retrieve template ID and name from the request
            $templateId = !empty($request->input('template_id')) ? $request->input('template_id') : '';
            $templateEmail = !empty($request->input('test_email')) ? $request->input('test_email') : '';

            if(!empty($templateId) && !empty($templateEmail))
            {
                $testEmailTemplate = $this->emailTemplateService->testEmailTemplate($templateEmail, $templateId, $log_headers);

                if ($testEmailTemplate['success']) {
                    return response()->json([
                        'status' => [
                            'code' => 200,
                            'message' => $testEmailTemplate['message']
                        ],
                    ], Response::HTTP_OK);
                }else{
                    return response()->json([
                        'status' => [
                            'code' => 400,
                            'message' => $testEmailTemplate['message']
                        ]
                    ], Response::HTTP_BAD_REQUEST);
                }
            }else{
                return response()->json([
                    'status' => [
                        'code' => 400,
                        'message' => 'All required parameters are not provided.'
                    ],
                    'data' => []
                ], Response::HTTP_BAD_REQUEST);
            }
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse(['isvalid' => false], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }

    public function updateBulkStatus(BulkEmailTemplatesStatusUpdateRequest $request)
    {
        try {
            $log_headers = $this->getHttpData($request);
            // Update the status of multiple email templates using the EmailTemplateService
            $templates = $this->emailTemplateService->updateBulkStatus($request->all(), $log_headers);
            // Generate and return a successful response with the result of the bulk email template status update operation
            return $this->handleResponse($templates, __("message.email_template_status_updated_successfully"), Response::HTTP_OK);
        } catch (\Exception $e) {
            // Handle any exceptions that occur
            $this->helper->handleException($e);
            // Generate and return a response indicating the error that occurred
            return $this->handleResponse([], $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR, []);
        }
    }
}
