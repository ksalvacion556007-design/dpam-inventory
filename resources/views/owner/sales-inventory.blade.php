<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales & Inventory - DPAM IMS</title>

    <style>
        :root {
            --bg:#f4f6f9; --card:#fff; --ink:#111827; --muted:#6b7280; --line:#e5e7eb; --line-strong:#d1d5db;
            --sidebar:#0f172a; --accent:#2563eb; --accent-dark:#1d4ed8;
            --green-bg:#dcfce7; --amber:#92400e; --amber-bg:#fef3c7; --red:#b91c1c; --red-bg:#fee2e2;
            --blue:#1e40af; --blue-bg:#dbeafe; --gray-bg:#f3f4f6;
        }
        * { box-sizing:border-box; }
        body { margin:0; font-family:"Segoe UI",system-ui,-apple-system,Roboto,Arial,sans-serif; background:var(--bg); color:var(--ink); font-size:14px; }
        a { color:inherit; text-decoration:none; }
        h2 { font-size:15px; margin:0; font-weight:700; }
        h3 { font-size:13px; margin:0 0 8px; font-weight:700; }
        .hidden { display:none !important; }
        .text-right { text-align:right; }
        .muted { color:var(--muted); }
        .required { color:var(--red); }

        /* ---------- Sidebar ---------- */
        .sidebar { width:240px; background:var(--sidebar); color:#fff; padding:22px 12px; display:flex; flex-direction:column; position:fixed; inset:0 auto 0 0; overflow-y:auto; }
        .brand { font-size:19px; font-weight:700; padding:0 12px; }
        .subtitle { font-size:12px; color:#94a3b8; padding:0 12px; margin:3px 0 24px; line-height:1.5; }
        .menu-title { font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:.08em; margin:0 12px 8px; }
        .menu a, .logout-button { display:flex; align-items:center; gap:10px; width:100%; padding:10px 12px; margin-bottom:3px; border-radius:8px; color:#cbd5e1; background:transparent; border:none; font:inherit; font-size:14px; cursor:pointer; text-align:left; }
        .menu a svg, .logout-button svg { width:17px; height:17px; flex-shrink:0; }
        .menu a:hover, .logout-button:hover { background:#1e293b; color:#fff; }
        .menu a.active { background:var(--accent); color:#fff; font-weight:600; }
        .logout-form { margin-top:auto; padding-top:20px; }

        /* ---------- Main ---------- */
        .main { margin-left:240px; padding:24px 30px 50px; min-width:0; }
        .topbar { display:flex; justify-content:space-between; align-items:center; gap:14px; flex-wrap:wrap; margin-bottom:14px; }
        .page-title { margin:0; font-size:22px; font-weight:700; letter-spacing:.01em; }
        .role-badge { background:var(--blue-bg); color:var(--blue); padding:6px 12px; border-radius:999px; font-size:11px; font-weight:700; letter-spacing:.05em; }

        /* ---------- Buttons ---------- */
        .btn { border:1px solid transparent; border-radius:8px; padding:9px 14px; cursor:pointer; font:inherit; font-size:13px; font-weight:600; display:inline-flex; align-items:center; justify-content:center; gap:6px; transition:background .15s; white-space:nowrap; }
        .btn:disabled { opacity:.55; cursor:not-allowed; }
        .btn-sm { padding:5px 10px; font-size:12px; }
        .btn-lg { padding:12px 22px; font-size:14px; letter-spacing:.03em; }
        .btn-primary { background:var(--accent); color:#fff; } .btn-primary:hover:not(:disabled) { background:var(--accent-dark); }
        .btn-success { background:#16a34a; color:#fff; } .btn-success:hover:not(:disabled) { background:#15803d; }
        .btn-warning { background:#d97706; color:#fff; } .btn-warning:hover:not(:disabled) { background:#b45309; }
        .btn-danger { background:#dc2626; color:#fff; } .btn-danger:hover:not(:disabled) { background:#b91c1c; }
        .btn-outline { background:#fff; color:#374151; border-color:var(--line-strong); } .btn-outline:hover:not(:disabled) { background:var(--gray-bg); }
        .btn-light { background:#eff6ff; color:var(--accent); } .btn-light:hover:not(:disabled) { background:var(--blue-bg); }
        .link-btn { border:none; background:none; color:var(--accent); font:inherit; font-size:12px; font-weight:600; cursor:pointer; padding:0; text-decoration:underline; }

        /* ---------- Notices ---------- */
        .notice { padding:10px 14px; border-radius:10px; margin-bottom:12px; font-size:13px; line-height:1.5; border:1px solid; }
        .notice ul { margin:6px 0 0 18px; padding:0; }
        .notice-success { background:var(--green-bg); color:#166534; border-color:#bbf7d0; }
        .notice-error { background:var(--red-bg); color:#991b1b; border-color:#fecaca; }
        .notice-warning { background:#fffbeb; color:var(--amber); border-color:#fde68a; }
        .notice-info { background:#eff6ff; color:var(--blue); border-color:#bfdbfe; }
        .notice-sm { padding:7px 12px; font-size:12px; margin-bottom:0; }
        .inline-error { background:var(--red-bg); color:#991b1b; border:1px solid #fecaca; border-radius:8px; padding:8px 12px; font-size:12.5px; }
        .loading { color:var(--muted); font-size:12.5px; padding:8px 0; }
        .loading::before { content:""; display:inline-block; width:11px; height:11px; border:2px solid var(--line-strong); border-top-color:var(--accent); border-radius:50%; margin-right:8px; vertical-align:-1px; animation:spin .8s linear infinite; }
        @keyframes spin { to { transform:rotate(360deg); } }
        .info-tip { display:inline-block; width:14px; height:14px; line-height:14px; border-radius:50%; background:#e2e8f0; color:#475569; font-size:10px; font-weight:700; text-align:center; cursor:help; margin-left:3px; text-transform:none; letter-spacing:0; }

        /* ---------- Top navigation ---------- */
        .tabs { display:flex; gap:2px; border-bottom:1px solid var(--line-strong); margin-bottom:16px; overflow-x:auto; }
        .tab-button { border:none; background:transparent; color:var(--muted); padding:11px 20px; cursor:pointer; font:inherit; font-size:13px; font-weight:700; letter-spacing:.06em; text-transform:uppercase; border-bottom:3px solid transparent; margin-bottom:-1px; white-space:nowrap; }
        .tab-button:hover { color:var(--ink); }
        .tab-button.active { color:var(--accent); border-bottom-color:var(--accent); }
        .tab-button .pill { background:var(--gray-bg); color:var(--muted); border-radius:999px; padding:1px 8px; font-size:11px; margin-left:6px; letter-spacing:0; }
        .tab-button.active .pill { background:var(--blue-bg); color:var(--blue); }
        .tab-pane { display:none; } .tab-pane.active { display:block; }

        /* ---------- Cards / tables ---------- */
        .card { background:var(--card); border:1px solid var(--line); border-radius:12px; padding:14px; }
        .card-head { display:flex; justify-content:space-between; align-items:center; gap:10px; margin-bottom:10px; flex-wrap:wrap; }
        .table-card { background:var(--card); border:1px solid var(--line); border-radius:12px; overflow:hidden; }
        .table-wrapper { overflow-x:auto; }
        .scroll-y { max-height:250px; overflow-y:auto; }
        .scroll-y-lg { max-height:560px; overflow-y:auto; }
        table { width:100%; border-collapse:collapse; }
        th, td { padding:9px 12px; border-bottom:1px solid var(--line); text-align:left; font-size:13px; vertical-align:middle; white-space:nowrap; }
        th { color:var(--muted); font-weight:600; font-size:11.5px; text-transform:uppercase; letter-spacing:.03em; background:#f9fafb; position:sticky; top:0; z-index:1; }
        tbody tr:hover td { background:#fafafa; }
        tbody tr:last-child td { border-bottom:none; }
        tr.selectable { cursor:pointer; }
        tr.selected td { background:#eff6ff !important; }
        tr.row-out td { background:#fff7f7; }
        .empty-state { color:var(--muted); text-align:center; padding:26px 16px !important; white-space:normal; }
        .sub { color:var(--muted); font-size:11.5px; margin-top:2px; white-space:normal; }
        .num { font-variant-numeric:tabular-nums; }
        .toolbar { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
        .toolbar input, .toolbar select { padding:9px 12px; border:1px solid var(--line-strong); border-radius:8px; font:inherit; font-size:13px; background:#fff; outline:none; }
        .toolbar input[type=text] { flex:1; min-width:180px; }
        .toolbar input:focus, .toolbar select:focus { border-color:var(--accent); }
        .toolbar .spacer { flex:1; }
        .search-wrap { position:relative; flex:1; min-width:180px; display:flex; }
        .search-wrap > svg { position:absolute; left:11px; top:50%; transform:translateY(-50%); width:15px; height:15px; color:var(--muted); pointer-events:none; }
        .search-wrap input[type=text] { width:100%; padding-left:32px; }

        /* ---------- Summary ---------- */
        .summary { display:grid; grid-template-columns:repeat(6,1fr); gap:8px; margin-bottom:10px; }
        .stat { background:#fff; border:1px solid var(--line); border-radius:10px; padding:7px 12px; text-align:left; font:inherit; color:inherit; display:flex; align-items:center; justify-content:space-between; gap:8px; }
        button.stat, a.stat { cursor:pointer; } button.stat:hover, a.stat:hover { border-color:var(--accent); }
        .stat-label { font-size:11.5px; color:var(--muted); font-weight:600; }
        .stat-value { font-size:18px; font-weight:700; line-height:1.1; }
        .stat.green .stat-value { color:#15803d; } .stat.amber .stat-value { color:#b45309; } .stat.red .stat-value { color:#b91c1c; } .stat.blue .stat-value { color:#1d4ed8; }
        .stat.red.has { background:#fff7f7; border-color:#fca5a5; } .stat.amber.has { background:#fffbeb; border-color:#fcd34d; }

        /* ---------- Control bar ---------- */
        .control-bar { background:#fff; border:1px solid var(--line); border-radius:12px; padding:12px; margin-bottom:10px; }

        /* ---------- Badges ---------- */
        .badge { display:inline-block; padding:3px 9px; border-radius:999px; font-size:11px; font-weight:700; white-space:nowrap; }
        .b-green { background:var(--green-bg); color:#166534; }
        .b-amber { background:var(--amber-bg); color:var(--amber); }
        .b-red { background:var(--red-bg); color:#991b1b; }
        .b-blue { background:var(--blue-bg); color:var(--blue); }
        .b-orange { background:#ffedd5; color:#9a3412; }
        .b-gray { background:#e5e7eb; color:#374151; }
        .b-damaged { background:#7f1d1d; color:#fff; }
        .chip-sup { display:inline-block; background:#f1f5f9; border:1px solid #e2e8f0; color:#334155; border-radius:6px; padding:2px 8px; font-size:11.5px; margin:0 4px 4px 0; white-space:nowrap; }
        .chip-more { background:#eff6ff; border-color:#bfdbfe; color:var(--accent); cursor:pointer; font-weight:600; }
        .sup-cell { white-space:normal; min-width:170px; max-width:260px; }

        /* ---------- Inventory table: suppliers, expiration, batches ---------- */
        #inventoryTable { min-width:1000px; }
        .sup-list { white-space:normal; min-width:190px; max-width:320px; }
        .exp-cell { min-width:150px; }
        .exp-lbl { display:inline-block; font-size:10.5px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.03em; margin-right:5px; }
        .exp-flag { margin-top:3px; }
        tr.batch-depleted td { opacity:.6; }

        /* ---------- Dropdown ---------- */
        .actions { display:flex; gap:6px; align-items:center; }
        details.dd { position:relative; }
        details.dd > summary { list-style:none; cursor:pointer; }
        details.dd > summary::-webkit-details-marker { display:none; }
        .dd-menu { position:fixed; z-index:900; min-width:160px; background:#fff; border:1px solid var(--line-strong); border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,.15); padding:5px; display:flex; flex-direction:column; }
        .dd-menu button { border:none; background:transparent; text-align:left; padding:8px 10px; border-radius:6px; font:inherit; font-size:13px; cursor:pointer; color:var(--ink); }
        .dd-menu button:hover { background:var(--gray-bg); }
        .dd-menu button.danger { color:var(--red); }
        .dd-menu hr { border:none; border-top:1px solid var(--line); margin:4px 0; width:100%; }

        /* ---------- Forms ---------- */
        .form-group { margin-bottom:10px; }
        .form-group label { display:block; font-size:12px; font-weight:600; margin-bottom:4px; color:#374151; }
        .form-group small { color:var(--muted); font-weight:400; }
        .form-group > small { display:block; margin-top:3px; font-size:11px; }
        .form-group input, .form-group select, .form-group textarea { width:100%; padding:8px 10px; border:1px solid var(--line-strong); border-radius:8px; font:inherit; font-size:13px; background:#fff; outline:none; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(37,99,235,.12); }
        .form-group input.bad, .form-group select.bad { border-color:#dc2626; background:#fef2f2; }
        .form-group input[readonly] { background:#f3f4f6; color:#374151; cursor:default; }
        .form-group input[readonly]:focus { border-color:var(--line-strong); box-shadow:none; }
        .form-group input:disabled, .form-group select:disabled { background:#f3f4f6; cursor:not-allowed; }
        .form-group textarea { resize:vertical; min-height:56px; }
        .form-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:0 12px; }
        .form-actions { display:flex; justify-content:flex-end; gap:8px; margin-top:16px; flex-wrap:wrap; }

        /* ---------- Required marker + field errors ---------- */
        .req { color:var(--red); font-weight:700; margin-left:2px; }
        .field-error { display:block; color:#b91c1c; font-size:11.5px; margin-top:4px; font-weight:600; }
        .field-note { display:block; color:var(--blue); font-size:11.5px; margin-top:4px; font-weight:600; }
        .legend-req { font-size:12px; color:var(--muted); margin:0 0 12px; }

        /* ---------- Add Product sections ---------- */
        .form-section { border-top:2px solid var(--line); margin-top:16px; padding-top:14px; }
        .form-section:first-of-type { border-top:none; margin-top:0; padding-top:0; }
        .form-section > .section-title { margin-bottom:10px; }
        .form-group .input-row { display:flex; gap:8px; align-items:flex-start; }
        .form-group .input-row select { width:auto; flex:1; min-width:0; }
        .input-row .btn { height:36px; flex-shrink:0; }
        .batch-box { background:#f9fafb; border:1px dashed var(--line-strong); border-radius:10px; padding:12px 12px 2px; margin-top:4px; }

        /* ---------- Multi-select picker (suppliers) ---------- */
        .picker { border:1px solid var(--line-strong); border-radius:8px; overflow:hidden; background:#fff; }
        .form-group input.picker-search { border:none; border-bottom:1px solid var(--line); border-radius:0; box-shadow:none; }
        .picker-list { max-height:180px; overflow-y:auto; }
        .pick-row { display:flex; gap:10px; align-items:flex-start; padding:7px 12px; cursor:pointer; font-size:13px; border-bottom:1px solid #f3f4f6; margin:0 !important; font-weight:400 !important; }
        .pick-row:hover { background:#f9fafb; }
        .form-group .pick-row input { width:auto; margin-top:2px; flex-shrink:0; padding:0; }
        .picker-empty { padding:12px 14px; font-size:12.5px; color:var(--muted); }
        .picker-foot { padding:6px 12px; font-size:11.5px; color:var(--muted); background:#f9fafb; border-top:1px solid var(--line); }

        /* ---------- Searchable selector (combobox) ---------- */
        .combo { position:relative; }
        .combo-field { display:flex; align-items:center; gap:6px; border:1px solid var(--line-strong); border-radius:8px; background:#fff; padding:0 8px 0 10px; min-height:36px; }
        .combo-field:focus-within { border-color:var(--accent); box-shadow:0 0 0 3px rgba(37,99,235,.12); }
        .combo-field.bad { border-color:#dc2626; background:#fef2f2; }
        .combo-field > svg { width:14px; height:14px; color:var(--muted); flex-shrink:0; }
        .form-group .combo-field input.combo-input { border:none; box-shadow:none; background:transparent; padding:8px 0; width:auto; flex:1; min-width:0; outline:none; }
        .combo-selected { flex:1; min-width:0; display:flex; align-items:center; justify-content:space-between; gap:8px; font-size:13px; font-weight:600; padding:7px 0; }
        .combo-selected span { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
        .combo-clear { border:none; background:#e5e7eb; color:#374151; width:20px; height:20px; border-radius:50%; cursor:pointer; line-height:1; font-size:14px; flex-shrink:0; padding:0; }
        .combo-clear:hover { background:#d1d5db; }
        .combo-list { position:absolute; left:0; right:0; top:calc(100% + 4px); z-index:30; margin:0; padding:4px; list-style:none; background:#fff; border:1px solid var(--line-strong); border-radius:10px; box-shadow:0 10px 30px rgba(0,0,0,.15); max-height:220px; overflow-y:auto; }
        .combo-list li { padding:8px 10px; border-radius:6px; cursor:pointer; font-size:13px; }
        .combo-list li:hover, .combo-list li.active { background:#eff6ff; color:var(--blue); }
        .combo-list li.combo-note { color:var(--muted); font-size:11.5px; cursor:default; background:transparent; }
        .combo-list li mark { background:#fef08a; color:inherit; padding:0; }

        /* ---------- Modals ---------- */
        .modal { display:none; position:fixed; inset:0; background:rgba(17,24,39,.55); align-items:center; justify-content:center; padding:16px; z-index:1000; }
        .modal.show { display:flex; }
        .modal-content { background:#fff; width:100%; max-width:560px; max-height:92vh; overflow-y:auto; border-radius:14px; box-shadow:0 25px 60px rgba(0,0,0,.3); }
        .modal-large { max-width:1050px; }
        .modal-xl { max-width:1320px; }
        .modal-header { display:flex; justify-content:space-between; align-items:flex-start; gap:12px; padding:14px 20px; border-bottom:1px solid var(--line); position:sticky; top:0; background:#fff; z-index:5; }
        .modal-header h2 { font-size:17px; letter-spacing:.02em; }
        .modal-header p { margin:3px 0 0; color:var(--muted); font-size:12px; }
        .modal-body { padding:18px 20px; }
        .modal-footer { position:sticky; bottom:0; background:#fff; border-top:1px solid var(--line); padding:12px 20px; display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; z-index:5; }
        .modal-footer .btns { display:flex; gap:8px; flex-wrap:wrap; }
        .foot-total { font-size:12px; color:var(--muted); } .foot-total strong { display:block; font-size:22px; color:var(--ink); line-height:1.1; }
        .close-x { border:none; background:transparent; font-size:24px; line-height:1; cursor:pointer; color:var(--muted); border-radius:6px; width:32px; height:32px; }
        .close-x:hover { background:var(--gray-bg); color:var(--ink); }
        .details-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:12px 22px; margin-bottom:14px; }
        .box { background:#f9fafb; border:1px solid var(--line); border-radius:10px; padding:10px 14px; margin-top:10px; font-size:13px; line-height:1.5; }
        .box-title { font-size:11px; font-weight:700; color:var(--muted); margin-bottom:4px; text-transform:uppercase; letter-spacing:.04em; }
        .inner-table { border:1px solid var(--line); border-radius:10px; overflow-x:auto; margin-bottom:12px; }
        .stock-info { background:#f9fafb; border:1px solid var(--line); border-radius:10px; padding:10px 14px; margin-bottom:12px; display:flex; justify-content:space-between; font-size:14px; }
        .section-title { font-size:11px; font-weight:700; color:var(--muted); text-transform:uppercase; letter-spacing:.07em; margin:0 0 10px; }
        .detail-item { border-bottom:1px solid var(--line); padding-bottom:6px; min-width:0; }
        .detail-label { display:block; color:var(--muted); font-size:11px; margin-bottom:2px; }
        .detail-value { font-weight:600; font-size:13px; word-break:break-word; }
        .detail-item.price-item { background:#eff6ff; border-radius:6px; padding:4px 8px 6px; border-bottom-color:#bfdbfe; }
        .detail-item.price-item .detail-value { color:var(--blue); }

        /* ---------- Customer order modal ---------- */
        .oc-top { display:grid; grid-template-columns:minmax(0,45fr) minmax(0,55fr); gap:16px; align-items:start; }
        .oc-col { display:flex; flex-direction:column; gap:14px; min-width:0; }
        .oc-bottom { display:grid; grid-template-columns:1fr 1fr 0.9fr; gap:18px; margin-top:16px; padding-top:16px; border-top:2px solid var(--line); }
        .oc-bottom h3.section-title { margin-bottom:10px; }
        .info-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:9px 14px; }
        .stock-alert { padding:9px 13px; border-radius:10px; font-size:13px; font-weight:700; margin-bottom:10px; border:1px solid; }
        .stock-alert.out { background:var(--red-bg); color:#991b1b; border-color:#fca5a5; }
        .stock-alert.low { background:var(--amber-bg); color:var(--amber); border-color:#fcd34d; }
        .stock-alert.ok { background:var(--green-bg); color:#166534; border-color:#bbf7d0; }
        .add-row { display:flex; gap:8px; align-items:center; margin-top:12px; flex-wrap:wrap; }
        .ci-short { color:#92400e !important; }
        .cart-item.short { background:#fffbeb; }
        .stepper input.short { background:#fef3c7; color:#92400e; }
        .pager { display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; margin:8px 2px 0; font-size:12px; color:var(--muted); }
        .pager-btns { display:flex; gap:4px; align-items:center; }
        .pager-btn { border:1px solid var(--line-strong); background:#fff; color:#374151; border-radius:6px; padding:4px 10px; font:inherit; font-size:12px; font-weight:600; cursor:pointer; }
        .pager-btn:hover:not(:disabled) { background:var(--gray-bg); }
        .pager-btn.active { background:var(--accent); border-color:var(--accent); color:#fff; }
        .pager-btn:disabled { opacity:.5; cursor:not-allowed; }
        .pager-gap { padding:0 4px; }
        .sup-block { margin-top:12px; border-top:1px dashed var(--line-strong); padding-top:10px; }
        .sup-block table th, .sup-block table td { padding:5px 8px; font-size:12px; }
        .cart-list { max-height:310px; overflow-y:auto; border:1px solid var(--line); border-radius:10px; }
        .cart-item { display:grid; grid-template-columns:minmax(0,1fr) auto auto auto; gap:6px 12px; align-items:center; padding:10px 12px; border-bottom:1px solid var(--line); }
        .cart-item:last-child { border-bottom:none; }
        .cart-item.over { background:#fff7f7; }
        .ci-name { font-weight:700; font-size:13px; }
        .ci-warn { grid-column:1 / -1; color:#b91c1c; font-size:11.5px; font-weight:700; }
        .ci-price { text-align:right; font-size:12px; color:var(--muted); }
        .ci-sub { text-align:right; font-weight:700; min-width:92px; }
        .stepper { display:inline-flex; align-items:center; border:1px solid var(--line-strong); border-radius:8px; overflow:hidden; }
        .stepper button { border:none; background:#f3f4f6; width:28px; height:30px; cursor:pointer; font-size:16px; line-height:1; }
        .stepper button:hover { background:#e5e7eb; }
        .stepper input { width:52px; text-align:center; border:none; border-left:1px solid var(--line-strong); border-right:1px solid var(--line-strong); height:30px; font:inherit; font-size:13px; outline:none; }
        .stepper input.over { background:#fee2e2; color:#991b1b; }
        .icon-btn { border:none; background:transparent; cursor:pointer; color:#dc2626; font-size:12px; font-weight:600; padding:4px 6px; border-radius:6px; }
        .icon-btn:hover { background:var(--red-bg); }
        .totals-box { background:#f9fafb; border:1px solid var(--line); border-radius:10px; padding:10px 14px; }
        .totals-row { display:flex; justify-content:space-between; padding:3px 0; font-size:13px; }
        .totals-row.big { font-size:22px; font-weight:700; padding:6px 0; border-top:1px solid var(--line); margin-top:4px; }
        .totals-row.hl { font-weight:700; }

        @media (max-width:1250px) { .summary { grid-template-columns:repeat(3,1fr); } }
        @media (max-width:1100px) {
            .oc-top, .oc-bottom { grid-template-columns:1fr; }
            .details-grid { grid-template-columns:repeat(2,1fr); }
        }
        @media (max-width:800px) {
            .sidebar { position:relative; width:100%; inset:auto; }
            .main { margin-left:0; padding:16px; }
            .summary { grid-template-columns:repeat(2,1fr); }
            .form-grid, .info-grid, .details-grid { grid-template-columns:1fr; }
            .cart-item { grid-template-columns:1fr auto; }
            .modal-footer { flex-direction:column; align-items:stretch; }
            .modal-footer .btns .btn { flex:1; }
        }
    </style>
</head>

<body>

{{-- ##################################################################
     SIDEBAR
     ################################################################## --}}
<aside class="sidebar">
    <div class="brand">DPAM IMS</div>
    <div class="subtitle">Industrial Supplies & Services Inc.</div>
    <div class="menu-title">Owner</div>

    <nav class="menu">
        <a href="{{ route('owner.dashboard') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/></svg>
            Dashboard
        </a>
        <a href="{{ route('owner.sales-inventory') }}" class="active">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l9 5-9 5-9-5z"/><path d="M3 13l9 5 9-5"/></svg>
            Sales & Inventory
        </a>
        <a href="{{ route('owner.suppliers') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="12" height="9" rx="1"/><path d="M14 10h4l3 3v3h-7"/><circle cx="7" cy="18" r="1.8"/><circle cx="17" cy="18" r="1.8"/></svg>
            Suppliers
        </a>
        <a href="{{ route('owner.purchase-orders') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 4h2l2.5 10h10L20 7H7"/><circle cx="9" cy="19" r="1.5"/><circle cx="17" cy="19" r="1.5"/></svg>
            Purchase Orders
        </a>
        <a href="{{ route('owner.reports') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20V10M10 20V4M16 20v-8M22 20H2"/></svg>
            Reports
        </a>
        <a href="{{ route('owner.archive') }}">
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

@php
    $stockBadge = ['OUT OF STOCK' => 'b-red', 'LOW STOCK' => 'b-amber', 'IN STOCK' => 'b-green'];

    $orderBadge = [
        'pending_inventory_check' => 'b-amber',
        'confirmed' => 'b-blue',
        'partially_fulfilled' => 'b-orange',
        'fulfilled' => 'b-green',
        'for_purchasing' => 'b-orange',
        'cancelled' => 'b-red',
    ];

    $payBadge = [
        'paid' => ['Paid', 'b-green'],
        'partial' => ['Partial', 'b-orange'],
        'pending' => ['Pending Check', 'b-amber'],
        'unpaid' => ['Unpaid', 'b-red'],
        'cancelled' => ['—', 'b-gray'],
    ];

    $peso = fn ($v) => '₱' . number_format((float) $v, 2);

    // ---- Summary figures (only from variables the controller already passes) ----
    $cntTotal = $products->count();
    $cntIn = $products->filter(fn ($p) => $p->stock_status === 'IN STOCK')->count();
    $cntLow = $products->filter(fn ($p) => $p->stock_status === 'LOW STOCK')->count();
    $cntOut = $products->filter(fn ($p) => $p->stock_status === 'OUT OF STOCK')->count();
    $cntPendingOrders = collect($orderPayloads)->where('status', 'pending_inventory_check')->count();
    $cntPendingPo = count($purchaseOrders);
    $cntOrders = count($orderPayloads);

    $categoryList = $products->map(fn ($p) => $p->category->category_name ?? null)->filter()->unique()->sort()->values();
    $brandList = $products->map(fn ($p) => $p->brand ?: null)->filter()->unique()->sort()->values();
    $unitList = $products->map(fn ($p) => $p->unit ?: null)->filter()->unique()->sort()->values();

    // Category choices for Add Product.
    // Uses a full $categories list when the controller passes one; otherwise only the categories already attached to products.
    $categoryOptions = isset($categories)
        ? collect($categories)->filter()->unique('id')->sortBy('category_name')->values()
        : $products->map(fn ($p) => $p->category)->filter()->unique('id')->sortBy('category_name')->values();

    // Plain id/name list used by the searchable category selector (UI only; the submitted field stays category_id)
    $categoryChoices = $categoryOptions->map(fn ($c) => ['id' => (int) $c->id, 'name' => (string) $c->category_name])->values();

    // Supplier choices for Add Product: ACTIVE suppliers only.
    // Uses a $suppliers list when the controller passes one; otherwise the suppliers already linked to products.
    $supplierSource = isset($suppliers)
        ? collect($suppliers)
        : $products->flatMap(fn ($p) => $p->suppliers)->filter()->unique('id');
    $supplierChoices = $supplierSource
        ->filter(fn ($s) => ($s->status ?? 'active') === 'active')
        ->map(fn ($s) => ['id' => (int) $s->id, 'name' => (string) $s->supplier_name])
        ->sortBy('name')
        ->values();
    $oldSupplierIds = array_values(array_map('intval', (array) old('supplier_ids', [])));

    // Add Product is only enabled when one of the existing product-save routes is defined.
    $productStoreRoute = collect([
        'owner.sales-inventory.product.store',
        'owner.sales-inventory.products.store',
        'owner.sales-inventory.store-product',
        'owner.sales-inventory.product-store',
    ])->first(fn ($r) => \Illuminate\Support\Facades\Route::has($r));

    // Add Category uses the existing category route when it is defined.
    $categoryStoreRoute = \Illuminate\Support\Facades\Route::has('owner.categories.store') ? 'owner.categories.store' : null;

    // Return / Record Damage: used only when those routes are defined (so the page never breaks if they are not yet).
    $returnRoute = \Illuminate\Support\Facades\Route::has('owner.sales-inventory.return') ? route('owner.sales-inventory.return') : null;
    $damageRoute = \Illuminate\Support\Facades\Route::has('owner.sales-inventory.damage') ? route('owner.sales-inventory.damage') : null;

    // Inventory table: in-stock / low-stock first, out-of-stock last
    $inventoryProducts = $products->sortBy(fn ($p) => $p->stock_status === 'OUT OF STOCK' ? 1 : 0)->values();

    // ---- Inventory table helpers (backend-provided product fields) ----
    $payloadById = collect($productPayloads)->keyBy('id');

    // Reads a backend field from the JSON payload first, then from the Eloquent model, then the fallback.
    $pf = function ($product, $key, $default = null) use ($payloadById) {
        $v = data_get($payloadById->get($product->id), $key);
        if ($v === null) $v = data_get($product, $key);
        return $v === null ? $default : $v;
    };

    // ALL supplier names for a product (supplier_names first, then suppliers), de-duplicated.
    $supplierNamesOf = function ($product) use ($pf) {
        $raw = $pf($product, 'supplier_names');
        if (is_string($raw)) $raw = preg_split('/\s*[,;|]\s*/', $raw, -1, PREG_SPLIT_NO_EMPTY);
        if (empty($raw)) $raw = $pf($product, 'suppliers', []);
        $items = $raw instanceof \Illuminate\Support\Collection ? $raw->all() : (array) $raw;

        return collect($items)->map(function ($s) {
            if (is_string($s)) return trim($s);
            $n = data_get($s, 'supplier_name') ?? data_get($s, 'name') ?? data_get($s, 'supplier.supplier_name') ?? data_get($s, 'supplier');
            return is_scalar($n) ? trim((string) $n) : '';
        })->filter()->unique()->values()->all();
    };

    // Earliest expiration / best-before of the active batches, plus an expired / expiring-soon state.
    $expiryInfo = function ($product) use ($pf) {
        $parse = function ($v) {
            if (! $v) return null;
            try { return \Carbon\Carbon::parse($v)->startOfDay(); } catch (\Throwable $e) { return null; }
        };

        $exp = $parse($pf($product, 'earliest_expiration'));
        $bb  = $parse($pf($product, 'earliest_best_before'));

        $earliest = collect([['exp', $exp], ['bb', $bb]])
            ->filter(fn ($x) => $x[1])
            ->sortBy(fn ($x) => $x[1]->timestamp)
            ->first();

        $kind = $earliest[0] ?? null;
        $days = $earliest ? (int) now()->startOfDay()->diffInDays($earliest[1], false) : null;

        $raw = strtolower((string) $pf($product, 'earliest_expiration_status', ''));
        $state = null;
        if (str_contains($raw, 'expired') || str_contains($raw, 'past')) {
            $state = 'expired';
        } elseif (str_contains($raw, 'soon') || str_contains($raw, 'near') || str_contains($raw, 'expiring')) {
            $state = 'soon';
        } elseif ($days !== null) {
            $state = $days < 0 ? 'expired' : ($days <= 30 ? 'soon' : null);
        }

        return ['exp' => $exp, 'bb' => $bb, 'kind' => $kind, 'days' => $days, 'state' => $state];
    };

    // Product Data Grid (Customer Order): active products only, ordered by stock urgency:
    // OUT OF STOCK first, then LOW STOCK, then IN STOCK, lowest available stock first inside each group.
    $urgencyRank = fn ($p) => $p->stock_status === 'OUT OF STOCK' ? 0 : ($p->stock_status === 'LOW STOCK' ? 1 : 2);
    $gridProducts = $products->where('status', 'active')
        ->sort(function ($a, $b) use ($urgencyRank) {
            $ra = $urgencyRank($a);
            $rb = $urgencyRank($b);
            if ($ra !== $rb) return $ra <=> $rb;
            $sa = (int) $a->available_stock;
            $sb = (int) $b->available_stock;
            if ($sa !== $sb) return $sa <=> $sb;
            return strcmp((string) $a->product_name, (string) $b->product_name);
        })
        ->values();

    // Which modal should re-open (with its errors) after a failed submit
    $hasFlashError = $errors->any() || session('error');
    $failedModal = null;
    if ($hasFlashError) {
        if (is_array(old('items')) && count(old('items'))) $failedModal = 'order';
        elseif (old('purchase_order_id') !== null) $failedModal = 'stockIn';
        elseif (old('actual_stock') !== null) $failedModal = 'adjust';
        elseif (old('product_name') !== null) $failedModal = 'addProduct';
        elseif (old('category_name') !== null) $failedModal = 'addCategory';
    }

    // Values handed to JavaScript (built here so @json receives a plain variable)
    $oldStockInData = null;
    if ($failedModal === 'stockIn') {
        $oldStockInData = [
            'date'    => old('transaction_date'),
            'po'      => old('purchase_order_id'),
            'product' => old('product_id'),
            'qty'     => old('quantity'),
            'batch'   => old('batch_number'),
            'exp'     => old('expiration_date'),
            'bb'      => old('best_before_date'),
            'reason'  => old('reason'),
        ];
    }

    $oldAdjustProduct = $failedModal === 'adjust' ? old('product_id') : null;
@endphp

<main class="main">

    {{-- ##################################################################
         HEADER + ALERTS
         ################################################################## --}}
    <div class="topbar">
        <div>
            <h1 class="page-title">SALES &amp; INVENTORY</h1>
        </div>
        <div class="role-badge">OWNER</div>
    </div>

    @if(session('success'))
        <div class="notice notice-success" role="status">✔ {{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="notice notice-error" role="alert">⚠ {{ session('error') }}</div>
    @endif

    @if(session('warning'))
        <div class="notice notice-warning" role="alert">⚠ {{ session('warning') }}</div>
    @endif

    @if(session('info'))
        <div class="notice notice-info" role="status">ℹ {{ session('info') }}</div>
    @endif

    @if($errors->any() && $failedModal !== 'stockIn')
        <div class="notice notice-error" role="alert">
            <strong>⚠ Please correct the following:</strong>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ##################################################################
         TOP NAVIGATION  (Inventory | Transactions)
         ################################################################## --}}
    <div class="tabs" role="tablist">
        <button type="button" class="tab-button active" data-tab="inventory" onclick="showTab('inventory')">Inventory<span class="pill">{{ number_format($cntTotal) }}</span></button>
        <button type="button" class="tab-button" data-tab="transactions" onclick="showTab('transactions')">Transactions<span class="pill">{{ number_format($cntOrders) }}</span></button>
    </div>


    {{-- ##################################################################
         1. INVENTORY (default)
         Shows the CURRENT state of inventory only.
         Beginning Balance / Stock In / Stock Out history is a reporting
         (stock card) value and belongs to the Reports module.
         ################################################################## --}}
    <section class="tab-pane" id="tab-inventory">

        <div class="summary">
            <button type="button" class="stat" onclick="setInvStatus('')">
                <div class="stat-label">Total Products</div><div class="stat-value">{{ number_format($cntTotal) }}</div>
            </button>
            <button type="button" class="stat green" onclick="setInvStatus('IN STOCK')">
                <div class="stat-label">In Stock</div><div class="stat-value">{{ number_format($cntIn) }}</div>
            </button>
            <button type="button" class="stat amber {{ $cntLow > 0 ? 'has' : '' }}" onclick="setInvStatus('LOW STOCK')">
                <div class="stat-label">Low Stock</div><div class="stat-value">{{ number_format($cntLow) }}</div>
            </button>
            <button type="button" class="stat red {{ $cntOut > 0 ? 'has' : '' }}" onclick="setInvStatus('OUT OF STOCK')">
                <div class="stat-label">Out of Stock</div><div class="stat-value">{{ number_format($cntOut) }}</div>
            </button>
            <button type="button" class="stat blue" onclick="showTab('transactions'); setOrderFilter('pending_inventory_check');">
                <div class="stat-label">Pending Orders</div><div class="stat-value">{{ number_format($cntPendingOrders) }}</div>
            </button>
            <a href="{{ route('owner.purchase-orders') }}" class="stat blue" title="Approved / partially received purchase orders still awaiting Stock In">
                <div class="stat-label">Pending Purchase Orders</div><div class="stat-value">{{ number_format($cntPendingPo) }}</div>
            </a>
        </div>

        <div class="control-bar">
            <div class="toolbar">
                <div class="search-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    <input type="text" id="invSearch" placeholder="Search product, brand, category or supplier..." oninput="filterInventory()">
                </div>
                <select id="invBrand" onchange="filterInventory()">
                    <option value="">All Brands</option>
                    @foreach($brandList as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
                <select id="invCategory" onchange="filterInventory()">
                    <option value="">All Categories</option>
                    @foreach($categoryList as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
                <select id="invStatus" onchange="filterInventory()">
                    <option value="">All Stock Status</option>
                    <option value="IN STOCK">In Stock</option>
                    <option value="LOW STOCK">Low Stock</option>
                    <option value="OUT OF STOCK">Out of Stock</option>
                </select>
                <span class="spacer"></span>
                <button type="button" class="btn btn-primary" onclick="openAddProduct()">+ ADD PRODUCT</button>
                <button type="button" class="btn btn-success" onclick="openOrderModal()">+ CUSTOMER ORDER</button>
                <button type="button" class="btn btn-outline" onclick="openStockIn()">+ STOCK IN</button>
                <button type="button" class="btn btn-outline" onclick="openReturn()">↩ Return</button>
                <button type="button" class="btn btn-outline" onclick="openDamage()">⚠ Record Damage</button>
            </div>
        </div>

        <div class="table-card">
            <div class="table-wrapper">
                <table id="inventoryTable">
                    <thead>
                        <tr>
                            <th>Item / Product</th><th>Brand</th><th>Category</th><th>Supplier(s)</th>
                            <th class="text-right">Current Stock</th><th class="text-right">Reorder Level</th>
                            <th>Expiration / Best Before</th><th>Status</th><th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inventoryProducts as $product)
                            @php
                                $current  = (int) ($product->inventory->current_stock ?? 0);
                                $reserved = max(0, $current - (int) $product->available_stock);

                                $supNames = $supplierNamesOf($product);
                                $ex       = $expiryInfo($product);
                            @endphp
                            <tr class="{{ $product->stock_status === 'OUT OF STOCK' ? 'row-out' : '' }}"
                                data-search="{{ mb_strtolower($product->product_name . ' ' . $product->brand . ' ' . ($product->category->category_name ?? '') . ' ' . implode(' ', $supNames)) }}"
                                data-status="{{ $product->stock_status }}"
                                data-brand="{{ $product->brand }}"
                                data-category="{{ $product->category->category_name ?? '' }}">
                                <td>
                                    <strong>{{ $product->product_name }}</strong>
                                    <div class="sub">{{ $product->unit }}@if($product->status !== 'active') &middot; <span class="badge b-gray">{{ ucfirst($product->status) }}</span>@endif</div>
                                </td>
                                <td>{{ $product->brand ?: '—' }}</td>
                                <td>{{ $product->category->category_name ?? '—' }}</td>

                                {{-- ALL suppliers of the product (no "first supplier only") --}}
                                <td class="sup-list">
                                    @forelse($supNames as $supName)
                                        <span class="chip-sup">{{ $supName }}</span>
                                    @empty
                                        <span class="muted" title="No suppliers found">—</span>
                                    @endforelse
                                </td>

                                {{-- Current physical stock (Opening Stock + Stock In − Stock Out ± adjustments) --}}
                                <td class="text-right num">
                                    <strong>{{ number_format($current) }}</strong>
                                    <div class="sub">Reserved {{ number_format($reserved) }} &middot; Avail. {{ number_format($product->available_stock) }}</div>
                                </td>

                                <td class="text-right num">{{ number_format($product->reorder_level) }}</td>

                                {{-- Earliest active-batch expiration / best-before (batch information, not a product field) --}}
                                <td class="exp-cell">
                                    @if($ex['exp'] || $ex['bb'])
                                        @if($ex['exp'])<div><span class="exp-lbl">Exp.</span>{{ $ex['exp']->format('M d, Y') }}</div>@endif
                                        @if($ex['bb'])<div><span class="exp-lbl">Best before</span>{{ $ex['bb']->format('M d, Y') }}</div>@endif

                                        @if($ex['state'] === 'expired')
                                            <div class="exp-flag"><span class="badge b-red">{{ $ex['kind'] === 'bb' ? 'PAST BEST BEFORE' : 'EXPIRED' }}</span></div>
                                        @elseif($ex['state'] === 'soon')
                                            <div class="exp-flag"><span class="badge b-amber">
                                                @if($ex['days'] !== null && $ex['days'] >= 0)
                                                    {{ $ex['kind'] === 'bb' ? 'Best before in' : 'Expires in' }} {{ $ex['days'] }} day{{ $ex['days'] === 1 ? '' : 's' }}
                                                @else
                                                    Expiring soon
                                                @endif
                                            </span></div>
                                        @endif
                                    @else
                                        <span class="muted">No Date</span>
                                    @endif
                                </td>

                                <td><span class="badge {{ $stockBadge[$product->stock_status] ?? 'b-gray' }}">{{ $product->stock_status }}</span></td>
                                <td>
                                    <div class="actions">
                                        <button type="button" class="btn btn-sm btn-light" onclick="openProductModal({{ $product->id }})">View</button>
                                        @if($product->status === 'active')
                                            <button type="button" class="btn btn-sm btn-success" onclick="openOrderModal({{ $product->id }})">+ Order</button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-outline" onclick="openAdjust({{ $product->id }})">Adjust</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="empty-state">No products yet. Click <strong>+ ADD PRODUCT</strong> to create one.</td></tr>
                        @endforelse

                        @if($products->count())
                            <tr class="no-match hidden"><td colspan="9" class="empty-state">No products found for the selected filters.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pager" id="pager-inventoryTable"></div>
    </section>


    {{-- ##################################################################
         2. TRANSACTIONS
         ################################################################## --}}
    <section class="tab-pane" id="tab-transactions">

        <div class="control-bar">
            <div class="toolbar">
                <div class="search-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                    <input type="text" id="orderSearch" placeholder="Search order number or customer..." oninput="filterOrders()">
                </div>
                <select id="orderStatusFilter" onchange="filterOrders()">
                    <option value="">All Order Statuses</option>
                    <option value="pending_inventory_check">Pending</option>
                    <option value="confirmed">Confirmed (Open)</option>
                    <option value="partially_fulfilled">Partially Fulfilled</option>
                    <option value="fulfilled">Fulfilled</option>
                    <option value="for_purchasing">For Purchasing</option>
                    <option value="cancelled">Cancelled / Voided</option>
                </select>
                <select id="orderPayFilter" onchange="filterOrders()">
                    <option value="">All Payment Statuses</option>
                    <option value="paid">Paid</option>
                    <option value="partial">Partial</option>
                    <option value="pending">Pending</option>
                    <option value="unpaid">Unpaid</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <span class="spacer"></span>
                <button type="button" class="btn btn-success" onclick="openOrderModal()">+ CUSTOMER ORDER</button>
            </div>
        </div>

        <div class="table-card">
            <div class="table-wrapper scroll-y-lg">
                <table id="ordersTable">
                    <thead>
                        <tr>
                            <th>Order #</th><th>Date</th><th>Customer</th><th>Items</th>
                            <th class="text-right">Total</th><th>Payment</th><th class="text-right">Collected</th><th class="text-right">Balance</th>
                            <th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orderPayloads as $o)
                            @php
                                $hasMore = $o['can_recheck'] || $o['can_confirm'] || $o['can_release'] || $o['can_pay'] || $o['can_cancel'] || $o['can_void'];
                                $itemCount = is_countable($o['items'] ?? null) ? count($o['items']) : 0;
                            @endphp
                            <tr data-search="{{ strtolower($o['order_number'] . ' ' . $o['customer_name']) }}" data-status="{{ $o['status'] }}" data-pay="{{ $o['payment_status'] }}">
                                <td><strong>{{ $o['order_number'] }}</strong></td>
                                <td>{{ $o['order_date'] }}</td>
                                <td>
                                    <strong>{{ $o['customer_name'] }}</strong>
                                    @if($o['customer_contact'])<div class="sub">{{ $o['customer_contact'] }}</div>@endif
                                </td>
                                <td>{{ $itemCount }} item{{ $itemCount === 1 ? '' : 's' }}<div class="sub">Remaining qty: {{ number_format($o['remaining_qty']) }}</div></td>
                                <td class="text-right num"><strong>{{ $peso($o['total']) }}</strong></td>
                                <td><span class="badge {{ $payBadge[$o['payment_status']][1] ?? 'b-gray' }}">{{ $payBadge[$o['payment_status']][0] ?? ucfirst($o['payment_status']) }}</span></td>
                                <td class="text-right num">{{ $peso($o['collected']) }}</td>
                                <td class="text-right num">{{ $peso($o['balance']) }}</td>
                                <td><span class="badge {{ $orderBadge[$o['status']] ?? 'b-gray' }}">{{ $o['status_label'] }}</span></td>
                                <td>
                                    <div class="actions">
                                        <button type="button" class="btn btn-sm btn-light" onclick="viewOrder({{ $o['id'] }})">View</button>

                                        @if($hasMore)
                                            <details class="dd">
                                                <summary class="btn btn-sm btn-outline">Actions &#9662;</summary>
                                                <div class="dd-menu">
                                                    @if($o['can_recheck'])<button type="button" onclick="orderAction('recheck', {{ $o['id'] }})">Recheck</button>@endif
                                                    @if($o['can_confirm'])<button type="button" onclick="orderAction('confirm', {{ $o['id'] }})">Confirm</button>@endif
                                                    @if($o['can_release'])<button type="button" onclick="openRelease({{ $o['id'] }})">Release</button>@endif
                                                    @if($o['can_pay'])<button type="button" onclick="openPayment({{ $o['id'] }})">Payment</button>@endif
                                                    @if($o['can_cancel'] || $o['can_void'])<hr>@endif
                                                    @if($o['can_cancel'])<button type="button" class="danger" onclick="orderAction('cancel', {{ $o['id'] }})">Cancel</button>@endif
                                                    @if($o['can_void'])<button type="button" class="danger" onclick="openVoid({{ $o['id'] }})">Void</button>@endif
                                                </div>
                                            </details>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="empty-state">No customer transactions found. Click <strong>+ CUSTOMER ORDER</strong> to create one.</td></tr>
                        @endforelse

                        @if($cntOrders)
                            <tr class="no-match hidden"><td colspan="10" class="empty-state">No customer transactions match the selected filters.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="pager" id="pager-ordersTable"></div>
    </section>

</main>


{{-- ##################################################################
     ADD PRODUCT MODAL
     PRODUCT INFORMATION (product master)  +  OPENING STOCK / BATCH
     INFORMATION. Expiration / best-before are batch fields and are
     never part of Product Information.
     Required (*): Product Name, Category, Unit, Selling Price.
     ################################################################## --}}
<datalist id="unitOptions">
    @foreach($unitList as $u)<option value="{{ $u }}"></option>@endforeach
</datalist>

<div class="modal" id="addProductModal">
    <div class="modal-content" style="max-width:680px;">
        <div class="modal-header">
            <h2>ADD NEW PRODUCT</h2>
            <button type="button" class="close-x" aria-label="Close" onclick="closeModal('addProductModal')">&times;</button>
        </div>
        <div class="modal-body">

            @if($failedModal === 'addProduct')
                <div class="notice notice-error" role="alert">
                    <strong>⚠ Please correct the following:</strong>
                    <ul>
                        @if(session('error'))<li>{{ session('error') }}</li>@endif
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            @if(! $productStoreRoute)
                <div class="notice notice-warning notice-sm" role="alert" style="margin-bottom:12px;">Saving products is not connected to a route yet.</div>
            @endif

            <div id="apError" class="inline-error hidden" role="alert" style="margin-bottom:12px;"></div>

            <p class="legend-req"><span class="req" aria-hidden="true">*</span> Required field</p>

            <form action="{{ $productStoreRoute ? route($productStoreRoute) : '#' }}" method="POST" id="productForm" novalidate>
                @csrf

                {{-- ============ PRODUCT INFORMATION ============ --}}
                <div class="form-section">
                    <p class="section-title">Product Information</p>

                    <div class="form-group">
                        <label for="apName">Product Name <span class="req" aria-hidden="true">*</span></label>
                        <input type="text" name="product_name" id="apName" maxlength="255" aria-required="true" class="{{ $errors->has('product_name') ? 'bad' : '' }}" value="{{ old('product_name') }}">
                        @error('product_name')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="apBrand">Brand</label>
                        <div class="input-row">
                            <select name="brand" id="apBrand">
                                <option value="">— No brand —</option>
                                @foreach($brandList as $b)
                                    <option value="{{ $b }}" {{ old('brand') === $b ? 'selected' : '' }}>{{ $b }}</option>
                                @endforeach
                                @if(old('brand') && ! $brandList->contains(old('brand')))
                                    <option value="{{ old('brand') }}" selected>{{ old('brand') }}</option>
                                @endif
                            </select>
                            <button type="button" class="btn btn-outline btn-sm" onclick="openAddBrand()">+ Add Brand</button>
                        </div>
                        <span class="field-note hidden" id="apBrandNote"></span>
                        @error('brand')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="catSearch" id="catLabel">Category <span class="req" aria-hidden="true">*</span></label>

                        <div class="input-row">
                            <div class="combo" id="catCombo" style="flex:1;min-width:0;">
                                {{-- The real submitted field (unchanged name): category_id --}}
                                <input type="hidden" name="category_id" id="apCategory" value="{{ old('category_id') }}">

                                <div class="combo-field {{ $errors->has('category_id') ? 'bad' : '' }}" id="catField">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>

                                    <input type="text" id="catSearch" class="combo-input" placeholder="Search category..." autocomplete="off"
                                           role="combobox" aria-required="true" aria-autocomplete="list" aria-expanded="false" aria-controls="catList">

                                    <div class="combo-selected hidden" id="catSelected">
                                        <span id="catSelectedText"></span>
                                        <button type="button" class="combo-clear" id="catClear" aria-label="Clear selected category" title="Clear">&times;</button>
                                    </div>
                                </div>

                                <ul class="combo-list hidden" id="catList" role="listbox" aria-labelledby="catLabel"></ul>
                            </div>

                            @if($categoryStoreRoute)
                                <button type="button" class="btn btn-outline btn-sm" onclick="openAddCategory()">+ Add Category</button>
                            @endif
                        </div>
                        @error('category_id')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="apApi">API</label>
                            <input type="text" name="api" id="apApi" maxlength="255" value="{{ old('api') }}">
                            @error('api')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="apBaseOil">Base Oil</label>
                            <input type="text" name="base_oil" id="apBaseOil" maxlength="255" value="{{ old('base_oil') }}">
                            @error('base_oil')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="apPackage">Package Size</label>
                            <input type="text" name="package_size" id="apPackage" maxlength="255" value="{{ old('package_size') }}">
                            @error('package_size')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="apUnit">Unit <span class="req" aria-hidden="true">*</span></label>
                            <input type="text" name="unit" id="apUnit" list="unitOptions" maxlength="255" autocomplete="off" aria-required="true" class="{{ $errors->has('unit') ? 'bad' : '' }}" value="{{ old('unit') }}">
                            @error('unit')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="apPrice">Selling Price <span class="req" aria-hidden="true">*</span></label>
                            <input type="number" name="unit_price" id="apPrice" min="0" step="0.01" aria-required="true" class="{{ $errors->has('unit_price') ? 'bad' : '' }}" value="{{ old('unit_price') }}">
                            <small>Price DPAM charges the customer (₱). Not the supplier purchase cost.</small>
                            @error('unit_price')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label for="apReorder">Reorder Level</label>
                            <input type="number" name="reorder_level" id="apReorder" min="0" step="1" value="{{ old('reorder_level', 0) }}">
                            @error('reorder_level')<span class="field-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="apSupSearch">Supplier(s)</label>
                        <div class="picker">
                            <input type="text" class="picker-search" id="apSupSearch" placeholder="Search suppliers..." autocomplete="off">
                            <div class="picker-list" id="apSupList"></div>
                            <div class="picker-foot"><span id="apSupCount">0 selected</span></div>
                        </div>
                        <small>A product can have several suppliers. This only links the supplier; the purchase cost is entered on each Purchase Order.</small>
                        @if($supplierChoices->isEmpty())
                            <small>No active suppliers yet. <a href="{{ route('owner.suppliers') }}" class="link-btn">Add a supplier</a></small>
                        @endif
                        @error('supplier_ids')<span class="field-error">{{ $message }}</span>@enderror
                        @error('supplier_ids.*')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="apStatus">Status</label>
                        <select name="status" id="apStatus">
                            <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                        @error('status')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{-- ============ OPENING STOCK / BATCH INFORMATION ============ --}}
                <div class="form-section">
                    <p class="section-title">Initial Inventory / Batch Information</p>

                    <div class="form-group">
                        <label for="apOpening">Opening Stock (Existing Stock on Hand)</label>
                        <input type="number" name="opening_stock" id="apOpening" min="0" step="1" value="{{ old('opening_stock', 0) }}" oninput="apToggleBatch()">
                        <small>Enter the quantity DPAM already has in stock before this product is tracked in the system. Do not enter a new supplier delivery here. Supplier deliveries are recorded through Stock In.</small>
                        <div class="notice notice-info notice-sm" style="margin-top:6px;">
                            ℹ Opening Stock is your <strong>starting inventory</strong>, not a supplier delivery. Do not record the same quantity again under Stock In. Leave it at 0 if there is no stock on hand yet.
                        </div>
                        @error('opening_stock')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="batch-box hidden" id="apBatchGroup">
                        <p class="section-title" style="margin-bottom:8px;">Opening batch <span class="muted" style="text-transform:none;letter-spacing:0;font-weight:400;">(batch information, not product information)</span></p>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="apBatchNo">Batch Number <small>(Optional)</small></label>
                                <input type="text" name="batch_number" id="apBatchNo" maxlength="100" autocomplete="off" value="{{ old('batch_number') }}">
                                @error('batch_number')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="apBatchCost">Purchase Unit Cost <small>(Optional)</small></label>
                                <input type="number" name="purchase_unit_cost" id="apBatchCost" min="0" step="0.01" value="{{ old('purchase_unit_cost') }}">
                                <small>What DPAM paid per unit (₱). Not the selling price.</small>
                                @error('purchase_unit_cost')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="apBatchExp">Expiration Date <small>(Optional)</small></label>
                                <input type="date" name="expiration_date" id="apBatchExp" value="{{ old('expiration_date') }}">
                                @error('expiration_date')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group">
                                <label for="apBatchBb">Best Before Date <small>(Optional)</small></label>
                                <input type="date" name="best_before_date" id="apBatchBb" value="{{ old('best_before_date') }}">
                                @error('best_before_date')<span class="field-error">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('addProductModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-label="Save Product" {{ $productStoreRoute ? '' : 'disabled' }}>Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ADD BRAND (brand is a text value on the product; no brand route is needed) --}}
<div class="modal" id="addBrandModal">
    <div class="modal-content" style="max-width:420px;">
        <div class="modal-header">
            <h2>Add Brand</h2>
            <button type="button" class="close-x" aria-label="Close" onclick="closeModal('addBrandModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div id="abError" class="inline-error hidden" role="alert" style="margin-bottom:10px;"></div>
            <div class="form-group">
                <label for="abName">Brand Name <span class="req" aria-hidden="true">*</span></label>
                <input type="text" id="abName" maxlength="255" autocomplete="off" placeholder="e.g. Castrol">
            </div>
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="closeModal('addBrandModal')">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveBrand()">Save Brand</button>
            </div>
        </div>
    </div>
</div>


{{-- ADD CATEGORY (posts to the existing category route) --}}
@if($categoryStoreRoute)
<div class="modal" id="addCategoryModal">
    <div class="modal-content" style="max-width:420px;">
        <div class="modal-header">
            <h2>Add Category</h2>
            <button type="button" class="close-x" aria-label="Close" onclick="closeModal('addCategoryModal')">&times;</button>
        </div>
        <div class="modal-body">
            @if($failedModal === 'addCategory')
                <div class="notice notice-error" role="alert">
                    <strong>⚠ Please correct the following:</strong>
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            <div id="acError" class="inline-error hidden" role="alert" style="margin-bottom:10px;"></div>

            <form action="{{ route($categoryStoreRoute) }}" method="POST" id="categoryForm" novalidate>
                @csrf
                <input type="hidden" name="status" value="active">
                <div class="form-group">
                    <label for="acName">Category Name <span class="req" aria-hidden="true">*</span></label>
                    <input type="text" name="category_name" id="acName" maxlength="255" autocomplete="off" placeholder="e.g. Lubricants" value="{{ old('category_name') }}">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('addCategoryModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-label="Save Category">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif


{{-- ##################################################################
     CUSTOMER ORDER MODAL  (the main sales workflow)
     ################################################################## --}}
<div class="modal" id="orderFormModal">
    <div class="modal-content modal-xl">
        <div class="modal-header">
            <h2>CUSTOMER ORDER</h2>
            <button type="button" class="close-x" title="Close (your draft is kept)" onclick="closeModal('orderFormModal')">&times;</button>
        </div>

        <form method="POST" action="{{ route('owner.sales-inventory.checkout') }}" id="saleForm" onkeydown="if (event.key === 'Enter' && event.target.tagName !== 'TEXTAREA' && event.target.type !== 'submit') { event.preventDefault(); }">
            @csrf
            <input type="hidden" name="checkout_token" id="checkoutToken">
            <div id="cartHidden"></div>

            <div class="modal-body">

                @if($failedModal === 'order')
                    <div class="notice notice-error" role="alert">
                        <strong>⚠ Please correct the following:</strong>
                        <ul>
                            @if(session('error'))<li>{{ session('error') }}</li>@endif
                            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                        </ul>
                        <div style="margin-top:6px;">Your selected items and entries were kept.</div>
                    </div>
                @endif

                <div id="orderWarn" class="notice notice-warning hidden" role="alert"></div>

                <div class="oc-top">

                    {{-- LEFT: PRODUCT INFORMATION + PRODUCT DATA GRID --}}
                    <div class="oc-col">

                        <div class="card">
                            <p class="section-title">Product Information</p>

                            <div id="infoEmpty" class="empty-state">Select a product from the grid below.</div>

                            <div id="infoBody" class="hidden">
                                <div id="stockAlert" class="stock-alert ok"></div>

                                <div class="info-grid">
                                    <div class="detail-item"><span class="detail-label">Product Name</span><span class="detail-value" id="iName"></span></div>
                                    <div class="detail-item"><span class="detail-label">Brand</span><span class="detail-value" id="iBrand"></span></div>
                                    <div class="detail-item"><span class="detail-label">Category</span><span class="detail-value" id="iCategory"></span></div>
                                    <div class="detail-item"><span class="detail-label">API</span><span class="detail-value" id="iApi"></span></div>
                                    <div class="detail-item"><span class="detail-label">Base Oil</span><span class="detail-value" id="iBaseOil"></span></div>
                                    <div class="detail-item"><span class="detail-label">Package Size</span><span class="detail-value" id="iPackage"></span></div>
                                    <div class="detail-item"><span class="detail-label">Unit</span><span class="detail-value" id="iUnit"></span></div>
                                    <div class="detail-item price-item"><span class="detail-label">Selling Price</span><span class="detail-value" id="iPrice"></span></div>
                                    <div class="detail-item"><span class="detail-label">Product Status</span><span class="detail-value" id="iProdStatus"></span></div>
                                    <div class="detail-item"><span class="detail-label">Stock Status</span><span class="detail-value" id="iStatus"></span></div>
                                    <div class="detail-item"><span class="detail-label">Current Stock</span><span class="detail-value" id="iCurrent"></span></div>
                                    <div class="detail-item"><span class="detail-label">Reserved Stock</span><span class="detail-value" id="iReserved"></span></div>
                                    <div class="detail-item"><span class="detail-label">Available Stock</span><span class="detail-value" id="iAvailable"></span></div>
                                    <div class="detail-item"><span class="detail-label">Reorder Level</span><span class="detail-value" id="iReorder"></span></div>
                                    <div class="detail-item"><span class="detail-label">Expiration / Best Before</span><span class="detail-value" id="iExpiry"></span></div>
                                </div>

                                <div class="add-row">
                                    <button type="button" class="btn btn-primary" id="addBtn" onclick="addSelectedToCart()">Add to Order</button>
                                    <button type="button" class="btn btn-outline btn-sm" onclick="openProductModal(selectedId)">Full details</button>
                                </div>

                                <div class="sup-block">
                                    <p class="section-title" style="margin-bottom:6px;">Supplier(s)</p>
                                    <div id="supplierList"></div>
                                </div>

                                <div class="sup-block">
                                    <p class="section-title" style="margin-bottom:6px;">Where the current stock came from (active batches)</p>
                                    <div class="inner-table" style="margin-bottom:4px;">
                                        <table style="min-width:640px;">
                                            <thead>
                                                <tr>
                                                    <th>Batch No.</th><th>Supplier</th><th>Received</th><th>Expiration</th><th>Best Before</th>
                                                    <th class="text-right">Qty Remaining</th><th>Batch Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="infoBatches"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-head" style="margin-bottom:8px;">
                                <p class="section-title" style="margin:0;">Product Data Grid</p>
                                <span class="muted" id="gridCount" style="font-size:12px;"></span>
                            </div>

                            <div class="toolbar" style="margin-bottom:8px;">
                                <div class="search-wrap">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                                    <input type="text" id="gridSearch" placeholder="Search product, brand or category..." oninput="filterGrid()" onkeydown="if (event.key === 'Enter') { event.preventDefault(); }">
                                </div>
                            </div>

                            <div class="table-card">
                                <div class="table-wrapper scroll-y">
                                    <table id="productGrid">
                                        <thead>
                                            <tr>
                                                <th>Product</th><th>Brand</th><th>Category</th><th>Supplier(s)</th>
                                                <th class="text-right">Available</th><th class="text-right">Selling Price</th><th>Status</th><th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- All active products stay visible, most urgent first: OUT OF STOCK, LOW STOCK, then IN STOCK (lowest stock first) --}}
                                            @forelse($gridProducts as $product)
                                                <tr class="selectable {{ $product->stock_status === 'OUT OF STOCK' ? 'row-out' : '' }}"
                                                    data-id="{{ $product->id }}"
                                                    data-search="{{ strtolower($product->product_name . ' ' . $product->brand . ' ' . ($product->category->category_name ?? '')) }}"
                                                    onclick="selectProduct({{ $product->id }})">
                                                    <td><strong>{{ $product->product_name }}</strong><div class="sub">{{ $product->unit }}</div></td>
                                                    <td>{{ $product->brand ?: '—' }}</td>
                                                    <td>{{ $product->category->category_name ?? '—' }}</td>
                                                    <td class="sup-cell" data-id="{{ $product->id }}"><span class="sub">...</span></td>
                                                    <td class="text-right num"><strong>{{ number_format($product->available_stock) }}</strong></td>
                                                    <td class="text-right num">{{ $peso($product->unit_price) }}</td>
                                                    <td><span class="badge {{ $stockBadge[$product->stock_status] ?? 'b-gray' }}">{{ $product->stock_status }}</span></td>
                                                    <td><button type="button" class="btn btn-sm btn-light" onclick="event.stopPropagation(); selectProduct({{ $product->id }})">Select</button></td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="8" class="empty-state">No products found.</td></tr>
                                            @endforelse

                                            @if($gridProducts->count())
                                                <tr class="no-match hidden"><td colspan="8" class="empty-state">No products found for this search.</td></tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="pager" id="pager-productGrid"></div>
                        </div>

                    </div>

                    {{-- RIGHT: SELECTED ITEMS --}}
                    <div class="oc-col">
                        <div class="card">
                            <div class="card-head" style="margin-bottom:8px;">
                                <p class="section-title" style="margin:0;">Selected Items <span class="badge b-blue" id="cartCount">0</span></p>
                            </div>
                            <div class="cart-list" id="cartList"></div>
                            <div class="notice notice-warning notice-sm hidden" id="shortBox" role="alert" style="margin-top:8px;"></div>
                        </div>
                    </div>
                </div>

                {{-- BOTTOM: CASHIERING --}}
                <div class="oc-bottom">

                    <div>
                        <h3 class="section-title">Cashiering &mdash; Customer</h3>
                        <div class="form-group">
                            <label>Customer Name</label>
                            <input type="text" name="customer_name" maxlength="255" placeholder="Walk-in Customer" value="{{ old('customer_name') }}">
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Customer Contact</label>
                                <input type="text" name="customer_contact" maxlength="255" value="{{ old('customer_contact') }}">
                            </div>
                            <div class="form-group">
                                <label>Order Date</label>
                                <input type="date" name="order_date" value="{{ old('order_date', now()->format('Y-m-d')) }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Notes</label>
                            <textarea name="notes" maxlength="5000" placeholder="Optional order notes">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    <div>
                        <h3 class="section-title">Payment</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Payment Type</label>
                                <select name="payment_method" id="payMethod" onchange="updatePaymentUI(true)">
                                    <option value="cash">Cash</option>
                                    <option value="gcash">GCash</option>
                                    <option value="maya">Maya</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="check">Check</option>
                                    <option value="pdc">PDC</option>
                                    <option value="credit">Credit / Utang</option>
                                </select>
                            </div>
                            <div class="form-group" id="amountGroup">
                                <label>Amount Paid</label>
                                <input type="number" name="amount_paid" id="amountPaid" min="0" step="0.01" oninput="amountTouched=true; updatePaymentUI(false)">
                            </div>
                            <div class="form-group">
                                <label>Receipt Number</label>
                                <input type="text" name="receipt_number" maxlength="100" placeholder="Auto-generated if blank" value="{{ old('receipt_number') }}">
                            </div>
                            <div class="form-group">
                                <label>Received By <span class="required">*</span></label>
                                <input type="text" name="received_by" id="receivedBy" maxlength="255" placeholder="Person who received goods" value="{{ old('received_by') }}">
                            </div>
                        </div>

                        <div id="cartRefFields" class="hidden">
                            <div class="form-group">
                                <label>Reference Number <small>(optional)</small></label>
                                <input type="text" name="reference_number" maxlength="100" placeholder="GCash / Maya / bank transaction reference" value="{{ old('reference_number') }}">
                            </div>
                        </div>

                        <div id="cartCheckFields" class="hidden">
                            <div class="form-grid">
                                <div class="form-group"><label>Check Number</label><input type="text" name="check_number" maxlength="100" value="{{ old('check_number') }}"></div>
                                <div class="form-group"><label>Bank Name</label><input type="text" name="bank_name" maxlength="150" value="{{ old('bank_name') }}"></div>
                                <div class="form-group"><label>Check Date</label><input type="date" name="check_date" value="{{ old('check_date') }}"></div>
                                <div class="form-group"><label>Maturity Date</label><input type="date" name="maturity_date" value="{{ old('maturity_date') }}"></div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="section-title">Totals</h3>
                        <div class="totals-box">
                            <div class="totals-row"><span>Subtotal</span><span id="cartSubtotal" class="num">₱0.00</span></div>
                            <div class="totals-row big"><span>TOTAL</span><span id="cartTotal" class="num">₱0.00</span></div>
                            <div class="totals-row hl"><span id="balLabel">Balance</span><span id="cartBalance" class="num">₱0.00</span></div>
                            <div class="totals-row hl"><span>Change</span><span id="cartChange" class="num">₱0.00</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div class="foot-total">Order Total<strong id="footTotal">₱0.00</strong></div>
                <div class="btns">
                    <button type="button" class="btn btn-outline btn-lg" onclick="cancelOrder()">CANCEL</button>
                    <button type="submit" class="btn btn-outline btn-lg" id="openOrderBtn" title="Reserves stock without deducting it" formnovalidate formaction="{{ route('owner.sales-inventory.order') }}" data-label="SAVE ORDER">SAVE ORDER</button>
                    <button type="submit" class="btn btn-success btn-lg" id="checkoutBtn" title="Deducts stock and records the payment" formaction="{{ route('owner.sales-inventory.checkout') }}" data-label="CHECKOUT">CHECKOUT</button>
                </div>
            </div>
        </form>
    </div>
</div>


{{-- ##################################################################
     ORDER DETAILS MODAL
     ################################################################## --}}
<div class="modal" id="orderModal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <div><h2>Order Details</h2><p id="voNumber"></p></div>
            <button type="button" class="close-x" onclick="closeModal('orderModal')">&times;</button>
        </div>
        <div class="modal-body">

            <p class="section-title">Customer Information</p>
            <div class="details-grid" style="grid-template-columns:repeat(2,1fr);">
                <div class="detail-item"><span class="detail-label">Customer Name</span><span class="detail-value" id="voCustomer"></span></div>
                <div class="detail-item"><span class="detail-label">Contact</span><span class="detail-value" id="voContact"></span></div>
            </div>

            <p class="section-title">Order Information</p>
            <div class="details-grid">
                <div class="detail-item"><span class="detail-label">Order Number</span><span class="detail-value" id="voOrderNo"></span></div>
                <div class="detail-item"><span class="detail-label">Order Date</span><span class="detail-value" id="voDate"></span></div>
                <div class="detail-item"><span class="detail-label">Status</span><span class="detail-value" id="voStatus"></span></div>
                <div class="detail-item"><span class="detail-label">Inventory Check</span><span class="detail-value" id="voCheckNotes"></span></div>
                <div class="detail-item"><span class="detail-label">Checked By</span><span class="detail-value" id="voCheckedBy"></span></div>
                <div class="detail-item"><span class="detail-label">Notes</span><span class="detail-value" id="voNotes"></span></div>
            </div>

            <p class="section-title">Items</p>
            <div class="inner-table">
                <table>
                    <thead><tr><th>Product</th><th>Brand</th><th class="text-right">Quantity</th><th class="text-right">Reserved</th><th class="text-right">Fulfilled</th><th class="text-right">Unit Price</th><th class="text-right">Subtotal</th></tr></thead>
                    <tbody id="voItems"></tbody>
                </table>
            </div>

            <p class="section-title">Payment Summary</p>
            <div class="totals-box" style="margin-bottom:14px;">
                <div class="totals-row big" style="border-top:none;margin-top:0;"><span>Total</span><span id="voTotal" class="num"></span></div>
                <div class="totals-row"><span>Collected (paid / cleared)</span><span id="voCollected" class="num"></span></div>
                <div class="totals-row"><span>Pending checks</span><span id="voPending" class="num"></span></div>
                <div class="totals-row hl"><span>Balance</span><span id="voBalance" class="num"></span></div>
                <div class="totals-row"><span>Payment Status</span><span id="voPayStatus"></span></div>
            </div>

            <p class="section-title">Payment History</p>
            <div class="inner-table">
                <table>
                    <thead><tr><th>Receipt</th><th>Date</th><th>Method</th><th class="text-right">Amount</th><th>Status</th></tr></thead>
                    <tbody id="voPayments"></tbody>
                </table>
            </div>

            <div class="box hidden" id="voVoidBox" style="border-color:#fecaca;background:#fff7f7;">
                <div class="box-title" style="color:#991b1b;">Void Information</div>
                <div id="voVoid"></div>
            </div>

            <div class="form-actions"><button type="button" class="btn btn-outline" onclick="closeModal('orderModal')">Close</button></div>
        </div>
    </div>
</div>


{{-- ##################################################################
     PRODUCT DETAILS MODAL
     ################################################################## --}}
<div class="modal" id="productModal">
    <div class="modal-content modal-large">
        <div class="modal-header">
            <h2>Product Information</h2>
            <button type="button" class="close-x" onclick="closeModal('productModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div id="pmLoading" class="loading">Loading product information...</div>
            <div id="pmError" class="hidden"></div>

            <div id="pmBody" class="hidden">
                <div id="pmAlert"></div>
                <div class="details-grid" id="pmDetails"></div>

                <p class="section-title">Suppliers</p>
                <div id="pmSupplierChips" style="margin-bottom:8px;"></div>

                <div class="inner-table">
                    <table>
                        <thead><tr><th>Supplier</th><th>PO No.</th><th>PO Date</th><th class="text-right">Purchase Cost <span class="info-tip" title="Cost charged by the supplier on that purchase order">i</span></th><th class="text-right">Ordered</th><th class="text-right">Received</th></tr></thead>
                        <tbody id="pmSuppliers"></tbody>
                    </table>
                </div>

                <p class="section-title">Batches / Expiration</p>
                <div class="inner-table">
                    <table style="min-width:1050px;">
                        <thead>
                            <tr>
                                <th>Batch / Lot No.</th><th>Supplier</th><th>Purchase Order</th><th>Received Date</th>
                                <th>Expiration Date</th><th>Best Before Date</th>
                                <th class="text-right">Qty Received</th><th class="text-right">Qty Remaining</th>
                                <th class="text-right">Unit Cost</th><th>Batch Status</th>
                            </tr>
                        </thead>
                        <tbody id="pmBatches"></tbody>
                    </table>
                </div>

                <p class="section-title">Recent Movement History</p>
                <div class="inner-table">
                    <table style="min-width:1000px;">
                        <thead><tr><th>Transaction Date</th><th>Movement</th><th>Qty</th><th>Before</th><th>After</th><th>Reason</th><th>Order</th><th>Receipt</th><th>Received By</th><th>Reference</th><th>By</th></tr></thead>
                        <tbody id="pmMovements"></tbody>
                    </table>
                </div>
            </div>

            <div class="form-actions"><button type="button" class="btn btn-outline" onclick="closeModal('productModal')">Close</button></div>
        </div>
    </div>
</div>


{{-- ##################################################################
     STOCK IN MODAL
     New inventory physically received from a supplier. Everything except
     the transaction date, quantity, batch, expiration, best-before and
     reason comes from the selected Purchase Order and is read-only.
     The system increases current stock automatically on save.
     ################################################################## --}}
<div class="modal" id="stockInModal">
    <div class="modal-content">
        <div class="modal-header">
            <div><h2>Stock In</h2><p>Record new inventory physically received from a supplier.</p></div>
            <button type="button" class="close-x" aria-label="Close" onclick="closeModal('stockInModal')">&times;</button>
        </div>
        <div class="modal-body">

            @if($failedModal === 'stockIn')
                <div class="notice notice-error" role="alert" id="siServerError">
                    <strong>⚠ Please correct the following:</strong>
                    <ul>
                        @if(session('error'))<li>{{ session('error') }}</li>@endif
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="notice notice-info notice-sm" style="margin-bottom:12px;">
                ℹ <strong>Stock In records new inventory physically received by DPAM.</strong> Stock DPAM already had before the product was added is entered as Opening Stock when adding the product, not here. The current stock updates automatically when you save.
            </div>

            <div id="siError" class="inline-error hidden" role="alert" style="margin-bottom:12px;"></div>

            <form action="{{ route('owner.sales-inventory.stock-in') }}" method="POST" id="stockInForm" novalidate>
                @csrf

                <div class="form-group">
                    <label for="siDate">Transaction Date (Date Received)</label>
                    <input type="date" name="transaction_date" id="siDate" value="{{ now()->format('Y-m-d') }}" onchange="siSetMinDates()">
                    <small>Actual date the supplier delivery was received.</small>
                </div>

                <div class="form-group">
                    <label for="siPo">Purchase Order</label>
                    <select name="purchase_order_id" id="siPo" onchange="populateStockInProducts(this.value)">
                        <option value="">Select Purchase Order</option>
                        @foreach($purchaseOrders as $po)
                            <option value="{{ $po->id }}" data-supplier="{{ $po->supplier->supplier_name ?? '' }}">{{ $po->po_number }} — {{ $po->supplier->supplier_name ?? 'No Supplier' }} — {{ ucfirst(str_replace('_', ' ', $po->status)) }}</option>
                        @endforeach
                    </select>
                    <small>Approved or Partially Received Purchase Orders only.</small>
                    @if(count($purchaseOrders) === 0)
                        <div class="inline-error" style="margin-top:6px;">No Purchase Orders are awaiting delivery.</div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="siSupplier">Supplier</label>
                    <input type="text" id="siSupplier" readonly tabindex="-1" placeholder="Filled from the Purchase Order">
                </div>

                <div class="form-group">
                    <label for="siProduct">Product</label>
                    <select name="product_id" id="siProduct" disabled onchange="updateStockInRemaining(this.value)">
                        <option value="">Select Purchase Order first</option>
                    </select>
                    <small>Only products included in the selected Purchase Order.</small>
                </div>

                <div class="form-group">
                    <label for="siBrand">Brand</label>
                    <input type="text" id="siBrand" readonly tabindex="-1" placeholder="Filled from the product">
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="siRemaining">Remaining PO Quantity</label>
                        <input type="text" id="siRemaining" readonly tabindex="-1" placeholder="—">
                        <small>Ordered on the PO minus already received. Not the current stock.</small>
                    </div>
                    <div class="form-group">
                        <label for="siQty">Quantity Received</label>
                        <input type="number" name="quantity" id="siQty" min="1" step="1" disabled placeholder="Quantity actually received" oninput="siLiveQty()">
                        <span class="field-error hidden" id="siQtyMsg"></span>
                    </div>
                </div>

                <div class="form-group">
                    <label for="siCost">Purchase Unit Cost <span class="info-tip" title="Taken from the Purchase Order item. Not the product selling price.">i</span></label>
                    <input type="text" id="siCost" readonly tabindex="-1" placeholder="Filled from the Purchase Order">
                </div>

                <p class="section-title" style="margin-top:14px;">Batch / Lot Details</p>

                <div class="form-group">
                    <label for="siBatch">Batch Number <small>(Optional)</small></label>
                    <input type="text" name="batch_number" id="siBatch" maxlength="100" placeholder="e.g. LOT-2026-001" autocomplete="off">
                    <small>Leave blank if the supplier gave none; the system will generate one.</small>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="siExp">Expiration Date <small>(Optional)</small></label>
                        <input type="date" name="expiration_date" id="siExp">
                    </div>
                    <div class="form-group">
                        <label for="siBestBefore">Best Before Date <small>(Optional)</small></label>
                        <input type="date" name="best_before_date" id="siBestBefore">
                    </div>
                </div>

                <div class="form-group">
                    <label for="siReason">Reason <small>(Optional)</small></label>
                    <input type="text" name="reason" id="siReason" maxlength="255" placeholder="e.g. Supplier delivery received" autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="siRef">Reference</label>
                    <input type="text" name="reference" id="siRef" readonly tabindex="-1" placeholder="Filled from the Purchase Order number">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('stockInModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-label="Save Stock In">Save Stock In</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ##################################################################
     ADJUST STOCK MODAL
     ################################################################## --}}
<div class="modal" id="adjustModal">
    <div class="modal-content">
        <div class="modal-header">
            <div><h2>Adjust Stock</h2><p>Match the system to the actual physical count.</p></div>
            <button type="button" class="close-x" onclick="closeModal('adjustModal')">&times;</button>
        </div>
        <div class="modal-body">

            @if($failedModal === 'adjust')
                <div class="notice notice-error" role="alert">
                    <strong>⚠ Please correct the following:</strong>
                    <ul>
                        @if(session('error'))<li>{{ session('error') }}</li>@endif
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('owner.sales-inventory.adjust') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Transaction Date</label>
                    <input type="date" name="transaction_date" value="{{ old('transaction_date', now()->format('Y-m-d')) }}" required>
                    <small>Actual date the physical count occurred.</small>
                </div>

                <div class="form-group">
                    <label>Product</label>
                    <select name="product_id" id="adjProduct" required onchange="updateAdjustStock()">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }}{{ $product->brand ? ' — ' . $product->brand : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="stock-info">
                    <span class="muted">Current System Stock</span>
                    <strong id="adjCurrent">0</strong>
                </div>

                <div class="form-group">
                    <label>Actual Physical Stock</label>
                    <input type="number" name="actual_stock" id="adjActual" min="0" step="1" required placeholder="Enter actual physical count" value="{{ old('actual_stock') }}" oninput="updateAdjustStock()">
                </div>

                <div class="stock-info" id="adjDiffBox">
                    <span class="muted">Difference</span>
                    <strong id="adjDiff">—</strong>
                </div>

                <div class="form-group">
                    <label>Reason</label>
                    <input type="text" name="reason" required placeholder="e.g. Physical count difference" value="{{ old('reason') }}">
                </div>

                <div class="form-group">
                    <label>Reference <small>(Optional)</small></label>
                    <input type="text" name="reference" placeholder="e.g. Count sheet" value="{{ old('reference') }}">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('adjustModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-label="Save Adjustment">Save Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ##################################################################
     RETURN MODAL
     ################################################################## --}}
<div class="modal" id="returnModal">
    <div class="modal-content">
        <div class="modal-header">
            <div><h2>Return</h2><p>Record products returned to inventory.</p></div>
            <button type="button" class="close-x" aria-label="Close" onclick="closeModal('returnModal')">&times;</button>
        </div>
        <div class="modal-body">

            <form action="{{ $returnRoute ?: '#' }}" method="POST" id="returnForm">
                @csrf

                <div class="form-group">
                    <label for="rtProduct">Product</label>
                    <select name="product_id" id="rtProduct" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }}{{ $product->brand ? ' — ' . $product->brand : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="rtQty">Quantity</label>
                        <input type="number" name="quantity" id="rtQty" min="1" step="1" required placeholder="Quantity returned">
                    </div>
                    <div class="form-group">
                        <label for="rtDate">Transaction Date</label>
                        <input type="date" name="transaction_date" id="rtDate" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="rtReason">Reason</label>
                    <input type="text" name="reason" id="rtReason" maxlength="255" required placeholder="e.g. Customer returned unused item" autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="rtOrderRef">Customer Order Reference <small>(Optional)</small></label>
                    <input type="text" name="customer_order_reference" id="rtOrderRef" maxlength="255" placeholder="e.g. CO-2026-0001" autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="rtRef">Reference <small>(Optional)</small></label>
                    <input type="text" name="reference" id="rtRef" maxlength="255" placeholder="e.g. Return slip" autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="rtReceivedBy">Received By</label>
                    <input type="text" name="received_by" id="rtReceivedBy" maxlength="255" required placeholder="Person who received the returned item" value="{{ auth()->user()?->name }}" autocomplete="off">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('returnModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary" data-label="Save Return" {{ $returnRoute ? '' : 'disabled' }}>Save Return</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ##################################################################
     RECORD DAMAGE MODAL
     ################################################################## --}}
<div class="modal" id="damageModal">
    <div class="modal-content">
        <div class="modal-header">
            <div><h2>Record Damage</h2><p>Record damaged products.</p></div>
            <button type="button" class="close-x" aria-label="Close" onclick="closeModal('damageModal')">&times;</button>
        </div>
        <div class="modal-body">

            <form action="{{ $damageRoute ?: '#' }}" method="POST" id="damageForm">
                @csrf

                <div class="form-group">
                    <label for="dmProduct">Product</label>
                    <select name="product_id" id="dmProduct" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->product_name }}{{ $product->brand ? ' — ' . $product->brand : '' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="dmQty">Quantity</label>
                        <input type="number" name="quantity" id="dmQty" min="1" step="1" required placeholder="Quantity damaged">
                    </div>
                    <div class="form-group">
                        <label for="dmDate">Transaction Date</label>
                        <input type="date" name="transaction_date" id="dmDate" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="dmReason">Reason</label>
                    <input type="text" name="reason" id="dmReason" maxlength="255" required placeholder="e.g. Container leaked during handling" autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="dmRef">Reference <small>(Optional)</small></label>
                    <input type="text" name="reference" id="dmRef" maxlength="255" placeholder="e.g. Damage report" autocomplete="off">
                </div>

                <div class="form-group">
                    <label for="dmReceivedBy">Received By</label>
                    <input type="text" name="received_by" id="dmReceivedBy" maxlength="255" required placeholder="Person recording the damage" value="{{ auth()->user()?->name }}" autocomplete="off">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('damageModal')">Cancel</button>
                    <button type="submit" class="btn btn-danger" data-label="Save Damage" {{ $damageRoute ? '' : 'disabled' }}>Save Damage</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ##################################################################
     RELEASE ORDER MODAL
     ################################################################## --}}
<div class="modal" id="releaseModal">
    <div class="modal-content">
        <div class="modal-header">
            <div><h2>Release Order</h2><p id="rlNumber"></p></div>
            <button type="button" class="close-x" onclick="closeModal('releaseModal')">&times;</button>
        </div>
        <div class="modal-body">
            <form method="POST" id="releaseForm">
                @csrf
                <div class="inner-table">
                    <table>
                        <thead><tr><th>Product</th><th class="text-right">Remaining</th><th>Release Qty</th></tr></thead>
                        <tbody id="rlItems"></tbody>
                    </table>
                </div>
                <div class="form-grid">
                    <div class="form-group"><label>Release Date</label><input type="date" name="transaction_date" value="{{ now()->format('Y-m-d') }}" required></div>
                    <div class="form-group"><label>Received By</label><input type="text" name="received_by" maxlength="255" required placeholder="Person who received goods"></div>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('releaseModal')">Cancel</button>
                    <button type="submit" class="btn btn-warning" data-label="Confirm Release">Confirm Release</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ##################################################################
     PAYMENT MODAL
     ################################################################## --}}
<div class="modal" id="paymentModal">
    <div class="modal-content">
        <div class="modal-header">
            <div><h2>Record Payment</h2><p id="pyNumber"></p></div>
            <button type="button" class="close-x" onclick="closeModal('paymentModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="totals-box" style="margin-bottom:12px;">
                <div class="totals-row"><span>Order Total</span><span id="pyTotal" class="num"></span></div>
                <div class="totals-row"><span>Collected</span><span id="pyCollected" class="num"></span></div>
                <div class="totals-row"><span>Pending checks</span><span id="pyPending" class="num"></span></div>
                <div class="totals-row big"><span>Can still be paid</span><span id="pyPayable" class="num"></span></div>
            </div>

            <form method="POST" id="paymentForm">
                @csrf
                <div class="form-grid">
                    <div class="form-group"><label>Payment Date</label><input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required></div>
                    <div class="form-group">
                        <label>Payment Method</label>
                        <select name="payment_method" id="pyMethod" required onchange="togglePayFields()">
                            <option value="cash">Cash</option>
                            <option value="gcash">GCash</option>
                            <option value="maya">Maya</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="check">Check</option>
                            <option value="pdc">PDC</option>
                            <option value="credit">Credit / Utang</option>
                        </select>
                    </div>
                    <div class="form-group"><label>Amount</label><input type="number" name="amount" id="pyAmount" min="0.01" step="0.01"></div>
                    <div class="form-group"><label>Receipt Number</label><input type="text" name="receipt_number" maxlength="100" placeholder="Auto-generated if blank"></div>
                </div>

                <div id="pyRef" class="hidden"><div class="form-group"><label>Reference Number <small>(optional)</small></label><input type="text" name="reference_number" maxlength="100" placeholder="GCash / Maya / bank transaction reference"></div></div>

                <div id="pyCheck" class="hidden">
                    <div class="form-grid">
                        <div class="form-group"><label>Check Number</label><input type="text" name="check_number" maxlength="100"></div>
                        <div class="form-group"><label>Bank Name</label><input type="text" name="bank_name" maxlength="150"></div>
                        <div class="form-group"><label>Check Date</label><input type="date" name="check_date"></div>
                        <div class="form-group"><label>Maturity Date</label><input type="date" name="maturity_date"></div>
                    </div>
                </div>

                <div class="form-group"><label>Notes</label><textarea name="notes" maxlength="1000" placeholder="Optional"></textarea></div>

                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('paymentModal')">Cancel</button>
                    <button type="submit" class="btn btn-success" data-label="Save Payment">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ##################################################################
     VOID ORDER MODAL
     ################################################################## --}}
<div class="modal" id="voidModal">
    <div class="modal-content">
        <div class="modal-header">
            <div><h2>Void Order</h2><p id="vdNumber"></p></div>
            <button type="button" class="close-x" onclick="closeModal('voidModal')">&times;</button>
        </div>
        <div class="modal-body">
            <div class="notice notice-warning">Voiding reverses the stock-out, voids the payments and cancels the order. This cannot be undone.</div>
            <form method="POST" id="voidForm">
                @csrf
                <div class="form-group"><label>Reason for Void</label><textarea name="void_reason" maxlength="1000" required placeholder="Why is this transaction being voided?"></textarea></div>
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closeModal('voidModal')">Cancel</button>
                    <button type="submit" class="btn btn-danger" data-label="Void Order">Void Order</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- ##################################################################
     CONFIRMATION MODAL + SCRIPT
     ################################################################## --}}
<div class="modal" id="confirmModal" style="z-index:1100;">
    <div class="modal-content" style="max-width:480px;">
        <div class="modal-header"><h2 id="cfTitle">Please confirm</h2><button type="button" class="close-x" onclick="cfCancel()">&times;</button></div>
        <div class="modal-body">
            <div id="cfBody"></div>
            <div class="form-actions">
                <button type="button" class="btn btn-outline" onclick="cfCancel()">Cancel</button>
                <button type="button" class="btn btn-primary" id="cfOkBtn" onclick="cfOk()">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>

    const csrfToken = @json(csrf_token());
    const products = @json($productPayloads);
    const productStatusMap = @json($products->pluck('status', 'id'));
    const orders = @json($orderPayloads);
    const purchaseOrders = @json($purchaseOrderPayloads);
    const categoryChoices = @json($categoryChoices);
    const supplierChoices = @json($supplierChoices);
    const oldSupplierIds = @json($oldSupplierIds);
    const oldCategoryId = @json(old('category_id'));
    const oldItems = @json(old('items', []));
    const initialTab = @json($activeTab);
    const failedModal = @json($failedModal);

    // Stock In values are restored only when Stock In itself failed (never from another form's old input)
    const oldStockIn = @json($oldStockInData);

    const oldAdjust = {
        product: @json($oldAdjustProduct),
    };

    const urls = {
        productInfo: @json(route('owner.sales-inventory.product-info', '__ID__')),
        release: @json(route('owner.sales-inventory.release', '__ID__')),
        payment: @json(route('owner.sales-inventory.payment', '__ID__')),
        recheck: @json(route('owner.sales-inventory.recheck', '__ID__')),
        confirm: @json(route('owner.sales-inventory.confirm', '__ID__')),
        cancel: @json(route('owner.sales-inventory.cancel', '__ID__')),
        void: @json(route('owner.sales-inventory.void', '__ID__')),
    };

    const url = (name, id) => urls[name].replace('__ID__', id);
    const productById = id => products.find(p => Number(p.id) === Number(id));
    const orderById = id => orders.find(o => Number(o.id) === Number(id));
    const money = v => '₱' + Number(v || 0).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    const $ = id => document.getElementById(id);
    const dash = v => (v === null || v === undefined || v === '') ? '—' : v;
    const num = v => Number(v || 0).toLocaleString();

    const stockClass = { 'OUT OF STOCK': 'b-red', 'LOW STOCK': 'b-amber', 'IN STOCK': 'b-green' };

    // Labels for the per-product movement list inside the Product Information modal
    function mvLabel(type) {
        const t = String(type || '').toLowerCase();
        if (t === 'opening_balance') return ['Opening Balance', 'b-blue'];
        if (t === 'stock_in') return ['Stock In', 'b-green'];
        if (t === 'stock_out') return ['Stock Out', 'b-red'];
        if (t === 'void_reversal') return ['Void Reversal', 'b-amber'];
        if (t.includes('return')) return ['Return', 'b-blue'];
        if (t.includes('damag')) return ['Damaged', 'b-damaged'];
        return ['Adjustment', 'b-gray'];
    }

    function escapeHtml(value) {
        if (value === null || value === undefined) return '';
        return String(value).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    /* Show / hide a block of fields and disable its inputs while hidden (so hidden values are not submitted) */
    function toggleGroup(id, show) {
        const el = $(id);
        if (!el) return;
        el.classList.toggle('hidden', !show);
        el.querySelectorAll('input, select, textarea').forEach(i => { i.disabled = !show; });
    }

    /* ---------------- MODALS ---------------- */
    function openModal(id) { const m = $(id); if (!m) return; closeAllMenus(); m.classList.add('show'); document.body.style.overflow = 'hidden'; }
    function closeModal(id) {
        const m = $(id); if (!m) return;
        // Closing Stock In by ANY route (Cancel, X, backdrop, Esc) always clears the form
        if (id === 'stockInModal') resetStockIn();
        m.classList.remove('show');
        if (!document.querySelector('.modal.show')) document.body.style.overflow = '';
    }

    document.addEventListener('click', e => { if (e.target.classList && e.target.classList.contains('modal')) closeModal(e.target.id); });
    document.addEventListener('keydown', e => {
        if (e.key !== 'Escape') return;
        if ($('confirmModal').classList.contains('show')) { cfCancel(); return; }
        closeAllMenus();
        // Close the top-most open modal (so a small Add Brand / Add Category window closes without losing the form behind it)
        const open = Array.from(document.querySelectorAll('.modal.show'));
        if (open.length) closeModal(open[open.length - 1].id);
    });

    /* ---------------- ACTION DROPDOWNS ---------------- */
    function closeAllMenus() { document.querySelectorAll('details.dd[open]').forEach(d => d.removeAttribute('open')); }

    document.addEventListener('toggle', e => {
        const d = e.target;
        if (!d.matches || !d.matches('details.dd') || !d.open) return;
        document.querySelectorAll('details.dd[open]').forEach(o => { if (o !== d) o.removeAttribute('open'); });
        const s = d.querySelector('summary').getBoundingClientRect();
        const m = d.querySelector('.dd-menu');
        const top = s.bottom + 4 + m.offsetHeight > window.innerHeight ? s.top - m.offsetHeight - 4 : s.bottom + 4;
        m.style.top = Math.max(8, top) + 'px';
        m.style.left = Math.max(8, s.right - m.offsetWidth) + 'px';
    }, true);

    document.addEventListener('click', e => { if (!e.target.closest('details.dd') || e.target.closest('.dd-menu button')) closeAllMenus(); });
    window.addEventListener('scroll', closeAllMenus, true);
    window.addEventListener('resize', closeAllMenus);

    /* ---------------- TOP NAVIGATION ---------------- */
    // The ?tab= values stay the ones the controller already understands (sale / orders / inventory).
    // Old links that still carry ?tab=history simply open Inventory.
    const tabToParam = { inventory: 'inventory', transactions: 'orders' };
    const paramToTab = { sale: 'inventory', inventory: 'inventory', orders: 'transactions', transactions: 'transactions', history: 'inventory' };

    function showTab(name) {
        if (!tabToParam[name]) name = 'inventory';
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        document.querySelectorAll('.tab-button').forEach(b => b.classList.toggle('active', b.dataset.tab === name));
        $('tab-' + name).classList.add('active');

        const u = new URL(window.location.href);
        u.searchParams.set('tab', tabToParam[name]);
        history.replaceState(null, '', u);
    }

    /* ---------------- FILTERS + PAGINATION (15 per page) ---------------- */
    const PAGE_SIZE = 15;
    const pageState = { inventoryTable: 1, ordersTable: 1, productGrid: 1 };

    function pageWindow(page, pages) {
        const set = new Set([1, pages, page - 1, page, page + 1]);
        const list = Array.from(set).filter(n => n >= 1 && n <= pages).sort((a, b) => a - b);
        const out = [];
        list.forEach((n, i) => { if (i && n - list[i - 1] > 1) out.push('…'); out.push(n); });
        return out;
    }

    function renderPager(tableId, total, page, pages) {
        const el = $('pager-' + tableId);
        if (!el) return;
        if (!total) { el.innerHTML = ''; return; }
        const from = (page - 1) * PAGE_SIZE + 1, to = Math.min(total, page * PAGE_SIZE);
        let html = '<span>Showing ' + from + '–' + to + ' of ' + total + '</span>';
        if (pages > 1) {
            const btn = (label, n, disabled, active) => '<button type="button" class="pager-btn' + (active ? ' active' : '') + '"' + (disabled ? ' disabled' : '') +
                ' onclick="gotoPage(\'' + tableId + '\', ' + n + ')">' + label + '</button>';
            html += '<div class="pager-btns">' + btn('‹ Previous', page - 1, page <= 1) +
                pageWindow(page, pages).map(n => n === '…' ? '<span class="pager-gap">…</span>' : btn(n, n, false, n === page)).join('') +
                btn('Next ›', page + 1, page >= pages) + '</div>';
        }
        el.innerHTML = html;
    }

    // Keeps the current page inside range, draws the pager, returns the set of rows to show.
    function pageOf(tableId, matched) {
        const total = matched.length;
        const pages = Math.max(1, Math.ceil(total / PAGE_SIZE));
        const page = Math.min(Math.max(1, pageState[tableId]), pages);
        pageState[tableId] = page;
        renderPager(tableId, total, page, pages);
        return new Set(matched.slice((page - 1) * PAGE_SIZE, page * PAGE_SIZE));
    }

    function filterRows(tableId, search, filters, keepPage) {
        if (!keepPage) pageState[tableId] = 1;
        const s = (search || '').toLowerCase().trim();
        const rows = Array.from(document.querySelectorAll('#' + tableId + ' tbody tr')).filter(r => r.dataset.search !== undefined);
        const matched = rows.filter(row => row.dataset.search.includes(s) && Object.entries(filters || {}).every(([k, v]) => !v || row.dataset[k] === v));
        const shown = pageOf(tableId, matched);
        rows.forEach(r => { r.style.display = shown.has(r) ? '' : 'none'; });

        const nm = document.querySelector('#' + tableId + ' tr.no-match');
        if (nm) nm.classList.toggle('hidden', matched.length > 0 || rows.length === 0);
        return matched.length;
    }

    function filterInventory(keep) {
        filterRows('inventoryTable', $('invSearch').value, { status: $('invStatus').value, brand: $('invBrand').value, category: $('invCategory').value }, keep === true);
    }
    function setInvStatus(v) { $('invStatus').value = v; filterInventory(); }

    function filterOrders(keep) {
        filterRows('ordersTable', $('orderSearch').value, { status: $('orderStatusFilter').value, pay: $('orderPayFilter').value }, keep === true);
    }
    function setOrderFilter(v) { $('orderStatusFilter').value = v; filterOrders(); }

    function filterGrid(keep) {
        const n = filterRows('productGrid', $('gridSearch').value, {}, keep === true);
        $('gridCount').textContent = n + ' product' + (n === 1 ? '' : 's');
    }

    const refreshers = {
        inventoryTable: () => filterInventory(true),
        ordersTable: () => filterOrders(true),
        productGrid: () => filterGrid(true),
    };
    function gotoPage(tableId, n) {
        pageState[tableId] = n;
        if (refreshers[tableId]) refreshers[tableId]();
    }

    /* ---------------- SEARCHABLE SELECTOR (UI only) ----------------
       Works on a list that is already on the page (no backend request).
       The chosen id is written into the existing hidden form field, so the
       submitted name/value (category_id) is unchanged. */
    function initCombo(cfg) {
        const hidden = $(cfg.hidden);
        const input = $(cfg.input);
        const list = $(cfg.list);
        const field = $(cfg.field);
        const selected = $(cfg.selected);
        const selectedText = $(cfg.selectedText);
        const clearBtn = $(cfg.clear);
        const options = cfg.options || [];
        const MAX = 8;
        let active = -1;
        let shown = [];

        const norm = s => String(s === null || s === undefined ? '' : s).toLowerCase();

        function openList() { list.classList.remove('hidden'); input.setAttribute('aria-expanded', 'true'); }
        function closeList() { list.classList.add('hidden'); input.setAttribute('aria-expanded', 'false'); active = -1; }

        function highlight(name, q) {
            const i = q ? norm(name).indexOf(q) : -1;
            if (i < 0) return escapeHtml(name);
            return escapeHtml(name.slice(0, i)) + '<mark>' + escapeHtml(name.slice(i, i + q.length)) + '</mark>' + escapeHtml(name.slice(i + q.length));
        }

        function render() {
            const q = norm(input.value).trim();
            const matches = options.filter(o => !q || norm(o.name).includes(q));
            shown = matches.slice(0, MAX);

            let html = shown.map((o, i) =>
                '<li role="option" id="' + cfg.list + '-' + i + '" data-id="' + o.id + '" class="' + (i === active ? 'active' : '') + '" aria-selected="' + (i === active ? 'true' : 'false') + '">' +
                highlight(o.name, q) + '</li>').join('');

            if (!options.length) html = '<li class="combo-note">' + escapeHtml(cfg.emptyText || 'Nothing available.') + '</li>';
            else if (!matches.length) html = '<li class="combo-note">No match found. Try a different search.</li>';
            else if (matches.length > MAX) html += '<li class="combo-note">Showing ' + MAX + ' of ' + matches.length + ' matches — keep typing to narrow down.</li>';

            list.innerHTML = html;
            openList();
        }

        function pick(opt) {
            hidden.value = opt.id;
            selectedText.textContent = opt.name;
            selected.classList.remove('hidden');
            input.classList.add('hidden');
            input.value = '';
            field.classList.remove('bad');
            closeList();
        }

        function clear(focus) {
            hidden.value = '';
            selectedText.textContent = '';
            selected.classList.add('hidden');
            input.classList.remove('hidden');
            input.value = '';
            if (focus) { input.focus(); render(); }
        }

        function setActive(i) {
            active = i;
            Array.from(list.querySelectorAll('li[data-id]')).forEach((li, idx) => {
                const on = idx === i;
                li.classList.toggle('active', on);
                li.setAttribute('aria-selected', on ? 'true' : 'false');
                if (on) { li.scrollIntoView({ block: 'nearest' }); input.setAttribute('aria-activedescendant', li.id); }
            });
        }

        input.addEventListener('input', () => { active = -1; render(); });
        input.addEventListener('focus', render);
        input.addEventListener('blur', closeList);

        input.addEventListener('keydown', e => {
            const isOpen = !list.classList.contains('hidden');
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!isOpen) render();
                if (shown.length) setActive(active < shown.length - 1 ? active + 1 : 0);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (!isOpen) render();
                if (shown.length) setActive(active > 0 ? active - 1 : shown.length - 1);
            } else if (e.key === 'Enter') {
                // Never submit the form from the search box
                e.preventDefault();
                if (active >= 0 && shown[active]) pick(shown[active]);
                else if (shown.length === 1) pick(shown[0]);
            } else if (e.key === 'Escape' && isOpen) {
                e.preventDefault();
                e.stopPropagation();
                closeList();
            }
        });

        // mousedown (not click) so the input does not lose focus before the choice is registered
        list.addEventListener('mousedown', e => {
            e.preventDefault();
            const li = e.target.closest('li[data-id]');
            if (!li) return;
            const opt = options.find(o => String(o.id) === String(li.dataset.id));
            if (opt) pick(opt);
        });

        clearBtn.addEventListener('click', () => clear(true));

        return {
            set: function (id) {
                const opt = options.find(o => String(o.id) === String(id));
                if (opt) pick(opt);
            },
            clear: function () { clear(false); }
        };
    }

    const catCombo = initCombo({
        hidden: 'apCategory', input: 'catSearch', list: 'catList', field: 'catField',
        selected: 'catSelected', selectedText: 'catSelectedText', clear: 'catClear',
        options: categoryChoices, emptyText: 'No categories available.'
    });

    // Keep the previously chosen category after a failed (backend-validated) submit
    if (oldCategoryId !== null && oldCategoryId !== undefined && String(oldCategoryId) !== '') {
        catCombo.set(oldCategoryId);
    }

    /* ---------------- ADD PRODUCT ---------------- */
    function openAddProduct() {
        $('apError').classList.add('hidden');
        openModal('addProductModal');
        setTimeout(() => $('apName').focus(), 50);
    }

    // Opening batch fields only apply when Opening Stock is greater than 0.
    // While hidden they are disabled, so they are not submitted.
    function apToggleBatch() {
        const n = Number($('apOpening').value);
        toggleGroup('apBatchGroup', !isNaN(n) && n > 0);
    }

    /* Supplier(s) multi-select (searchable) */
    function updateSupCount() {
        $('apSupCount').textContent = $('apSupList').querySelectorAll('input:checked').length + ' selected';
    }

    function renderSupplierPicker(selectedIds) {
        const selected = new Set((selectedIds || []).map(Number));
        const box = $('apSupList');

        box.innerHTML = supplierChoices.length ? '' : '<div class="picker-empty">No active suppliers available.</div>';

        supplierChoices.forEach(s => {
            const row = document.createElement('label');
            row.className = 'pick-row';
            row.dataset.search = String(s.name).toLowerCase();
            row.innerHTML = '<input type="checkbox" name="supplier_ids[]" value="' + Number(s.id) + '"' + (selected.has(Number(s.id)) ? ' checked' : '') + '>' +
                '<span>' + escapeHtml(s.name) + '</span>';
            box.appendChild(row);
        });

        $('apSupSearch').value = '';
        updateSupCount();
    }

    (function () {
        const search = $('apSupSearch');
        search.addEventListener('keydown', e => { if (e.key === 'Enter') e.preventDefault(); });
        search.addEventListener('input', () => {
            const q = search.value.toLowerCase().trim();
            $('apSupList').querySelectorAll('.pick-row').forEach(r => { r.style.display = (r.dataset.search || '').includes(q) ? '' : 'none'; });
        });
        $('apSupList').addEventListener('change', updateSupCount);
    })();

    /* + Add Brand: brand is a plain text value on the product, so no route is needed */
    function openAddBrand() {
        $('abName').value = '';
        $('abError').classList.add('hidden');
        openModal('addBrandModal');
        setTimeout(() => $('abName').focus(), 50);
    }

    function saveBrand() {
        const name = $('abName').value.replace(/\s+/g, ' ').trim();
        const err = $('abError');
        err.classList.add('hidden');

        if (!name) { err.textContent = 'Please enter a brand name.'; err.classList.remove('hidden'); $('abName').focus(); return; }

        const select = $('apBrand');
        const note = $('apBrandNote');
        const existing = Array.from(select.options).find(o => o.value && o.value.toLowerCase() === name.toLowerCase());

        if (existing) {
            // Shell / shell / SHELL are treated as the same brand: use the existing one
            select.value = existing.value;
            note.textContent = '"' + existing.value + '" already exists, so the existing brand was selected.';
        } else {
            const opt = document.createElement('option');
            opt.value = name;
            opt.textContent = name;
            const after = Array.from(select.options).find(o => o.value && o.value.toLowerCase() > name.toLowerCase());
            select.insertBefore(opt, after || null);
            select.value = name;
            note.textContent = '"' + name + '" will be saved with this product.';
        }

        note.classList.remove('hidden');
        closeModal('addBrandModal');
    }

    $('abName').addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); saveBrand(); } });
    $('apBrand').addEventListener('change', () => $('apBrandNote').classList.add('hidden'));

    /* + Add Category: posts to the existing category route */
    function openAddCategory() {
        if (!$('addCategoryModal')) return;
        $('acError').classList.add('hidden');
        openModal('addCategoryModal');
        setTimeout(() => $('acName').focus(), 50);
    }

    if ($('categoryForm')) {
        $('categoryForm').addEventListener('submit', function (e) {
            const name = $('acName').value.replace(/\s+/g, ' ').trim();
            const err = $('acError');
            err.classList.add('hidden');

            let problem = '';
            if (!name) problem = 'Please enter a category name.';
            else if (categoryChoices.some(c => String(c.name).toLowerCase() === name.toLowerCase())) problem = 'This category already exists. Search for it in the Category field instead.';

            if (problem) {
                e.preventDefault();
                err.textContent = problem;
                err.classList.remove('hidden');
                return;
            }
            $('acName').value = name;
        });
    }

    // Quick checks only; the backend validation remains authoritative.
    $('productForm').addEventListener('submit', function (e) {
        const problems = [];
        const mark = (el, bad) => el.classList.toggle('bad', bad);

        const name = $('apName').value.trim();
        const unit = $('apUnit').value.trim();
        const priceRaw = $('apPrice').value.trim();
        const price = Number(priceRaw);

        mark($('apName'), !name); if (!name) problems.push('Product name is required.');
        mark($('catField'), !$('apCategory').value); if (!$('apCategory').value) problems.push('Please search for and select a category.');
        mark($('apUnit'), !unit); if (!unit) problems.push('Unit is required.');
        const badPrice = priceRaw === '' || isNaN(price) || price < 0;
        mark($('apPrice'), badPrice); if (badPrice) problems.push('Enter a selling price of 0 or higher.');

        const reorderRaw = $('apReorder').value.trim();
        const badReorder = reorderRaw !== '' && (isNaN(Number(reorderRaw)) || Number(reorderRaw) < 0 || !Number.isInteger(Number(reorderRaw)));
        mark($('apReorder'), badReorder); if (badReorder) problems.push('Reorder level must be a whole number of 0 or more.');

        const openRaw = $('apOpening').value.trim();
        const openN = Number(openRaw);
        const badOpen = openRaw !== '' && (isNaN(openN) || openN < 0 || !Number.isInteger(openN));
        mark($('apOpening'), badOpen); if (badOpen) problems.push('Opening stock must be a whole number of 0 or more.');

        if (!badOpen && openN > 0) {
            const costRaw = $('apBatchCost').value.trim();
            const badCost = costRaw !== '' && (isNaN(Number(costRaw)) || Number(costRaw) < 0);
            mark($('apBatchCost'), badCost); if (badCost) problems.push('Purchase unit cost must be 0 or higher.');
        }

        if (problems.length) {
            e.preventDefault();
            const box = $('apError');
            box.innerHTML = '<strong>⚠ Please fix the following:</strong><ul style="margin:6px 0 0 18px;padding:0;">' + problems.map(p => '<li>' + escapeHtml(p) + '</li>').join('') + '</ul>';
            box.classList.remove('hidden');
        }
    });

    /* ---------------- PRODUCT INFO / SUPPLIERS (fetch with error handling) ---------------- */
    const infoCache = {};

    function fetchProductInfo(id) {
        id = Number(id);
        if (!infoCache[id]) {
            infoCache[id] = fetch(url('productInfo', id), { headers: { 'Accept': 'application/json' } })
                .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                .then(d => { if (!d || !Array.isArray(d.suppliers)) throw new Error('Unexpected response'); return d; })
                .catch(err => { delete infoCache[id]; throw err; });
        }
        return infoCache[id];
    }

    // If the product payload already carries supplier data, use it; otherwise fall back to the product-info endpoint.
    function payloadSuppliers(p) {
        if (!p) return null;
        const src = (Array.isArray(p.supplier_names) && p.supplier_names.length) ? p.supplier_names
                  : (Array.isArray(p.suppliers) ? p.suppliers
                  : (Array.isArray(p.supplier_names) ? p.supplier_names : null));
        if (!src) return null;
        return src.map(s => {
            if (typeof s === 'string') return s;
            if (!s) return null;
            const n = s.supplier_name || s.name || (s.supplier && (s.supplier.supplier_name || s.supplier.name)) || s.supplier;
            return typeof n === 'string' ? n : null;
        }).filter(Boolean);
    }

    function uniqueSuppliers(list) {
        const seen = new Set(), out = [];
        (list || []).forEach(s => {
            const name = typeof s === 'string' ? s : (s && s.supplier);
            if (name && !seen.has(name)) { seen.add(name); out.push(name); }
        });
        return out;
    }

    function chipsHtml(names, productId, max) {
        if (!names.length) return '<span class="muted" title="No suppliers found">—</span>';
        max = max || 3;
        const first = names.slice(0, max).map(n => '<span class="chip-sup">' + escapeHtml(n) + '</span>').join('');
        const more = names.length > max
            ? '<span class="chip-sup chip-more" title="View all suppliers" onclick="openProductModal(' + productId + ')">+' + (names.length - max) + ' more</span>' : '';
        return first + more;
    }

    function loadRowSuppliers(cell) {
        const id = cell.dataset.id;
        const pre = payloadSuppliers(productById(id));
        if (pre) { cell.innerHTML = chipsHtml(uniqueSuppliers(pre), id); return; }

        cell.innerHTML = '<span class="sub">Loading suppliers...</span>';
        fetchProductInfo(id)
            .then(d => { cell.innerHTML = chipsHtml(uniqueSuppliers(d.suppliers), id); })
            .catch(() => { cell.innerHTML = '<button type="button" class="link-btn" onclick="loadRowSuppliers(this.parentElement)">Unable to load — Retry</button>'; });
    }

    function supplierTable(list, limit) {
        const rows = (limit ? list.slice(0, limit) : list);
        return '<table><thead><tr><th>Supplier</th><th>PO</th><th>Date</th><th class="text-right">Purchase Cost</th></tr></thead><tbody>' +
            rows.map(s => '<tr><td>' + escapeHtml(s.supplier) + '</td><td>' + escapeHtml(s.po_number) + '</td><td>' + escapeHtml(s.po_date) +
                '</td><td class="text-right">' + (s.unit_cost === null || s.unit_cost === undefined ? '—' : money(s.unit_cost)) + '</td></tr>').join('') +
            '</tbody></table>';
    }

    // Lazy-load supplier chips as rows scroll into view (Product Data Grid in the order modal)
    (function initSupplierCells() {
        const cells = document.querySelectorAll('.sup-cell');
        if ('IntersectionObserver' in window) {
            const obs = new IntersectionObserver((entries, o) => {
                entries.forEach(en => { if (en.isIntersecting) { o.unobserve(en.target); loadRowSuppliers(en.target); } });
            }, { rootMargin: '250px' });
            cells.forEach(c => obs.observe(c));
        } else {
            cells.forEach(loadRowSuppliers);
        }
    })();

    /* ---------------- QUANTITY RULES ---------------- */
    // Only the basic rule is enforced while typing: a whole number, at least 1. The typed value is never changed.
    // Quantities above the available stock are flagged; CHECKOUT is blocked for them, SAVE ORDER is allowed after a double-check.
    function qtyIssue(raw) {
        const s = String(raw === null || raw === undefined ? '' : raw).trim();
        if (s === '') return 'Enter a quantity (whole number, at least 1).';
        const n = Number(s);
        if (isNaN(n)) return 'Quantity must be a number.';
        if (!Number.isInteger(n)) return 'Quantity must be a whole number.';
        if (n < 1) return 'Quantity must be at least 1.';
        return '';
    }

    function shortText(p, qty) {
        const avail = Math.max(0, Number(p.available_stock));
        if (qty <= avail) return '';
        if (avail === 0) return 'Insufficient stock. None available.';
        return 'Insufficient stock. Only ' + num(avail) + ' unit' + (avail === 1 ? ' is' : 's are') + ' available.';
    }

    // Returns { type: 'error' | 'short' | '', text }
    function qtyNote(p, raw) {
        const issue = qtyIssue(raw);
        if (issue) return { type: 'error', text: issue };
        const t = shortText(p, Number(raw));
        return t ? { type: 'short', text: t } : { type: '', text: '' };
    }

    /* ---------------- CUSTOMER ORDER: PRODUCT SELECTION ---------------- */
    let selectedId = null;
    let warnTimer = null;

    function orderWarn(msg) {
        const el = $('orderWarn');
        el.textContent = '⚠ ' + msg;
        el.classList.remove('hidden');
        clearTimeout(warnTimer);
        warnTimer = setTimeout(() => el.classList.add('hidden'), 6500);
    }

    function openOrderModal(productId) {
        openModal('orderFormModal');
        filterGrid();
        if (productId) {
            selectProduct(productId);
            const row = document.querySelector('#productGrid tbody tr[data-id="' + productId + '"]');
            if (row) {
                // Jump to the page that holds the selected product so the highlighted row is visible
                const visibleRows = Array.from(document.querySelectorAll('#productGrid tbody tr')).filter(r => r.dataset.search !== undefined);
                const idx = visibleRows.indexOf(row);
                if (idx >= 0) { pageState.productGrid = Math.floor(idx / PAGE_SIZE) + 1; filterGrid(true); }
                row.scrollIntoView({ block: 'nearest' });
            }
        } else {
            setTimeout(() => $('gridSearch').focus(), 50);
        }
    }

    function selectProduct(id) {
        const p = productById(id);
        if (!p) return;
        selectedId = Number(p.id);

        document.querySelectorAll('#productGrid tbody tr').forEach(r => r.classList.toggle('selected', Number(r.dataset.id) === selectedId));
        $('infoEmpty').classList.add('hidden');
        $('infoBody').classList.remove('hidden');

        const set = (el, v) => $(el).textContent = dash(v);
        const reserved = Math.max(0, p.current_stock - p.available_stock);
        const prodStatus = productStatusMap[p.id];

        set('iName', p.name); set('iBrand', p.brand); set('iCategory', p.category); set('iApi', p.api);
        set('iBaseOil', p.base_oil); set('iPackage', p.package_size); set('iUnit', p.unit);
        set('iPrice', money(p.price)); set('iReorder', num(p.reorder_level));
        set('iProdStatus', prodStatus ? prodStatus.charAt(0).toUpperCase() + prodStatus.slice(1) : null);
        set('iCurrent', num(p.current_stock)); set('iReserved', num(reserved)); set('iAvailable', num(p.available_stock));
        set('iExpiry', expiryText(p));
        $('iStatus').innerHTML = '<span class="badge ' + (stockClass[p.stock_status] || 'b-gray') + '">' + escapeHtml(p.stock_status) + '</span>';

        const a = $('stockAlert');
        if (p.available_stock <= 0) {
            a.className = 'stock-alert out';
            a.textContent = 'OUT OF STOCK — can be saved as an order, but cannot be checked out.';
        } else if (p.stock_status === 'LOW STOCK') {
            a.className = 'stock-alert low';
            a.textContent = 'LOW STOCK — only ' + num(p.available_stock) + ' available (reorder level ' + num(p.reorder_level) + ').';
        } else {
            a.className = 'stock-alert ok';
            a.textContent = 'IN STOCK — ' + num(p.available_stock) + ' available to sell.';
        }

        renderInfoSuppliers(p.id);
        renderInfoBatches(p.id);
    }

    function renderInfoSuppliers(id) {
        const box = $('supplierList');
        const pre = payloadSuppliers(productById(id));

        if (pre) {
            const names = uniqueSuppliers(pre);
            box.innerHTML = names.length ? chipsHtml(names, id, 50) : '<div class="sub">No suppliers found for this product.</div>';
            return;
        }

        box.innerHTML = '<div class="loading">Loading product information...</div>';

        fetchProductInfo(id)
            .then(d => {
                if (selectedId !== Number(id)) return;
                const names = uniqueSuppliers(d.suppliers);
                if (!names.length) { box.innerHTML = '<div class="sub">No suppliers found for this product.</div>'; return; }
                box.innerHTML =
                    '<div style="margin-bottom:6px;">' + chipsHtml(names, id, 50) + '</div>' +
                    '<div class="inner-table" style="margin-bottom:4px;">' + supplierTable(d.suppliers, 5) + '</div>';
            })
            .catch(() => {
                if (selectedId !== Number(id)) return;
                box.innerHTML = '<div class="inline-error">Unable to load product information. Please try again. ' +
                    '<button type="button" class="link-btn" onclick="renderInfoSuppliers(' + Number(id) + ')">Retry</button></div>';
            });
    }

    // Active batches already carried by the product payload (no extra request)
    function renderInfoBatches(id) {
        const body = $('infoBatches');
        const list = (productById(id) || {}).batches;
        const rows = (Array.isArray(list) ? list : []).map(normalizeBatch);

        if (!rows.length) {
            body.innerHTML = '<tr><td colspan="7" class="empty-state">No active batches. Batches are created through Stock In or Opening Stock.</td></tr>';
            return;
        }

        rows.sort((a, b) => batchSortDate(a) - batchSortDate(b));

        body.innerHTML = rows.map(x => {
            const st = batchStatusOf(x);
            return '<tr><td><strong>' + escapeHtml(dash(x.number)) + '</strong></td>' +
                '<td>' + escapeHtml(dash(x.supplier)) + '</td>' +
                '<td>' + escapeHtml(fmtDate(x.received)) + '</td>' +
                '<td>' + escapeHtml(x.expiration ? fmtDate(x.expiration) : 'No Date') + '</td>' +
                '<td>' + escapeHtml(x.bestBefore ? fmtDate(x.bestBefore) : 'No Date') + '</td>' +
                '<td class="text-right num"><strong>' + (x.qtyRemaining === null ? '—' : num(x.qtyRemaining)) + '</strong></td>' +
                '<td><span class="badge ' + st[2] + '">' + escapeHtml(st[1]) + '</span></td></tr>';
        }).join('');
    }

    // "Add to Order" adds ONE unit (or +1 if already listed). Quantities are edited in Selected Items.
    function addSelectedToCart() {
        if (!selectedId) { orderWarn('Select a product from the grid first.'); return; }
        addToCart(selectedId, 1);
    }

    /* ---------------- CUSTOMER ORDER: SELECTED ITEMS ---------------- */
    let cart = [];
    let amountTouched = false;

    function addToCart(productId, qty, quiet) {
        const p = productById(productId);
        if (!p || !qty || qty < 1) return;

        const existing = cart.find(c => Number(c.product_id) === Number(productId));
        if (existing) {
            existing.qty = (Number(existing.qty) || 0) + qty;
            delete existing.raw;
            if (!quiet) orderWarn('This product is already in the order. Quantity increased to ' + num(existing.qty) + '.');
        } else {
            cart.push({ product_id: p.id, qty: qty });
        }
        renderCart();
    }

    // Typed / committed quantity. The typed value is kept; problems are shown inline, never silently changed.
    function setQty(index, value) {
        const item = cart[index];
        if (!item) return;
        const raw = String(value === null || value === undefined ? '' : value).trim();
        const n = Number(raw);

        if (raw !== '' && Number.isInteger(n) && n >= 1) { item.qty = n; delete item.raw; }
        else { item.qty = 0; item.raw = raw; }
        renderCart();
    }

    // Live inline message while typing (no re-render, so focus is kept).
    function liveQty(index, el) {
        const item = cart[index];
        if (!item) return;
        const note = qtyNote(productById(item.product_id), el.value);
        const w = $('ciw-' + index);
        if (w) {
            w.textContent = note.text ? '⚠ ' + note.text : '';
            w.className = 'ci-warn' + (note.type === 'short' ? ' ci-short' : '') + (note.text ? '' : ' hidden');
        }
        el.classList.toggle('over', note.type === 'error');
        el.classList.toggle('short', note.type === 'short');
        const row = el.closest('.cart-item');
        if (row) { row.classList.toggle('over', note.type === 'error'); row.classList.toggle('short', note.type === 'short'); }
    }

    function cartMsg(index, msg) {
        const w = $('ciw-' + index);
        if (!w) return;
        w.textContent = '⚠ ' + msg;
        w.className = 'ci-warn';
    }

    function stepQty(index, delta) {
        const item = cart[index];
        if (!item) return;
        const base = Number(item.qty) > 0 ? Number(item.qty) : 0;
        const next = base + delta;
        if (next < 1) { cartMsg(index, 'Quantity must be at least 1. Use Remove to delete the item.'); return; }
        setQty(index, next);
    }

    function removeItem(index) { cart.splice(index, 1); renderCart(); }
    function cartTotal() { return cart.reduce((sum, c) => sum + (Number(c.qty) || 0) * productById(c.product_id).price, 0); }
    function cartHasQtyIssue() { return cart.some(c => qtyIssue(c.raw !== undefined ? c.raw : c.qty) !== ''); }
    function cartShortages() {
        return cart.map(c => {
            const p = productById(c.product_id);
            const avail = Math.max(0, Number(p.available_stock));
            const qty = Number(c.qty) || 0;
            return qty > avail ? { name: p.name, qty: qty, avail: avail, short: qty - avail } : null;
        }).filter(Boolean);
    }

    function renderCart() {
        const list = $('cartList');
        const hidden = $('cartHidden');

        $('cartCount').textContent = cart.length;

        if (!cart.length) {
            list.innerHTML = '<div class="empty-state">No items selected yet.</div>';
        } else {
            list.innerHTML = cart.map((c, i) => {
                const p = productById(c.product_id);
                const shown = c.raw !== undefined ? c.raw : c.qty;
                const note = qtyNote(p, shown);
                const warn = '<div class="ci-warn' + (note.type === 'short' ? ' ci-short' : '') + (note.text ? '' : ' hidden') + '" id="ciw-' + i + '">' + (note.text ? '⚠ ' + escapeHtml(note.text) : '') + '</div>';
                return '<div class="cart-item ' + (note.type === 'error' ? 'over' : (note.type === 'short' ? 'short' : '')) + '">' +
                    '<div><div class="ci-name">' + escapeHtml(p.name) + '</div><div class="sub">' + escapeHtml(p.brand || '—') + ' &middot; Available: ' + num(p.available_stock) + '</div></div>' +
                    '<div class="stepper"><button type="button" onclick="stepQty(' + i + ', -1)" title="Decrease">&minus;</button>' +
                        '<input type="number" min="1" step="1" class="' + (note.type === 'error' ? 'over' : (note.type === 'short' ? 'short' : '')) + '" value="' + escapeHtml(shown) + '" oninput="liveQty(' + i + ', this)" onchange="setQty(' + i + ', this.value)" onkeydown="if (event.key === \'Enter\') { event.preventDefault(); this.blur(); }">' +
                        '<button type="button" onclick="stepQty(' + i + ', 1)" title="Increase">+</button></div>' +
                    '<div><div class="ci-price">' + money(p.price) + ' each</div><div class="ci-sub">' + money((Number(c.qty) || 0) * p.price) + '</div></div>' +
                    '<button type="button" class="icon-btn" onclick="removeItem(' + i + ')">Remove</button>' +
                    warn + '</div>';
            }).join('');
        }

        hidden.innerHTML = cart.map((c, i) =>
            '<input type="hidden" name="items[' + i + '][product_id]" value="' + c.product_id + '">' +
            '<input type="hidden" name="items[' + i + '][quantity]" value="' + escapeHtml(c.raw !== undefined ? c.raw : c.qty) + '">').join('');

        const sh = cartShortages();
        const box = $('shortBox');
        if (box) {
            if (sh.length) {
                box.textContent = '⚠ ' + sh.length + ' item' + (sh.length === 1 ? ' is' : 's are') + ' above the available stock. Use SAVE ORDER, or reduce the quantity to check out.';
                box.classList.remove('hidden');
            } else {
                box.classList.add('hidden');
            }
        }

        updatePaymentUI(false);
    }

    function updatePaymentUI(methodChanged) {
        const method = $('payMethod').value;
        const total = cartTotal();
        const amountInput = $('amountPaid');

        // Reference Number: only for methods that carry an external reference. Check fields: only for check / PDC.
        toggleGroup('cartRefFields', ['gcash', 'maya', 'bank_transfer'].includes(method));
        toggleGroup('cartCheckFields', ['check', 'pdc'].includes(method));
        $('amountGroup').classList.toggle('hidden', method === 'credit');

        // Amount Paid is pre-filled with the total for convenience but always stays editable.
        if (methodChanged) amountTouched = false;
        if (!amountTouched) amountInput.value = total > 0 ? total.toFixed(2) : '';

        $('cartSubtotal').textContent = money(total);
        $('cartTotal').textContent = money(total);
        $('footTotal').textContent = money(total);

        let balance, change;
        if (method === 'credit') {
            balance = total; change = 0;
        } else {
            const paid = Number(amountInput.value || 0);
            balance = Math.max(0, total - paid);
            change = method === 'cash' ? Math.max(0, paid - total) : 0;
        }

        $('balLabel').textContent = method === 'credit' ? 'Balance (on credit)' : 'Balance';
        $('cartBalance').textContent = money(balance);
        $('cartChange').textContent = money(change);
    }

    function resetOrderForm() {
        cart = [];
        amountTouched = false;
        $('saleForm').reset();
        selectedId = null;
        $('infoBody').classList.add('hidden');
        $('infoEmpty').classList.remove('hidden');
        document.querySelectorAll('#productGrid tbody tr').forEach(r => r.classList.remove('selected'));
        $('orderWarn').classList.add('hidden');
        renderCart();
        resetSubmitButtons();
        closeModal('orderFormModal');
    }

    function cancelOrder() {
        if (!cart.length) { resetOrderForm(); return; }
        uiConfirm({ title: 'Discard this order?', text: 'All selected items and entries will be cleared.', okLabel: 'Yes, discard', okClass: 'btn-danger', onOk: resetOrderForm });
    }

    /* ---------------- IN-PAGE CONFIRMATION (replaces the browser pop-up) ---------------- */
    let cfCallback = null;

    function uiConfirm(o) {
        $('cfTitle').textContent = o.title || 'Please confirm';
        $('cfBody').innerHTML = o.html || ('<p style="margin:0;">' + escapeHtml(o.text || '') + '</p>');
        const ok = $('cfOkBtn');
        ok.textContent = o.okLabel || 'Confirm';
        ok.className = 'btn ' + (o.okClass || 'btn-primary');
        cfCallback = o.onOk || null;
        openModal('confirmModal');
        setTimeout(() => ok.focus(), 30);
    }
    function cfCancel() { cfCallback = null; closeModal('confirmModal'); }
    function cfOk() { const cb = cfCallback; cfCallback = null; closeModal('confirmModal'); if (cb) cb(); }

    /* ---------------- SUBMIT PROTECTION ---------------- */
    let submitFallback = null;

    function resetSubmitButtons() {
        clearTimeout(submitFallback);
        document.querySelectorAll('button[type=submit][data-label]').forEach(b => { b.disabled = false; b.textContent = b.dataset.label; });
    }

    // Any form submit: lock its submit buttons, show "Saving...", restore if the page is restored or the request hangs.
    document.addEventListener('submit', function (e) {
        if (e.defaultPrevented) return;
        const form = e.target;
        const submitter = e.submitter;
        setTimeout(() => {
            form.querySelectorAll('button[type=submit]').forEach(b => {
                b.disabled = true;
                if (b === submitter || b.id === 'checkoutBtn') b.textContent = 'Saving...';
            });
        }, 0);
        clearTimeout(submitFallback);
        submitFallback = setTimeout(resetSubmitButtons, 30000);
    });
    window.addEventListener('pageshow', e => { if (e.persisted) resetSubmitButtons(); });

    let skipConfirm = false;

    $('saleForm').addEventListener('submit', function (e) {
        if (skipConfirm) { skipConfirm = false; return; }

        const submitter = e.submitter;
        const isCheckout = !!submitter && submitter.id === 'checkoutBtn';

        if (!cart.length) { e.preventDefault(); orderWarn('Please add at least one product to the order.'); return; }
        if (cartHasQtyIssue()) { e.preventDefault(); orderWarn('One or more item quantities are invalid. Please correct them in Selected Items.'); return; }

        // Immediate checkout deducts stock, so it cannot go above what is available. The typed quantities are kept.
        if (isCheckout) {
            const over = cartShortages();
            if (over.length) {
                e.preventDefault();
                orderWarn(over.map(x => 'Insufficient stock for ' + x.name + '. Only ' + num(x.avail) + ' unit' + (x.avail === 1 ? ' is' : 's are') + ' available.').join(' ') + ' Reduce the quantity, or use SAVE ORDER.');
                return;
            }
            if (!$('receivedBy').value.trim()) { e.preventDefault(); orderWarn('Please enter who received the goods.'); $('receivedBy').focus(); return; }
        }

        // Double-check step, shown inside the page
        e.preventDefault();
        const sh = cartShortages();
        let html = '<p style="margin:0 0 8px;">' + (isCheckout
            ? 'Stock will be <b>deducted</b> and the payment recorded.'
            : 'Stock will be <b>reserved</b> but NOT deducted.') + '</p>' +
            '<div class="totals-box"><div class="totals-row"><span>Items</span><span class="num">' + cart.length + '</span></div>' +
            '<div class="totals-row hl"><span>Order total</span><span class="num">' + money(cartTotal()) + '</span></div></div>';
        if (sh.length) {
            html += '<div class="notice notice-warning" style="margin:10px 0 0;"><strong>⚠ Short on stock — please double-check:</strong><ul>' +
                sh.map(x => '<li>' + escapeHtml(x.name) + ': ordering ' + num(x.qty) + ', available ' + num(x.avail) + ' (' + num(x.short) + ' short)</li>').join('') +
                '</ul></div>';
        }
        uiConfirm({
            title: isCheckout ? 'Complete this sale?' : 'Save as open order?',
            html: html,
            okLabel: sh.length ? 'Continue anyway' : (isCheckout ? 'Yes, checkout' : 'Yes, save order'),
            okClass: sh.length ? 'btn-warning' : (isCheckout ? 'btn-success' : 'btn-primary'),
            onOk: () => { skipConfirm = true; $('saleForm').requestSubmit(submitter || undefined); }
        });
    });

    /* ---------------- TRANSACTIONS ---------------- */
    function postAction(actionUrl, message, danger) {
        const go = () => {
            const f = document.createElement('form');
            f.method = 'POST';
            f.action = actionUrl;
            f.innerHTML = '<input type="hidden" name="_token" value="' + csrfToken + '">';
            document.body.appendChild(f);
            f.submit();
        };
        if (message) uiConfirm({ title: 'Please confirm', text: message, okLabel: 'Yes, continue', okClass: danger ? 'btn-danger' : 'btn-primary', onOk: go });
        else go();
    }

    function orderAction(kind, id) {
        const o = orderById(id);
        if (!o) return;

        const messages = {
            recheck: 'Recheck inventory availability for ' + o.order_number + '?',
            confirm: 'Confirm ' + o.order_number + '? Stock will be reserved (physical stock is not deducted).',
            cancel: 'Cancel ' + o.order_number + '? Any reservation will be released.',
        };

        postAction(url(kind, id), messages[kind], kind === 'cancel');
    }

    function viewOrder(id) {
        const o = orderById(id);
        if (!o) return;

        const payLabels = { paid: 'Paid', partial: 'Partial', pending: 'Pending Check', unpaid: 'Unpaid', cancelled: 'Cancelled' };
        const payClass = { paid: 'b-green', partial: 'b-orange', pending: 'b-amber', unpaid: 'b-red', cancelled: 'b-gray' };
        const text = (el, v) => $(el).textContent = dash(v);

        $('voNumber').textContent = o.order_number + ' — ' + dash(o.customer_name);
        text('voOrderNo', o.order_number); text('voCustomer', o.customer_name); text('voContact', o.customer_contact);
        text('voDate', o.order_date); text('voStatus', o.status_label);
        text('voCheckedBy', o.checked_by || 'Not checked yet');
        text('voCheckNotes', o.inventory_check_notes); text('voNotes', o.notes);
        text('voTotal', money(o.total)); text('voCollected', money(o.collected)); text('voPending', money(o.pending)); text('voBalance', money(o.balance));
        $('voPayStatus').innerHTML = '<span class="badge ' + (payClass[o.payment_status] || 'b-gray') + '">' + escapeHtml(payLabels[o.payment_status] || o.payment_status) + '</span>';

        const items = Array.isArray(o.items) ? o.items : [];
        $('voItems').innerHTML = items.length ? items.map(i => {
            const brand = i.brand || (productById(i.product_id) || {}).brand;
            return '<tr><td>' + escapeHtml(i.product_name) + '</td><td>' + escapeHtml(dash(brand)) + '</td>' +
                '<td class="text-right">' + num(i.quantity) + '</td>' +
                '<td class="text-right" style="color:#1e40af;font-weight:700;">' + num(i.reserved) + '</td>' +
                '<td class="text-right" style="color:#166534;font-weight:700;">' + num(i.fulfilled) + '</td>' +
                '<td class="text-right">' + money(i.unit_price) + '</td>' +
                '<td class="text-right"><strong>' + money(i.subtotal) + '</strong></td></tr>';
        }).join('') : '<tr><td colspan="7" class="empty-state">No items on this order.</td></tr>';

        const pays = Array.isArray(o.payments) ? o.payments : [];
        $('voPayments').innerHTML = pays.length
            ? pays.map(p =>
                '<tr><td>' + escapeHtml(p.receipt) + '</td><td>' + escapeHtml(p.date) + '</td><td>' + escapeHtml(String(p.method || '').replace('_', ' ').toUpperCase()) +
                '</td><td class="text-right">' + money(p.amount) + '</td><td>' + escapeHtml(String(p.status || '').toUpperCase()) + '</td></tr>').join('')
            : '<tr><td colspan="5" class="empty-state">No payments recorded.</td></tr>';

        const voidBox = $('voVoidBox');
        if (o.voided_at) {
            voidBox.classList.remove('hidden');
            $('voVoid').innerHTML =
                '<div><strong>Voided At:</strong> ' + escapeHtml(o.voided_at) + '</div>' +
                '<div><strong>Voided By:</strong> ' + escapeHtml(o.voided_by || '—') + '</div>' +
                '<div><strong>Void Reason:</strong> ' + escapeHtml(o.void_reason || '—') + '</div>';
        } else {
            voidBox.classList.add('hidden');
        }

        openModal('orderModal');
    }

    function openRelease(id) {
        const o = orderById(id);
        if (!o) return;

        $('rlNumber').textContent = o.order_number + ' — ' + o.customer_name;
        $('releaseForm').action = url('release', id);

        const rows = (o.items || []).filter(i => i.remaining > 0);
        $('rlItems').innerHTML = rows.length ? rows.map(i =>
            '<tr><td>' + escapeHtml(i.product_name) + '</td><td class="text-right">' + i.remaining + '</td>' +
            '<td><input type="number" name="quantities[' + i.id + ']" min="0" max="' + i.remaining + '" step="1" value="' + i.remaining +
            '" style="width:90px;padding:6px 8px;border:1px solid #d1d5db;border-radius:6px;"></td></tr>').join('')
            : '<tr><td colspan="3" class="empty-state">Nothing left to release on this order.</td></tr>';

        openModal('releaseModal');
    }

    function openPayment(id) {
        const o = orderById(id);
        if (!o) return;

        $('pyNumber').textContent = o.order_number + ' — ' + o.customer_name;
        $('paymentForm').action = url('payment', id);
        $('pyTotal').textContent = money(o.total);
        $('pyCollected').textContent = money(o.collected);
        $('pyPending').textContent = money(o.pending);
        $('pyPayable').textContent = money(o.payable);
        $('pyAmount').value = o.payable.toFixed(2);
        $('pyMethod').value = 'cash';
        togglePayFields();

        openModal('paymentModal');
    }

    function togglePayFields() {
        const m = $('pyMethod').value;
        toggleGroup('pyRef', ['gcash', 'maya', 'bank_transfer'].includes(m));
        toggleGroup('pyCheck', ['check', 'pdc'].includes(m));
        $('pyAmount').disabled = m === 'credit';
    }

    function openVoid(id) {
        const o = orderById(id);
        if (!o) return;

        $('vdNumber').textContent = o.order_number + ' — ' + o.customer_name;
        $('voidForm').action = url('void', id);

        openModal('voidModal');
    }

    /* ---------------- BATCHES / EXPIRATION ---------------- */
    function fmtDate(v) {
        if (!v) return '—';
        const s = String(v);
        const m = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
        const d = m ? new Date(Number(m[1]), Number(m[2]) - 1, Number(m[3])) : new Date(s);
        return isNaN(d) ? s : d.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
    }

    // Days from today (accepts "2027-05-01" and "May 01, 2027")
    function daysUntil(v) {
        if (!v) return null;
        const s = String(v);
        const m = s.match(/^(\d{4})-(\d{2})-(\d{2})/);
        const d = m ? new Date(Number(m[1]), Number(m[2]) - 1, Number(m[3])) : new Date(s);
        if (isNaN(d)) return null;
        d.setHours(0, 0, 0, 0);
        const t = new Date(); t.setHours(0, 0, 0, 0);
        return Math.round((d - t) / 86400000);
    }

    // Text for the "Expiration / Best Before" line in the order-modal Product Information panel
    function expiryText(p) {
        const parts = [];
        if (p.earliest_expiration) parts.push('Exp: ' + fmtDate(p.earliest_expiration));
        if (p.earliest_best_before) parts.push('Best Before: ' + fmtDate(p.earliest_best_before));
        if (!parts.length) return 'No Date';
        const st = String(p.earliest_expiration_status || '').toLowerCase();
        return parts.join(' · ') + (st.includes('expired') ? ' — EXPIRED' : '');
    }

    // First non-empty scalar among several possible key names (supports "a.b" paths)
    function batchPick(obj, keys) {
        for (const k of keys) {
            let v = obj;
            for (const part of k.split('.')) v = (v === null || v === undefined) ? undefined : v[part];
            if (v !== undefined && v !== null && v !== '' && typeof v !== 'object') return v;
        }
        return null;
    }

    function normalizeBatch(b) {
        const qtyRec = batchPick(b, ['quantity_received', 'received_quantity', 'qty_received', 'quantity']);
        const qtyRem = batchPick(b, ['quantity_remaining', 'remaining_quantity', 'qty_remaining']);
        return {
            number:      batchPick(b, ['batch_number', 'lot_number', 'batch_lot_number', 'batch_no', 'lot_no', 'batch']),
            supplier:    batchPick(b, ['supplier_name', 'supplier.supplier_name', 'supplier.name', 'supplier']),
            po:          batchPick(b, ['po_number', 'purchase_order.po_number', 'purchase_order_number', 'purchase_order']),
            received:    batchPick(b, ['received_date', 'date_received', 'transaction_date']),
            expiration:  batchPick(b, ['expiration_date', 'expiry_date']),
            bestBefore:  batchPick(b, ['best_before_date', 'best_before']),
            qtyReceived: qtyRec === null ? null : Number(qtyRec),
            qtyRemaining: qtyRem === null ? null : Number(qtyRem),
            unitCost:    batchPick(b, ['unit_cost']),
            status:      batchPick(b, ['status', 'batch_status']),
        };
    }

    // Returns [key, label, badgeClass]
    function batchStatusOf(x) {
        const raw = String(x.status || '').toLowerCase().replace(/[\s-]+/g, '_');
        const map = {
            expired:  ['expired', 'EXPIRED', 'b-red'],
            soon:     ['soon', 'EXPIRING SOON', 'b-amber'],
            depleted: ['depleted', 'DEPLETED', 'b-gray'],
            active:   ['active', 'ACTIVE', 'b-green'],
        };

        if (raw.includes('expired') || raw.includes('past')) return map.expired;
        if (raw.includes('soon') || raw.includes('near') || raw.includes('expiring')) return map.soon;
        if (['depleted', 'empty', 'consumed', 'sold_out'].includes(raw)) return map.depleted;
        if (['active', 'available', 'ok', 'good', 'valid'].includes(raw)) {
            // Trust "active" unless the batch is clearly empty or past its date
            if (x.qtyRemaining !== null && x.qtyRemaining <= 0) return map.depleted;
            const ds = [x.expiration, x.bestBefore].map(daysUntil).filter(d => d !== null);
            if (ds.length) {
                const d = Math.min.apply(null, ds);
                if (d < 0) return map.expired;
                if (d <= 30) return map.soon;
            }
            return map.active;
        }
        if (raw) return ['other', raw.replace(/_/g, ' ').toUpperCase(), 'b-gray'];

        // No status from the backend: derive it
        if (x.qtyRemaining !== null && x.qtyRemaining <= 0) return map.depleted;
        const dates = [x.expiration, x.bestBefore].map(daysUntil).filter(d => d !== null);
        if (dates.length) {
            const d = Math.min.apply(null, dates);
            if (d < 0) return map.expired;
            if (d <= 30) return map.soon;
        }
        return map.active;
    }

    function batchSortDate(x) {
        const ds = [x.expiration, x.bestBefore].map(daysUntil).filter(d => d !== null);
        return ds.length ? Math.min.apply(null, ds) : 999999;
    }

    function batchesFor(id, data) {
        const candidates = [data && data.product && data.product.batches, data && data.batches, (productById(id) || {}).batches];
        return candidates.find(c => Array.isArray(c) && c.length) || [];
    }

    function renderBatches(list) {
        const body = $('pmBatches');
        const rows = (Array.isArray(list) ? list : []).map(normalizeBatch);

        if (!rows.length) {
            body.innerHTML = '<tr><td colspan="10" class="empty-state">No batches recorded yet. Batches are created through Opening Stock or Stock In.</td></tr>';
            return;
        }

        rows.sort((a, b) => {
            const da = batchStatusOf(a)[0] === 'depleted' ? 1 : 0;
            const db = batchStatusOf(b)[0] === 'depleted' ? 1 : 0;
            return da - db || batchSortDate(a) - batchSortDate(b);
        });

        body.innerHTML = rows.map(x => {
            const st = batchStatusOf(x);
            return '<tr class="' + (st[0] === 'depleted' ? 'batch-depleted' : '') + '">' +
                '<td><strong>' + escapeHtml(dash(x.number)) + '</strong></td>' +
                '<td>' + escapeHtml(dash(x.supplier)) + '</td>' +
                '<td>' + escapeHtml(dash(x.po)) + '</td>' +
                '<td>' + escapeHtml(fmtDate(x.received)) + '</td>' +
                '<td>' + escapeHtml(x.expiration ? fmtDate(x.expiration) : 'No Date') + '</td>' +
                '<td>' + escapeHtml(x.bestBefore ? fmtDate(x.bestBefore) : 'No Date') + '</td>' +
                '<td class="text-right num">' + (x.qtyReceived === null ? '—' : num(x.qtyReceived)) + '</td>' +
                '<td class="text-right num"><strong>' + (x.qtyRemaining === null ? '—' : num(x.qtyRemaining)) + '</strong></td>' +
                '<td class="text-right num">' + (x.unitCost === null ? '—' : money(x.unitCost)) + '</td>' +
                '<td><span class="badge ' + st[2] + '">' + escapeHtml(st[1]) + '</span></td></tr>';
        }).join('');
    }

    /* ---------------- PRODUCT DETAILS MODAL ---------------- */
    function openProductModal(id) {
        if (!id) return;
        id = Number(id);

        $('pmLoading').classList.remove('hidden');
        $('pmError').classList.add('hidden');
        $('pmBody').classList.add('hidden');
        openModal('productModal');

        fetchProductInfo(id)
            .then(data => {
                const p = data.product || {};
                const reserved = Math.max(0, Number(p.current_stock || 0) - Number(p.available_stock || 0));
                const prodStatus = productStatusMap[id];
                const item = (label, value) => '<div class="detail-item"><span class="detail-label">' + label + '</span><span class="detail-value">' + escapeHtml(dash(value)) + '</span></div>';

                $('pmAlert').innerHTML = Number(p.available_stock) <= 0
                    ? '<div class="stock-alert out">OUT OF STOCK — no units available right now.</div>'
                    : (p.stock_status === 'LOW STOCK' ? '<div class="stock-alert low">LOW STOCK — consider reordering from a supplier.</div>' : '');

                // Current state only (the stock-card style history belongs to the Reports module)
                $('pmDetails').innerHTML =
                    item('Product Name', p.name) + item('Brand', p.brand) + item('Category', p.category) +
                    item('API', p.api) + item('Base Oil', p.base_oil) + item('Package Size', p.package_size) +
                    item('Unit', p.unit) + item('Selling Price', money(p.price)) + item('Product Status', prodStatus ? prodStatus.charAt(0).toUpperCase() + prodStatus.slice(1) : null) +
                    item('Stock Status', p.stock_status) +
                    item('Current Stock', num(p.current_stock)) + item('Reserved Stock', num(reserved)) + item('Available Stock', num(p.available_stock)) +
                    item('Reorder Level', num(p.reorder_level)) +
                    item('Expiration / Best Before', expiryText(p));

                const names = uniqueSuppliers(data.suppliers);
                const linked = payloadSuppliers(p) || [];
                const allNames = uniqueSuppliers(linked.concat(names));
                $('pmSupplierChips').innerHTML = allNames.length ? chipsHtml(allNames, id, 100) : '<div class="sub">No suppliers found for this product.</div>';

                $('pmSuppliers').innerHTML = data.suppliers.length
                    ? data.suppliers.map(s => '<tr><td>' + escapeHtml(s.supplier) + '</td><td>' + escapeHtml(s.po_number) + '</td><td>' + escapeHtml(s.po_date) +
                        '</td><td class="text-right">' + (s.unit_cost === null || s.unit_cost === undefined ? '—' : money(s.unit_cost)) + '</td><td class="text-right">' + num(s.quantity) +
                        '</td><td class="text-right">' + num(s.received) + '</td></tr>').join('')
                    : '<tr><td colspan="6" class="empty-state">No purchase history for this product yet.</td></tr>';

                renderBatches(batchesFor(id, data));

                const moves = Array.isArray(data.movements) ? data.movements : [];
                $('pmMovements').innerHTML = moves.length
                    ? moves.map(m => {
                        const l = mvLabel(m.type);
                        // Opening balance: Qty shown as 0, Before = opening quantity (it is the starting balance, not a Stock In)
                        const isOpen = String(m.type || '').toLowerCase() === 'opening_balance';
                        const qtyTxt = isOpen ? '0' : ((m.quantity > 0 ? '+' : '') + num(m.quantity));
                        const before = isOpen ? m.after : m.before;
                        return '<tr><td>' + escapeHtml(m.transaction_date) + '</td><td><span class="badge ' + l[1] + '">' + l[0] + '</span></td><td>' +
                            qtyTxt + '</td><td>' + num(before) + '</td><td>' + num(m.after) +
                            '</td><td>' + escapeHtml(m.reason || '—') + '</td><td>' + escapeHtml(m.order || '—') + '</td><td>' + escapeHtml(m.receipt || '—') +
                            '</td><td>' + escapeHtml(m.received_by || '—') + '</td><td>' + escapeHtml(m.reference || '—') + '</td><td>' + escapeHtml(m.user || '—') + '</td></tr>';
                    }).join('')
                    : '<tr><td colspan="11" class="empty-state">No stock movements recorded for this product yet.</td></tr>';

                $('pmLoading').classList.add('hidden');
                $('pmBody').classList.remove('hidden');
            })
            .catch(() => {
                $('pmLoading').classList.add('hidden');
                $('pmError').classList.remove('hidden');
                $('pmError').innerHTML = '<div class="inline-error">Unable to load product information. Please try again. ' +
                    '<button type="button" class="link-btn" onclick="openProductModal(' + id + ')">Retry</button></div>';
            });
    }

    /* ---------------- STOCK IN ----------------
       PO  ->  Supplier, Reference, Product list
       Product  ->  Brand, Remaining PO Quantity, Purchase Unit Cost (from the PO item)
       The backend re-reads all of these from the database; nothing here is trusted.
       Current stock is increased by the server; the user never types a balance. */

    function todayStr() {
        const d = new Date();
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    function siSelectedPo() {
        return purchaseOrders.find(p => Number(p.id) === Number($('siPo').value)) || null;
    }

    function siSelectedItem() {
        const po = siSelectedPo();
        return po ? (po.items || []).find(i => Number(i.product_id) === Number($('siProduct').value)) || null : null;
    }

    // Expiration / Best Before can never be earlier than the Transaction Date
    function siSetMinDates() {
        const d = $('siDate').value || '';
        $('siExp').min = d;
        $('siBestBefore').min = d;
    }

    function siClearErrors() {
        const box = $('siError');
        box.classList.add('hidden');
        box.innerHTML = '';
        const qm = $('siQtyMsg');
        qm.classList.add('hidden');
        qm.textContent = '';
        document.querySelectorAll('#stockInForm .bad').forEach(el => el.classList.remove('bad'));
        const server = $('siServerError');
        if (server) server.classList.add('hidden');
    }

    // Clears everything that depends on the Purchase Order
    function siClearDependents(includeReason) {
        const sel = $('siProduct');
        sel.innerHTML = '<option value="">Select Purchase Order first</option>';
        sel.disabled = true;

        $('siSupplier').value = '';
        $('siBrand').value = '';
        $('siRemaining').value = '';
        $('siCost').value = '';
        $('siRef').value = '';

        const q = $('siQty');
        q.value = '';
        q.removeAttribute('max');
        q.disabled = true;

        $('siBatch').value = '';
        $('siExp').value = '';
        $('siBestBefore').value = '';
        if (includeReason) $('siReason').value = '';
    }

    // Full reset: used by Cancel, X, backdrop, Esc and before every fresh open
    function resetStockIn() {
        $('stockInForm').reset();
        $('siDate').value = todayStr();
        $('siPo').value = '';
        siClearDependents(true);
        siClearErrors();
        siSetMinDates();
    }

    function openStockIn() {
        resetStockIn();
        openModal('stockInModal');
    }

    // PO changed: reload everything that belongs to the new PO and drop everything from the old one
    function populateStockInProducts(poId) {
        siClearDependents(true);
        siClearErrors();

        const po = purchaseOrders.find(p => Number(p.id) === Number(poId));
        if (!po) return;

        const opt = $('siPo').selectedOptions[0];
        $('siSupplier').value = po.supplier_name || (opt && opt.dataset.supplier) || '';
        $('siRef').value = po.po_number || '';

        const select = $('siProduct');
        select.innerHTML = '<option value="">Select Product</option>';

        let has = false;
        (po.items || []).forEach(item => {
            const remaining = Number(item.remaining_quantity);
            if (remaining <= 0) return;
            has = true;
            const o = document.createElement('option');
            o.value = item.product_id;
            o.textContent = item.product_name + ' — ' + (item.brand || 'No brand') + ' — Remaining: ' + remaining;
            select.appendChild(o);
        });

        if (!has) {
            select.innerHTML = '<option value="">All products in this PO are fully received</option>';
            return;
        }

        select.disabled = false;
    }

    // Product changed: refresh product-dependent fields and drop the previous product's entries
    function updateStockInRemaining(productId) {
        const q = $('siQty');

        q.value = '';
        q.removeAttribute('max');
        $('siBatch').value = '';
        $('siExp').value = '';
        $('siBestBefore').value = '';
        siClearErrors();

        const item = siSelectedItem();

        if (!productId || !item) {
            $('siBrand').value = '';
            $('siRemaining').value = '';
            $('siCost').value = '';
            q.disabled = true;
            return;
        }

        const remaining = Number(item.remaining_quantity);

        $('siBrand').value = item.brand || '';
        $('siRemaining').value = remaining.toLocaleString();
        $('siCost').value = (item.unit_cost === null || item.unit_cost === undefined) ? '' : money(item.unit_cost);

        q.min = 1;
        q.max = remaining;
        q.disabled = remaining <= 0;
    }

    // Live inline message under Quantity Received (the backend repeats and enforces the rule)
    function siLiveQty() {
        const q = $('siQty');
        const msg = $('siQtyMsg');
        msg.classList.add('hidden');
        msg.textContent = '';
        q.classList.remove('bad');

        const item = siSelectedItem();
        const raw = q.value.trim();
        if (!item || raw === '') return;

        const n = Number(raw);
        const remaining = Number(item.remaining_quantity);
        let text = '';

        if (isNaN(n) || !Number.isInteger(n)) text = 'Quantity received must be a whole number.';
        else if (n < 1) text = 'Quantity received must be greater than 0.';
        else if (n > remaining) text = 'Quantity received cannot exceed the remaining PO quantity (' + remaining.toLocaleString() + ').';

        if (text) {
            msg.textContent = text;
            msg.classList.remove('hidden');
            q.classList.add('bad');
        }
    }

    // Inline validation (the backend repeats and enforces every rule)
    $('stockInForm').addEventListener('submit', function (e) {
        siClearErrors();

        const problems = [];
        const mark = (id, bad, msg) => { if (bad) { $(id).classList.add('bad'); problems.push(msg); } };

        const po = siSelectedPo();
        const item = siSelectedItem();
        const date = $('siDate').value;

        mark('siDate', !date, 'Transaction date is required.');
        mark('siPo', !po, 'Select a Purchase Order.');
        mark('siProduct', !!po && !item, 'Select a product from the Purchase Order.');

        if (item) {
            const raw = $('siQty').value.trim();
            const n = Number(raw);
            const remaining = Number(item.remaining_quantity);

            if (raw === '' || isNaN(n)) mark('siQty', true, 'Enter the quantity received.');
            else if (!Number.isInteger(n)) mark('siQty', true, 'Quantity received must be a whole number.');
            else if (n < 1) mark('siQty', true, 'Quantity received must be greater than 0.');
            else if (n > remaining) mark('siQty', true, 'Quantity received cannot exceed the remaining PO quantity (' + remaining.toLocaleString() + ').');
        }

        const exp = $('siExp').value, bb = $('siBestBefore').value;
        if (date && exp && exp < date) mark('siExp', true, 'Expiration date cannot be earlier than the transaction date.');
        if (date && bb && bb < date) mark('siBestBefore', true, 'Best before date cannot be earlier than the transaction date.');

        if (problems.length) {
            e.preventDefault();
            const box = $('siError');
            box.innerHTML = '<strong>⚠ Please fix the following:</strong><ul style="margin:6px 0 0 18px;padding:0;">' +
                problems.map(p => '<li>' + escapeHtml(p) + '</li>').join('') + '</ul>';
            box.classList.remove('hidden');
        }
    });

    // After a failed Stock In submit, put the user's entries back (the PO and product lists come from the page data)
    function restoreStockIn() {
        if (!oldStockIn || oldStockIn.po === null || oldStockIn.po === undefined) return;

        if (oldStockIn.date) $('siDate').value = oldStockIn.date;
        siSetMinDates();

        $('siPo').value = oldStockIn.po;
        populateStockInProducts(oldStockIn.po);

        if (oldStockIn.product) {
            $('siProduct').value = oldStockIn.product;
            updateStockInRemaining(oldStockIn.product);
        }

        if (oldStockIn.qty) $('siQty').value = oldStockIn.qty;
        if (oldStockIn.batch) $('siBatch').value = oldStockIn.batch;
        if (oldStockIn.exp) $('siExp').value = oldStockIn.exp;
        if (oldStockIn.bb) $('siBestBefore').value = oldStockIn.bb;
        if (oldStockIn.reason) $('siReason').value = oldStockIn.reason;

        // populate/update helpers hide the server error box; show it again for this restored form
        const server = $('siServerError');
        if (server) server.classList.remove('hidden');
    }

    /* ---------------- ADJUST STOCK ---------------- */
    function updateAdjustStock() {
        const p = productById($('adjProduct').value);
        const current = p ? Number(p.current_stock) : null;
        $('adjCurrent').textContent = current === null ? '0' : current.toLocaleString();

        const raw = $('adjActual').value;
        const diffEl = $('adjDiff');
        if (current === null || raw === '') { diffEl.textContent = '—'; diffEl.style.color = ''; return; }

        const diff = Number(raw) - current;
        diffEl.textContent = (diff > 0 ? '+' : '') + diff.toLocaleString() + (diff === 0 ? ' (matches system)' : (diff > 0 ? ' (increase)' : ' (decrease)'));
        diffEl.style.color = diff === 0 ? '#166534' : (diff > 0 ? '#1d4ed8' : '#b91c1c');
    }

    function openAdjust(id) {
        if (!id) return;
        $('adjProduct').value = id;
        updateAdjustStock();
        openModal('adjustModal');
    }

    /* ---------------- RETURN / RECORD DAMAGE ---------------- */
    // Fresh form every time: cleared fields, date back to today, Received By back to the signed-in user.
    function openReturn() {
        $('returnForm').reset();
        $('rtDate').value = todayStr();
        openModal('returnModal');
    }

    function openDamage() {
        $('damageForm').reset();
        $('dmDate').value = todayStr();
        openModal('damageModal');
    }

    /* ---------------- INIT ---------------- */
    (function init() {
        $('checkoutToken').value =
            (window.crypto && crypto.randomUUID) ? crypto.randomUUID() : (Date.now() + '-' + Math.random().toString(16).slice(2));

        // Restore the cart after a validation / stock error
        if (Array.isArray(oldItems)) {
            oldItems.forEach(it => addToCart(Number(it.product_id), Number(it.quantity), true));
        }

        renderSupplierPicker(oldSupplierIds);
        apToggleBatch();
        siSetMinDates();
        renderCart();
        filterInventory();
        filterOrders();
        filterGrid();
        togglePayFields();
        showTab(paramToTab[initialTab] || 'inventory');

        // Re-open the form that failed so its errors and entries stay in front of the user
        if (failedModal === 'order') {
            openOrderModal();
            if (cartShortages().length) orderWarn('Some items are above the available stock. Please double-check before continuing.');
        } else if (failedModal === 'stockIn') {
            restoreStockIn();
            openModal('stockInModal');
        } else if (failedModal === 'adjust') {
            if (oldAdjust.product) $('adjProduct').value = oldAdjust.product;
            updateAdjustStock();
            openModal('adjustModal');
        } else if (failedModal === 'addProduct') {
            openModal('addProductModal');
        } else if (failedModal === 'addCategory') {
            openModal('addProductModal');
            openAddCategory();
        }
    })();

</script>

</body>

</html>