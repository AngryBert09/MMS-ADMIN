<!DOCTYPE html>
<html lang="en">

@include('layout.headerAssets')

<body>

    <div class="main-wrapper">

        @include('layout.navbar')


        @include('layout.sidebar')


        <div class="page-wrapper">
            <div class="content container-fluid">
                <div class="row justify-content-center">
                    <div class="col-xl-8">
                        <div class="text-md-end">
                            <div class="btn-group btn-group-sm d-print-none mb-4">
                                <a href="javascript:window.print()" class="btn btn-white text-black-50"><i
                                        class="fa fa-print"></i> Print</a>

                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <div class="invoice-item">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="invoice-logo">
                                                <img src="{{ asset('img/greatwall-logo.png') }}" alt="logo"
                                                    style="width: 50px; height: 50px;">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="invoice-details">
                                                <strong>Invoice Number:</strong> {{ $invoice['invoiceNumber'] }} <br>
                                                <strong>Issued:</strong>
                                                {{ \Carbon\Carbon::parse($invoice['invoiceDate'])->format('d/m/Y') }}
                                                <br>
                                                <strong>Due:</strong>
                                                {{ \Carbon\Carbon::parse($invoice['dueDate'])->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="invoice-item">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="invoice-info">
                                                <strong class="customer-text">Invoice From</strong>
                                                <p class="invoice-details invoice-details-two">
                                                    UNAVAILABLE
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="invoice-info invoice-info2">
                                                <strong class="customer-text">Invoice To</strong>
                                                <p class="invoice-details">
                                                    UNAVAILABLE
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="mt-0">



                                <div class="invoice-item invoice-table-wrap">
                                    <h5>Items</h5>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="table-responsive">
                                                <table class="invoice-table table table-border mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th class="w-75">Items</th>
                                                            <th class="text-end">Quantity</th>
                                                            <th class="text-end">Price</th>
                                                            <th class="text-end">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($invoice['order_items'] as $item)
                                                            <tr>
                                                                <td class="w-50">{{ $item['item_description'] }}</td>
                                                                <td class="text-end">{{ $item['quantity'] }}</td>
                                                                <td class="text-end">
                                                                    ₱{{ number_format($item['unit_price'], 2) }}</td>
                                                                <td class="text-end">
                                                                    ₱{{ number_format($item['total_price'], 2) }}</td>
                                                            </tr>
                                                        @endforeach
                                                        <tr>
                                                            <td colspan="3"
                                                                class="text-end text-muted border-bottom-0">Subtotal
                                                            </td>
                                                            <td class="text-end border-bottom-0">
                                                                ₱{{ number_format($invoice['totalAmount'], 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3"
                                                                class="text-end text-muted border-bottom-0">Tax</td>
                                                            <td class="text-end border-bottom-0">
                                                                ₱{{ number_format($invoice['taxAmount'], 2) }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="3" class="text-end text-muted">Discount</td>
                                                            <td class="text-end">
                                                                ₱{{ number_format($invoice['discountAmount'], 2) }}
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    <tfoot class="border-bottom border-1">
                                                        <tr>
                                                            <th colspan="3" class="text-end font-weight-600">Total
                                                            </th>
                                                            <th class="text-end font-weight-600">
                                                                ₱{{ number_format($invoice['totalAmount'] - $invoice['discountAmount'] + $invoice['taxAmount'], 2) }}
                                                            </th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="invoice-sign text-end py-5">
                                    _________________
                                    <span class="d-block">Digital Signature</span>
                                </div>
                                <hr>
                                <div class="invoice-terms">
                                    <h6>Notes:</h6>
                                    {{-- <p class="mb-0">{{ $invoice['notes'] }}</p> --}}
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>








    </div>

    @include('layout.footerjs')
</body>

</html>
