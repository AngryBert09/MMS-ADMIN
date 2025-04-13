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
                            <h3 class="page-title">Budget Report</h3>
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

                                    <table class="table table-center table-hover datatable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>#Reference</th>
                                                <th>Request Date</th>
                                                <th>Requester</th>
                                                <th>Department</th>
                                                <th>Total Amount</th>
                                                <th>Status</th>
                                                <th>Approval/Decline By</th>
                                                <th>Approval/Decline Date</th>
                                                <th>Reason (if Declined)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="budget_table_body">
                                            @foreach ($budgets as $budget)
                                                <tr>
                                                    <td>{{ $budget['reference_number'] }}</td>
                                                    <td>{{ $budget['date_of_request'] }}</td>
                                                    <td>{{ $budget['requested_by'] }}</td>
                                                    <td>{{ $budget['department'] }}</td>
                                                    <td>₱{{ number_format(array_sum(array_column($budget['budget_details'], 'amount')), 2) }}
                                                    </td>
                                                    <td>
                                                        <span
                                                            class="badge {{ $budget['status'] == 'approved' ? 'bg-success' : 'bg-danger' }}">
                                                            {{ ucfirst($budget['status']) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if (isset($budget['approval']))
                                                            {{ $budget['approval']['approved_by'] }}
                                                        @elseif(isset($budget['decline']))
                                                            {{ $budget['decline']['declined_by'] }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (isset($budget['approval']))
                                                            {{ $budget['approval']['date'] }}
                                                        @elseif(isset($budget['decline']))
                                                            {{ $budget['decline']['date'] }}
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $budget['decline']['reason'] ?? 'N/A' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>

                                    <!-- JavaScript for Date Filtering -->
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            const applyFilter = document.getElementById('applyFilter');
                                            const resetFilter = document.getElementById('resetFilter');
                                            const startDate = document.getElementById('startDate');
                                            const endDate = document.getElementById('endDate');
                                            const rows = document.querySelectorAll('#budget_table_body tr');

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

        <!-- Budget Report Modal -->
        <div class="modal fade" id="budgetReportModal" tabindex="-1" aria-labelledby="budgetReportModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl"> <!-- Extra-large modal -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="budgetReportModalLabel">Budget Report</h5> <!-- Changed title -->
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;"> <!-- Scrollable content -->
                        <div class="mb-3">
                            <label for="customPrompt" class="form-label">Enter a custom prompt for AI:</label>
                            <textarea class="form-control" id="customPrompt" rows="3"
                                placeholder="e.g., Give me a summary focused on cost-saving trends..."></textarea>
                            <button class="btn btn-outline-primary mt-2" id="generateWithPrompt">Ask AI</button>
                        </div>
                        <hr />
                        <div id="budgetReportContent">
                            <p>Generating budget report...</p>
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
                const budgetModal = new bootstrap.Modal(document.getElementById("budgetReportModal"));
                const reportContent = document.getElementById("budgetReportContent");

                document.getElementById("generate_report").addEventListener("click", function() {
                    reportContent.innerHTML = "<p>Generating budget report...</p>";
                    budgetModal.show();

                    fetch('/api/analyze-budgets')
                        .then(response => response.json())
                        .then(data => {
                            if (data.analysis) {
                                reportContent.innerHTML =
                                    `<pre style="white-space: pre-wrap;">${data.analysis}</pre>`;
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

                // Generate report with custom prompt
                document.getElementById("generateWithPrompt").addEventListener("click", function() {
                    const promptText = document.getElementById("customPrompt").value.trim();

                    if (!promptText) {
                        alert("Please enter a prompt before generating.");
                        return;
                    }

                    reportContent.innerHTML = "<p>Generating report please wait...</p>";
                    budgetModal.show();

                    fetch('/api/analyze-budgets-with-prompt', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                prompt: promptText,
                                existing_report: reportContent.innerText
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.report) {
                                reportContent.innerHTML =
                                    `<pre style="white-space: pre-wrap;">${data.report}</pre>`;
                            } else {
                                reportContent.innerHTML =
                                    `<p class="text-danger">Failed to generate report with prompt.</p>`;
                            }
                        })
                        .catch(error => {
                            reportContent.innerHTML = `<p class="text-danger">Error: ${error.message}</p>`;
                        });
                });

                // Copy to clipboard
                document.getElementById("copyReport").addEventListener("click", function() {
                    let text = reportContent.innerText;
                    navigator.clipboard.writeText(text).then(() => {
                        alert("Report copied to clipboard!");
                    }).catch(err => {
                        alert("Failed to copy report: " + err);
                    });
                });

                // Download report as PDF
                document.getElementById("downloadReport").addEventListener("click", function() {
                    let {
                        jsPDF
                    } = window.jspdf;
                    let doc = new jsPDF();
                    let text = reportContent.innerText;
                    let pageWidth = doc.internal.pageSize.getWidth();

                    doc.setFont("helvetica", "normal");
                    doc.setFontSize(12);
                    doc.text("Budget Performance Report", pageWidth / 2, 20, {
                        align: "center"
                    });
                    doc.setFontSize(10);
                    doc.text(text, 15, 30, {
                        maxWidth: 180,
                        align: "left"
                    });

                    doc.save("budget-report.pdf");
                });
            });
        </script>





    </div>


    @include('layout.footerjs')
</body>

</html>
