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
                                <h4 class="card-title">List of Users</h4>
                                <a href="{{ route('users.create') }}" class="btn btn-primary px-4">
                                    <i class="fas fa-plus"></i> Create User
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-stripped table-center table-hover datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Registered On</th>
                                                <th>Role</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $user)
                                                <tr>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->created_at->format('M d, Y h:i A') }}</td>
                                                    <td>{{ ucfirst($user->role) }}</td>
                                                    <td>
                                                        @if ($user->status == 'Active')
                                                            <span class="badge bg-success">Active</span>
                                                        @else
                                                            <span class="badge bg-danger">Inactive</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <a href="javascript:void(0);"
                                                            class="btn btn-sm btn-white text-success me-2"
                                                            data-bs-toggle="modal" data-bs-target="#viewUserModal"
                                                            onclick="fetchUserActivityLogs({{ $user->id }})">
                                                            <i class="far fa-eye me-1"></i> View
                                                        </a>

                                                        <a href="{{ route('users.edit', $user->id) }}"
                                                            class="btn btn-sm btn-white text-success me-2">
                                                            <i class="far fa-edit me-1"></i> Edit
                                                        </a>
                                                        @include('admin.users.delete-users')
                                                    </td>
                                                </tr>
                                            @endforeach
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

    <!-- ACTIVITY LOG MODAL -->
    <div class="modal fade" id="viewUserModal" tabindex="-1" aria-labelledby="viewUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Activity Logs</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Activity</h5>
                        </div>
                        <div class="card-body card-body-height">
                            <ul class="activity-feed" id="activityFeed">
                                <li class="feed-item">
                                    <span class="feed-text text-muted">Loading activities...</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function fetchUserActivityLogs(userId) {
            let activityFeed = document.getElementById("activityFeed");
            activityFeed.innerHTML =
                `<li class="feed-item"><span class="feed-text text-muted">Loading activities...</span></li>`;

            fetch(`/user/${userId}/activity-logs`)
                .then(response => response.json())
                .then(data => {
                    activityFeed.innerHTML = "";

                    if (data.error) {
                        activityFeed.innerHTML =
                            `<li class="feed-item"><span class="feed-text text-danger">Failed to load activities.</span></li>`;
                        return;
                    }

                    if (data.activities.length === 0) {
                        activityFeed.innerHTML =
                            `<li class="feed-item"><span class="feed-text text-muted">No recent activities found.</span></li>`;
                        return;
                    }

                    data.activities.forEach(activity => {
                        let listItem = `
                    <li class="feed-item">
                        <div class="feed-date">${new Date(activity.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })}</div>
                        <span class="feed-text">${activity.description}</span>
                    </li>
                `;
                        activityFeed.innerHTML += listItem;
                    });
                })
                .catch(error => {
                    console.error('Error fetching activity logs:', error);
                    activityFeed.innerHTML =
                        `<li class="feed-item"><span class="feed-text text-danger">Error loading activities.</span></li>`;
                });
        }
    </script>

    @include('layout.footerjs')
</body>

</html>
