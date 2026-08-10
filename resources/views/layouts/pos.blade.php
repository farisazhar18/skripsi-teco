<!DOCTYPE html>
<html>
<head>
    <title>Terminal Coffee POS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('logo-terminal.png') }}">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            margin: 0;
            background: #f5efe6;
            color: #183f37;
        }

        .sidebar {
            width: 300px;
            height: 100vh;
            background: #183f37;
            color: #efe6d8;
            position: fixed;
            left: 0;
            top: 0;
            padding: 25px 18px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* Efek saat menu sedang aktif dibuka */
        .active-menu {
            background-color: rgba(255, 255, 255, 0.15); /* Efek terang transparan */
            border-left: 4px solid #fcd34d; /* Garis kuning/emas di pinggir kiri */
            font-weight: bold !important;
            border-radius: 0 8px 8px 0; /* Biar sudut kanannya melengkung halus */
        }

        /* Styling Khusus Dropdown Sidebar */
        .dropdown-btn {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            background: none;
            color: #efe6d8;
            border: none;
            padding: 13px 16px;
            margin-bottom: 4px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 15px;
            text-align: left;
            cursor: pointer;
            transition: background 0.2s;
        }

        .dropdown-btn:hover {
            background: #2e5a4f;
        }

        .dropdown-btn .arrow {
            font-size: 11px;
            transition: transform 0.2s;
        }

        .dropdown-container {
            display: none;
            background: rgba(0, 0, 0, 0.12);
            border-radius: 12px;
            margin-bottom: 8px;
            padding: 4px 0 4px 10px;
        }

        .dropdown-container a {
            font-size: 14px !important;
            padding: 10px 16px !important;
            margin-bottom: 4px !important;
        }

        .active-parent {
            color: #fcd34d !important;
        }

        /* Mengunci area menu atas agar bisa di-scroll secara mandiri */
        .menu-wrapper {
            overflow-y: auto;
            flex-grow: 1;
            padding-right: 5px;
            margin-bottom: 10px;
        }

        /* Menghilangkan scrollbar bawaan browser di menu-wrapper biar makin clean */
        .menu-wrapper::-webkit-scrollbar {
            width: 5px;
        }
        .menu-wrapper::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
        }

        .brand {
            text-align: center;
            margin-bottom: 20px;
            flex-shrink: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(255,255,255,0.08); 
        }

        .brand img {
            width: 75px; 
            border-radius: 50%;
            margin-bottom: 8px;
        }

        .brand h2 {
            margin: 0;
            font-size: 20px; 
            letter-spacing: 0.5px;
            font-weight: bold;
        }

        .brand small {
            color: #d8cbb8;
            font-size: 14px; 
            opacity: 0.8;
        }

        .menu a {
            display: block;
            color: #efe6d8;
            text-decoration: none;
            padding: 13px 16px;
            margin-bottom: 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 15px;
        }

        .menu a:hover {
            background: #2e5a4f;
        }

        .content {
            margin-left: 300px;
            padding: 40px;
        }

        .page-title {
            font-size: 36px;
            margin-bottom: 25px;
            color: #183f37;
            text-align: center;
        }

        .card,
        .form-card,
        .stat-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(24,63,55,0.10);
        }

        .card {
            padding: 24px;
            margin-bottom: 20px;
        }

        .card h2 {
            margin: 0;
            font-size: 20px;
            color: #183f37;
            font-weight: 600;
        }

        .form-card {
            padding: 32px;
            max-width: 900px;
        }

        .btn,
        button {
            background: #183f37;
            color: #efe6d8;
            padding: 11px 16px;
            border: none;
            border-radius: 11px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
        }

        .btn:hover,
        button:hover {
            background: #2e5a4f;
        }

        .btn-danger {
            background: #7a2e2e;
            color: white;
        }

        .btn-danger:hover {
            background: #5c2020;
        }

        .btn-secondary {
            background: #efe6d8;
            color: #183f37;
            padding: 10px 16px;
            border-radius: 10px;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
            border: 2px solid #d8cbb8;
            transition: 0.3s;
        }

        .btn-secondary:hover {
            background: #d8cbb8;
        }

        .action-link {
            background: #efe6d8;
            color: #183f37;
            padding: 9px 14px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            display: inline-block;
        }

        .action-link:hover {
            background: #d8cbb8;
        }

        .back-link {
            text-decoration: none;
            color: #183f37;
            font-weight: bold;
        }

        .success {
            color: #0f7a3a;
            font-weight: bold;
            background: #e5f5ec;
            padding: 12px 16px;
            border-radius: 12px;
            display: inline-block;
        }

        .table-card {
            background: white;
            border-radius: 20px;
            overflow-x: auto;
            box-shadow: 0 10px 25px rgba(24,63,55,0.10);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            table-layout: auto;
        }

        th {
            background: #183f37;
            color: #efe6d8;
            padding: 14px 12px;
            text-align: center;
            font-size: 15px;
            white-space: nowrap;
            border-right: 1px solid rgba(255,255,255,0.18);
        }

        th:last-child {
            border-right: none;
        }

        td {
            padding: 14px 12px;
            border-bottom: 1px solid #e5e0d8;
            border-right: 1px solid #e5e0d8;
            color: #123832;
            font-size: 15px;
            vertical-align: middle;
            text-align: center;
        }

        td:last-child {
            border-right: none;
        }

        tr:hover td {
            background: #faf7f0;
        }

        td:nth-child(3) {
            text-align: left;
        }

        td:nth-child(2),
        th:nth-child(2) {
            min-width: 140px;
        }

        td:nth-child(4),
        td:nth-child(5),
        td:nth-child(6) {
            white-space: nowrap;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
            justify-content: center;
        }

        input,
        select,
        textarea {
            padding: 12px;
            border-radius: 11px;
            border: 1px solid #c9bca8;
            width: 100%;
            max-width: 420px;
            background: white;
            color: #183f37;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #183f37;
            box-shadow: 0 0 0 3px rgba(24,63,55,0.12);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #183f37;
        }

        .form-actions {
            margin-top: 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .badge {
            padding: 7px 12px;
            border-radius: 999px;
            font-weight: bold;
            display: inline-block;
            font-size: 14px;
        }

        .badge-success {
            background: #e5f5ec;
            color: #0f7a3a;
        }

        .badge-warning {
            background: #fff3d8;
            color: #b56a00;
        }

        .badge-danger {
            background: #ffe1df;
            color: #c62828;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 20px;
        }

        .stat-card {
            padding: 24px;
        }

        .stat-card h3 {
            margin: 0 0 12px 0;
            color: #6b6256;
            font-size: 16px;
        }

        .stat-card h1 {
            margin: 0;
            color: #183f37;
            font-size: 36px;
        }

        .hamburger {
            display: none;
        }

        .overlay {
            display: none;
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 250px;
            }

            .content {
                margin-left: 250px;
                padding: 25px;
            }

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .menu a, .dropdown-btn {
                font-size: 14px;
            }
        }

        @media (max-width: 768px) {
            .hamburger {
                display: block;
                position: fixed;
                top: 15px;
                left: 15px;
                z-index: 1001;
                background: #183f37;
                color: #efe6d8;
                border: none;
                border-radius: 10px;
                padding: 10px 13px;
                font-size: 20px;
            }

            .sidebar {
                position: fixed;
                left: -300px;
                top: 0;
                width: 280px;
                height: 100vh;
                padding: 25px 18px;
                transition: 0.3s;
            }

            .sidebar.active {
                left: 0;
            }

            .overlay.active {
                display: block;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.35);
                z-index: 999;
            }

            .brand img {
                width: 70px;
            }

            .content {
                margin-left: 0;
                padding: 70px 20px 20px;
            }

            .page-title {
                font-size: 28px;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .menu {
                display: block;
            }

            .menu a, .dropdown-btn {
                text-align: left;
                margin-bottom: 8px;
            }

            input,
            select,
            textarea {
                max-width: 100%;
            }
        }

        @media (max-width: 480px) {
            .page-title {
                font-size: 24px;
            }

            .content {
                padding: 70px 15px 15px;
            }

            .btn,
            .action-link,
            .btn-danger,
            .btn-secondary,
            button {
                padding: 8px 10px;
                font-size: 13px;
            }
        }

        .ts-wrapper {
            width: 100% !important;
            max-width: 420px !important;
        }

        .ts-control {
            border-radius: 11px !important;
            padding: 12px !important;
            border: 1px solid #c9bca8 !important;
            font-size: 15px !important;
            background: white !important;
            color: #183f37 !important;
            box-shadow: none !important;
        }

        .ts-control.focus {
            border-color: #183f37 !important;
            box-shadow: 0 0 0 3px rgba(24,63,55,0.12) !important;
        }

        .ts-dropdown {
            border-radius: 11px !important;
            border: 1px solid #c9bca8 !important;
            margin-top: 5px !important;
            box-shadow: 0 10px 25px rgba(24,63,55,0.10) !important;
            max-width: 420px !important;
        }

        .ts-dropdown .option.active, 
        .ts-dropdown .option:hover {
            background-color: #183f37 !important; 
            color: #efe6d8 !important;
        }

        /* ============================================ */
        /* UTILITY CLASSES                              */
        /* ============================================ */
        .flex { display: flex; }
        .flex-wrap { flex-wrap: wrap; }
        .items-center { align-items: center; }
        .items-start { align-items: flex-start; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .justify-end { justify-content: flex-end; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .gap-xs { gap: 4px; }
        .gap-sm { gap: 8px; }
        .gap-md { gap: 10px; }
        .gap-lg { gap: 12px; }
        .gap-xl { gap: 15px; }
        .gap-2xl { gap: 20px; }
        .m-0 { margin: 0; }
        .mb-0 { margin-bottom: 0; }
        .mb-sm { margin-bottom: 10px; }
        .mb-md { margin-bottom: 20px; }
        .mb-lg { margin-bottom: 25px; }
        .mt-sm { margin-top: 8px; }
        .w-full { width: 100%; }
        .w-auto { width: auto; }
        .inline-block { display: inline-block; }
        .block { display: block; }
        .nowrap { white-space: nowrap; }
        .font-bold { font-weight: 700; }
        .font-semibold { font-weight: 600; }
        .font-medium { font-weight: 500; }

        /* ============================================ */
        /* NOTIFICATION BADGE (Sidebar)                 */
        /* ============================================ */
        .notif-badge {
            background: #ef4444;
            color: white;
            font-size: 11px;
            padding: 2px 7px;
            border-radius: 8px;
            line-height: 1.2;
        }

        /* ============================================ */
        /* ALERT & FEEDBACK                             */
        /* ============================================ */
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
            border: 1px solid #a7f3d0;
        }

        .error-list {
            color: red;
        }

        .text-muted {
            color: #6b6256;
        }

        /* ============================================ */
        /* FORM HELPERS                                 */
        /* ============================================ */
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .checkbox-inline {
            width: auto;
        }

        .checkbox-row {
            display: flex;
            gap: 20px;
            margin-top: 8px;
        }

        /* ============================================ */
        /* PRODUCT IMAGE PREVIEW                        */
        /* ============================================ */
        .product-img-preview {
            width: 150px;
            border-radius: 10px;
            margin-bottom: 10px;
            border: 1px solid #c9bca8;
        }

        .product-img-empty {
            width: 150px;
            height: 100px;
            background: #e8e2d8;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #8a8073;
            margin-bottom: 10px;
        }

        /* ============================================ */
        /* PRODUCT TABLE (produk/index)                 */
        /* ============================================ */
        .produk-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .produk-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }

        .outlet-select {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            height: 42px;
            outline: none;
            font-weight: 500;
        }

        .btn-add {
            height: 42px;
            display: flex;
            align-items: center;
            padding: 0 16px;
            margin: 0;
            border-radius: 6px;
        }

        .outlet-badge-inline {
            display: flex;
            align-items: center;
            height: 42px;
            background: #e5f5ec;
            color: #0f7a3a;
            padding: 0 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            border: 1px solid #b7e4c7;
        }

        .produk-foto-box {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            flex-shrink: 0;
        }

        .produk-foto-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .produk-foto-placeholder {
            font-size: 20px;
            opacity: 0.5;
        }

        .produk-nama {
            display: block;
            font-size: 15px;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .produk-kategori {
            display: inline-block;
            font-size: 11px;
            font-weight: 600;
            color: #475569;
            background: #f1f5f9;
            padding: 3px 8px;
            border-radius: 4px;
            border: 1px solid #e2e8f0;
        }

        .harga-reguler {
            font-weight: 700;
            color: #0f172a;
            font-size: 14px;
        }

        .harga-large {
            font-weight: 600;
            color: #334155;
            font-size: 13px;
            margin-top: 4px;
        }

        /* Badge penyajian */
        .badge-racikan {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 12px;
            background: #e0e7ff;
            color: #3730a3;
        }

        .badge-vendor {
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 12px;
            background: #fef3c7;
            color: #92400e;
        }

        .badge-hot {
            font-size: 11px;
            background: #fee2e2;
            color: #b91c1c;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: bold;
        }

        .badge-ice {
            font-size: 11px;
            background: #dbeafe;
            color: #1d4ed8;
            padding: 3px 8px;
            border-radius: 12px;
            font-weight: bold;
        }

        /* Badge status produk */
        .badge-tersedia {
            background: #d1fae5;
            color: #047857;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-kosong {
            background: #fee2e2;
            color: #b91c1c;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .badge-no-resep {
            background: #fef3c7;
            color: #b45309;
            padding: 5px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }

        .btn-edit {
            background: #f1f5f9;
            color: #334155;
            padding: 6px 12px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #cbd5e1;
        }

        .btn-hapus {
            background: #fee2e2;
            color: #b91c1c;
            padding: 6px 12px;
            border-radius: 6px;
            border: 1px solid #fecaca;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }

        /* ============================================ */
        /* PENJUALAN (Order Cards)                      */
        /* ============================================ */
        .action-bar {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .search-box {
            display: flex;
            gap: 8px;
            margin: 0;
            align-items: center;
        }

        .search-input {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            outline: none;
            width: 280px;
            font-size: 14px;
        }

        .order-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
        }

        .order-card {
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .order-id {
            margin: 0;
            font-size: 14px;
            color: #64748b;
        }

        .order-customer {
            color: #0f172a;
            font-weight: 700;
            font-size: 18px;
            margin-top: 4px;
        }

        .badge-status {
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 12px;
            text-align: center;
            line-height: 1.2;
        }

        .order-meta {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 5px;
        }

        .order-meta-method {
            color: #475569;
        }

        .order-meta-total {
            color: #1e293b;
            font-size: 15px;
            display: inline-block;
            margin-top: 4px;
        }

        .order-divider {
            border: none;
            border-top: 1px dashed #cbd5e1;
            margin: 12px 0;
        }

        .order-items {
            list-style-type: none;
            padding: 0;
            margin: 10px 0;
        }

        .order-item {
            margin-bottom: 10px;
            background: #f8fafc;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .order-item-name {
            color: #0f172a;
            font-size: 14px;
        }

        .order-item-detail {
            color: #64748b;
            font-size: 12px;
        }

        .order-item-note {
            color: #d97706;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            margin-top: 2px;
        }

        .order-actions {
            margin-top: 15px;
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-proses {
            background-color: #3b82f6;
            color: white;
            border: none;
            margin: 0;
        }

        .btn-siap {
            background-color: #10b981;
            color: white;
            border: none;
            margin: 0;
        }

        .btn-selesai {
            background-color: #0f172a;
            color: white;
            border: none;
            margin: 0;
        }

        .btn-detail {
            background: #64748b;
            color: white;
            margin: 0 0 0 auto;
        }

        /* ============================================ */
        /* MODAL (Kembalian)                            */
        /* ============================================ */
        .modal-overlay {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.6);
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(2px);
        }

        .modal-container {
            background-color: white;
            width: 90%;
            max-width: 450px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.2);
        }

        .modal-header {
            background-color: #183f37;
            padding: 15px 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 24px;
            cursor: pointer;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-tagihan {
            text-align: center;
            margin-bottom: 20px;
        }

        .modal-tagihan p {
            margin: 0 0 5px 0;
            color: #64748b;
            font-size: 14px;
        }

        .modal-tagihan h2 {
            margin: 0;
            color: #b45309;
            font-size: 28px;
        }

        .modal-divider {
            border-top: 1px dashed #cbd5e1;
            margin-bottom: 20px;
        }

        .modal-input-label {
            font-weight: bold;
            color: #1e293b;
        }

        .modal-input-uang {
            font-size: 18px;
            font-weight: bold;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            outline: none;
            margin-top: 8px;
        }

        .quick-cash-container {
            display: flex;
            gap: 8px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .modal-kembalian-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }

        .modal-kembalian-box p {
            margin: 0 0 5px 0;
            color: #64748b;
            font-weight: bold;
        }

        .modal-kembalian-box h2 {
            margin: 0;
            color: #059669;
            font-size: 28px;
        }

        .modal-footer {
            padding: 15px 20px;
            background-color: #f8fafc;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-batal {
            padding: 10px 20px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            background: white;
            color: #475569;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-konfirmasi {
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            background: #059669;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        /* ============================================ */
        /* BAHAN BAKU (Action bar buttons)              */
        /* ============================================ */
        .btn-action-bar {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            height: 42px;
            display: flex;
            align-items: center;
        }

        .btn-rekap-keluar {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            height: 42px;
            display: flex;
            align-items: center;
        }

        .btn-rekap-masuk {
            background-color: #e0f2fe;
            color: #0369a1;
            border: 1px solid #bae6fd;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            height: 42px;
            display: flex;
            align-items: center;
        }

        .btn-laporan {
            background-color: #f8fafc;
            color: #183f37;
            border: 1px solid #cbd5e1;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            height: 42px;
            display: flex;
            align-items: center;
        }

        .badge-stok-habis {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        .badge-stok-menipis {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fcd34d;
        }

        .badge-stok-tersedia {
            background-color: #d1fae5;
            color: #047857;
            border: 1px solid #6ee7b7;
        }

        .th-bahan {
            padding: 12px 15px;
        }

        .outlet-row td {
            background-color: #183f37;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 15px;
            padding: 12px 15px 12px 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: left;
        }

        .kategori-row td {
            background-color: #dbe4e0;
            color: #183f37 !important;
            font-weight: 700;
            font-size: 13px;
            padding: 10px 15px 10px 20px;
            text-align: left;
            border-top: 1px solid #b7c7c1;
            border-bottom: 1px solid #b7c7c1;
            text-transform: uppercase;
        }

        .stok-value {
            font-weight: 600;
        }

        .stok-unit {
            font-size: 12px;
            color: #6b7280;
            font-weight: normal;
        }

        .empty-row {
            text-align: center;
            padding: 30px;
            color: #6b7280;
            font-style: italic;
        }

        /* ============================================ */
        /* SIDEBAR USER FOOTER                          */
        /* ============================================ */
        .sidebar-divider {
            margin: 10px 0;
            border: none;
            border-top: 1px solid rgba(255,255,255,0.15);
        }

        .user-info-box {
            background: rgba(255,255,255,0.10);
            padding: 12px;
            border-radius: 12px;
            margin-bottom: 12px;
            color: #efe6d8;
            font-size: 14px;
            line-height: 1.8;
        }

        .btn-ganti-outlet {
            margin-top: 8px;
            display: inline-block;
            background: #efe6d8;
            color: #183f37;
            padding: 6px 10px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 11px;
            font-weight: bold;
        }

        .btn-logout {
            width: 100%;
            text-align: left;
            background: #7a2e2e;
            color: white;
            border: none;
            padding: 13px 16px;
            border-radius: 12px;
            font-weight: bold;
            cursor: pointer;
        }

        .dropdown-badge-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .menu-item-with-badge {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
    </style>
</head>

<body>

<button class="hamburger" onclick="toggleSidebar()">
    ☰
</button>

<div class="overlay" onclick="toggleSidebar()"></div>

<div class="sidebar" id="sidebar">
    <div class="brand">
        <img src="{{ asset('logo-terminal.png') }}" alt="Terminal Coffee">
        <h2>Terminal Coffee</h2>
        <small>POS System</small>
    </div>

    <div class="menu-wrapper">
        <div class="menu">
            <a href="{{ url('/dashboard') }}" class="{{ request()->is('dashboard*') ? 'active-menu' : '' }}">
                🏠 Dashboard
            </a>

            @if(auth()->user()->role == 'owner')
                
                <a href="{{ url('/penjualan') }}" class="{{ request()->is('penjualan*') ? 'active-menu' : '' }}">
                    💰 Sales
                </a>

                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <span>☕ Product Catalog</span>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ url('/produk') }}" class="{{ request()->is('produk*') ? 'active-menu' : '' }}">☕ Produk</a>
                        <a href="{{ url('/resep-produk') }}" class="{{ request()->is('resep-produk*') ? 'active-menu' : '' }}">📖 Resep Produk</a>
                    </div>
                </div>

                @php $pendingStok = \App\Models\PengajuanStok::where('status', 'pending')->count(); @endphp
                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <span>📦 Outlet Inventory</span>
                        @if($pendingStok > 0)
                                <span class="notif-badge">{{ $pendingStok }}</span>
                            @endif
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ url('/pengajuan-stok') }}" class="{{ request()->is('pengajuan-stok*') ? 'active-menu' : '' }}" class="menu-item-with-badge">
                            <span>✅ Persetujuan Stok</span>
                            @if($pendingStok > 0)
                                <span class="notif-badge">{{ $pendingStok }}</span>
                            @endif
                        </a>
                        <a href="{{ url('/bahan-baku') }}" class="{{ request()->is('bahan-baku*') ? 'active-menu' : '' }}">📦 Bahan Baku</a>
                    </div>
                </div>

                @php 
                    // Hitung data sekali saja di luar untuk dipakai di induk dan anak
                    $pendingPengadaan = \App\Models\Pembelian::where(function($query) {
                        $query->whereNull('status_acc')
                            ->orWhere('status_acc', 'menunggu ACC');
                    })->count(); 
                @endphp

                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <!-- Bungkus ikon, teks, dan badge agar berjejer rapi di kiri -->
                        <div class="dropdown-badge-wrapper">
                            <span>🚚 Supply Chain</span>
                            
                            <!-- Munculkan Badge di Induk Dropdown -->
                            @if($pendingPengadaan > 0)
                                <span class="notif-badge">
                                    {{ $pendingPengadaan }}
                                </span>
                            @endif
                        </div>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ route('pembelian.pengajuan') }}" class="{{ Route::is('pembelian.pengajuan*') ? 'active-menu' : '' }}" class="menu-item-with-badge">
                            <span>📝 Pengajuan Pengadaan</span>
                            @if($pendingPengadaan > 0)
                                <span class="notif-badge">
                                    {{ $pendingPengadaan }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('pembelian.stok') }}" class="{{ Route::is('pembelian.stok*') ? 'active-menu' : '' }}">🛒 Pengadaan & Stok</a>
                        <a href="{{ url('/distribusi') }}" class="{{ request()->is('distribusi*') ? 'active-menu' : '' }}">🚚 Distribusi</a>
                    </div>
                </div>

                @php 
                    // Owner/Op Manager ngelihat TOTAL semua tugas yang belum beres (Logistik + Barista)
                    $tugasTotal = \App\Models\Event::whereIn('status', ['menunggu_logistik', 'bahan_ready', 'diserahkan'])->count(); 
                @endphp
                
                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <div class="dropdown-badge-wrapper">
                            <span>🎪 Event Management</span>
                            @if($tugasTotal > 0)
                                <span class="notif-badge">
                                    {{ $tugasTotal }}
                                </span>
                            @endif
                        </div>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <!-- 1. Cuma buat mantau riwayat (Notif angkanya dipindah dari sini) -->
                        <a href="{{ route('event.index') }}" class="{{ request()->routeIs('event.index') ? 'active-menu' : '' }}">
                            📋 Daftar Event
                        </a>

                        <!-- 2. INI MENU BARUNYA BANG (Papan Tugas) -->
                        <a href="{{ route('event.tugas') }}" class="{{ request()->routeIs('event.tugas') ? 'active-menu' : '' }}" class="menu-item-with-badge">
                            <span>⚡ Papan Tugas Eksekusi</span>
                            @if($tugasTotal > 0)
                                <span class="notif-badge">
                                    {{ $tugasTotal }}
                                </span>
                            @endif
                        </a>

                        <!-- 3. Menu Paket Event (Tetap aman) -->
                        <a href="{{ route('paket-event.index') }}" class="{{ request()->is('paket-event*') ? 'active-menu' : '' }}">
                            📦 Menu Paket Event
                        </a>
                    </div>
                </div>

                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <span>📋 Reports</span>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ url('/laporan-penjualan') }}" class="{{ request()->is('laporan-penjualan*') ? 'active-menu' : '' }}">📋 Laporan Penjualan</a>
                        <a href="{{ url('/laporan-pembelian') }}" class="{{ request()->is('laporan-pembelian*') ? 'active-menu' : '' }}">📋 Laporan Pengadaan</a>
                        <a href="{{ url('/laporan-bahan-baku') }}" class="{{ request()->is('laporan-bahan-baku*') ? 'active-menu' : '' }}">📋 Laporan Bahan Baku</a>
                        <a href="{{ url('/laporan-distribusi') }}" class="{{ request()->is('laporan-distribusi*') ? 'active-menu' : '' }}">📋 Laporan Distribusi</a>
                        <a href="{{ route('event.laporan') }}" class="{{ Route::is('event.laporan*') ? 'active-menu' : '' }}">📋 Laporan Event</a>
                    </div>
                </div>

                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <span>👥 Users & Customers</span>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ route('pelanggan.index') }}" class="{{ request()->is('pelanggan*') ? 'active-menu' : '' }}">👥 Customer Data</a>
                        <a href="{{ url('/users') }}" class="{{ request()->is('users*') ? 'active-menu' : '' }}">👥 User Management</a>
                    </div>
                </div>

            @elseif(auth()->user()->role == 'operational_manager')
                
                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <span>☕ Product Catalog</span>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ url('/produk') }}" class="{{ request()->is('produk*') ? 'active-menu' : '' }}">☕ Produk</a>
                        <a href="{{ url('/resep-produk') }}" class="{{ request()->is('resep-produk*') ? 'active-menu' : '' }}">📖 Resep Produk</a>
                    </div>
                </div>

                @php $pendingStok = \App\Models\PengajuanStok::where('status', 'pending')->count(); @endphp
                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <span>📦 Outlet Inventory</span>
                        @if($pendingStok > 0)
                                <span class="notif-badge">{{ $pendingStok }}</span>
                            @endif
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ url('/pengajuan-stok') }}" class="{{ request()->is('pengajuan-stok*') ? 'active-menu' : '' }}" class="menu-item-with-badge">
                            <span>✅ Persetujuan Stok</span>
                            @if($pendingStok > 0)
                                <span class="notif-badge">{{ $pendingStok }}</span>
                            @endif
                        </a>
                        <a href="{{ url('/bahan-baku') }}" class="{{ request()->is('bahan-baku*') ? 'active-menu' : '' }}">📦 Bahan Baku</a>
                    </div>
                </div>
                
                @php 
                    // Hitung data sekali saja di luar untuk dipakai di induk dan anak
                    $pendingPengadaan = \App\Models\Pembelian::where(function($query) {
                        $query->whereNull('status_acc')
                            ->orWhere('status_acc', 'menunggu ACC');
                    })->count(); 
                @endphp

                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <!-- Bungkus ikon, teks, dan badge agar berjejer rapi di kiri -->
                        <div class="dropdown-badge-wrapper">
                            <span>🚚 Supply Chain</span>
                            
                            <!-- Munculkan Badge di Induk Dropdown -->
                            @if($pendingPengadaan > 0)
                                <span class="notif-badge">
                                    {{ $pendingPengadaan }}
                                </span>
                            @endif
                        </div>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ route('pembelian.pengajuan') }}" class="{{ Route::is('pembelian.pengajuan*') ? 'active-menu' : '' }}" class="menu-item-with-badge">
                            <span>📝 Pengajuan Pengadaan</span>
                            @if($pendingPengadaan > 0)
                                <span class="notif-badge">
                                    {{ $pendingPengadaan }}
                                </span>
                            @endif
                        </a>
                        <a href="{{ route('pembelian.stok') }}" class="{{ Route::is('pembelian.stok*') ? 'active-menu' : '' }}">🛒 Pengadaan & Stok</a>
                        <a href="{{ url('/distribusi') }}" class="{{ request()->is('distribusi*') ? 'active-menu' : '' }}">🚚 Distribusi</a>
                    </div>
                </div>

                @php 
                    // Hitung tugas event untuk Op Manager biar muncul notif merahnya
                    $tugasOpManager = \App\Models\Event::whereIn('status', ['menunggu_acc_pengadaan', 'menunggu_logistik', 'menunggu_barang_event', 'bahan_ready', 'diserahkan'])->count(); 
                @endphp

                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                        <div class="dropdown-badge-wrapper">
                            <span>🎪 Event Management</span>
                            @if($tugasOpManager > 0)
                                <span class="notif-badge">
                                    {{ $tugasOpManager }}
                                </span>
                            @endif
                        </div>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ route('event.index') }}" class="{{ request()->routeIs('event.index') ? 'active-menu' : '' }}">📋 Daftar Event</a>

                        <!-- 🔥 INI DIA TAMBAHAN PAPAN TUGAS BUAT OP MANAGER 🔥 -->
                        <a href="{{ route('event.tugas') }}" class="{{ request()->routeIs('event.tugas') ? 'active-menu' : '' }}" class="menu-item-with-badge">
                            <span>✅ Persetujuan Pengadaan Event</span>
                            @if($tugasOpManager > 0)
                                <span class="notif-badge">
                                    {{ $tugasOpManager }}
                                </span>
                            @endif
                        </a>

                        <a href="{{ route('paket-event.index') }}" class="{{ request()->is('paket-event*') ? 'active-menu' : '' }}">📦 Menu Paket Event</a>
                    </div>
                </div>
                
                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <span>📋 Reports</span>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ url('/laporan-penjualan') }}" class="{{ request()->is('laporan-penjualan*') ? 'active-menu' : '' }}">📋 Laporan Penjualan</a>
                        <a href="{{ url('/laporan-pembelian') }}" class="{{ request()->is('laporan-pembelian*') ? 'active-menu' : '' }}">📋 Laporan Pengadaan</a>
                        <a href="{{ url('/laporan-bahan-baku') }}" class="{{ request()->is('laporan-bahan-baku*') ? 'active-menu' : '' }}">📋 Laporan Bahan Baku</a>
                        <a href="{{ url('/laporan-distribusi') }}" class="{{ request()->is('laporan-distribusi*') ? 'active-menu' : '' }}">📋 Laporan Distribusi</a>
                        <a href="{{ route('event.laporan') }}" class="{{ Route::is('event.laporan*') ? 'active-menu' : '' }}">📋 Laporan Event</a>
                    </div>
                </div>

                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <span>👥 Users & Customers</span>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ url('/users') }}" class="{{ request()->is('users*') ? 'active-menu' : '' }}">👥 User Management</a>
                    </div>
                </div>

            @elseif(auth()->user()->role == 'logistik')
                
                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <span>📦 Outlet Inventory</span>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ url('/bahan-baku') }}" class="{{ request()->is('bahan-baku*') ? 'active-menu' : '' }}">📦 Bahan Baku</a>
                    </div>
                </div>

                @php
                    $adaSisaEvent = isset($data) && $data->contains(function ($item) {
                        return str_contains($item->keterangan, 'Sisa fisik aktual Event');
                    });
                @endphp
                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <span>🚚 Supply Chain</span>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ route('pembelian.pengajuan') }}" class="{{ Route::is('pembelian.pengajuan*') ? 'active-menu' : '' }}">📝 Pengajuan Pengadaan</a>
                        <a href="{{ route('pembelian.stok') }}" class="{{ Route::is('pembelian.stok*') ? 'active-menu' : '' }}">🛒 Pengadaan & Stok</a>
                        <a href="{{ url('/distribusi') }}" class="{{ request()->is('distribusi*') ? 'active-menu' : '' }}">🚚 Distribusi</a>
                    </div>
                </div>

                @php 
                    // Hitung semua tugas tanpa peduli outlet
                    $tugasLogistik = \App\Models\Event::whereIn('status', ['menunggu_logistik', 'bahan_ready'])->count(); 
                @endphp

                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <div class="dropdown-badge-wrapper">
                            <span>🎪 Event Management</span>
                            @if($tugasLogistik > 0)
                                <span class="notif-badge">
                                    {{ $tugasLogistik }}
                                </span>
                            @endif
                        </div>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <!-- Cuma buat mantau riwayat keseluruhan -->
                        <a href="{{ route('event.index') }}" class="{{ Route::is('event.index*') ? 'active-menu' : '' }}">📋 Daftar Event</a>
                        
                        <!-- Papan tugas eksekusi ngarah ke route event.tugas -->
                        <a href="{{ route('event.tugas') }}" class="{{ Route::is('event.tugas*') ? 'active-menu' : '' }}" class="menu-item-with-badge">
                            <span>📦 Pengadaan Event</span>
                            @if($tugasLogistik > 0)
                                <span class="notif-badge">
                                    {{ $tugasLogistik }}
                                </span>
                            @endif
                        </a>
                    </div>
                </div>

                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <span>📋 Reports</span>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ url('/laporan-bahan-baku') }}" class="{{ request()->is('laporan-bahan-baku*') ? 'active-menu' : '' }}">📋 Laporan Bahan Baku</a>
                        <a href="{{ url('/laporan-pembelian') }}" class="{{ request()->is('laporan-pembelian*') ? 'active-menu' : '' }}">📋 Laporan Pengadaan</a>
                        <a href="{{ url('/laporan-distribusi') }}" class="{{ request()->is('laporan-distribusi*') ? 'active-menu' : '' }}">📋 Laporan Distribusi</a>
                    </div>
                </div>

            @elseif(auth()->user()->role == 'kasir')
                
                <a href="{{ url('/penjualan') }}" class="{{ request()->is('penjualan*') ? 'active-menu' : '' }}">
                    💰 Sales
                </a>

                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <span>☕ Product Catalog</span>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ url('/produk') }}" class="{{ request()->is('produk*') ? 'active-menu' : '' }}">☕ Produk</a>
                    </div>
                </div>

                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <span>📋 Reports</span>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ url('/laporan-penjualan') }}" class="{{ request()->is('laporan-penjualan*') ? 'active-menu' : '' }}">📋 Laporan Penjualan</a>
                    </div>
                </div>

                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <span>👥 Users & Customers</span>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ route('pelanggan.index') }}" class="{{ request()->is('pelanggan*') ? 'active-menu' : '' }}">👥 Customer Data</a>
                    </div>
                </div>

            @elseif(auth()->user()->role == 'barista')
                
                {{-- 1. MENU HARIAN (Disembunyikan dari Barista Event) --}}
                @if(session('outlet_aktif') != 'event')
                    <a href="{{ url('/penjualan') }}" class="{{ request()->is('penjualan*') ? 'active-menu' : '' }}">
                        💰 Sales
                    </a>
                @endif

                {{-- 2. KATALOG PRODUK (Dilihat Semua Barista buat nyontek resep) --}}
                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <span>☕ Product Catalog</span>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <a href="{{ url('/produk') }}" class="{{ request()->is('produk*') ? 'active-menu' : '' }}">☕ Produk</a>
                        <a href="{{ url('/resep-produk') }}" class="{{ request()->is('resep-produk*') ? 'active-menu' : '' }}">📖 Resep Produk</a>
                    </div>
                </div>

                {{-- 3. INVENTORY & REPORT HARIAN (Disembunyikan dari Barista Event) --}}
                @if(session('outlet_aktif') != 'event')
                    <div class="dropdown-wrapper">
                        <button class="dropdown-btn" onclick="toggleDropdown(this)">
                            <span>📦 Outlet Inventory</span>
                            <span class="arrow">▼</span>
                        </button>
                        <div class="dropdown-container">
                            <a href="{{ url('/bahan-baku') }}" class="{{ request()->is('bahan-baku*') ? 'active-menu' : '' }}">📦 Bahan Baku</a>
                        </div>
                    </div>
                @endif

                {{-- 4. EVENT MANAGEMENT (Selalu Muncul agar Barista tahu jadwal event) --}}
                @php 
                    $tugasBarista = \App\Models\Event::where('status', 'diserahkan')->count(); 
                @endphp

                <div class="dropdown-wrapper">
                    <button class="dropdown-btn" onclick="toggleDropdown(this)">
                        <div class="dropdown-badge-wrapper">
                            <span>🎪 Event Management</span>
                            @if($tugasBarista > 0 && session('outlet_aktif') == 'event')
                                <span class="notif-badge">
                                    {{ $tugasBarista }}
                                </span>
                            @endif
                        </div>
                        <span class="arrow">▼</span>
                    </button>
                    <div class="dropdown-container">
                        <!-- Selalu Muncul: Daftar Event biar Barista bisa persiapan -->
                        <a href="{{ route('event.index') }}" class="{{ Route::is('event.index*') ? 'active-menu' : '' }}" style="display: block; padding: 12px 15px; color: #efe6d8; text-decoration: none; border-radius: 8px; margin-bottom: 5px;">
                            📋 Jadwal & Daftar Event
                        </a>

                        <!-- Tugas Event: Muncul pas lagi aktif di outlet event -->
                        @if(session('outlet_aktif') == 'event')
                            <a href="{{ route('event.tugas') }}" class="{{ Route::is('event.tugas*') ? 'active-menu' : '' }}" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 15px; color: #efe6d8; text-decoration: none; border-radius: 8px; margin-bottom: 5px;">
                                <div class="dropdown-badge-wrapper">
                                    <span>🎪 Tugas Event</span>
                                </div>
                                @if($tugasBarista > 0)
                                    <span class="notif-badge">
                                        {{ $tugasBarista }}
                                    </span>
                                @endif
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Lanjutan Menu Harian (Report) --}}
                @if(session('outlet_aktif') != 'event')
                    <div class="dropdown-wrapper">
                        <button class="dropdown-btn" onclick="toggleDropdown(this)">
                            <span>📋 Reports</span>
                            <span class="arrow">▼</span>
                        </button>
                        <div class="dropdown-container">
                            <a href="{{ url('/laporan-bahan-baku') }}" class="{{ request()->is('laporan-bahan-baku*') ? 'active-menu' : '' }}">📋 Laporan Bahan Baku</a>
                        </div>
                    </div>
                @endif

            @endif
        </div>
    </div>
    
    <div class="user-footer" style="flex-shrink: 0; width: 100%;">
        <hr class="sidebar-divider">

        <div class="user-info-box">
            <div>
                👤 <strong>{{ auth()->user()->name }}</strong>
            </div>

            <div>
                💼
                @switch(auth()->user()->role)
                    @case('owner')
                        Owner
                        @break
                    @case('operational_manager')
                        Operational Manager
                        @break
                    @case('logistik')
                        Logistik
                        @break
                    @case('kasir')
                        Kasir
                        @break
                    @case('barista')
                        Barista
                        @break
                    @default
                        {{ ucfirst(auth()->user()->role) }}
                @endswitch
            </div>

            @if(in_array(auth()->user()->role, ['kasir', 'barista']) && session('outlet_aktif'))
                <div>
                    🏪 Outlet:
                    {{ ucfirst(session('outlet_aktif')) }}
                </div>

                <a href="{{ route('outlet.ganti') }}" class="btn-ganti-outlet">
                    Ganti Outlet
                </a>
            @endif
        </div>

        <hr class="sidebar-divider">

        <form id="logout-form" method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="button"
                onclick="confirmLogout()"
                class="btn-logout">
                🚪 Logout
            </button>
        </form>
    </div>
</div>

<div class="content">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Fungsi interaktif buka-tutup dropdown menu sidebar
    function toggleDropdown(btn) {
        const container = btn.nextElementSibling;
        const arrow = btn.querySelector('.arrow');
        
        if (container.style.display === 'block') {
            container.style.display = 'none';
            btn.classList.remove('active-parent');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
        } else {
            container.style.display = 'block';
            btn.classList.add('active-parent');
            if (arrow) arrow.style.transform = 'rotate(180deg)';
        }
    }

    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
        document.querySelector('.overlay').classList.toggle('active');
    }

    function confirmLogout() {
        Swal.fire({
            title: 'Yakin logout?',
            text: 'Anda akan keluar dari sistem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#14532d',
            cancelButtonColor: '#7a2e2e',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('logout-form').submit();
            }
        });
    }

    // Aksi otomatis pas halaman kelar dimuat (Loading)
    document.addEventListener("DOMContentLoaded", function() {
        // 1. OTOMATIS BUKA GRUP DROPDOWN jika ada sub-menu di dalamnya yang sedang aktif dibuka
        document.querySelectorAll('.dropdown-container').forEach(container => {
            if (container.querySelector('.active-menu')) {
                container.style.display = 'block';
                const btn = container.previousElementSibling;
                if (btn) {
                    btn.classList.add('active-parent');
                    const arrow = btn.querySelector('.arrow');
                    if (arrow) arrow.style.transform = 'rotate(180deg)';
                }
            }
        });
        
        // 2. KUNCI POSISI SCROLL instan tepat pada menu yang sedang aktif dibuka (Biar gak mental ke atas)
        const activeMenu = document.querySelector('.active-menu');
        if (activeMenu) {
            const scrollContainer = document.querySelector('.menu-wrapper');
            if (scrollContainer) {
                scrollContainer.scrollTop = activeMenu.offsetTop - (scrollContainer.clientHeight / 2) + (activeMenu.clientHeight / 2);
            }
        }
    });    
</script>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

</body>
</html>