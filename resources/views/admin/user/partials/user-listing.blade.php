  <div class="card-body p-0">
      <div class="table-responsive custom-table">
          <table id="userTable" class="table table-head-fixed text-nowrap table-hover m-0">
              <thead>
                  <tr>
                      <th style="width: 10px">
                          <div class="custom-control custom-checkbox">
                              <input class="custom-control-input userTableCheckbox" type="checkbox" id="bulkUserAction">
                              <label for="bulkUserAction" class="custom-control-label"></label>
                          </div>
                      </th>
                      <th>Name <i class="fa fa-sort ml-1 text-dark" id="sortName" aria-hidden="true"></i></th>
                      <th>Contact<i class="fa fa-sort ml-1 text-dark" id="sortPhone" aria-hidden="true"></i></th>
                      <th>Email<i class="fa fa-sort ml-1 text-dark" id="sortEmail" aria-hidden="true"></i></th>
                      <th>Department<i class="fa fa-sort ml-1 text-dark" id="sortDepartment" aria-hidden="true"></i>
                      </th>
                      <th>User Type<i class="fa fa-sort ml-1 text-dark" id="sortUserType" aria-hidden="true"></i></th>
                      <th>Status<i class="fa fa-sort ml-1 text-dark" id="sortStatus" aria-hidden="true"></i></th>
                      <th style="width: 40px">Action</th>
                  </tr>
              </thead>
              <tbody>
                  @php
                      use App\CustomHelper;
                  @endphp
                  @forelse($userData as $key => $user)
                      @php
                          $isChecked = $user->status === 'ACTIVE' ? 'checked' : '';
                          $fullName = CustomHelper::getFullName($user->first_name, $user->last_name);
                      @endphp
                      @if ($user->id !== Auth::user()->id)
                          <tr data-user-id="{{ $user->id }}">
                              <td>
                                  <div class="custom-control custom-checkbox">
                                      <input class="custom-control-input userTableCheckbox cellCheckbox" type="checkbox"
                                          id="bulkUserAction_{{ $user->id }}">
                                      <label for="bulkUserAction_{{ $user->id }}"
                                          class="custom-control-label"></label>
                                  </div>
                              </td>
                              <td class="text-truncate cell-width-200" title="{{ $fullName ?? 'N/A' }}">
                                  {{ $fullName ?? 'N/A' }}</td>
                              <td class="text-truncate cell-width-200"
                                  title="{{ $user->country_code ? '+' . $user->country_code : '' }}{{ $user->phone ?? 'N/A' }}">
                                  {{ $user->country_code ? '+' . $user->country_code : '' }}{{ $user->phone ?? 'N/A' }}
                              </td>
                              <td class="text-truncate cell-width-200" title="{{ $user->email ?? 'N/A' }}">
                                  {{ $user->email ?? 'N/A' }}</td>
                              <td class="text-truncate cell-width-200" title="{{ $user->department ?? 'N/A' }}">
                                  {{ $user->department ?? 'N/A' }}</td>
                              <td class="text-truncate cell-width-200" title="{{ $user->userType->name ?? 'N/A' }}">
                                  {{ $user->userType->name ?? 'N/A' }}</td>
                              <td>
                                  <div
                                      class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success me-4">
                                      <input type="checkbox" class="custom-control-input userStatusToggal"
                                          id="userStatusToggal_{{ $user->id }}" {{ $isChecked }}>
                                      <label class="custom-control-label"
                                          for="userStatusToggal_{{ $user->id }}"></label>
                                  </div>
                              </td>
                              <td>
                                  <a class="text-dark mx-1" href="{{ route('edit-user', ['user' => $user->id]) }}"
                                      title="Edit"><i class="fas fa-pencil-alt mr-1"></i></a>
                                  <button title="Delete"><i data-user-id="{{ $user->id }}"
                                          class="fas fa-solid fa-trash text-danger mx-1"></i></button>
                              </td>
                          </tr>
                      @endif
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
  <div class="card-footer clearfix" id="usersPagination">
      {{ $userData->links('pagination::bootstrap-5') }}
  </div>
