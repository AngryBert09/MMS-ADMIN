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
                            <h3 class="page-title">Sales Report</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item active">Reports</li>
                            </ul>
                        </div>
                        <div class="col-auto">
                            <a class="btn btn-success generate-report" href="javascript:void(0);" id="generate_report">
                                <i class="fas fa-file-alt"></i> Generate Report
                            </a>
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
                                    <!-- Date Filter Controls -->


                                    <table class="table table-center table-hover datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#ID</th>
                                                <th>Order Date</th>
                                                <th>Products</th>
                                                <th>Total Amount</th>
                                                <th>Earnings</th>
                                                <th class="text-end">Created At</th>
                                            </tr>
                                        </thead>
                                        <tbody id="sales_table_body">
                                            @foreach ($sales as $index => $sale)
                                                <tr
                                                    data-date="{{ \Carbon\Carbon::parse($sale['timestamp'])->format('Y-m-d') }}">
                                                    <td>{{ $sale['id'] }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($sale['timestamp'])->format('Y-m-d H:i') }}
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                            data-bs-target="#productModal{{ $index }}">
                                                            View Products
                                                        </button>
                                                    </td>
                                                    <td>₱{{ number_format($sale['total_sum'], 2) }}</td>
                                                    <td>₱{{ number_format($sale['earnings'], 2) }}</td>
                                                    <td class="text-end">
                                                        {{ \Carbon\Carbon::parse($sale['timestamp'])->format('Y-m-d H:i') }}
                                                    </td>
                                                </tr>

                                                <!-- Product Modal -->
                                                <div class="modal fade" id="productModal{{ $index }}"
                                                    tabindex="-1"
                                                    aria-labelledby="productModalLabel{{ $index }}"
                                                    aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title"
                                                                    id="productModalLabel{{ $index }}">Product
                                                                    Details</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <ul class="list-group">
                                                                    @foreach ($sale['cart_items'] as $item)
                                                                        <li class="list-group-item">
                                                                            <strong>{{ $item['product_name'] }}</strong><br>
                                                                            Regular Price:
                                                                            ₱{{ number_format($item['regular_price'], 2) }}<br>
                                                                            Sale Price:
                                                                            ₱{{ number_format($item['sale_price'], 2) }}
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
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

                                <!-- JavaScript for Date Filtering -->
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        const applyFilter = document.getElementById('applyFilter');
                                        const resetFilter = document.getElementById('resetFilter');
                                        const startDate = document.getElementById('startDate');
                                        const endDate = document.getElementById('endDate');
                                        const rows = document.querySelectorAll('#sales_table_body tr');

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

        <!-- Sales Report Modal -->
        <div class="modal fade" id="salesReportModal" tabindex="-1" aria-labelledby="salesReportModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="salesReportModalLabel">Sales Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div id="salesReportContent">
                            <p>Generating sales report...</p>
                        </div>

                        <!-- AI Analysis Section -->
                        <div class="mt-4 border-top pt-3">
                            <h6>Create report (AI) <span class="badge bg-info">Powered by AI</span></h6>
                            <div class="mb-3">
                                <textarea class="form-control" id="aiPromptInput" rows="2"
                                    placeholder="Ask AI to analyze the report (e.g., 'What are the key trends?', 'Suggest improvements')"></textarea>
                            </div>
                            <button class="btn btn-info mb-3" id="askAIButton">Ask AI</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="copyReport">Copy to Clipboard</button>
                        <button class="btn btn-success" id="downloadReport">Download Report</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                let currentReportType = 'auto'; // 'auto' or 'prompt'
                let generatedReport = '';

                document.getElementById("generate_report").addEventListener("click", function() {
                    let reportContent = document.getElementById("salesReportContent");
                    reportContent.innerHTML = "<p>Generating sales report...</p>";
                    document.getElementById("aiPromptInput").value = ''; // Clear prompt input
                    currentReportType = 'auto';

                    let salesModal = new bootstrap.Modal(document.getElementById("salesReportModal"));
                    salesModal.show();

                    fetch(`/api/generate-sales-report`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.report) {
                                generatedReport = data.report;
                                reportContent.innerHTML =
                                    `<pre style="white-space: pre-wrap;">${data.report}</pre>`;
                            } else {
                                reportContent.innerHTML =
                                    `<p class="text-danger">Failed to generate report.</p>`;
                            }
                        })
                        .catch(error => {
                            reportContent.innerHTML =
                                `<p class="text-danger">Error fetching report: ${error.message}</p>`;
                        });
                });

                // AI Analysis functionality
                document.getElementById('askAIButton').addEventListener('click', function() {
                    const prompt = document.getElementById('aiPromptInput').value;
                    const reportContent = document.getElementById('salesReportContent');

                    if (!prompt) {
                        alert('Please enter a question or prompt for the AI.');
                        return;
                    }

                    // Show loading state
                    reportContent.innerHTML =
                        '<div class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p>Analyzing with AI...</p></div>';
                    currentReportType = 'prompt';

                    // Send both the prompt and existing report data to backend
                    fetch('/api/analyzeSalesWithPrompt', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                prompt: prompt,
                                existing_report: generatedReport
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.report) {
                                reportContent.innerHTML =
                                    `<pre style="white-space: pre-wrap;">${data.report}</pre>`;
                            } else {
                                reportContent.innerHTML =
                                    `<p class="text-danger">Failed to generate analysis.</p>`;
                            }
                        })
                        .catch(error => {
                            reportContent.innerHTML =
                                `<p class="text-danger">Error analyzing report: ${error.message}</p>`;
                        });
                });

                // Copy to clipboard - handles both report types
                document.getElementById("copyReport").addEventListener("click", function() {
                    let text = document.getElementById("salesReportContent").innerText;
                    navigator.clipboard.writeText(text).then(() => {
                        alert("Report copied to clipboard!");
                    }).catch(err => {
                        alert("Failed to copy report: " + err);
                    });
                });

                // Download report as PDF - handles both report types
                document.getElementById("downloadReport").addEventListener("click", function() {
                    let {
                        jsPDF
                    } = window.jspdf;
                    let doc = new jsPDF();

                    let text = document.getElementById("salesReportContent").innerText;
                    let pageWidth = doc.internal.pageSize.getWidth();

                    doc.setFont("helvetica", "normal");
                    doc.setFontSize(12);
                    doc.text(currentReportType === 'auto' ? "Sales Performance Report" : "AI Sales Analysis",
                        pageWidth / 2, 20, {
                            align: "center"
                        });
                    doc.setFontSize(10);
                    doc.text(text, 15, 30, {
                        maxWidth: 180,
                        align: "left"
                    });

                    doc.save(currentReportType === 'auto' ? "sales-report.pdf" : "sales-analysis.pdf");
                });
            });
        </script>


    </div>


    @include('layout.footerjs')
</body>

</html>
