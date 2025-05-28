   <div class="card-body p-0">
       <div class="table-responsive custom-table table-scroll-height">
           <table id="hotelsTable" class="table table-head-fixed text-nowrap table-hover m-0">
               <thead>
                   <tr>
                       <th style="width: 10px">
                           <div class="custom-control custom-checkbox">
                               <input class="custom-control-input hotelTableCheckbox" type="checkbox" id="bulkHotelAction">
                               <label for="bulkHotelAction" class="custom-control-label"></label>
                           </div>
                       </th>
                       <th>Name <i class="fa fa-sort ml-1 theme-color" id="sortName" aria-hidden="true"></i></th>
                       <th>Status<i class="fa fa-sort ml-1 theme-color" id="sortStatus" aria-hidden="true"></i></th>
                       <th style="width: 40px">Action</th>
                   </tr>
               </thead>
               <tbody>
                   @forelse($hotelData as $key => $hotel)
                       @php
                           $isChecked = $hotel->status === 'ACTIVE' ? 'checked' : '';
                       @endphp
                       <tr data-id="{{ $hotel->id }}">
                           <td>
                               <div class="custom-control custom-checkbox">
                                   <input class="custom-control-input hotelTableCheckbox cellCheckbox" type="checkbox"
                                       id="bulkHotelAction_{{ $hotel->id }}">
                                   <label for="bulkHotelAction_{{ $hotel->id }}"
                                       class="custom-control-label"></label>
                               </div>
                           </td>
                           <td class="text-truncate cell-width-200" title="{{ $hotel->name ?? 'N/A' }}">
                               {{ $hotel->name ?? 'N/A' }}</td>
                           <td>
                               <div
                                   class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                   <input type="checkbox" class="custom-control-input hotelStatusToggal"
                                       id="hotelStatusToggal_{{ $hotel->id }}" {{ $isChecked }}>
                                   <label class="custom-control-label"
                                       for="hotelStatusToggal_{{ $hotel->id }}"></label>
                               </div>
                           </td>

                           <td>
                               <a class="text-dark mx-1" href="{{ route('edit-hotel', ['hotel' => $hotel->id]) }}"
                                   title="Edit"><i class="fas fa-pencil-alt mr-1"></i></a>
                               <button title="Delete"><i data-id="{{ $hotel->id }}"
                                       class="fas fa-solid fa-trash text-danger mr-2 mx-1"></i></button>
                           </td>
                       </tr>
                   @empty
                       <tr>
                           <td colspan="4" class="text-center">No Record Found.</td>
                       </tr>
                   @endforelse
               </tbody>
           </table>
       </div>
   </div>
   <!-- /.card-body -->
   <div class="card-footer clearfix" id="hotelPagination">
       {!! $hotelData->links('pagination::bootstrap-5') !!}
   </div>
