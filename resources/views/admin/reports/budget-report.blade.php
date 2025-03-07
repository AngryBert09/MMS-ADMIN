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
                                        <tbody>
                                            @foreach ($budgets as $budget)
                                                <tr>
                                                    <td>{{ $budget['reference_number'] }}</td>
                                                    <td>{{ $budget['date_of_request'] }}</td>
                                                    <td>{{ $budget['requested_by'] }}</td>
                                                    <td>{{ $budget['department'] }}</td>
                                                    <td>${{ number_format(array_sum(array_column($budget['budget_details'], 'amount')), 2) }}
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
                document.getElementById("generate_report").addEventListener("click", function() {
                    let reportContent = document.getElementById("budgetReportContent");
                    reportContent.innerHTML = "<p>Generating budget report...</p>";

                    let budgetModal = new bootstrap.Modal(document.getElementById("budgetReportModal"));
                    budgetModal.show();

                    fetch('/api/analyze-budgets') // Updated API endpoint
                        .then(response => response.json())
                        .then(data => {
                            if (data.analysis) { // Updated key
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

                // Copy to clipboard
                document.getElementById("copyReport").addEventListener("click", function() {
                    let text = document.getElementById("budgetReportContent").innerText;
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
                    let text = document.getElementById("budgetReportContent").innerText;
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

                    doc.save("budget-report.pdf"); // Updated filename
                });
            });
        </script>




    </div>


    @include('layout.footerjs')
</body>

</html>
