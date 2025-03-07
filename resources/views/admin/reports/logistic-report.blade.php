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
                            <h3 class="page-title">Logistic Reports</h3>
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
                                                <th>#Invoice ID</th>
                                                <th>Order Date</th>
                                                <th>Products</th>
                                                <th>Total Amount</th>
                                                <th>Tax Amount</th>
                                                <th>Status</th>
                                                <th class="text-end">Created At</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if (isset($logisticsInvoices) && count($logisticsInvoices) > 0)
                                                @foreach ($logisticsInvoices as $index => $report)
                                                    <tr>
                                                        <td>{{ $report['invoice']['invoice_id'] ?? 'N/A' }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($report['purchase_order']['order_date'])->format('Y-m-d') }}
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-primary btn-sm"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#productModal{{ $index }}">
                                                                View Products
                                                            </button>
                                                        </td>
                                                        <td>${{ number_format($report['invoice']['total_amount'], 2) }}
                                                        </td>
                                                        <td>${{ number_format($report['invoice']['tax_amount'], 2) }}
                                                            ({{ $report['invoice']['tax_rate'] }}%)
                                                        </td>
                                                        <td>
                                                            <span
                                                                class="badge bg-{{ $report['invoice']['status'] == 'Paid' ? 'success' : 'warning' }}">
                                                                {{ ucfirst($report['invoice']['status']) }}
                                                            </span>
                                                        </td>
                                                        <td class="text-end">
                                                            {{ \Carbon\Carbon::parse($report['invoice']['created_at'])->format('Y-m-d H:i') }}
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
                                                                        id="productModalLabel{{ $index }}">
                                                                        Product Details</h5>
                                                                    <button type="button" class="btn-close"
                                                                        data-bs-dismiss="modal"
                                                                        aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <ul class="list-group">
                                                                        @foreach ($report['purchase_order']['products'] as $product)
                                                                            <li class="list-group-item">
                                                                                <strong>{{ $product['name'] }}</strong>
                                                                                (Brand: {{ $product['brand'] }})
                                                                                <br>
                                                                                Qty: {{ $product['quantity'] }} | Sale
                                                                                Price:
                                                                                ${{ number_format($product['sale_price'], 2) }}
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
                                            @else
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted">No logistics
                                                        reports available.</td>
                                                </tr>
                                            @endif
                                        </tbody>

                                    </table>



                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Logistics Report Modal -->
        <div class="modal fade" id="logisticsReportModal" tabindex="-1" aria-labelledby="logisticsReportModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="logisticsReportModalLabel">Logistics Report</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div id="logisticsReportContent">
                            <p>Generating logistics report...</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" id="copyLogisticsReport">Copy to Clipboard</button>
                        <button class="btn btn-success" id="downloadLogisticsReport">Download Report</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById("generate_report").addEventListener("click", function() {
                    let reportContent = document.getElementById("logisticsReportContent");
                    reportContent.innerHTML = "<p>Generating logistics report...</p>";

                    let logisticsModal = new bootstrap.Modal(document.getElementById("logisticsReportModal"));
                    logisticsModal.show();

                    fetch(`/api/analyze-logistics`)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.report && data.report.trim() !== "") {
                                reportContent.innerHTML =
                                    `<pre style="white-space: pre-wrap;">${data.report}</pre>`;
                            } else {
                                reportContent.innerHTML =
                                    `<p class="text-danger">No logistics report available.</p>`;
                            }
                        })
                        .catch(error => {
                            reportContent.innerHTML =
                                `<p class="text-danger">Error fetching report: ${error.message}</p>`;
                            console.error("Error fetching logistics report:", error);
                        });
                });

                // Copy to clipboard
                document.getElementById("copyLogisticsReport").addEventListener("click", function() {
                    let text = document.getElementById("logisticsReportContent").innerText;
                    if (navigator.clipboard) {
                        navigator.clipboard.writeText(text).then(() => {
                            alert("Logistics Report copied to clipboard!");
                        }).catch(err => {
                            alert("Failed to copy report: " + err);
                        });
                    } else {
                        alert("Clipboard access not supported in this browser.");
                    }
                });

                // Download report as PDF
                document.getElementById("downloadLogisticsReport").addEventListener("click", function() {
                    let {
                        jsPDF
                    } = window.jspdf;
                    let doc = new jsPDF();

                    let text = document.getElementById("logisticsReportContent").innerText;
                    let pageWidth = doc.internal.pageSize.getWidth();
                    let margin = 15;
                    let textWidth = pageWidth - margin * 2;

                    doc.setFont("helvetica", "normal");
                    doc.setFontSize(12);
                    doc.text("Logistics Performance Report", pageWidth / 2, 20, {
                        align: "center"
                    });
                    doc.setFontSize(10);

                    let splitText = doc.splitTextToSize(text, textWidth);
                    doc.text(splitText, margin, 30);

                    doc.save("logistics-report.pdf");
                });
            });
        </script>





    </div>


    @include('layout.footerjs')
</body>

</html>
