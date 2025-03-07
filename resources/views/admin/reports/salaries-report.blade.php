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
                            <h3 class="page-title">Employee Salaries</h3>
                            <ul class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.html">Dashboard</a></li>
                                <li class="breadcrumb-item active">Reports</li>
                            </ul>
                        </div>
                        <div class="col-auto">
                            {{-- <a class="btn btn-success generate-report" href="javascript:void(0);" id="generate_report">
                                <i class="fas fa-file-alt"></i> Generate Report
                            </a> --}}
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
                                                <th>#</th>
                                                <th>Employee Name</th>
                                                <th>Employee ID</th>
                                                <th>Department</th>
                                                <th>Employer</th>
                                                <th>Contact</th>
                                                <th>Pay Period</th>
                                                <th>Payment Date</th>
                                                <th>Base Salary ($)</th>
                                                <th>Overtime ($)</th>
                                                <th>Bonuses ($)</th>
                                                <th>Gross Earnings ($)</th>
                                                <th>Total Deductions ($)</th>
                                                <th>Net Pay ($)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($payrolls as $index => $payroll)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $payroll['employee_name'] }}</td>
                                                    <td>{{ $payroll['employee_id'] }}</td>
                                                    <td>{{ $payroll['department'] }}</td>
                                                    <td>{{ $payroll['employer'] }}</td>
                                                    <td>{{ $payroll['contact'] }}</td>
                                                    <td>{{ ucfirst($payroll['pay_period']) }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($payroll['payment_date'])->format('M d, Y') }}
                                                    </td>
                                                    <td>${{ number_format($payroll['base_salary'], 2) }}</td>
                                                    <td>${{ number_format($payroll['overtime'], 2) }}</td>
                                                    <td>${{ number_format($payroll['bonuses'], 2) }}</td>
                                                    <td>${{ number_format($payroll['gross_earnings'], 2) }}</td>
                                                    <td>${{ number_format($payroll['total_deductions'], 2) }}</td>
                                                    <td>
                                                        <strong class="text-success">
                                                            ${{ number_format($payroll['net_pay'], 2) }}
                                                        </strong>
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

        {{-- <script>
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
 --}}



    </div>


    @include('layout.footerjs')
</body>

</html>
