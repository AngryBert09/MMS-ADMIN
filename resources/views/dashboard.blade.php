<!DOCTYPE html>
<html lang="en">
@include('layout.headerAssets')

<body class="nk-body bg-lighter npc-default has-sidebar no-touch nk-nio-theme">
    <div class="main-wrapper">
        @include('layout.navbar')
        @include('layout.sidebar')


        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="row">
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <span class="dash-widget-icon bg-1">
                                        <i class="fas fa-dollar-sign"></i>
                                    </span>
                                    <div class="dash-count">
                                        <div class="dash-title">Amount Due</div>
                                        <div class="dash-counts">
                                            <p>1,642</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress progress-sm mt-3">
                                    <div class="progress-bar bg-5" role="progressbar" style="width: 75%"
                                        aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="text-muted mt-3 mb-0"><span class="text-danger me-1"><i
                                            class="fas fa-arrow-down me-1"></i>1.15%</span> since last week</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <span class="dash-widget-icon bg-2">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    <div class="dash-count">
                                        <div class="dash-title">Customers</div>
                                        <div class="dash-counts">
                                            <p>3,642</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress progress-sm mt-3">
                                    <div class="progress-bar bg-6" role="progressbar" style="width: 65%"
                                        aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="text-muted mt-3 mb-0"><span class="text-success me-1"><i
                                            class="fas fa-arrow-up me-1"></i>2.37%</span> since last week</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <span class="dash-widget-icon bg-3">
                                        <i class="fas fa-file-alt"></i>
                                    </span>
                                    <div class="dash-count">
                                        <div class="dash-title">Invoices</div>
                                        <div class="dash-counts">
                                            <p id="invoiceCount">Loading...</p> <!-- Placeholder -->
                                        </div>
                                    </div>
                                </div>
                                <div class="progress progress-sm mt-3">
                                    <div class="progress-bar bg-7" id="invoiceProgress" role="progressbar"
                                        style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    </div>
                                </div>
                                <p class="text-muted mt-3 mb-0">
                                    <span class="text-success me-1">
                                        <i class="fas fa-arrow-up me-1"></i><span id="invoicePercentage">0%</span>
                                    </span> since last week
                                </p>
                            </div>
                        </div>
                    </div>

                    <script>
                        function loadInvoiceCount() {
                            $.ajax({
                                url: "/invoices/count",
                                type: "GET",
                                success: function(response) {
                                    if (response.invoiceCount !== undefined) {
                                        let count = response.invoiceCount;
                                        let lastWeekCount = response.lastWeekCount || 0; // Get last week's count from API
                                        let percentageChange = 0;

                                        if (lastWeekCount > 0) {
                                            percentageChange = ((count - lastWeekCount) / lastWeekCount) * 100;
                                        }

                                        let progress = Math.min((count / 2000) * 100, 100); // Assume 2000 as max for the bar

                                        $("#invoiceCount").text(count.toLocaleString()); // Format number
                                        $("#invoiceProgress").css("width", progress + "%").attr("aria-valuenow", progress);
                                        $("#invoicePercentage").text(percentageChange.toFixed(2) + "%");
                                    } else {
                                        $("#invoiceCount").text("0");
                                    }
                                },
                                error: function() {
                                    $("#invoiceCount").text("Error");
                                }
                            });
                        }

                        // Load invoice count on page load
                        $(document).ready(function() {
                            loadInvoiceCount();
                        });
                    </script>


                    <div class="col-xl-3 col-sm-6 col-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="dash-widget-header">
                                    <span class="dash-widget-icon bg-4">
                                        <i class="far fa-file"></i>
                                    </span>
                                    <div class="dash-count">
                                        <div class="dash-title">Documents</div>
                                        <div class="dash-counts">
                                            <p id="documentCount">0</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="progress progress-sm mt-3">
                                    <div id="progressBar" class="progress-bar bg-8" role="progressbar" style="width: 0%"
                                        aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <p class="text-muted mt-3 mb-0">
                                    <span id="percentageChange" class="text-danger me-1">
                                        <i class="fas fa-arrow-down me-1"></i>0%
                                    </span> since last week
                                </p>
                            </div>
                        </div>
                    </div>

                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            fetchDocumentStats();
                        });

                        function fetchDocumentStats() {
                            fetch('/document-stats')
                                .then(response => response.json())
                                .then(data => {
                                    if (data.error) {
                                        console.error('Error fetching document stats:', data.error);
                                        return;
                                    }

                                    // Update document count
                                    document.getElementById("documentCount").textContent = data.count;

                                    // Update percentage change
                                    let percentageText = document.getElementById("percentageChange");
                                    percentageText.innerHTML = `${data.percentageChange}%`;

                                    // Set color based on increase/decrease
                                    if (data.percentageChange >= 0) {
                                        percentageText.classList.remove("text-danger");
                                        percentageText.classList.add("text-success");
                                    } else {
                                        percentageText.classList.remove("text-success");
                                        percentageText.classList.add("text-danger");
                                    }

                                    // Update progress bar
                                    let progressBar = document.getElementById("progressBar");
                                    progressBar.style.width = `${data.progress}%`;
                                    progressBar.setAttribute("aria-valuenow", data.progress);
                                })
                                .catch(error => console.error('Failed to load document stats:', error));
                        }
                    </script>
                </div>
                <div class="row">
                    <div class="col-xl-7 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title">Sales Analytics</h5>
                                    <div class="dropdown">
                                        {{-- <button class="btn btn-white btn-sm dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                            Monthly
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Weekly</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Monthly</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Yearly</a>
                                            </li>
                                        </ul> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between flex-wrap flex-md-nowrap">
                                    <div class="w-md-100 d-flex align-items-center mb-3 flex-wrap flex-md-nowrap">
                                        <div>
                                            <span>Total Sales</span>
                                            <p class="h3 text-primary me-5" id="totalSales">₱0</p>
                                        </div>

                                        <div>
                                            <span>Earnings</span>
                                            <p class="h3 text-dark me-5" id="earnings">₱0</p>
                                        </div>


                                    </div>

                                </div>
                                <div id="sales_chart"></div>
                            </div>
                        </div>
                    </div>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            fetchSalesData();
                        });

                        function fetchSalesData() {
                            fetch('/sales-data')
                                .then(response => response.json())
                                .then(data => {
                                    if (data.error) {
                                        console.error('Error fetching sales data:', data.error);
                                        return;
                                    }

                                    // Extract and parse data from response
                                    let totalSales = parseFloat(data.total_sales) || 0;
                                    let totalEarnings = parseFloat(data.total_earnings) || 0;

                                    // Format and update DOM
                                    document.getElementById("totalSales").textContent = `₱${totalSales.toFixed(2)}`;
                                    document.getElementById("earnings").textContent = `₱${totalEarnings.toFixed(2)}`;
                                })
                                .catch(error => console.error('Failed to load sales data:', error));
                        }
                    </script>



                    <div class="col-xl-5 d-flex">
                        <div class="card flex-fill">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title">Invoice Analytics</h5>
                                    <div class="dropdown">
                                        {{-- <button class="btn btn-white btn-sm dropdown-toggle" type="button"
                                            id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                            Monthly
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Weekly</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Monthly</a>
                                            </li>
                                            <li>
                                                <a href="javascript:void(0);" class="dropdown-item">Yearly</a>
                                            </li>
                                        </ul> --}}
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div id="invoice_chart"></div>
                                <div class="text-center text-muted">
                                    <div class="row">
                                        <div class="col-3">
                                            <div class="mt-4">
                                                <p class="mb-2 text-truncate"><i
                                                        class="fas fa-circle text-primary me-1"></i> Total Invoiced
                                                    Amount</p>
                                                <h5 id="totalInvoiced">₱0</h5>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="mt-4">
                                                <p class="mb-2 text-truncate"><i
                                                        class="fas fa-circle text-success me-1"></i> Paid</p>
                                                <h5 id="paidAmount">₱0</h5>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="mt-4">
                                                <p class="mb-2 text-truncate"><i
                                                        class="fas fa-circle text-warning me-1"></i> Unpaid</p>
                                                <h5 id="unpaidAmount">₱0</h5>
                                            </div>
                                        </div>
                                        <div class="col-3">
                                            <div class="mt-4">
                                                <p class="mb-2 text-truncate"><i
                                                        class="fas fa-circle text-danger me-1"></i> Overdue</p>
                                                <h5 id="overdueAmount">₱0</h5>
                                            </div>
                                        </div>
                                    </div>
                                    <script>
                                        $(document).ready(function() {
                                            function fetchInvoiceData(period) {
                                                $.ajax({
                                                    url: "/invoice-analytics",
                                                    type: "GET",
                                                    dataType: "json",
                                                    success: function(data) {
                                                        if (!data.analytics) {
                                                            console.error("No analytics data found.");
                                                            return;
                                                        }

                                                        let firstKey = Object.keys(data.analytics)[0];
                                                        let analytics = data.analytics[firstKey] || {};

                                                        let totalInvoiced = analytics.totalAmount || 0;
                                                        let paid = analytics.paidInvoices || 0;
                                                        let unpaid = analytics.unpaidInvoices || 0;
                                                        let overdue = analytics.overdueInvoices || 0;

                                                        $("#totalInvoiced").text(`₱${totalInvoiced.toLocaleString()}`);
                                                        $("#paidAmount").text(`₱${paid.toLocaleString()}`);
                                                        $("#unpaidAmount").text(`₱${unpaid.toLocaleString()}`);
                                                        $("#overdueAmount").text(`₱${overdue.toLocaleString()}`);
                                                    },
                                                    error: function(xhr, status, error) {
                                                        console.error("Error fetching invoice analytics:", error);
                                                    },
                                                });
                                            }

                                            // Load default (monthly) data on page load
                                            fetchInvoiceData("monthly");

                                            // Dropdown Click Event
                                            $(".dropdown-menu .dropdown-item").on("click", function() {
                                                let selectedPeriod = $(this).data("period");
                                                $("#dropdownMenuButton1").text($(this).text()); // Update button text
                                                fetchInvoiceData(selectedPeriod);
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-sm-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col">
                                        <h5 class="card-title">New Hired List</h5>
                                    </div>
                                    <div class="col-auto">
                                        <a href="{{ route('upcoming.users') }}"
                                            class="btn-right btn btn-sm btn-outline-primary">
                                            View All
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">

                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Name</th>
                                                <th>Contact</th>
                                                <th>Email</th>
                                                <th>Department</th>
                                                <th>Status</th>
                                                <th class="text-right">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="employeeTableBody">
                                            <!-- Employees will be dynamically added here -->
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        $(document).ready(function() {
                            function fetchEmployees() {
                                $.ajax({
                                    url: "/fetch-employees",
                                    type: "GET",
                                    dataType: "json",
                                    success: function(data) {
                                        let employees = data.employees;
                                        let tableBody = $("#employeeTableBody");
                                        tableBody.empty(); // Clear existing data

                                        if (employees.length === 0) {
                                            tableBody.append(
                                                '<tr><td colspan="6" class="text-center">No employees found</td></tr>'
                                            );
                                        } else {
                                            employees.forEach(function(employee) {
                                                let row = `
                                                <tr>
                                                    <td>${employee.name}</td>
                                                    <td>${employee.contact}</td>
                                                    <td>${employee.email}</td>
                                                    <td>${employee.department}</td>
                                                    <td><span class="badge bg-${getStatusBadge(employee.status)}">${employee.status}</span></td>
                                                    <td class="text-right">
                                                        <div class="dropdown dropdown-action">
                                                            <a href="#" class="action-icon dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="fas fa-ellipsis-h"></i>
                                                            </a>
                                                            <div class="dropdown-menu dropdown-menu-right">
                                                                <a class="dropdown-item" href="{{ route('upcoming.users') }}"><i class="far fa-eye me-2"></i>View</a>

                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                            `;
                                                tableBody.append(row);
                                            });
                                        }
                                    },
                                    error: function(xhr, status, error) {
                                        console.error("Error fetching employee data:", error);
                                    }
                                });
                            }

                            function getStatusBadge(status) {
                                switch (status.toLowerCase()) {
                                    case "pending":
                                        return "warning-light";
                                    case "active":
                                        return "success-light";
                                    case "inactive":
                                        return "danger-light";
                                    default:
                                        return "secondary-light";
                                }
                            }

                            // Fetch data on page load
                            fetchEmployees();
                        });
                    </script>

                </div>
            </div>
        </div>
    </div>

    @include('layout.footerjs')
</body>

</html>
