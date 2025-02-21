<!DOCTYPE html>
<html lang="en">

@include('layout.headerAssets')

<body>
    <div class="main-wrapper">
        @include('layout.navbar')
        @include('layout.sidebar')


        <div class="page-wrapper">
            <div class="content container-fluid">
                @include('layout.breadcrumb')

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        @if (session('success'))
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: '{{ session('success') }}',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        @endif

                        @if (session('error'))
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: '{{ session('error') }}',
                                showConfirmButton: true
                            });
                        @endif
                    });
                </script>

                <div class="row">
                    <div class="col-sm-12">
                        <div class="card card-table">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h4 class="card-title">New Hired List</h4>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-stripped table-center table-hover datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Contact#</th>
                                                <th>Email</th>
                                                <th>Department</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($employees) && count($employees) > 0)
                                                @foreach ($employees as $employee)
                                                    <tr>
                                                        <td>
                                                            {{ trim(
                                                                ($employee['first_name'] ?? '') . ' ' . ($employee['middle_name'] ?? '') . ' ' . ($employee['last_name'] ?? ''),
                                                            ) ?:
                                                                'N/A' }}
                                                        </td>
                                                        <td>{{ $employee['contact'] ?? 'N/A' }}</td>
                                                        <td>{{ $employee['email'] ?? 'N/A' }}</td>
                                                        <td>{{ $employee['department'] ?? 'N/A' }}</td>
                                                        <td>{{ $employee['status'] ?? 'N/A' }}</td>
                                                        <td class="text-end">
                                                            <!-- CREATE ACCOUNT button -->
                                                            <form
                                                                action="{{ route('hr.employee.create_account', $employee['id']) }}"
                                                                method="POST" style="display:inline;">
                                                                @csrf
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-success">CREATE
                                                                    ACCOUNT</button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                <tr>
                                                    <td colspan="6" class="text-center">No employee applications
                                                        found.</td>
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
