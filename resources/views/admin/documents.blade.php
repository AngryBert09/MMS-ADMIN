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
                                                    <td>{{ $doc->uploaded_by ?? 'Unknown' }}</td>
                                                    <td>{{ number_format($doc->file_size / 1024, 2) }} KB</td>
                                                    <td>{{ $doc->department ?? 'Unknown' }}</td>
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

                                                        <!-- Delete Form -->
                                                        <form action="{{ route('documents.delete', $doc->id) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-white"
                                                                onclick="return confirm('Delete this document?')">
                                                                <i class="far fa-trash-alt me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>

                                                <!-- Modal for Viewing File -->
                                                <div class="modal fade" id="viewFileModal{{ $doc->id }}"
                                                    tabindex="-1"
                                                    aria-labelledby="viewFileModalLabel{{ $doc->id }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="viewFileModalLabel{{ $doc->id }}">View
                                                                    File: {{ $doc->title }}</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                @if (in_array($doc->file_type, ['pdf', 'jpg', 'jpeg', 'png', 'gif']))
                                                                    <!-- Display PDF or Images -->
                                                                    @if ($doc->file_type === 'pdf')
                                                                        <iframe
                                                                            src="{{ route('documents.view', $doc->id) }}"
                                                                            width="100%" height="500px"></iframe>
                                                                    @else
                                                                        <img src="{{ route('documents.view', $doc->id) }}"
                                                                            alt="{{ $doc->title }}"
                                                                            class="img-fluid">
                                                                    @endif
                                                                @else
                                                                    <!-- Display plain text or unsupported file types -->
                                                                    <p>File type not supported for preview.</p>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
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



    @include('layout.footerjs')
</body>

</html>
