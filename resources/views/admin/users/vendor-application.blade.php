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
                                <a href="#" class="btn btn-primary px-4" data-bs-toggle="modal"
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
