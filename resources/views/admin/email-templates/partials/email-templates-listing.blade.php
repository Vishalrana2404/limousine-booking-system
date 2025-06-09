  <div class="card-body p-0">
      <div class="table-responsive custom-table">
          <table id="emailTemplatesTable" class="table table-head-fixed text-nowrap table-hover m-0">
              <thead>
                  <tr>
                      <th style="width: 10px">
                          <div class="custom-control custom-checkbox">
                              <input class="custom-control-input emailTemplatesTableCheckbox" type="checkbox" id="bulkEmailTemplatesAction">
                              <label for="bulkEmailTemplatesAction" class="custom-control-label"></label>
                          </div>
                      </th>
                      <th>Name <i class="fa fa-sort ml-1 text-dark" id="sortName" aria-hidden="true"></i></th>
                      <th>Subject<i class="fa fa-sort ml-1 text-dark" id="sortSubject" aria-hidden="true"></i></th>
                      <th>Status<i class="fa fa-sort ml-1 text-dark" id="sortStatus" aria-hidden="true"></i></th>
                      <th style="width: 40px">Action</th>
                  </tr>
              </thead>
              <tbody>
                  @forelse($templates as $key => $template)
                    @php
                        $isChecked = $template->status === 'ACTIVE' ? 'checked' : '';
                    @endphp
                    <tr data-email-template-id="{{ $template->id }}">
                        <td>
                            <div class="custom-control custom-checkbox">
                                <input class="custom-control-input emailTemplatesTableCheckbox cellCheckbox" type="checkbox"
                                    id="bulkEmailTemplatesAction_{{ $template->id }}">
                                <label for="bulkEmailTemplatesAction_{{ $template->id }}"
                                    class="custom-control-label"></label>
                            </div>
                        </td>
                        <td class="text-truncate cell-width-200" title="{{ $template->name ?? 'N/A' }}">
                            {{ $template->name ?? 'N/A' }}</td>
                        <td class="text-truncate cell-width-200" title="{{ $template->subject ?? 'N/A' }}">
                            {{ $template->subject ?? 'N/A' }}
                        </td>
                        <td>
                            <div
                                class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success me-4">
                                <input type="checkbox" class="custom-control-input emailTemplateStatusToggal"
                                    id="emailTemplateStatusToggal_{{ $template->id }}" {{ $isChecked }}>
                                <label class="custom-control-label"
                                    for="emailTemplateStatusToggal_{{ $template->id }}"></label>
                            </div>
                        </td>
                        <td>
                            <a class="text-dark mx-1" href="{{ route('view-email-template', ['emailTemplate' => $template->id]) }}"
                                title="View"><i class="fas fa-eye mr-1"></i></a>
                            <button class="text-dark mx-1" title="Clone" data-email-template-id="{{ $template->id }}"><i data-email-template-id="{{ $template->id }}" class="fas fa-clone mr-1 make-clone"></i></button>
                            <a class="text-dark mx-1" href="{{ route('edit-email-template', ['emailTemplate' => $template->id]) }}"
                                title="Edit"><i class="fas fa-pencil-alt mr-1"></i></a>
                            <button title="Delete"><i data-email-template-id="{{ $template->id }}"
                                    class="fas fa-solid fa-trash text-danger mx-1"></i></button>
                        </td>
                    </tr>
                  @empty
                      <tr>
                          <td colspan="9" class="text-center">No Record Found.</td>
                      </tr>
                  @endforelse
              </tbody>
          </table>
      </div>
  </div>
  <!-- /.card-body -->
  <div class="card-footer clearfix" id="emailTemplatesPagination">
      {{ $templates->links('pagination::bootstrap-5') }}
  </div>

  <!-- Clone Template Modal -->
<div class="modal fade" id="cloneTemplateModal" tabindex="-1" role="dialog" aria-labelledby="cloneTemplateModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
      <input type="hidden" name="template_id" id="cloneTemplateId">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="cloneTemplateModalLabel">Clone Template</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="cloneTemplateName">Name</label>
            <input type="text" name="name" id="cloneTemplateName" class="form-control" required placeholder="Enter new template name">
            <span id="cloneTemplateName-error" class="error invalid-feedback" style="display:none;"></span>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="create-clone">Save</button>
        </div>
      </div>
  </div>
</div>
