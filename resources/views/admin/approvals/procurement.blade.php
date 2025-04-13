<!DOCTYPE html>
<html lang="en">

@include('layout.headerAssets')

<body>

    <div class="main-wrapper">

        @include('layout.navbar')


        @include('layout.sidebar')

        <div class="page-wrapper">
            <div class="content container-fluid">

                <div class="page-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h3 class="page-title">Procurement Approvals</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item active">Reports</li>
                            </ul>
                        </div>


                    </div>
                </div>



                <div class="row">
                    <div class="col-sm-12">
                        <div class="card card-table">
                            <div class="card-body">
                                <div class="table-responsive">

                                    <table class="table table-center table-hover datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#Procurement ID</th>
                                                <th>Vendor</th>
                                                <th>Order Date</th>
                                                <th>Expected Delivery</th>
                                                <th>Status</th>
                                                <th>Created At</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (!empty($procurements['data']) && count($procurements['data']) > 0)
                                                @foreach ($procurements['data'] as $procurement)
                                                    <tr>
                                                        <td>{{ $procurement['id'] }}</td>
                                                        <td>{{ $procurement['vendor']['name'] ?? 'N/A' }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($procurement['order_date'])->format('Y-m-d') }}
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($procurement['delivery_date'])->format('Y-m-d') }}
                                                        </td>
                                                        <td>
                                                            @php
                                                                $statusClass =
                                                                    [
                                                                        'pending' => 'bg-secondary',
                                                                        'on order' => 'bg-info',
                                                                        'approved' => 'bg-success',
                                                                        'rejected' => 'bg-danger',
                                                                        'delivered' => 'bg-primary',
                                                                        'cancelled' => 'bg-warning',
                                                                    ][strtolower($procurement['order_status'])] ??
                                                                    'bg-secondary';
                                                            @endphp
                                                            <span class="badge {{ $statusClass }}">
                                                                {{ ucfirst($procurement['order_status']) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            {{ \Carbon\Carbon::parse($procurement['created_at'])->format('Y-m-d H:i') }}
                                                        </td>
                                                        <td class="text-end">
                                                            @if (strtolower($procurement['order_status']) == 'pending')
                                                                <div class="btn-group" role="group">
                                                                    <form
                                                                        action="{{ route('procurements.update', $procurement['id']) }}"
                                                                        method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="status"
                                                                            value="approved">
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-success me-1"
                                                                            onclick="return confirm('Approve this procurement?')">
                                                                            <i class="fas fa-check"></i> Approve
                                                                        </button>
                                                                    </form>

                                                                    <form
                                                                        action="{{ route('procurements.update', $procurement['id']) }}"
                                                                        method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="status"
                                                                            value="rejected">
                                                                        <button type="submit"
                                                                            class="btn btn-sm btn-danger"
                                                                            onclick="return confirm('Reject this procurement?')">
                                                                            <i class="fas fa-times"></i> Reject
                                                                        </button>
                                                                    </form>

                                                                </div>
                                                            @else
                                                                <span class="text-muted">Action completed</span>
                                                            @endif
                                                        </td>

                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No procurements
                                                        available.</td>
                                                </tr>
                                            @endif
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>






    </div>


    @include('layout.footerjs')
</body>

</html>
