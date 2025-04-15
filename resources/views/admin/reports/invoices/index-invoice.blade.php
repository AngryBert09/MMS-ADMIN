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
                            <h3 class="page-title">Invoices</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item active">Invoices</li>
                            </ul>
                        </div>
                        {{-- <div class="col-auto">
                            <a href="invoices.html" class="invoices-links active">
                                <i data-feather="list"></i>
                            </a>
                            <a href="invoice-grid.html" class="invoices-links">
                                <i data-feather="grid"></i>
                            </a>
                        </div> --}}
                    </div>
                </div>


                <div class="card report-card">
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-md-12">
                                <ul class="app-listing d-flex justify-content-end">
                                    <li>
                                        <div class="report-btn">
                                            <a href="javascript:void(0);" class="btn" id="generateReport">
                                                <img src="assets/img/icons/invoices-icon5.svg" alt=""
                                                    class="me-2"> Generate report
                                            </a>
                                        </div>
                                    </li>
                                </ul>

                            </div>
                        </div>
                    </div>
                </div>




                <div class="row">
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card inovices-card">
                            <div class="card-body">
                                <div class="inovices-widget-header">
                                    <span class="inovices-widget-icon">
                                        <img src="assets/img/icons/invoices-icon1.svg" alt="">
                                    </span>
                                    <div class="inovices-dash-count">
                                        <div class="inovices-amount">
                                            ₱{{ number_format(collect($invoices)->sum('totalAmount'), 2) }}</div>
                                    </div>
                                </div>
                                <p class="inovices-all">All Invoices <span>{{ count($invoices) }}</span></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card inovices-card">
                            <div class="card-body">
                                <div class="inovices-widget-header">
                                    <span class="inovices-widget-icon">
                                        <img src="assets/img/icons/invoices-icon2.svg" alt="">
                                    </span>
                                    <div class="inovices-dash-count">
                                        <div class="inovices-amount">
                                            ₱{{ number_format(collect($invoices)->where('status', 'paid')->sum('totalAmount'), 2) }}
                                        </div>
                                    </div>
                                </div>
                                <p class="inovices-all">Paid Invoices
                                    <span>{{ collect($invoices)->where('status', 'paid')->count() }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card inovices-card">
                            <div class="card-body">
                                <div class="inovices-widget-header">
                                    <span class="inovices-widget-icon">
                                        <img src="assets/img/icons/invoices-icon3.svg" alt="">
                                    </span>
                                    <div class="inovices-dash-count">
                                        <div class="inovices-amount">
                                            ₱{{ number_format(collect($invoices)->where('status', 'unpaid')->sum('totalAmount'), 2) }}
                                        </div>
                                    </div>
                                </div>
                                <p class="inovices-all">Unpaid Invoices
                                    <span>{{ collect($invoices)->where('status', 'unpaid')->count() }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card inovices-card">
                            <div class="card-body">
                                <div class="inovices-widget-header">
                                    <span class="inovices-widget-icon">
                                        <img src="assets/img/icons/invoices-icon4.svg" alt="">
                                    </span>
                                    <div class="inovices-dash-count">
                                        <div class="inovices-amount">
                                            ₱{{ number_format(collect($invoices)->where('status', 'cancelled')->sum('totalAmount'), 2) }}
                                        </div>
                                    </div>
                                </div>
                                <p class="inovices-all">Cancelled Invoices
                                    <span>{{ collect($invoices)->where('status', 'cancelled')->count() }}</span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-3 mt-3 g-3">
                    <div class="col-md-3">
                        <label for="startDate" class="form-label">From Date</label>
                        <input type="date" class="form-control" id="startDate">
                    </div>
                    <div class="col-md-3">
                        <label for="endDate" class="form-label">To Date</label>
                        <input type="date" class="form-control" id="endDate">
                    </div>
                    <div class="col-md-4 align-self-end">
                        <div class="btn-group" role="group">
                            <button class="btn btn-primary" id="applyFilter">
                                <i class="fas fa-filter me-1"></i> Apply Filter
                            </button>
                            <button class="btn btn-outline-secondary" id="resetFilter">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="card card-table">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Invoice ID</th>
                                                <th>Invoice Number</th>
                                                <th>Created on</th>
                                                <th>Invoice to</th>
                                                <th>Amount</th>
                                                <th>Due date</th>
                                                <th>Status</th>
                                                <th class="text-end">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="invoice_table_body">
                                            @forelse ($invoices as $invoice)
                                                <tr>
                                                    <td>
                                                        <label class="custom_check">
                                                            <input type="checkbox" name="invoice">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                        <a href="{{ url('view-invoice/' . $invoice['invoiceId']) }}"
                                                            class="invoice-link">
                                                            {{ $invoice['invoiceId'] }}
                                                        </a>
                                                    </td>
                                                    <td> {{ $invoice['invoiceNumber'] }}</td>
                                                    <!-- Replace with actual category if available -->
                                                    <td>{{ Carbon\Carbon::parse($invoice['invoiceDate'])->format('d M Y') }}
                                                    </td>
                                                    <td>
                                                        GREATWALL ARTS
                                                    </td>
                                                    <td class="text-primary">
                                                        ₱{{ number_format($invoice['totalAmount'], 2) }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($invoice['dueDate'])->format('d M Y') }}
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge
                                                            @if ($invoice['status'] == 'paid') bg-success-light
                                                            @elseif($invoice['status'] == 'unpaid') bg-danger-light
                                                            @else bg-warning-light @endif">
                                                            {{ ucfirst($invoice['status']) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="dropdown dropdown-action">
                                                            <a href="#" class="action-icon dropdown-toggle"
                                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-ellipsis-v"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-end">
                                                                <a class="dropdown-item"
                                                                    href="{{ route('invoices.show', $invoice['invoiceId']) }}">
                                                                    <i class="far fa-eye me-2"></i>View
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">No invoices found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>


                                    <!-- JavaScript for Date Filtering -->
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const applyFilter = document.getElementById('applyFilter');
                                            const resetFilter = document.getElementById('resetFilter');
                                            const startDate = document.getElementById('startDate');
                                            const endDate = document.getElementById('endDate');
                                            const rows = document.querySelectorAll('#invoice_table_body tr');

                                            // Set default dates (last 30 days)
                                            const today = new Date();
                                            const thirtyDaysAgo = new Date();
                                            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);

                                            startDate.valueAsDate = thirtyDaysAgo;
                                            endDate.valueAsDate = today;

                                            // Apply filter function
                                            function filterByDate() {
                                                const start = new Date(startDate.value);
                                                const end = new Date(endDate.value);
                                                end.setHours(23, 59, 59); // Include entire end day

                                                rows.forEach(row => {
                                                    const rowDate = new Date(row.getAttribute('data-date'));
                                                    if ((!startDate.value || rowDate >= start) &&
                                                        (!endDate.value || rowDate <= end)) {
                                                        row.style.display = '';
                                                    } else {
                                                        row.style.display = 'none';
                                                    }
                                                });
                                            }

                                            // Initial filter on page load
                                            filterByDate();

                                            // Event listeners
                                            applyFilter.addEventListener('click', filterByDate);

                                            resetFilter.addEventListener('click', function() {
                                                startDate.value = '';
                                                endDate.value = '';
                                                filterByDate();
                                            });

                                            // Optional: Auto-apply filter when dates change
                                            startDate.addEventListener('change', filterByDate);
                                            endDate.addEventListener('change', filterByDate);
                                        });
                                    </script>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Invoice Report Modal -->
        <div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl"> <!-- Enlarged modal -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="reportModalLabel">Invoice Analysis Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">


                        <div id="reportContent"
                            class="d-flex flex-column align-items-center justify-content-center text-center"
                            style="min-height: 300px;">
                            <p>Analyzing invoices...</p>
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <hr />
                        <div class="mb-3">
                            <label for="customInvoicePrompt" class="form-label">Customize Invoice Report
                                (optional)</label>
                            <textarea class="form-control" id="customInvoicePrompt" rows="3"
                                placeholder="e.g., Focus on unpaid invoices and vendor delays..."></textarea>
                            <button class="btn btn-warning mt-2" id="sendInvoicePrompt">Ask AI</button>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" id="copyReport">Copy</button>
                        <button class="btn btn-primary" id="downloadPDF">Download PDF</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Include jsPDF Library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script>
            document.getElementById('copyReport').addEventListener('click', function() {
                let text = document.getElementById('reportContent').innerText;
                navigator.clipboard.writeText(text).then(() => {
                    alert("Report copied to clipboard!");
                });
            });

            document.getElementById('downloadPDF').addEventListener('click', function() {
                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF();
                let content = document.getElementById('reportContent').innerText;

                // Set Title
                doc.setFont("times", "bold");
                doc.setFontSize(16);
                doc.text("INVOICE REPORT", 105, 20, {
                    align: "center"
                });

                // Set Report Content
                doc.setFont("times", "normal");
                doc.setFontSize(12);
                doc.text(content, 10, 40, {
                    maxWidth: 180
                });

                // Save PDF
                doc.save("Invoice_Report.pdf");
            });
        </script>

        <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

        <script>
            $(document).ready(function() {
                $("#generateReport").click(function() {
                    $("#reportModal").modal("show"); // Show modal

                    $.ajax({
                        url: "{{ route('admin.invoices.analyze') }}",
                        type: "GET",
                        beforeSend: function() {
                            $("#reportContent").html(`
                    <p>Analyzing invoices...</p>
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                `);
                        },
                        success: function(response) {
                            $("#reportContent").html(`<p>${response.analysis}</p>`);
                        },
                        error: function() {
                            $("#reportContent").html(
                                "<p class='text-danger'>Failed to analyze invoices.</p>");
                        },
                    });
                });
            });

            $('#sendInvoicePrompt').click(function() {
                const prompt = $('#customInvoicePrompt').val();

                if (!prompt.trim()) return alert("Please enter a custom prompt");

                $("#reportContent").html(
                    `<p>Analyzing with your prompt...</p><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>`
                );

                $.ajax({
                    url: "{{ route('admin.invoices.analyze.custom') }}",
                    type: "POST",
                    data: {
                        custom_prompt: prompt,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $("#reportContent").html(`<p>${response.analysis}</p>`);
                    },
                    error: function() {
                        $("#reportContent").html("<p class='text-danger'>Custom analysis failed.</p>");
                    }
                });
            });
        </script>
    </div>


    @include('layout.footerjs')
</body>

</html>
