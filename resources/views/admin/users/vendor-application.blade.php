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
                                <h4 class="card-title">Vendor Applications</h4>
                                <!-- Invite Vendor Button -->
                                <a href="#" class="btn btn-warning px-4" data-bs-toggle="modal"
                                    data-bs-target="#inviteVendorModal">
                                    <i class="fas fa-plus"></i> Invite Vendor
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-stripped table-center table-hover datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Company Name</th>
                                                <th>Email</th>
                                                <th>Address</th>
                                                <th>Documents</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($vendors as $vendor)
                                                <tr>
                                                    <td>{{ $vendor['companyName'] }}</td>
                                                    <td>{{ $vendor['email'] }}</td>
                                                    <td>{{ $vendor['address'] ?? 'N/A' }}</td>
                                                    <!-- Address field is not provided, use 'N/A' as fallback -->
                                                    <td>
                                                        <!-- Check if the documents exist and display links -->

                                                        <!-- Check if the documents exist and display links -->
                                                        @if ($vendor['businessRegistration'])
                                                            <a href="#" class="document-link"
                                                                data-file="{{ asset('storage/app/public/' . $vendor['businessRegistration']) }}"
                                                                data-type="pdf">Business Registration</a><br>
                                                        @endif
                                                        @if ($vendor['mayorsPermit'])
                                                            <a href="#" class="document-link"
                                                                data-file="{{ asset('storage/' . $vendor['mayorsPermit']) }}"
                                                                data-type="pdf">Mayors Permit</a><br>
                                                        @endif
                                                        @if ($vendor['taxIdentificationNumber'])
                                                            <a href="#" class="document-link"
                                                                data-file="{{ asset('storage/' . $vendor['taxIdentificationNumber']) }}"
                                                                data-type="pdf">Tax Identification Number</a><br>
                                                        @endif
                                                        @if ($vendor['proofOfIdentity'])
                                                            <a href="#" class="document-link"
                                                                data-file="{{ asset('storage/' . $vendor['proofOfIdentity']) }}"
                                                                data-type="pdf">Proof of Identity</a>
                                                        @endif

                                                    </td>
                                                    <td>{{ $vendor['status'] }}</td>
                                                    <td class="text-end">
                                                        @if ($vendor['status'] === 'Pending')
                                                            <!-- Approve Button -->
                                                            <form
                                                                action="{{ url('/vendor/' . $vendor['id'] . '/update-status/Approved') }}"
                                                                method="POST" style="display: inline;">
                                                                @csrf
                                                                @method('PUT')
                                                                <button type="submit"
                                                                    class="btn btn-success">Approve</button>
                                                            </form>
                                                            <!-- Reject Button -->
                                                            <button type="button" class="btn btn-danger"
                                                                data-bs-toggle="modal" data-bs-target="#rejectModal"
                                                                onclick="setRejectFormAction({{ $vendor['id'] }})">
                                                                Reject
                                                            </button>
                                                        @elseif ($vendor['status'] === 'Approved')
                                                            <!-- View Button -->
                                                            <a href="{{ url('/vendor/' . $vendor['id']) }}"
                                                                class="btn btn-primary">View</a>
                                                        @elseif ($vendor['status'] === 'Rejected')
                                                            <!-- No actions for rejected vendors -->
                                                            <span class="text-muted">No actions available</span>
                                                        @endif
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

    <!-- Modal for displaying documents -->
    <div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentModalLabel">Document Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="documentPreviewContainer" class="text-center"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript to Handle Document Preview -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.document-link').forEach(function(link) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    const fileUrl = link.getAttribute('data-file');
                    const fileExtension = fileUrl.split('.').pop().toLowerCase();

                    let previewContent = '';

                    if (fileExtension === 'pdf') {
                        previewContent =
                            `<iframe src="${fileUrl}" width="100%" height="500px"></iframe>`;
                    } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileExtension)) {
                        previewContent =
                            `<img src="${fileUrl}" alt="Document Image" style="max-width: 100%; height: auto;">`;
                    } else {
                        previewContent =
                            `<p>Preview not available. <a href="${fileUrl}" target="_blank">Download File</a></p>`;
                    }

                    document.getElementById('documentPreviewContainer').innerHTML = previewContent;

                    // Show the modal
                    new bootstrap.Modal(document.getElementById('documentModal')).show();
                });
            });
        });
    </script>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="rejectForm" action="{{ url('/vendor/' . $vendor['id'] . '/update-status/Rejected') }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="rejectReason" class="form-label">Reason for
                                Rejection</label>
                            <textarea class="form-control" id="rejectReason" name="reject_reason" required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Reject</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setRejectFormAction(vendorId) {
            document.getElementById('rejectForm').action = `/vendor/${vendorId}/update-status/Rejected`;
        }
    </script>


    <!-- Invite Vendor Modal -->
    <div class="modal fade" id="inviteVendorModal" tabindex="-1" aria-labelledby="inviteVendorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h5 class="modal-title" id="inviteVendorModalLabel">Invite Vendor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Body with Form -->
                <div class="modal-body">
                    <form action="{{ route('vendors.invite') }}" method="POST" id="vendorInviteForm">
                        @csrf
                        <!-- Vendor Name -->
                        <div class="mb-3">
                            <label for="name" class="form-label">Vendor Name</label>
                            <input type="text" name="name" class="form-control" id="vendorName" required>
                        </div>

                        <!-- Vendor Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Vendor Email</label>
                            <input type="email" name="email" class="form-control" id="vendorEmail" required>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Send Invitation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('vendorInviteForm').addEventListener('submit', function(event) {
            const name = document.getElementById('vendorName').value.trim();
            const email = document.getElementById('vendorEmail').value.trim();

            if (!name || !email) {
                event.preventDefault();
                alert('Please fill in all fields.');
            }
        });
    </script>



    @include('layout.footerjs')
</body>

</html>
