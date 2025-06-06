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
