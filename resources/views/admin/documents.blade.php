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
                            <h3 class="page-title">Documents</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item active">Documents</li>
                            </ul>
                        </div>
                        <div class="col-auto">
                            <a href="javascript:void(0);" class="btn btn-success me-1" data-bs-toggle="modal"
                                data-bs-target="#uploadFileModal">
                                <i class="fas fa-plus"></i> Upload File
                            </a>

                            <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#documentHistoryModal">
                                <i class="fas fa-history"></i> Document History
                            </a>

                            <a href="{{ route('admin.documents') }}"
                                class="btn btn-{{ !request()->is('documents/archives') ? 'primary' : 'outline-primary' }}">
                                Active Documents
                            </a>

                            <a href="{{ route('documents.archives') }}"
                                class="btn btn-{{ request()->is('documents/archives') ? 'primary' : 'outline-primary' }}">
                                Archived
                            </a>
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
                                                <th>File ID</th>
                                                <th>Title</th>
                                                <th>Uploaded By</th>
                                                <th>Size</th>
                                                <th>Department</th>
                                                <th>File Type</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($documents as $doc)
                                                <tr>
                                                    <td><a href="javascript:void(0);">#{{ $doc->id }}</a></td>
                                                    <td>{{ $doc->title }}</td>
                                                    <td>{{ $doc->uploaded_by ?? 'Admin' }}</td>
                                                    <td>{{ number_format($doc->file_size / 1024, 2) }} KB</td>
                                                    <td>{{ $doc->department ?? 'Admin' }}</td>
                                                    <td>{{ strtoupper($doc->file_type) }}</td>
                                                    <td class="text-end">
                                                        <!-- View Button -->
                                                        <button class="btn btn-sm btn-white me-2" data-bs-toggle="modal"
                                                            data-bs-target="#viewFileModal{{ $doc->id }}">
                                                            <i class="fas fa-eye me-1"></i> View
                                                        </button>

                                                        <!-- Download Button -->
                                                        <a class="btn btn-sm btn-white me-2"
                                                            href="{{ route('documents.download', $doc->id) }}">
                                                            <i class="fas fa-download me-1"></i> Download
                                                        </a>

                                                        @if ($isArchive)
                                                            <!-- Restore Button for Archives -->
                                                            <form action="{{ route('documents.restore', $doc->id) }}"
                                                                method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-success me-2"
                                                                    onclick="return confirm('Restore this document?')">
                                                                    <i class="fas fa-undo me-1"></i> Restore
                                                                </button>
                                                            </form>
                                                        @else
                                                            <!-- Delete/Archive Button for Active Documents -->
                                                            <form action="{{ route('documents.delete', $doc->id) }}"
                                                                method="POST" style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('Archive this document?')">
                                                                     Archive
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </td>
                                                </tr>

                                                <!-- Modal for Viewing File (same as before) -->
                                                <div class="modal fade" id="viewFileModal{{ $doc->id }}"
                                                    tabindex="-1"
                                                    aria-labelledby="viewFileModalLabel{{ $doc->id }}"
                                                    aria-hidden="true">
                                                    <!-- ... keep existing modal content ... -->
                                                </div>
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
    <!-- UPLOAD MODAL -->
    <div class="modal fade" id="uploadFileModal" tabindex="-1" aria-labelledby="uploadFileModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadFileModalLabel">Upload Document</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="uploadForm" action="{{ route('documents.upload') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="documentTitle" class="form-label">Title</label>
                            <input type="text" class="form-control" id="documentTitle" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="documentFile" class="form-label">Select File</label>
                            <input type="file" class="form-control" id="documentFile" name="file" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- DOCUMENT HISTORY MODAL -->
    <div class="modal fade" id="documentHistoryModal" tabindex="-1" aria-labelledby="documentHistoryLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="documentHistoryLabel">Document History</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">Document Activity Logs</h5>
                        </div>
                        <div class="card-body card-body-height">
                            <ul class="activity-feed" id="documentHistoryFeed">
                                <li class="feed-item">
                                    <span class="feed-text text-muted">Loading history...</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("documentHistoryModal").addEventListener("show.bs.modal", function() {
                fetch("/document-history") // Ensure this matches your route
                    .then(response => response.json())
                    .then(data => {
                        let activityFeed = document.getElementById("documentHistoryFeed");
                        activityFeed.innerHTML = ""; // Clear previous data

                        if (data.length === 0) {
                            activityFeed.innerHTML = `
                        <li class="feed-item">
                            <span class="feed-text text-muted">No document history found.</span>
                        </li>`;
                            return;
                        }

                        data.forEach(item => {
                            let activityItem = `
                        <li class="feed-item">
                            <div class="feed-date">
                                ${new Date(item.timestamp).toLocaleDateString('en-US', { month: 'short', day: '2-digit' })}
                            </div>
                            <span class="feed-text">
                                <strong>${item.user}</strong> ${item.action} <strong>${item.file_name}</strong>
                            </span>
                        </li>`;
                            activityFeed.innerHTML += activityItem;
                        });
                    })
                    .catch(error => {
                        console.error("Error loading document history:", error);
                        document.getElementById("documentHistoryFeed").innerHTML = `
                    <li class="feed-item">
                        <span class="feed-text text-danger">Failed to load document history.</span>
                    </li>`;
                    });
            });
        });
    </script>


    @include('layout.footerjs')
</body>

</html>
