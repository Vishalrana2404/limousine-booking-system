@php
    $user = Auth::user();
    $userTypeSlug = $user->userType->slug ?? null;
@endphp
@forelse($bookingData as $key => $booking)
   @include('admin.bookings-archives.partials.bookings-archive-row',['booking'=>$booking])
@empty
    <tr>
        @if ($userTypeSlug === null || in_array($userTypeSlug, ['admin', 'admin-staff']))
            <td colspan="20" class="text-center">No Record Found.</td>
        @else
            <td colspan="14" class="text-center">No Record Found.</td>
        @endif
    </tr>
@endforelse
