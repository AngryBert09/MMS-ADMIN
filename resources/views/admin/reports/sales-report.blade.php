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
                            <a href="#" class="btn btn-primary">
                                <i class="fas fa-file-pdf"></i>
                            </a>
                            <a class="btn btn-primary filter-btn" href="javascript:void(0);" id="filter_search">
                                <i class="fas fa-filter"></i>
                            </a>
                            <a class="btn btn-success generate-report" href="javascript:void(0);" id="generate_report">
                                <i class="fas fa-file-alt"></i> Generate Report
                            </a>
                        </div>

                    </div>
                </div>


                <div id="filter_inputs" class="card filter-card">
                    <div class="card-body pb-0">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Slect Date Range</label>
                                    <select class="select">
                                        <option>Select</option>
                                        <option>Today</option>
                                        <option>This Week</option>
                                        <option>This Month</option>
                                        <option>This Quarter</option>
                                        <option>This Year</option>
                                        <option>Previous Week</option>
                                        <option>Previous Month</option>
                                        <option>Previous Quarter</option>
                                        <option>Previous Year</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>From</label>
                                    <div class="cal-icon">
                                        <input class="form-control datetimepicker" type="text">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>To</label>
                                    <div class="cal-icon">
                                        <input class="form-control datetimepicker" type="text">
                                    </div>
                                </div>
                            </div>
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
                                                <th>Date</th>
                                                <th>Category</th>
                                                <th>Sales</th>
                                                <th>Refunded</th>
                                                <th>Discounts</th>
                                                <th>Taxs</th>
                                                <th class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>7 Jan 2021</td>
                                                <td>Accessories</td>
                                                <td>$42</td>
                                                <td>$0</td>
                                                <td>$163</td>
                                                <td>$221</td>
                                                <td class="text-end">$762</td>
                                            </tr>
                                            <tr>
                                                <td>2</td>
                                                <td>28 Feb 2021</td>
                                                <td>Books</td>
                                                <td>$1249</td>
                                                <td>$36</td>
                                                <td>$3</td>
                                                <td>$80</td>
                                                <td class="text-end">$1238</td>
                                            </tr>
                                            <tr>
                                                <td>3</td>
                                                <td>10 Mar 2021</td>
                                                <td>Others</td>
                                                <td>$76</td>
                                                <td>$0</td>
                                                <td>$0</td>
                                                <td>$4</td>
                                                <td class="text-end">$80</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Report Modal -->
        <div class="modal fade" id="salesReportModal" tabindex="-1" aria-labelledby="salesReportModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl"> <!-- Changed to extra-large modal -->
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="salesReportModalLabel">Sales Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;"> <!-- Scrollable content -->
                        <div id="salesReportContent">
                            <p>Generating sales report...</p>
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
                    let reportContent = document.getElementById("salesReportContent");
                    reportContent.innerHTML = "<p>Generating sales report...</p>";

                    let salesModal = new bootstrap.Modal(document.getElementById("salesReportModal"));
                    salesModal.show();

                    fetch(`/api/generate-sales-report`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.report) {
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

                // Copy to clipboard
                document.getElementById("copyReport").addEventListener("click", function() {
                    let text = document.getElementById("salesReportContent").innerText;
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

                    let text = document.getElementById("salesReportContent").innerText;
                    let pageWidth = doc.internal.pageSize.getWidth();

                    doc.setFont("helvetica", "normal");
                    doc.setFontSize(12);
                    doc.text("Sales Performance Report", pageWidth / 2, 20, {
                        align: "center"
                    });
                    doc.setFontSize(10);
                    doc.text(text, 15, 30, {
                        maxWidth: 180,
                        align: "left"
                    });

                    doc.save("sales-report.pdf");
                });
            });
        </script>




    </div>


    @include('layout.footerjs')
</body>

</html>
