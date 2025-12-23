<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin')</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />
    <style>
        :root {
            --header-h: 56px;
            --footer-h: 24px;
        }

        body {
            overflow: hidden;
            background: #f6f8fb;
        }

        .app {
            display: grid;
            grid-template-columns: 260px 1fr;
            grid-template-rows: auto 1fr auto;
            grid-template-areas:
                "sidebar header"
                "sidebar main"
                "sidebar footer";
            height: 100vh;
            position: relative;
            contain: layout style;
        }

        .sidebar {
            background: #2c3e50;
            color: #fff;
            position: relative;
            top: 0;
            left: 0;
            height: 100vh;
            overflow: hidden;
            width: 260px;
            grid-area: sidebar;
            z-index: 1030;
            display: flex;
            flex-direction: column;
            transform: translateZ(0);
            backface-visibility: hidden;
            will-change: width;
            contain: layout style;
        }

        /* Premium Floating Collapse Button */
        .sidebar-collapse-btn {
            position: fixed;
            top: 16px;
            left: 242px;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1050;
            transition: left 0.2s ease, background 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        }

        .sidebar-collapse-btn:hover {
            background: #2c3e50;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .sidebar-collapse-btn:hover svg {
            stroke: #ffffff;
        }

        .sidebar-collapse-btn:active {
            transform: scale(0.92);
        }

        .sidebar-collapse-btn svg {
            width: 16px;
            height: 16px;
            stroke: #2c3e50;
            stroke-width: 2.5;
            fill: none;
            transition: transform 0.25s ease;
        }

        .collapsed .sidebar-collapse-btn {
            left: 54px;
        }

        .collapsed .sidebar-collapse-btn svg {
            transform: rotate(180deg);
        }

        /* Hide on mobile */
        @media (max-width: 991.98px) {
            .sidebar-collapse-btn {
                display: none !important;
            }
        }

        .sidebar a {
            color: #cfe0ff;
            text-decoration: none;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, .08);
        }

        .app-header {
            grid-area: header;
            z-index: 1030;
            position: relative;
        }

        .app-footer {
            grid-area: footer;
            z-index: 1;
            position: relative;
        }

        .content {
            overflow: auto;
            background: #f6f8fb;
            height: auto;
            padding-bottom: 1rem;
            grid-area: main;
            z-index: 10;
            position: relative;
            contain: layout style;
            transform: translateZ(0);
            will-change: scroll-position;
        }

        /* Sidebar header with toggle button */
        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            padding: 0.5rem 0;
            margin-bottom: 1rem;
            padding-left: 0.5rem;
        }

        .brand {
            font-weight: 600;
            letter-spacing: .3px;
            display: flex;
            align-items: center;
        }

        .sidebar-toggle-inside {
            background: rgba(255, 255, 255, .1);
            border: none;
            color: #fff;
            padding: 0.375rem 0.5rem;
            border-radius: 0.375rem;
            cursor: pointer;
            flex-shrink: 0;
        }

        .sidebar-toggle-inside:hover {
            background: rgba(255, 255, 255, .2);
        }

        .sidebar-toggle-inside i {
            transition: transform 0.15s ease-out;
        }

        .sidebar-header {
            flex-shrink: 0; /* Don't shrink header */
        }

        .sidebar-nav-container {
            flex: 1; /* Take remaining space */
            overflow-y: auto; /* Make scrollable */
            overflow-x: hidden;
            padding-bottom: 1rem;
        }

        .sidebar-nav-container::-webkit-scrollbar {
            width: 0px; /* Hide scrollbar */
            background: transparent;
        }

        .sidebar-nav-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav-container::-webkit-scrollbar-thumb {
            background: transparent;
        }

        /* For Firefox */
        .sidebar-nav-container {
            scrollbar-width: none; /* Hide scrollbar */
        }

        .profile {
            flex-shrink: 0; /* Don't shrink profile */
            padding: 0.5rem 0.75rem;
            border-top: 1px solid rgba(255, 255, 255, .1);
            height: 50px;
            z-index: 1000; /* Higher z-index than menu items */
            background: #2c3e50; /* Ensure background covers menu items */
            display: flex;
            align-items: center;
        }

        .profile .dropdown-menu {
            position: absolute !important;
            inset: auto auto 50px 0 !important;
            transition: all 0.3s ease;
            z-index: 1001; /* Even higher for dropdown */
            background: #1e293b !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
        }

        .profile .dropdown-menu .dropdown-item {
            color: #e2e8f0 !important;
        }

        .profile .dropdown-menu .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            color: #fff !important;
        }

        .profile .dropdown-menu .dropdown-divider {
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        /* Label - no transitions */
        .label {
            opacity: 1;
            display: inline-block;
            white-space: nowrap;
        }

        /* Collapsible behavior */
        .toggle-btn {
            position: fixed;
            top: 14px;
            left: 14px;
            z-index: 1030;
        }

        /* MOBILE FIXES - CRITICAL */
        @media (max-width: 991.98px) {

            /* Prevent body scroll on mobile to fix sidebar issue */
            body {
                overflow: hidden !important;
                position: fixed !important;
                width: 100% !important;
                height: 100vh !important;
                height: 100dvh !important;
            }

            .app {
                grid-template-columns: 1fr;
                grid-template-areas:
                    "header"
                    "main"
                    "footer";
                height: 100vh !important;
                height: 100dvh !important;
                overflow: hidden;
            }

            .sidebar {
                position: fixed !important;
                width: 260px;
                z-index: 1029;
                left: 0;
                top: 0 !important;
                bottom: 0;
                transform: translateX(-100%);
                height: 100vh !important;
                height: 100dvh !important;
                overflow: hidden;
                display: flex;
                flex-direction: column;
                transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                will-change: transform;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .toggle-btn {
                display: none !important;
            }

            .content {
                grid-column: 1 / -1;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                height: 100vh !important;
                height: 100dvh !important;
                width: 100%;
                padding: 1rem 1rem 1rem 1rem !important;
                -webkit-overflow-scrolling: touch;
            }

            .sidebar-backdrop {
                content: "";
                position: fixed !important;
                inset: 0 !important;
                background: rgba(0, 0, 0, .35);
                z-index: 1028;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.2s ease-out, visibility 0.2s ease-out;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                height: 100vh !important;
                height: 100dvh !important;
            }

            .sidebar-backdrop.show {
                opacity: 1;
                visibility: visible;
            }
        }

        /* Desktop collapsed state */
        @media (min-width: 992px) {
            .collapsed .app {
                grid-template-columns: 72px 1fr;
            }

            .collapsed .sidebar {
                width: 72px;
            }

            .collapsed .sidebar .sidebar-nav-container {
                overflow: hidden; /* Hide scrollbar when collapsed */
            }

            .collapsed .sidebar .label {
                opacity: 0;
                width: 0;
                overflow: hidden;
            }

            .collapsed .sidebar .nav-link i {
                margin-right: 0 !important;
            }

            .collapsed .sidebar .brand .label {
                opacity: 0;
            }

            .collapsed .sidebar .brand i {
                margin-right: 0 !important;
            }

            .collapsed .sidebar [data-bs-toggle="collapse"]::after {
                content: none !important;
            }

            /* Rotate toggle button icon when collapsed */
            .collapsed .sidebar-toggle-inside i {
                transform: rotate(180deg);
            }

            /* Center sidebar header content */
            .collapsed .sidebar-header {
                justify-content: center;
                padding-left: 0;
            }

            .collapsed .sidebar-header .brand {
                display: flex;
                justify-content: center;
            }
            
            .collapsed .sidebar .sidebar-logo {
                height: 24px !important;
            }

            /* Fix Invoice Items Table Alignment - PC & Mobile */
            #itemsTable {
                table-layout: fixed;
                width: 100%;
                min-width: 1800px;
                /* Force minimum width */
            }

            #itemsTable thead th {
                vertical-align: middle;
                text-align: center;
                white-space: nowrap;
                /* Prevent text wrapping */
                padding: 12px 8px;
                font-weight: 600;
                font-size: 0.875rem;
                position: sticky;
                top: 0;
                background-color: #f8f9fa;
                z-index: 10;
            }

            #itemsTable tbody td {
                vertical-align: middle;
                padding: 8px 5px;
            }

            /* Column widths */
            #itemsTable th:nth-child(1),
            #itemsTable td:nth-child(1) {
                width: 80px;
            }

            #itemsTable th:nth-child(2),
            #itemsTable td:nth-child(2) {
                width: 150px;
            }

            #itemsTable th:nth-child(3),
            #itemsTable td:nth-child(3) {
                width: 120px;
            }

            #itemsTable th:nth-child(4),
            #itemsTable td:nth-child(4) {
                width: 90px;
            }

            #itemsTable th:nth-child(5),
            #itemsTable td:nth-child(5) {
                width: 80px;
            }

            #itemsTable th:nth-child(6),
            #itemsTable td:nth-child(6) {
                width: 110px;
            }

            #itemsTable th:nth-child(7),
            #itemsTable td:nth-child(7) {
                width: 70px;
            }

            #itemsTable th:nth-child(8),
            #itemsTable td:nth-child(8) {
                width: 70px;
            }

            #itemsTable th:nth-child(9),
            #itemsTable td:nth-child(9) {
                width: 70px;
            }

            #itemsTable th:nth-child(10),
            #itemsTable td:nth-child(10) {
                width: 80px;
            }

            #itemsTable th:nth-child(11),
            #itemsTable td:nth-child(11) {
                width: 90px;
            }

            #itemsTable th:nth-child(12),
            #itemsTable td:nth-child(12) {
                width: 80px;
            }

            #itemsTable th:nth-child(13),
            #itemsTable td:nth-child(13) {
                width: 80px;
            }

            #itemsTable th:nth-child(14),
            #itemsTable td:nth-child(14) {
                width: 100px;
            }

            #itemsTable th:nth-child(15),
            #itemsTable td:nth-child(15) {
                width: 60px;
                text-align: center;
            }

            /* Make inputs fit */
            #itemsTable input.form-control,
            #itemsTable select.form-select {
                width: 100%;
                font-size: 0.875rem;
                padding: 0.375rem 0.5rem;
            }

            /* Select2 fix */
            #itemsTable .select2-container {
                width: 100% !important;
            }

            #itemsTable .select2-selection {
                min-height: 36px !important;
            }

            .table-responsive {
                overflow-x: auto !important;
                overflow-y: visible;
                -webkit-overflow-scrolling: touch;
                display: block;
                width: 100%;
            }

            /* Mobile specific adjustments */
            @media (max-width: 768px) {
                #itemsTable {
                    min-width: 1800px !important;
                    /* Keep table wide */
                    font-size: 0.75rem;
                }

                #itemsTable thead th {
                    font-size: 0.7rem !important;
                    padding: 8px 5px !important;
                    white-space: nowrap !important;
                    /* Force single line - NO WRAPPING */
                    line-height: 1.2 !important;
                    height: auto !important;
                }

                #itemsTable tbody td {
                    padding: 5px 3px !important;
                    white-space: nowrap !important;
                }

                #itemsTable input.form-control,
                #itemsTable select.form-select {
                    font-size: 0.7rem !important;
                    padding: 0.25rem 0.3rem !important;
                    height: 30px !important;
                    min-height: 30px !important;
                }

                #itemsTable .select2-container .select2-selection {
                    min-height: 30px !important;
                    height: 30px !important;
                    font-size: 0.7rem !important;
                }

                #itemsTable .select2-container .select2-selection__rendered {
                    line-height: 28px !important;
                    padding-left: 5px !important;
                }

                #itemsTable .select2-container .select2-selection__arrow {
                    height: 28px !important;
                }

                /* Compact column widths for mobile */
                #itemsTable th:nth-child(1),
                #itemsTable td:nth-child(1) {
                    width: 70px !important;
                }

                #itemsTable th:nth-child(2),
                #itemsTable td:nth-child(2) {
                    width: 130px !important;
                }

                #itemsTable th:nth-child(3),
                #itemsTable td:nth-child(3) {
                    width: 110px !important;
                }

                #itemsTable th:nth-child(4),
                #itemsTable td:nth-child(4) {
                    width: 80px !important;
                }

                #itemsTable th:nth-child(5),
                #itemsTable td:nth-child(5) {
                    width: 70px !important;
                }

                #itemsTable th:nth-child(6),
                #itemsTable td:nth-child(6) {
                    width: 100px !important;
                }

                #itemsTable th:nth-child(7),
                #itemsTable td:nth-child(7) {
                    width: 60px !important;
                }

                #itemsTable th:nth-child(8),
                #itemsTable td:nth-child(8) {
                    width: 60px !important;
                }

                #itemsTable th:nth-child(9),
                #itemsTable td:nth-child(9) {
                    width: 60px !important;
                }

                #itemsTable th:nth-child(10),
                #itemsTable td:nth-child(10) {
                    width: 70px !important;
                }

                #itemsTable th:nth-child(11),
                #itemsTable td:nth-child(11) {
                    width: 80px !important;
                }

                #itemsTable th:nth-child(12),
                #itemsTable td:nth-child(12) {
                    width: 70px !important;
                }

                #itemsTable th:nth-child(13),
                #itemsTable td:nth-child(13) {
                    width: 70px !important;
                }

                #itemsTable th:nth-child(14),
                #itemsTable td:nth-child(14) {
                    width: 90px !important;
                }

                #itemsTable th:nth-child(15),
                #itemsTable td:nth-child(15) {
                    width: 50px !important;
                }

                /* Remove extra spacing */
                .card-body {
                    padding: 0.75rem !important;
                }
            }

            /* Extra small devices */
            @media (max-width: 576px) {
                #itemsTable {
                    min-width: 1600px !important;
                }

                #itemsTable thead th {
                    font-size: 0.65rem !important;
                    padding: 6px 4px !important;
                }

                #itemsTable input.form-control,
                #itemsTable select.form-select {
                    font-size: 0.65rem !important;
                    padding: 0.2rem 0.25rem !important;
                    height: 28px !important;
                }
            }

            /* Profile button in collapsed state */
            .collapsed .profile .btn {
                justify-content: center;
                padding: 0.5rem;
            }

            .collapsed .profile .btn img {
                margin: 0 !important;
            }

            .collapsed .profile .btn .flex-grow-1,
            .collapsed .profile .btn .bi-chevron-up {
                display: none !important;
            }

            /* Profile dropdown positioning in collapsed state */
            .collapsed .profile .dropdown-menu {
                left: 72px !important;
                bottom: 0 !important;
                min-width: 200px !important;
                inset: auto auto 0 72px !important;
            }

            /* Collapse button icons */
            .collapsed .sidebar .collapse {
                display: none !important;
            }

            .collapsed .sidebar [data-bs-toggle="collapse"] {
                justify-content: center !important;
                padding: 0.5rem !important;
            }

            .collapsed .sidebar [data-bs-toggle="collapse"] i {
                margin: 0 !important;
            }

            /* Remove Bootstrap focus glow globally */
            *:focus,
            input:focus,
            select:focus,
            textarea:focus,
            button:focus,
            .form-control:focus,
            .form-select:focus,
            .btn:focus {
                box-shadow: none !important;
                outline: none !important;
            }

            /* Keep border color normal */
            .form-control:focus,
            .form-select:focus {
                border-color: #dee2e6 !important;
            }

            /* Select2 fix */
            .select2-container--bootstrap-5 .select2-selection:focus,
            .select2-container--bootstrap-5.select2-container--focus .select2-selection,
            .select2-container--bootstrap-5.select2-container--open .select2-selection,
            .select2-selection:focus,
            .select2-selection--single:focus {
                box-shadow: none !important;
                outline: none !important;
                border-color: #dee2e6 !important;
            }

            /* Center align nav items when collapsed */
            .collapsed .sidebar .nav-link {
                justify-content: center;
                padding: 0.5rem !important;
                pointer-events: none;
                cursor: not-allowed;
                opacity: 0.6;
            }

            /* Prevent clicking on menu buttons when collapsed */
            .collapsed .sidebar [data-bs-toggle="collapse"] {
                pointer-events: none;
                cursor: not-allowed;
                opacity: 0.6;
            }

            /* Only allow toggle button to work */
            .collapsed .sidebar-toggle-inside {
                pointer-events: auto;
                cursor: pointer;
                opacity: 1;
            }
        }

        /* No transitions for instant response */
        .sidebar i {
            /* No transition */
        }

        /* Button styling */
        [data-bs-toggle="collapse"] {
            border: none;
        }

        [data-bs-toggle="collapse"]:hover {
            background: rgba(255, 255, 255, .08) !important;
        }

        .sidebar [data-bs-toggle="collapse"]::after {
            content: "+";
            margin-left: auto;
            color: #cfe0ff;
            font-weight: 700;
            line-height: 1;
        }

        .sidebar [data-bs-toggle="collapse"][aria-expanded="true"]::after {
            content: "−";
        }

        .sidebar [data-bs-toggle="collapse"][aria-expanded="true"]::after {
            transform: scale(1.05);
            opacity: 1;
        }

        .sidebar [data-bs-toggle="collapse"][aria-expanded="false"]::after {
            transform: scale(1);
            opacity: 0.9;
        }

        /* Global Scroll to Top Button - Fixed positioning */
        #scrollToTop {
            position: fixed !important;
            bottom: 30px !important;
            right: 30px !important;
            z-index: 10000 !important;
            border-radius: 50% !important;
            width: 50px !important;
            height: 50px !important;
            background: #0d6efd !important;
            color: #fff !important;
            border: none !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transition: all 0.3s ease !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
            padding: 0 !important;
            margin: 0 !important;
            line-height: 1 !important;
        }

        #scrollToTop:hover {
            transform: translateY(-3px) !important;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2) !important;
            background: #0b5ed7 !important;
        }

        #scrollToTop:active {
            transform: translateY(-1px) !important;
        }

        #scrollToTop i {
            font-size: 22px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }

        /* Show state for scroll to top button */
        #scrollToTop.show {
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto !important;
        }

        #scrollToTop.hide {
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
        }

        .app-footer .py-3 {
            padding-top: 12px !important;
            padding-bottom: 12px !important;
        }

         .app-header .py-3 {
            padding-top: 11px !important;
            padding-bottom: 11px !important;
        }

        /* Instant sidebar collapse - no animation for smooth feel */
        /* Smoother sidebar dropdown menus */
        .sidebar .collapse,
        .sidebar .collapsing {
            transition: height 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        
        .sidebar .collapsing {
            overflow: hidden;
        }

        /* Smooth sidebar collapse/expand with GPU acceleration */
        .app {
            transition: grid-template-columns 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        
        .sidebar {
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            will-change: width;
        }
        
        .sidebar .label {
            transition: opacity 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        
        .sidebar .nav-link,
        .sidebar .btn {
            transition: padding 0.3s cubic-bezier(0.4, 0, 0.2, 1), 
                        justify-content 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        
        .sidebar-toggle-inside i {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        
        /* GPU accelerated collapse button - smooth slide */
        .sidebar-collapse-btn {
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1), 
                        transform 0.15s ease-out,
                        background 0.15s ease-out !important;
            will-change: left;
        }
        
        .sidebar-collapse-btn svg {
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }
        
        /* Smooth content area adjustment */
        .content {
            transition: none !important;
            contain: layout paint;
        }
        
        /* PEAK OPTIMIZATION: Freeze content during sidebar animation */
        body.sidebar-animating .content {
            pointer-events: none !important;
            will-change: auto !important;
        }
        
        body.sidebar-animating .content * {
            animation: none !important;
            transition: none !important;
            will-change: auto !important;
        }
        
        /* Force GPU layer for content */
        body.sidebar-animating .content {
            transform: translateZ(0);
            backface-visibility: hidden;
        }
        
        /* Profile section smooth transition */
        .sidebar .profile .btn {
            transition: justify-content 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                        padding 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        /* ============================================
           GLOBAL MODAL BACKDROP FIX - CRITICAL
           This ensures ALL modal backdrops cover ENTIRE screen
           Including Bootstrap modals AND custom modals
           ============================================ */
        
        /* Force Bootstrap modal and backdrop to be fixed to viewport */
        .modal,
        .modal-backdrop {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            min-width: 100vw !important;
            min-height: 100vh !important;
            max-width: none !important;
            max-height: none !important;
        }

        .modal-backdrop {
            z-index: 1040 !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
        }

        .modal-backdrop.show {
            opacity: 1 !important;
        }

        .modal {
            z-index: 1050 !important;
            overflow-x: hidden !important;
            overflow-y: auto !important;
            display: none;
        }

        .modal.show {
            display: block !important;
        }

        /* ============================================
           CUSTOM MODAL BACKDROP FIX
           For pending-orders-backdrop, alert-modal-backdrop, etc.
           ============================================ */
        .pending-orders-backdrop,
        .alert-modal-backdrop,
        .choose-items-backdrop,
        [class*="-backdrop"]:not(#sidebarBackdrop) {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            width: 100vw !important;
            height: 100vh !important;
            min-width: 100vw !important;
            min-height: 100vh !important;
            z-index: 1040 !important;
            background-color: rgba(0, 0, 0, 0.5) !important;
        }

        .pending-orders-backdrop.show,
        .alert-modal-backdrop.show,
        .choose-items-backdrop.show,
        [class*="-backdrop"].show:not(#sidebarBackdrop) {
            display: block !important;
            opacity: 1 !important;
        }

        /* ============================================
           CUSTOM MODALS - CENTER IN CONTENT AREA
           (Excluding global delete modals which stay screen-centered)
           ============================================ */
        
        /* Custom modals centered in content area (sidebar width = 260px) */
        .pending-orders-modal,
        .alert-modal,
        .choose-items-modal {
            position: fixed !important;
            z-index: 1050 !important;
            top: 50% !important;
            left: calc(260px + (100vw - 260px) / 2) !important;
            transform: translate(-50%, -50%) !important;
        }

        /* When sidebar is collapsed (72px width) */
        .collapsed .pending-orders-modal,
        .collapsed .alert-modal,
        .collapsed .choose-items-modal {
            left: calc(72px + (100vw - 72px) / 2) !important;
        }

        /* Mobile - full screen center (no sidebar) */
        @media (max-width: 991.98px) {
            .pending-orders-modal,
            .alert-modal,
            .choose-items-modal {
                left: 50% !important;
            }
        }

        /* Global Delete Modal - stays centered on full screen */
        #globalMultipleDeleteModal,
        #globalDeleteModal,
        [id*="DeleteModal"],
        [id*="deleteModal"] {
            position: fixed !important;
            z-index: 1055 !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
        }

        /* Ensure modal dialog is properly centered */
        .modal-dialog {
            margin: 1.75rem auto !important;
            max-height: calc(100vh - 3.5rem) !important;
            position: relative !important;
        }

        .modal-content {
            max-height: calc(100vh - 3.5rem) !important;
            overflow: hidden !important;
        }

        .modal-body {
            overflow-y: auto !important;
            max-height: calc(100vh - 200px) !important;
        }

        /* When ANY modal is open, LOCK everything */
        body.modal-open,
        body.custom-modal-open {
            overflow: hidden !important;
            padding-right: 0 !important;
            height: 100vh !important;
            position: fixed !important;
            width: 100% !important;
        }

        body.modal-open .app,
        body.custom-modal-open .app {
            overflow: hidden !important;
            height: 100vh !important;
        }

        body.modal-open .content,
        body.custom-modal-open .content {
            overflow: hidden !important;
        }

        /* Prevent any parent from clipping the modal */
        .app, .content, .sidebar, main {
            contain: layout style !important;
        }

        /* When modal open, ensure nothing clips it */
        body.modal-open .app,
        body.modal-open .content,
        body.modal-open main {
            overflow: visible !important;
            contain: none !important;
        }
    </style>
    @stack('styles')
    @vite(['resources/js/app.js'])
    @csrf
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <div id="sidebarBackdrop" class="sidebar-backdrop d-lg-none"></div>
    
    <!-- Premium Floating Collapse Button - Outside sidebar for proper layering -->
    <button class="sidebar-collapse-btn d-none d-lg-flex" id="sidebarCollapseBtn" title="Toggle Sidebar">
        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <polyline points="15 18 9 12 15 6"></polyline>
        </svg>
    </button>
    
    <div class="app">
        @include('layouts.header')
        <aside class="sidebar p-3 position-relative">
            <div class="sidebar-header">
                <div class="brand d-flex align-items-center">
                    <img src="{{ asset('images/m-logo-01.svg') }}" alt="Medi BillSuite" style="height: 32px;" class="sidebar-logo">
                    <span class="label ms-2">Medi-BillSuite</span>
                </div>
            </div>

            <div class="sidebar-nav-container">
                @include('layouts.partials.sidebar-nav')
            </div>

            <div class="profile">
                <div class="dropup w-100">
                    <button class="btn w-100 d-flex align-items-center text-white" data-bs-toggle="dropdown"
                        style="background:transparent;border:none;padding:0.25rem 0;height:100%;">
                        <img src="{{ auth()->user()->profile_picture ? (str_starts_with(auth()->user()->profile_picture, 'storage/') ? asset(auth()->user()->profile_picture) : asset('storage/' . auth()->user()->profile_picture)) : 'https://i.pravatar.cc/32' }}"
                            class="rounded-circle me-2" width="28" height="28" alt="profile" onerror="this.src='https://i.pravatar.cc/32'">
                        <span class="flex-grow-1 text-truncate label text-start small">{{ auth()->user()->full_name }}</span>
                        <i class="bi bi-chevron-up ms-auto small"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="{{ route('profile.settings') }}"><i
                                    class="bi bi-gear me-2"></i>Settings</a></li>


                        <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="px-3 py-1">
                                @csrf
                                <button class="btn btn-outline-light w-100"><i
                                        class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>
        <main class="content p-3">
            @yield('content')
        </main>
        @include('layouts.footer')
    </div>

    <!-- Modals Section - Rendered at body level to avoid z-index conflicts -->
    @yield('modals')

    <!-- Global Scroll to Top Button -->
    <button id="scrollToTop" type="button" title="Scroll to top" onclick="scrollToTopNow()">
        <i class="bi bi-arrow-up"></i>
    </button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Initialize Select2 globally for all select elements
        $(document).ready(function() {
            // Initialize Select2 on all select elements
            $('select').not('.no-select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                placeholder: 'Select an option',
                allowClear: true
            });
            
            // Re-initialize Select2 when new content is added dynamically
            $(document).on('DOMNodeInserted', function(e) {
                if ($(e.target).is('select') || $(e.target).find('select').length) {
                    $(e.target).find('select').not('.no-select2, .select2-hidden-accessible').select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        placeholder: 'Select an option',
                        allowClear: true
                    });
                }
            });
        });
        
        // Global function for smooth scroll to top
        function scrollToTopNow() {
            const contentDiv = document.querySelector('.content');
            if (contentDiv) {
                contentDiv.scrollTo({ top: 0, behavior: 'smooth' });
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // GLOBAL FIX: Move ALL modals and backdrops to body level (Bootstrap + Custom)
        (function() {
            // Selectors for all types of modals and backdrops
            const modalSelectors = '.modal, .pending-orders-modal, .alert-modal, .choose-items-modal, [id$="Modal"]';
            const backdropSelectors = '.modal-backdrop, .pending-orders-backdrop, .alert-modal-backdrop, .choose-items-backdrop, [id$="Backdrop"]';

            function moveModalsToBody() {
                // Move all modals to body
                document.querySelectorAll(modalSelectors).forEach(function(modal) {
                    if (modal.parentElement && modal.parentElement !== document.body) {
                        document.body.appendChild(modal);
                    }
                });
                // Move all backdrops to body
                document.querySelectorAll(backdropSelectors).forEach(function(backdrop) {
                    if (backdrop.parentElement && backdrop.parentElement !== document.body) {
                        document.body.appendChild(backdrop);
                    }
                });
            }

            // Run on DOMContentLoaded
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', moveModalsToBody);
            } else {
                moveModalsToBody();
            }

            // Also run after a short delay to catch dynamically loaded content
            setTimeout(moveModalsToBody, 500);
            setTimeout(moveModalsToBody, 1000);
            setTimeout(moveModalsToBody, 2000);

            // Watch for new modals being added anywhere in the DOM
            const observer = new MutationObserver(function(mutations) {
                let shouldMove = false;
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) {
                            if (node.classList && (node.classList.contains('modal') || node.classList.contains('modal-backdrop'))) {
                                shouldMove = true;
                            }
                            // Also check children
                            if (node.querySelectorAll) {
                                if (node.querySelectorAll('.modal, .modal-backdrop').length > 0) {
                                    shouldMove = true;
                                }
                            }
                        }
                    });
                });
                if (shouldMove) {
                    setTimeout(moveModalsToBody, 0);
                }
            });

            observer.observe(document.documentElement, { childList: true, subtree: true });

            // Also hook into Bootstrap modal events
            document.addEventListener('show.bs.modal', function(e) {
                setTimeout(moveModalsToBody, 0);
            });
        })();

        // FIX: Move modals to body level when they open for proper backdrop coverage
        document.addEventListener('DOMContentLoaded', function() {
            // Move all modals to body level on page load
            document.querySelectorAll('.modal').forEach(function(modal) {
                if (modal.parentElement !== document.body) {
                    document.body.appendChild(modal);
                }
            });

            // Also handle dynamically created modals
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1 && node.classList && node.classList.contains('modal')) {
                            if (node.parentElement !== document.body) {
                                document.body.appendChild(node);
                            }
                        }
                    });
                });
            });

            observer.observe(document.body, { childList: true, subtree: true });
        });

        // Global scroll to top button handler - OPTIMIZED with throttle
        document.addEventListener('DOMContentLoaded', function() {
            const scrollBtn = document.getElementById('scrollToTop');
            const contentDiv = document.querySelector('.content');
            
            // Throttle function for scroll performance
            function throttle(fn, wait) {
                let lastTime = 0;
                return function(...args) {
                    const now = Date.now();
                    if (now - lastTime >= wait) {
                        lastTime = now;
                        fn.apply(this, args);
                    }
                };
            }
            
            function handleScroll(y) {
                if (y > 200) {
                    scrollBtn.classList.add('show');
                    scrollBtn.classList.remove('hide');
                } else {
                    scrollBtn.classList.add('hide');
                    scrollBtn.classList.remove('show');
                }
            }
            
            if (scrollBtn && contentDiv) {
                contentDiv.addEventListener('scroll', throttle(function() {
                    handleScroll(contentDiv.scrollTop);
                }, 100), { passive: true });
            }
            
            if (scrollBtn) {
                window.addEventListener('scroll', throttle(function() {
                    handleScroll(window.scrollY || document.documentElement.scrollTop);
                }, 100), { passive: true });
            }
        });

        (function () {
            const btn = document.getElementById('sidebarToggle');
            const sidebar = document.querySelector('.sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            const desktopBtn = document.getElementById('desktopSidebarToggle');
            const headerBtn = document.getElementById('headerSidebarToggle');
            const collapseBtn = document.getElementById('sidebarCollapseBtn');

            // --- MOBILE TOGGLE ---
            function toggleSidebar() {
                sidebar.classList.toggle('show');
                backdrop.classList.toggle('show');
            }
            
            // PEAK OPTIMIZATION: Freeze content during sidebar animation
            function toggleSidebarWithFreeze() {
                document.body.classList.add('sidebar-animating');
                document.body.classList.toggle('collapsed');
                // Remove animating class after transition completes
                setTimeout(() => {
                    document.body.classList.remove('sidebar-animating');
                }, 350);
            }
            
            if (btn && backdrop) {
                btn.addEventListener('click', toggleSidebar);
                backdrop.addEventListener('click', toggleSidebar);
            }
            if (headerBtn) {
                headerBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    if (window.innerWidth >= 992) {
                        // Desktop: collapse/expand sidebar with freeze
                        toggleSidebarWithFreeze();
                    } else {
                        // Mobile: slide sidebar
                        toggleSidebar();
                    }
                });
            }

            // --- FLOATING COLLAPSE BUTTON ---
            if (collapseBtn) {
                collapseBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    toggleSidebarWithFreeze();
                });
            }

            // --- DESKTOP COLLAPSE ---
            if (desktopBtn) {
                desktopBtn.addEventListener('click', e => {
                    e.preventDefault();
                    toggleSidebarWithFreeze();
                });
            }
            
            // Sidebar always starts OPEN - no localStorage restore

            // --- SET ACTIVE MENU ON PAGE LOAD ---
            (function () {
                const allCollapseElements = document.querySelectorAll('.sidebar .collapse');
                const sidebarTopMenuKey = 'sidebar:topMenuOpen';
                const currentUrlForMenu = window.location.href.split('?')[0].split('#')[0];
                const allSidebarLinks = document.querySelectorAll('.sidebar a[href]');
                let activeTopMenuId = null;
                let activeChildCollapseIds = [];

                // Find the active link and its parent collapses
                allSidebarLinks.forEach(link => {
                    if (link.href === currentUrlForMenu) {
                        let parent = link.closest('.collapse');
                        while (parent) {
                            activeChildCollapseIds.push(parent.id);
                            const isTopLevel = parent.id && parent.id.startsWith('menu') && !parent.closest('.collapse:not(#' + parent.id + ')');
                            if (isTopLevel) {
                                activeTopMenuId = parent.id;
                            }
                            parent = parent.parentElement.closest('.collapse');
                        }
                    }
                });

                const keysToRemove = [];
                for (let i = 0; i < localStorage.length; i++) {
                    const key = localStorage.key(i);
                    if (key && (key.startsWith('collapse:') || key === sidebarTopMenuKey)) {
                        keysToRemove.push(key);
                    }
                }
                keysToRemove.forEach(key => localStorage.removeItem(key));

                if (activeTopMenuId) {
                    localStorage.setItem(sidebarTopMenuKey, activeTopMenuId);
                    activeChildCollapseIds.forEach(id => {
                        localStorage.setItem('collapse:' + id, 'true');
                    });
                }
            })();

            // --- COLLAPSE HANDLING ---
            const collapseEls = document.querySelectorAll('.sidebar .collapse');
            const topMenuKey = 'sidebar:topMenuOpen';
            const savedTopMenu = localStorage.getItem(topMenuKey);

            collapseEls.forEach(collapseEl => {
                const collapse = new bootstrap.Collapse(collapseEl, { toggle: false });
                const trigger = document.querySelector('[data-bs-target="#' + collapseEl.id + '"]');

                const isTopLevel = collapseEl.id && collapseEl.id.startsWith('menu') && !collapseEl.closest('.collapse:not(#' + collapseEl.id + ')');

                // Restore saved open state
                if (isTopLevel) {
                    if (savedTopMenu && savedTopMenu === collapseEl.id) {
                        collapse.show();
                        localStorage.setItem('collapse:' + collapseEl.id, 'true');
                    } else {
                        collapse.hide();
                        localStorage.setItem('collapse:' + collapseEl.id, 'false');
                    }
                } else {
                    const isOpen = localStorage.getItem('collapse:' + collapseEl.id) === 'true';
                    if (isOpen) { collapse.show(); }
                }

                if (trigger) {
                    const isShown = collapseEl.classList.contains('show');
                    trigger.setAttribute('aria-expanded', isShown ? 'true' : 'false');
                    collapseEl.addEventListener('shown.bs.collapse', () => {
                        trigger.setAttribute('aria-expanded', 'true');
                    });
                    collapseEl.addEventListener('hidden.bs.collapse', () => {
                        trigger.setAttribute('aria-expanded', 'false');
                    });
                }
                if (trigger) {
                    trigger.addEventListener('click', e => {
                        e.preventDefault();
                        e.stopPropagation();
                        if (document.body.classList.contains('collapsed')) return false;

                        if (isTopLevel) {
                            // Close all other top-level menus
                            collapseEls.forEach(other => {
                                if (other !== collapseEl && other.id.startsWith('menu') && !other.closest('#' + collapseEl.id + '>.collapse')) {
                                    const inst = bootstrap.Collapse.getInstance(other);
                                    inst && inst.hide();
                                    localStorage.setItem('collapse:' + other.id, 'false');
                                }
                            });
                            // Remember this as the active top-level menu
                            localStorage.setItem(topMenuKey, collapseEl.id);
                        }

                        // Toggle current
                        const instance = bootstrap.Collapse.getInstance(collapseEl);
                        instance.toggle();

                        // Save state after toggle
                        setTimeout(() => {
                            const isNowOpen = collapseEl.classList.contains('show');
                            localStorage.setItem('collapse:' + collapseEl.id, isNowOpen ? 'true' : 'false');
                        }, 300);
                    });
                }
            });

            // --- WHEN SIDEBAR COLLAPSES, CLOSE ALL ---
            function closeAll() {
                collapseEls.forEach(el => {
                    const inst = bootstrap.Collapse.getInstance(el);
                    inst && inst.hide();
                    localStorage.setItem('collapse:' + el.id, 'false');
                });
                localStorage.removeItem(topMenuKey);
            }

            const observer = new MutationObserver(m => {
                m.forEach(mt => {
                    if (mt.attributeName === 'class' && document.body.classList.contains('collapsed')) {
                        closeAll();
                    }
                });
            });
            observer.observe(document.body, { attributes: true });
        })();
    </script>


    <!-- Global Delete Confirmation Modal -->
    <div class="modal fade" id="globalDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="globalDeleteMessage">Are you sure you want to delete this item? This action cannot be undone.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="globalDeleteConfirm" type="button" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Optional toast container for AJAX messages -->
    <div id="ajaxToastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1060;"></div>

    <!-- Global Multiple Delete Confirmation Modal -->
    <div class="modal fade" id="globalMultipleDeleteModal" tabindex="-1" aria-labelledby="globalMultipleDeleteModalLabel" aria-hidden="true" style="z-index: 1055;">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header border-danger">
            <h5 class="modal-title text-danger" id="globalMultipleDeleteModalLabel">
              <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Confirm Multiple Delete
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-danger fw-bold">Are you sure you want to delete <strong id="global-delete-count">0</strong> selected <span id="global-delete-type">items</span>?</p>
            <p class="text-danger small">This action cannot be undone.</p>
            <div id="global-selected-items-list" class="mt-3"></div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" id="global-confirm-multiple-delete">
              <i class="bi bi-trash me-1"></i>Delete <span id="global-delete-type-footer">Items</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let pending = null; // {url, row}

            function csrfToken() {
                const m = document.querySelector('meta[name="csrf-token"]');
                return m ? m.getAttribute('content') : '';
            }

            document.body.addEventListener('click', function (e) {
                const btn = e.target.closest('[data-delete-url], .ajax-delete');
                if (!btn) return;
                e.preventDefault();

                const url = btn.getAttribute('data-delete-url') || btn.getAttribute('href') || (btn.closest('form') && btn.closest('form').action);
                const row = btn.closest('tr');
                const msg = btn.getAttribute('data-delete-message') || 'Are you sure you want to delete this item? This action cannot be undone.';

                if (!url) return;
                pending = { url, row };

                document.getElementById('globalDeleteMessage').textContent = msg;
                const modal = new bootstrap.Modal(document.getElementById('globalDeleteModal'));
                modal.show();
            });

            document.getElementById('globalDeleteConfirm').addEventListener('click', async function () {
                if (!pending) return;
                const { url, row } = pending;
                const modalEl = document.getElementById('globalDeleteModal');
                const modal = bootstrap.Modal.getInstance(modalEl);

                // Use POST with method-spoofing to maximize compatibility (some servers block raw DELETE)
                try {
                    const fd = new FormData();
                    fd.append('_method', 'DELETE');
                    fd.append('_token', csrfToken());
                    let res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken(),
                            'Accept': 'application/json'
                        },
                        body: fd
                    });

                    // Treat 2xx and 3xx as success (some servers redirect after delete)
                    if (res.ok || (res.status >= 200 && res.status < 400)) {
                        if (row) row.remove();
                        modal && modal.hide();
                        
                        // Show success notification using our new system
                        if (window.crudNotification) {
                            // Try to get item name from the row for better UX
                            let itemName = 'Item';
                            if (row) {
                                const nameCell = row.querySelector('td:nth-child(2)'); // Usually name is in 2nd column
                                if (nameCell && nameCell.textContent.trim()) {
                                    itemName = nameCell.textContent.trim();
                                }
                            }
                            crudNotification.deleted(itemName);
                        }
                    } else {
                        modal && modal.hide();
                        // Try to extract a useful message only if server returned JSON
                        let txt = '';
                        try {
                            const j = await res.json();
                            if (j && j.message) txt = j.message;
                        } catch (e) {
                            // not JSON or no message; do not show blocking alert to user.
                            console.warn('Delete request failed', res.status, res.statusText);
                        }

                        // Show error notification using our new system
                        if (txt) {
                            if (window.crudNotification) {
                                crudNotification.error(txt);
                            } else {
                                console.warn('Delete failed: ' + txt);
                            }
                        } else {
                            if (window.crudNotification) {
                                crudNotification.error('Failed to delete item. Please try again.');
                            }
                        }
                    }
                } catch (err) {
                    modal && modal.hide();
                    console.warn('Delete network error', err);
                    
                    // Show network error notification using our new system
                    if (window.crudNotification) {
                        crudNotification.error('Delete failed — network error. Please check your connection and try again.');
                    }
                } finally {
                    pending = null;
                }
            });
        });
    </script>

    <!-- Global Select2 Initialization for All Dropdowns - OPTIMIZED -->
    <script>
        $(document).ready(function () {
            // Defer Select2 initialization to not block page load
            requestIdleCallback ? requestIdleCallback(initializeSelect2) : setTimeout(initializeSelect2, 100);

            // Function to initialize Select2 on select elements
            function initializeSelect2(container) {
                const selectElements = container ? $(container).find('select:not(.select2-hidden-accessible)') : $('select:not(.select2-hidden-accessible)');

                // Process in batches to avoid blocking
                const batchSize = 10;
                let index = 0;
                
                function processBatch() {
                    const batch = selectElements.slice(index, index + batchSize);
                    if (batch.length === 0) return;
                    
                    batch.each(function () {
                        const $select = $(this);
                        if ($select.hasClass('no-select2') || $select.data('select2')) return;

                        const placeholder = $select.data('placeholder') || $select.find('option:first').text() || 'Select an option';
                        const allowClear = $select.data('allow-clear') !== false;
                        const minimumResultsForSearch = $select.data('minimum-results-for-search') || 0;

                        $select.select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            placeholder: placeholder,
                            allowClear: allowClear,
                            minimumResultsForSearch: minimumResultsForSearch,
                            dropdownAutoWidth: true,
                            language: {
                                noResults: () => "No results found",
                                searching: () => "Searching..."
                            }
                        });
                    });
                    
                    index += batchSize;
                    if (index < selectElements.length) {
                        requestAnimationFrame(processBatch);
                    }
                }
                
                processBatch();
            }

            // Debounced MutationObserver for dynamic selects
            let mutationTimeout;
            const observer = new MutationObserver(function (mutations) {
                clearTimeout(mutationTimeout);
                mutationTimeout = setTimeout(() => {
                    mutations.forEach(function (mutation) {
                        if (mutation.addedNodes.length) {
                            mutation.addedNodes.forEach(function (node) {
                                if (node.nodeType === 1) {
                                    if (node.tagName === 'SELECT') {
                                        initializeSelect2($(node).parent());
                                    } else if ($(node).find('select').length > 0) {
                                        initializeSelect2(node);
                                    }
                                }
                            });
                        }
                    });
                }, 50);
            });

            observer.observe(document.body, { childList: true, subtree: true });

            $(document).on('shown.bs.modal', function (e) {
                initializeSelect2(e.target);
            });
        });
    </script>

    @stack('scripts')

    <!-- Global Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="bi bi-exclamation-triangle me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="deleteModalMessage">Are you sure you want to delete this item?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="bi bi-trash me-1"></i>Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global CRUD Notification Modal -->
    <div class="modal fade" id="crudNotificationModal" tabindex="-1" aria-labelledby="crudNotificationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0 pb-0" id="crudNotificationHeader">
                    <h5 class="modal-title d-flex align-items-center" id="crudNotificationModalLabel">
                        <i id="crudNotificationIcon" class="me-2"></i>
                        <span id="crudNotificationTitle">Notification</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body pt-2">
                    <p id="crudNotificationMessage" class="mb-0 text-center"></p>
                </div>
                <div class="modal-footer border-0 pt-2 justify-content-center">
                    <button type="button" class="btn btn-sm" id="crudNotificationBtn" data-bs-dismiss="modal">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container for Quick Notifications -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 11000;">
        <div id="crudToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i id="toastIcon" class="me-2"></i>
                <strong class="me-auto" id="toastTitle">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage">
                Default message
            </div>
        </div>
    </div>

    <script>
        // Global Delete Modal Handler
        window.deleteModal = {
            show: function(options) {
                const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                const confirmBtn = document.getElementById('confirmDeleteBtn');
                const messageEl = document.getElementById('deleteModalMessage');
                
                // Set custom message if provided
                if (options.message) {
                    messageEl.textContent = options.message;
                } else {
                    messageEl.textContent = 'Are you sure you want to delete this item?';
                }
                
                // Remove old event listeners
                const newConfirmBtn = confirmBtn.cloneNode(true);
                confirmBtn.parentNode.replaceChild(newConfirmBtn, confirmBtn);
                
                // Add new event listener
                newConfirmBtn.addEventListener('click', function() {
                    if (options.onConfirm) {
                        options.onConfirm();
                    }
                    modal.hide();
                });
                
                modal.show();
            }
        };

        // Global CRUD Notification System
        window.crudNotification = {
            // Show modal notification
            showModal: function(type, title, message, options = {}) {
                const modal = new bootstrap.Modal(document.getElementById('crudNotificationModal'));
                const header = document.getElementById('crudNotificationHeader');
                const icon = document.getElementById('crudNotificationIcon');
                const titleEl = document.getElementById('crudNotificationTitle');
                const messageEl = document.getElementById('crudNotificationMessage');
                const btn = document.getElementById('crudNotificationBtn');

                // Set colors and icons based on type
                const config = this.getConfig(type);
                
                header.className = `modal-header border-0 pb-0 ${config.headerClass}`;
                icon.className = `${config.icon} me-2`;
                titleEl.textContent = title || config.defaultTitle;
                messageEl.textContent = message;
                btn.className = `btn btn-sm ${config.btnClass}`;

                // Auto-hide after delay if specified
                if (options.autoHide !== false) {
                    setTimeout(() => {
                        modal.hide();
                    }, options.delay || 3000);
                }

                modal.show();
            },

            // Show toast notification
            showToast: function(type, title, message, options = {}) {
                const toast = document.getElementById('crudToast');
                const toastInstance = new bootstrap.Toast(toast, {
                    delay: options.delay || 4000
                });
                
                const header = toast.querySelector('.toast-header');
                const icon = document.getElementById('toastIcon');
                const titleEl = document.getElementById('toastTitle');
                const messageEl = document.getElementById('toastMessage');

                const config = this.getConfig(type);
                
                header.className = `toast-header ${config.toastHeaderClass}`;
                icon.className = `${config.icon} me-2`;
                titleEl.textContent = title || config.defaultTitle;
                messageEl.textContent = message;

                toastInstance.show();
            },

            // Configuration for different notification types
            getConfig: function(type) {
                const configs = {
                    success: {
                        icon: 'bi bi-check-circle-fill text-success',
                        headerClass: 'bg-success text-white',
                        toastHeaderClass: 'bg-success text-white',
                        btnClass: 'btn-success',
                        defaultTitle: 'Success'
                    },
                    error: {
                        icon: 'bi bi-x-circle-fill text-danger',
                        headerClass: 'bg-danger text-white',
                        toastHeaderClass: 'bg-danger text-white',
                        btnClass: 'btn-danger',
                        defaultTitle: 'Error'
                    },
                    warning: {
                        icon: 'bi bi-exclamation-triangle-fill text-warning',
                        headerClass: 'bg-warning text-dark',
                        toastHeaderClass: 'bg-warning text-dark',
                        btnClass: 'btn-warning',
                        defaultTitle: 'Warning'
                    },
                    info: {
                        icon: 'bi bi-info-circle-fill text-info',
                        headerClass: 'bg-info text-white',
                        toastHeaderClass: 'bg-info text-white',
                        btnClass: 'btn-info',
                        defaultTitle: 'Information'
                    }
                };
                return configs[type] || configs.info;
            },

            // Convenience methods for CRUD operations
            created: function(itemName, useToast = true) {
                const message = `${itemName} has been successfully created!`;
                if (useToast) {
                    this.showToast('success', 'Created', message);
                } else {
                    this.showModal('success', 'Successfully Created', message);
                }
            },

            updated: function(itemName, useToast = true) {
                const message = `${itemName} has been successfully updated!`;
                if (useToast) {
                    this.showToast('success', 'Updated', message);
                } else {
                    this.showModal('success', 'Successfully Updated', message);
                }
            },

            deleted: function(itemName, useToast = true) {
                const message = `${itemName} has been successfully deleted!`;
                if (useToast) {
                    this.showToast('error', 'Deleted', message);
                } else {
                    this.showModal('error', 'Successfully Deleted', message);
                }
            },

            error: function(message, useToast = true) {
                if (useToast) {
                    this.showToast('error', 'Error', message);
                } else {
                    this.showModal('error', 'Error Occurred', message);
                }
            }
        };

        // Auto-show notifications from Laravel session flash messages
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('crud_success'))
                crudNotification.showToast('success', 'Success', '{{ session('crud_success') }}');
            @endif

            @if(session('crud_error'))
                crudNotification.showToast('error', 'Error', '{{ session('crud_error') }}');
            @endif

            @if(session('crud_warning'))
                crudNotification.showToast('warning', 'Warning', '{{ session('crud_warning') }}');
            @endif

            @if(session('crud_info'))
                crudNotification.showToast('info', 'Info', '{{ session('crud_info') }}');
            @endif

            // Access Denied toast for permission errors
            @if(session('access_denied'))
                crudNotification.showToast('error', 'Access Denied', 'You do not have permission to access "{{ session('access_denied') }}" module. Please contact your administrator.');
            @endif

            // Specific CRUD operation messages
            @if(session('item_created'))
                crudNotification.created('{{ session('item_created') }}');
            @endif

            @if(session('item_updated'))
                crudNotification.updated('{{ session('item_updated') }}');
            @endif

            @if(session('item_deleted'))
                crudNotification.deleted('{{ session('item_deleted') }}');
            @endif
        });

        // Global Multiple Delete Utility
        window.GlobalMultipleDelete = {
            // Check if modal elements are available
            _checkModalAvailability: function() {
                const modalEl = document.getElementById('globalMultipleDeleteModal');
                const countEl = document.getElementById('global-delete-count');
                const typeEl = document.getElementById('global-delete-type');
                const footerTypeEl = document.getElementById('global-delete-type-footer');
                const itemsList = document.getElementById('global-selected-items-list');
                
                // Debug: Log what we found
                console.log('Modal element check details:', {
                    modalEl: modalEl ? 'found' : 'not found',
                    countEl: countEl ? 'found' : 'not found',
                    typeEl: typeEl ? 'found' : 'not found',
                    footerTypeEl: footerTypeEl ? 'found' : 'not found',
                    itemsList: itemsList ? 'found' : 'not found'
                });
                
                // If footerTypeEl is missing, let's try to find it differently
                if (!footerTypeEl && modalEl) {
                    console.log('Trying alternative search for footer type element...');
                    const alternativeFooterType = modalEl.querySelector('#global-delete-type-footer');
                    console.log('Alternative search result:', alternativeFooterType ? 'found' : 'not found');
                    
                    return {
                        modal: modalEl,
                        count: countEl,
                        type: typeEl,
                        footerType: alternativeFooterType,
                        itemsList: itemsList,
                        allAvailable: modalEl && countEl && typeEl && itemsList // footerType is optional
                    };
                }
                
                return {
                    modal: modalEl,
                    count: countEl,
                    type: typeEl,
                    footerType: footerTypeEl,
                    itemsList: itemsList,
                    allAvailable: modalEl && countEl && typeEl && itemsList // footerType is now optional
                };
            },
            /**
             * Show the global multiple delete confirmation modal
             * @param {Object} config - Configuration object
             * @param {Array} config.selectedItems - Array of selected items with {id, name} structure
             * @param {string} config.deleteUrl - URL for the delete endpoint
             * @param {string} config.itemType - Type of items (e.g., 'items', 'customers', 'suppliers')
             * @param {Function} config.onSuccess - Callback function on successful deletion
             * @param {Function} config.onError - Optional callback function on error
             * @param {Object} config.csrfToken - CSRF token for the request
             */
            show: function(config, retryCount = 0) {
                const {
                    selectedItems,
                    deleteUrl,
                    itemType = 'items',
                    onSuccess,
                    onError,
                    csrfToken
                } = config;

                if (!selectedItems || selectedItems.length === 0) {
                    console.error('No items selected for deletion');
                    return;
                }

                // Check modal availability - only require essential elements
                const availability = this._checkModalAvailability();
                const essentialElementsAvailable = availability.modal && availability.count && availability.type && availability.itemsList;
                
                if (!essentialElementsAvailable) {
                    console.warn('Essential modal elements not available:', {
                        modal: !!availability.modal,
                        count: !!availability.count,
                        type: !!availability.type,
                        itemsList: !!availability.itemsList
                    });
                    
                    // Retry up to 3 times with increasing delays
                    if (retryCount < 3) {
                        console.log(`Retrying modal access (attempt ${retryCount + 1}/3)...`);
                        setTimeout(() => {
                            this.show(config, retryCount + 1);
                        }, (retryCount + 1) * 200); // 200ms, 400ms, 600ms delays
                        return;
                    }
                    
                    console.error('Global multiple delete modal elements not found after retries. Modal may not be loaded.');
                    if (window.crudNotification) {
                        crudNotification.showToast('error', 'Error', 'Delete modal not available. Please refresh the page.');
                    }
                    return;
                }
                
                // Update modal content
                availability.count.textContent = selectedItems.length;
                availability.type.textContent = itemType;
                
                // Update footer type if available, otherwise update button text directly
                if (availability.footerType) {
                    availability.footerType.textContent = itemType.charAt(0).toUpperCase() + itemType.slice(1);
                } else {
                    // Fallback: update the entire button text
                    const confirmBtn = document.getElementById('global-confirm-multiple-delete');
                    if (confirmBtn) {
                        confirmBtn.innerHTML = `<i class="bi bi-trash me-1"></i>Delete ${itemType.charAt(0).toUpperCase() + itemType.slice(1)}`;
                    }
                }
                
                if (selectedItems.length <= 5) {
                    availability.itemsList.innerHTML = '<div class="alert alert-danger alert-dismissible"><strong class="text-danger">Selected ' + itemType + ' to be deleted:</strong><ul class="mb-0 mt-2 text-danger">' + 
                        selectedItems.map(item => `<li>${item.name}</li>`).join('') + '</ul></div>';
                } else {
                    availability.itemsList.innerHTML = `<div class="alert alert-danger alert-dismissible"><strong class="text-danger">Selected ${itemType} to be deleted:</strong><ul class="mb-0 mt-2 text-danger">` +
                        selectedItems.slice(0, 3).map(item => `<li>${item.name}</li>`).join('') +
                        `<li>... and ${selectedItems.length - 3} more ${itemType}</li></ul></div>`;
                }

                // Store configuration for the confirm handler
                this._currentConfig = config;

                // Show modal
                const modal = new bootstrap.Modal(availability.modal);
                modal.show();
            },

            /**
             * Handle the confirmation of deletion
             */
            _handleConfirm: function() {
                const config = this._currentConfig;
                if (!config) return;

                const confirmBtn = document.getElementById('global-confirm-multiple-delete');
                const itemIds = config.selectedItems.map(item => item.id);
                
                // Disable button and show loading
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Deleting...';

                // Determine field name based on item type
                let fieldName = 'item_ids'; // default (items)
                if (config.itemType === 'companies') {
                    fieldName = 'company_ids';
                } else if (config.itemType === 'customers') {
                    fieldName = 'customer_ids';
                } else if (config.itemType === 'suppliers') {
                    fieldName = 'supplier_ids';
                } else if (config.itemType === 'hsn-codes') {
                    fieldName = 'hsn_codes_ids';
                } else if (config.itemType === 'cash-bank-books') {
                    fieldName = 'cash_bank_books_ids';
                } else if (config.itemType === 'sale-ledger') {
                    fieldName = 'sale_ledger_ids';
                } else if (config.itemType === 'purchase-ledger') {
                    fieldName = 'purchase_ledger_ids';
                } else if (config.itemType === 'general-ledger') {
                    fieldName = 'general_ledger_ids';
                } else if (config.itemType === 'sales-men') {
                    fieldName = 'sales_man_ids';
                } else if (config.itemType === 'areas') {
                    fieldName = 'area_ids';
                } else if (config.itemType === 'routes') {
                    fieldName = 'route_ids';
                } else if (config.itemType === 'states') {
                    fieldName = 'state_ids';
                } else if (config.itemType === 'area managers') {
                    fieldName = 'area_manager_ids';
                } else if (config.itemType === 'regional managers') {
                    fieldName = 'regional_manager_ids';
                } else if (config.itemType === 'marketing managers') {
                    fieldName = 'marketing_manager_ids';
                } else if (config.itemType === 'general managers') {
                    fieldName = 'general_manager_ids';
                } else if (config.itemType === 'divisional managers') {
                    fieldName = 'divisional_manager_ids';
                } else if (config.itemType === 'country managers') {
                    fieldName = 'country_manager_ids';
                } else if (config.itemType === 'personal directory entries') {
                    fieldName = 'personal_directory_ids';
                } else if (config.itemType === 'general reminders') {
                    fieldName = 'general_reminder_ids';
                } else if (config.itemType === 'general notebook entries') {
                    fieldName = 'general_notebook_ids';
                } else if (config.itemType === 'item categories') {
                    fieldName = 'item_category_ids';
                } else if (config.itemType === 'transport masters') {
                    fieldName = 'transport_master_ids';
                }

                // Create FormData for better Laravel compatibility
                const formData = new FormData();
                itemIds.forEach(id => {
                    formData.append(fieldName + '[]', id);
                });
                formData.append('_token', config.csrfToken);
                
                // Debug logging
                console.log('Sending data for', config.itemType);
                console.log('Field name:', fieldName);
                console.log('Item IDs:', itemIds);
                for (let pair of formData.entries()) {
                    console.log(pair[0] + ': ' + pair[1]);
                }

                // Perform AJAX delete
                fetch(config.deleteUrl, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Hide modal first
                        bootstrap.Modal.getInstance(document.getElementById('globalMultipleDeleteModal')).hide();
                        
                        // Show success message in red/danger color for delete operations
                        if (window.crudNotification) {
                            crudNotification.showToast('error', 'Deleted', data.message || `${itemIds.length} ${config.itemType} deleted successfully`);
                        }
                        
                        // Call success callback with a slight delay to ensure modal is hidden
                        setTimeout(() => {
                            if (config.onSuccess) {
                                config.onSuccess(data);
                            }
                            
                            // Additional safety: Ensure event listeners are reattached after success callback
                            setTimeout(() => {
                                // For items page specifically
                                if (typeof window.reattachItemsEventListeners === 'function') {
                                    window.reattachItemsEventListeners();
                                }
                                
                                // Reset all checkboxes and update counts
                                document.querySelectorAll('.item-checkbox, .checkbox').forEach(cb => {
                                    cb.checked = false;
                                    cb.indeterminate = false;
                                });
                                
                                // Reset select all checkboxes
                                document.querySelectorAll('#select-all-items, .select-all').forEach(cb => {
                                    cb.checked = false;
                                    cb.indeterminate = false;
                                });
                                
                                // Update selected counts
                                if (typeof window.updateItemsSelectedCount === 'function') {
                                    window.updateItemsSelectedCount();
                                }
                                
                                // Hide delete buttons
                                document.querySelectorAll('#delete-selected-btn, .delete-selected-btn').forEach(btn => {
                                    btn.classList.add('d-none');
                                });
                            }, 100);
                        }, 100);
                    } else {
                        if (window.crudNotification) {
                            crudNotification.showToast('error', 'Error', data.message || `Error deleting ${config.itemType}`);
                        }
                        
                        // Call error callback
                        if (config.onError) {
                            config.onError(data);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (window.crudNotification) {
                        crudNotification.showToast('error', 'Error', `Error deleting ${config.itemType}. Please try again.`);
                    }
                    
                    // Call error callback
                    if (config.onError) {
                        config.onError(error);
                    }
                })
                .finally(() => {
                    // Re-enable button
                    confirmBtn.disabled = false;
                    confirmBtn.innerHTML = `<i class="bi bi-trash me-1"></i>Delete ${config.itemType.charAt(0).toUpperCase() + config.itemType.slice(1)}`;
                    
                    // Clear current config
                    this._currentConfig = null;
                });
            }
        };

        // Debug function to test modal availability (can be called from console)
        window.testGlobalModal = function() {
            const availability = window.GlobalMultipleDelete._checkModalAvailability();
            console.log('Manual Modal Test:', availability);
            
            if (availability.allAvailable) {
                console.log('✅ All modal elements are available');
                // Test showing the modal
                try {
                    window.GlobalMultipleDelete.show({
                        selectedItems: [{id: 1, name: 'Test Item'}],
                        deleteUrl: '/test',
                        itemType: 'test items',
                        csrfToken: 'test',
                        onSuccess: function() { console.log('Test success callback'); },
                        onError: function() { console.log('Test error callback'); }
                    });
                    console.log('✅ Modal show function executed successfully');
                } catch (error) {
                    console.error('❌ Error showing modal:', error);
                }
            } else {
                console.log('❌ Some modal elements are missing:', {
                    modal: !!availability.modal,
                    count: !!availability.count,
                    type: !!availability.type,
                    footerType: !!availability.footerType,
                    itemsList: !!availability.itemsList
                });
            }
        };

        // Attach confirm handler to the global modal
        document.addEventListener('DOMContentLoaded', function() {
            // Debug: Check if modal elements are loaded
            const modalCheck = window.GlobalMultipleDelete._checkModalAvailability();
            console.log('DOMContentLoaded - Modal availability:', {
                modal: !!modalCheck.modal,
                count: !!modalCheck.count,
                type: !!modalCheck.type,
                footerType: !!modalCheck.footerType,
                itemsList: !!modalCheck.itemsList,
                allAvailable: modalCheck.allAvailable
            });
            
            const confirmBtn = document.getElementById('global-confirm-multiple-delete');
            if (confirmBtn) {
                confirmBtn.addEventListener('click', function() {
                    GlobalMultipleDelete._handleConfirm();
                });
            } else {
                console.warn('Global confirm multiple delete button not found');
            }
            
            // Enter key to confirm delete when modal is open
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    // Check if globalMultipleDeleteModal is open
                    const multiDeleteModal = document.getElementById('globalMultipleDeleteModal');
                    if (multiDeleteModal && multiDeleteModal.classList.contains('show')) {
                        e.preventDefault();
                        e.stopPropagation();
                        const confirmBtn = document.getElementById('global-confirm-multiple-delete');
                        if (confirmBtn && !confirmBtn.disabled) {
                            confirmBtn.click();
                        }
                        return;
                    }
                    
                    // Check if single delete modal (globalDeleteModal) is open
                    const singleDeleteModal = document.getElementById('globalDeleteModal');
                    if (singleDeleteModal && singleDeleteModal.classList.contains('show')) {
                        e.preventDefault();
                        e.stopPropagation();
                        const confirmBtn = document.getElementById('globalDeleteConfirm');
                        if (confirmBtn && !confirmBtn.disabled) {
                            confirmBtn.click();
                        }
                        return;
                    }
                    
                    // Check if deleteModal is open
                    const deleteModal = document.getElementById('deleteModal');
                    if (deleteModal && deleteModal.classList.contains('show')) {
                        e.preventDefault();
                        e.stopPropagation();
                        const confirmBtn = document.getElementById('confirmDeleteBtn');
                        if (confirmBtn && !confirmBtn.disabled) {
                            confirmBtn.click();
                        }
                        return;
                    }
                }
            });
        });
    </script>
    
    <!-- Global Keyboard Shortcuts (EasySol-style) - Press F1 for help -->
    @include('layouts.partials.keyboard-shortcuts-config')
    @include('layouts.partials.index-shortcuts-config')
    @include('layouts.partials.keyboard-shortcuts-inline')
    @include('layouts.partials.index-shortcuts-inline')
    
    <!-- Transaction Shortcuts - End key to save -->
    @include('layouts.partials.transaction-shortcuts')
</body>

</html>