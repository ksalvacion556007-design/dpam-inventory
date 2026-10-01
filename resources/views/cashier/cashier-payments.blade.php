<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments | Cashier | DPAM IMS</title>

    <style>
        :root{--bg:#f1f5f9;--ink:#0f172a;--muted:#64748b;--line:#e2e8f0;--sidebar:#0b1220;--accent:#2563eb;--accent-dark:#1d4ed8;--danger:#dc2626;--success:#059669}
        *{box-sizing:border-box}
        body{margin:0;font-family:"Segoe UI",system-ui,-apple-system,Roboto,Arial,sans-serif;background:var(--bg);color:var(--ink)}
        a{color:inherit;text-decoration:none}
        .layout{display:flex;min-height:100vh}

        .sidebar{width:256px;flex-shrink:0;background:var(--sidebar);color:#fff;padding:24px 14px;display:flex;flex-direction:column}
        .brand{font-size:20px;font-weight:700;padding:0 12px}
        .subtitle{font-size:12px;color:#94a3b8;line-height:1.5;padding:0 12px;margin:4px 0 26px}
        .menu-title{font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.08em;margin:6px 12px 8px}
        .menu a,.logout-button{display:flex;align-items:center;gap:11px;width:100%;padding:11px 12px;margin-bottom:4px;border-radius:8px;color:#cbd5e1;background:transparent;border:none;font-size:14px;font-family:inherit;cursor:pointer;text-align:left;transition:background .15s,color .15s}
        .menu a svg,.logout-button svg{width:18px;height:18px;flex-shrink:0}
        .menu a:hover,.logout-button:hover{background:#1e293b;color:#fff}
        .menu a.active{background:var(--accent);color:#fff;font-weight:600}
        .logout-form{margin-top:auto;padding-top:24px}

        .main{flex:1;min-width:0;padding:30px 34px}
        .topbar{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:26px}
        .page-title{margin:0;font-size:26px;font-weight:700}
        .page-description{margin-top:5px;color:var(--muted);font-size:14px}
        .admin-badge{background:#dbeafe;color:#1e40af;padding:7px 14px;border-radius:999px;font-size:12px;font-weight:700;letter-spacing:.05em}

        .alert{padding:13px 16px;border-radius:10px;margin-bottom:20px;font-size:14px}
        .alert-success{background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0}
        .alert-error{background:#fef2f2;color:#991b1b;border:1px solid #fecaca}
        .alert ul{margin:8px 0 0;padding-left:20px}

        .cards{display:grid;grid-template-columns:repeat(3,1fr);gap:18px;margin-bottom:22px}
        .card{background:#fff;border:1px solid var(--line);border-radius:14px;padding:20px;position:relative;overflow:hidden}
        .card::before{content:"";position:absolute;left:0;top:0;bottom:0;width:4px;background:var(--card-color,var(--accent))}
        .card-label{font-size:13px;color:var(--muted);margin-bottom:10px}
        .card-value{font-size:28px;font-weight:700}

        .content-grid{display:grid;grid-template-columns:minmax(0,1fr) 390px;gap:20px;align-items:start}
        .panel{background:#fff;border:1px solid var(--line);border-radius:14px;overflow:hidden}
        .panel-header{padding:18px 20px;border-bottom:1px solid var(--line)}
        .panel-header h2{margin:0;font-size:17px}
        .panel-body{padding:20px}

        .filters{display:grid;grid-template-columns:1fr 150px 150px auto;gap:10px;margin-bottom:18px}
        .form-group{margin-bottom:15px;display:flex;flex-direction:column;gap:6px}
        label{font-size:12px;color:var(--muted);font-weight:600}
        .form-group label{font-size:13px;color:#334155}
        .req::after{content:" *";color:var(--danger)}
        input,select,textarea{width:100%;padding:10px 12px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;font-size:14px;font-family:inherit;color:var(--ink);transition:border-color .15s,box-shadow .15s}
        textarea{min-height:80px;resize:vertical}
        input:focus,select:focus,textarea:focus{outline:none;border-color:var(--accent);box-shadow:0 0 0 3px rgba(37,99,235,.15)}

        .button{display:inline-block;padding:10px 16px;border:none;border-radius:8px;cursor:pointer;font-size:13px;font-weight:600;font-family:inherit;text-align:center;transition:background .15s}
        .button-primary{background:var(--accent);color:#fff}.button-primary:hover{background:var(--accent-dark)}

        .table-wrapper{overflow-x:auto;border:1px solid var(--line);border-radius:10px}
        table{width:100%;border-collapse:collapse}
        th,td{padding:13px 16px;border-bottom:1px solid var(--line);text-align:left;font-size:13px;white-space:nowrap}
        th{color:var(--muted);font-weight:600;background:#f8fafc}
        tbody tr:hover td{background:#f8fafc}
        tr:last-child td{border-bottom:none}
        .empty{padding:35px 20px;text-align:center;color:var(--muted);font-size:13px}

        .status{display:inline-block;padding:4px 10px;border-radius:999px;font-size:11px;font-weight:700}
        .s-green{background:#dcfce7;color:#166534}.s-amber{background:#fef3c7;color:#92400e}.s-red{background:#fee2e2;color:#991b1b}.s-gray{background:#f1f5f9;color:#475569}

        .order-total{background:#f8fafc;border:1px solid var(--line);border-radius:10px;padding:12px;margin-bottom:15px}
        .order-total-label{font-size:11px;color:var(--muted);margin-bottom:5px;text-transform:uppercase}
        .order-total-value{font-size:20px;font-weight:700}
        .payment-fields{display:none}
        .payment-fields.show{display:block}

        @media(max-width:1100px){.content-grid{grid-template-columns:1fr}.filters{grid-template-columns:1fr 1fr}}
        @media(max-width:800px){.layout{flex-direction:column}.sidebar{width:100%}.main{padding:20px}.topbar{flex-direction:column;align-items:flex-start}.cards,.filters{grid-template-columns:1fr}}
    </style>
</head>

<body>

<div class="layout">

    <aside class="sidebar">
        <div class="brand">DPAM IMS</div>
        <div class="subtitle">Inventory Management System</div>
        <div class="menu-title">Cashier</div>

        <nav class="menu">
            <a href="{{ route('cashier.dashboard') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
                Dashboard
            </a>
            <a href="{{ route('cashier.customer-orders') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 4h6v3H9z"/><path d="M7 5H5v16h14V5h-2"/><path d="M9 12h6M9 16h6"/></svg>
                Customer Orders
            </a>
            <a href="{{ route('cashier.payments') }}" class="active">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="5" width="19" height="14" rx="2"/><path d="M2.5 10h19M6 15h4"/></svg>
                Payments
            </a>
            <a href="{{ route('cashier.ledger') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h12a2 2 0 012 2v14H7a2 2 0 01-2-2z"/><path d="M9 9h6M9 13h6"/></svg>
                Accounts Receivable / Ledger
            </a>
            <a href="{{ route('cashier.reports') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V4M4 20h16"/><path d="M8 16v-5M12 16V8M16 16v-3"/></svg>
                Reports
            </a>
            <a href="{{ route('cashier.archive') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="5" rx="1"/><path d="M5 9v10h14V9M10 13h4"/></svg>
                Archive
            </a>
        </nav>

        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="logout-button">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 4H5v16h4M16 8l4 4-4 4M20 12H9"/></svg>
                Logout
            </button>
        </form>
    </aside>


    <main class="main">

        <div class="topbar">
            <div>
                <h1 class="page-title">Payments</h1>
                <div class="page-description">Record and monitor customer payments and receipts.</div>
            </div>
            <div class="admin-badge">CASHIER</div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-error">
                <strong>Please correct the following:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif


        <div class="cards">
            <div class="card" style="--card-color:#2563eb">
                <div class="card-label">Today's Payment Count</div>
                <div class="card-value">{{ $todayCount }}</div>
            </div>

            <div class="card" style="--card-color:#059669">
                <div class="card-label">Today's Collections</div>
                <div class="card-value">₱{{ number_format((float) $todayTotal, 2) }}</div>
            </div>

            <div class="card" style="--card-color:#f59e0b">
                <div class="card-label">Pending / Unpaid</div>
                <div class="card-value">₱{{ number_format((float) $pendingTotal, 2) }}</div>
            </div>
        </div>


        <div class="content-grid">

            {{-- PAYMENT HISTORY --}}
            <section class="panel">

                <div class="panel-header">
                    <h2>Payment Records</h2>
                </div>

                <div class="panel-body">

                    <form method="GET" action="{{ route('cashier.payments') }}" class="filters">

                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search receipt or customer">

                        <select name="payment_method">
                            <option value="">All Methods</option>
                            <option value="cash" @selected(request('payment_method') === 'cash')>Cash</option>
                            <option value="gcash" @selected(request('payment_method') === 'gcash')>GCash</option>
                            <option value="maya" @selected(request('payment_method') === 'maya')>Maya</option>
                            <option value="bank_transfer" @selected(request('payment_method') === 'bank_transfer')>Bank Transfer</option>
                            <option value="check" @selected(request('payment_method') === 'check')>Check</option>
                            <option value="pdc" @selected(request('payment_method') === 'pdc')>PDC</option>
                            <option value="credit" @selected(request('payment_method') === 'credit')>Credit / Utang</option>
                        </select>

                        <select name="status">
                            <option value="">All Statuses</option>
                            <option value="paid" @selected(request('status') === 'paid')>Paid</option>
                            <option value="pending" @selected(request('status') === 'pending')>Pending</option>
                            <option value="cleared" @selected(request('status') === 'cleared')>Cleared</option>
                            <option value="unpaid" @selected(request('status') === 'unpaid')>Unpaid</option>
                            <option value="voided" @selected(request('status') === 'voided')>Voided</option>
                        </select>

                        <button type="submit" class="button button-primary">Search</button>

                    </form>


                    <div class="table-wrapper">

                        @if($payments->count())

                            <table>
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Receipt No.</th>
                                        <th>Customer</th>
                                        <th>Method</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>

                                <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td>{{ optional($payment->payment_date)->format('M d, Y') }}</td>
                                        <td><strong>{{ $payment->receipt_number }}</strong></td>
                                        <td>{{ $payment->customerOrder->customer_name ?? '—' }}</td>
                                        <td>{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                        <td>₱{{ number_format((float) $payment->amount, 2) }}</td>
                                        <td>
                                            @if($payment->status === 'paid' || $payment->status === 'cleared')
                                                <span class="status s-green">{{ ucfirst($payment->status) }}</span>
                                            @elseif($payment->status === 'pending')
                                                <span class="status s-amber">Pending</span>
                                            @elseif($payment->status === 'unpaid')
                                                <span class="status s-red">Unpaid</span>
                                            @else
                                                <span class="status s-gray">{{ ucfirst($payment->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>

                        @else

                            <div class="empty">No payment records found.</div>

                        @endif

                    </div>

                    <div style="margin-top:18px;">
                        {{ $payments->links() }}
                    </div>

                </div>

            </section>


            {{-- RECORD PAYMENT --}}
            <section class="panel">

                <div class="panel-header">
                    <h2>Record Payment</h2>
                </div>

                <div class="panel-body">

                    <form method="POST" action="{{ route('cashier.payments.store') }}">

                        @csrf

                        <div class="form-group">
                            <label for="customer_order_id" class="req">Confirmed Customer Order</label>

                            <select name="customer_order_id" id="customer_order_id" required>
                                <option value="">Select Customer Order</option>

                                @foreach($confirmedOrders as $order)
                                    @php
                                        $orderTotal = $order->items->sum(function ($item) {
                                            return (float) $item->subtotal;
                                        });
                                    @endphp

                                    <option value="{{ $order->id }}" data-total="{{ $orderTotal }}">
                                        {{ $order->order_number }} — {{ $order->customer_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="order-total" id="orderTotalBox" style="display:none;">
                            <div class="order-total-label">Order Total</div>
                            <div class="order-total-value" id="orderTotal">₱0.00</div>
                        </div>


                        <div class="form-group">
                            <label for="receipt_number" class="req">Receipt Number</label>
                            <input type="text" name="receipt_number" id="receipt_number" required value="{{ old('receipt_number') }}">
                        </div>

                        <div class="form-group">
                            <label for="payment_date" class="req">Payment Date</label>
                            <input type="date" name="payment_date" id="payment_date" required value="{{ old('payment_date', now()->format('Y-m-d')) }}">
                        </div>

                        <div class="form-group">
                            <label for="payment_method" class="req">Payment Method</label>

                            <select name="payment_method" id="payment_method" required>
                                <option value="">Select Payment Method</option>
                                <option value="cash">Cash</option>
                                <option value="gcash">GCash</option>
                                <option value="maya">Maya</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="check">Check</option>
                                <option value="pdc">PDC</option>
                                <option value="credit">Credit / Utang</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="amount" class="req">Amount</label>
                            <input type="number" name="amount" id="amount" step="0.01" min="0.01" required value="{{ old('amount') }}">
                        </div>


                        <div class="payment-fields" id="referenceFields">
                            <div class="form-group">
                                <label for="reference_number">Reference Number</label>
                                <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number') }}" placeholder="Transaction/reference number">
                            </div>
                        </div>


                        <div class="payment-fields" id="checkFields">
                            <div class="form-group">
                                <label for="check_number">Check Number</label>
                                <input type="text" name="check_number" id="check_number" value="{{ old('check_number') }}">
                            </div>

                            <div class="form-group">
                                <label for="bank_name">Bank Name</label>
                                <input type="text" name="bank_name" id="bank_name" value="{{ old('bank_name') }}">
                            </div>

                            <div class="form-group">
                                <label for="check_date">Check Date</label>
                                <input type="date" name="check_date" id="check_date" value="{{ old('check_date') }}">
                            </div>

                            <div class="form-group">
                                <label for="maturity_date">Maturity Date</label>
                                <input type="date" name="maturity_date" id="maturity_date" value="{{ old('maturity_date') }}">
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" placeholder="Optional">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="button button-primary" style="width:100%;">Record Payment</button>

                    </form>

                </div>

            </section>

        </div>

    </main>

</div>


<script>
    const orderSelect = document.getElementById('customer_order_id');
    const orderTotalBox = document.getElementById('orderTotalBox');
    const orderTotal = document.getElementById('orderTotal');

    const paymentMethod = document.getElementById('payment_method');

    const referenceFields = document.getElementById('referenceFields');
    const checkFields = document.getElementById('checkFields');

    function updateOrderTotal() {
        const selected = orderSelect.options[orderSelect.selectedIndex];

        if (!selected || !selected.value) {
            orderTotalBox.style.display = 'none';
            orderTotal.textContent = '₱0.00';
            return;
        }

        const total = parseFloat(selected.dataset.total || 0);

        orderTotal.textContent =
            '₱' + total.toLocaleString('en-PH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        orderTotalBox.style.display = 'block';
    }

    function updatePaymentFields() {
        const method = paymentMethod.value;

        referenceFields.classList.remove('show');
        checkFields.classList.remove('show');

        if (method === 'gcash' || method === 'maya' || method === 'bank_transfer') {
            referenceFields.classList.add('show');
        }

        if (method === 'check' || method === 'pdc') {
            checkFields.classList.add('show');
        }
    }

    orderSelect.addEventListener('change', updateOrderTotal);
    paymentMethod.addEventListener('change', updatePaymentFields);

    updateOrderTotal();
    updatePaymentFields();
</script>

</body>
</html>