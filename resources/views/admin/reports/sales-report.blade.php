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



                <div class="row">
                    <div class="col-sm-12">
                        <div class="card card-table">
                            <div class="card-body">
                                <div class="table-responsive">
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
                                                <tr>
                                                    <td>{{ $sale['id'] }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($sale['timestamp'])->format('Y-m-d H:i') }}
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                            data-bs-target="#productModal{{ $index }}">
                                                            View Products
                                                        </button>
                                                    </td>
                                                    <td>${{ number_format($sale['total_sum'], 2) }}</td>
                                                    <td>${{ number_format($sale['earnings'], 2) }}</td>
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
                                                                            ${{ number_format($item['regular_price'], 2) }}<br>
                                                                            Sale Price:
                                                                            ${{ number_format($item['sale_price'], 2) }}
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
