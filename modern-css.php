<?php
/**
 * 🚀 MultiTienda Pro - Modern CSS Framework
 * Sistema de diseño profesional para la plataforma
 */
?>
<style>
:root {
    --primary: #6366f1;
    --primary-dark: #4f46e5;
    --primary-light: #a5b4fc;
    --secondary: #8b5cf6;
    --success: #10b981;
    --success-light: #d1fae5;
    --warning: #f59e0b;
    --warning-light: #fef3c7;
    --error: #ef4444;
    --error-light: #fee2e2;
    --info: #3b82f6;
    --info-light: #dbeafe;
    --gray-50: #f9fafb;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    --radius: 0.5rem;
    --radius-lg: 1rem;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    min-height: 100vh;
    font-size: 14px;
    line-height: 1.6;
    color: var(--gray-700);
}

.app-layout {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 280px;
    background: white;
    border-right: 1px solid var(--gray-200);
    flex-shrink: 0;
    overflow-y: auto;
    box-shadow: var(--shadow-lg);
}

.sidebar-header {
    padding: 2rem 1.5rem;
    border-bottom: 1px solid var(--gray-100);
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    color: white;
}

.sidebar-logo {
    font-size: 1.5rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.sidebar-nav {
    padding: 1.5rem 0;
}

.nav-section {
    margin-bottom: 2rem;
}

.nav-section-title {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--gray-400);
    padding: 0 1.5rem;
    margin-bottom: 0.75rem;
}

.nav-item {
    display: block;
    padding: 0.75rem 1.5rem;
    color: var(--gray-600);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.2s ease;
    border-left: 3px solid transparent;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.nav-item:hover {
    background: var(--gray-50);
    color: var(--primary);
    border-left-color: var(--primary-light);
}

.nav-item.active {
    background: var(--primary-light);
    background: linear-gradient(90deg, var(--primary-light) 0%, transparent 100%);
    color: var(--primary);
    border-left-color: var(--primary);
    font-weight: 600;
}

.nav-icon {
    width: 20px;
    text-align: center;
    opacity: 0.7;
}

.main-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.top-header {
    background: white;
    border-bottom: 1px solid var(--gray-200);
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: var(--shadow-sm);
    position: sticky;
    top: 0;
    z-index: 10;
}

.header-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--gray-900);
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-menu {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 1rem;
    border-radius: var(--radius);
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    cursor: pointer;
    transition: all 0.2s ease;
}

.user-menu:hover {
    background: var(--gray-100);
    box-shadow: var(--shadow);
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.875rem;
}

.content-area {
    flex: 1;
    padding: 2rem;
    overflow-y: auto;
    background: var(--gray-50);
}

.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.metric-card {
    background: white;
    border-radius: var(--radius-lg);
    padding: 2rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--gray-100);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.metric-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--primary) 0%, var(--secondary) 100%);
}

.metric-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
}

.metric-header {
    display: flex;
    justify-content: between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.metric-icon {
    width: 48px;
    height: 48px;
    border-radius: var(--radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    margin-bottom: 1rem;
}

.metric-icon.primary { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); }
.metric-icon.success { background: linear-gradient(135deg, var(--success) 0%, #059669 100%); }
.metric-icon.warning { background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%); }
.metric-icon.info { background: linear-gradient(135deg, var(--info) 0%, #1d4ed8 100%); }

.metric-value {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--gray-900);
    margin-bottom: 0.25rem;
}

.metric-label {
    font-size: 0.875rem;
    color: var(--gray-500);
    font-weight: 500;
}

.metric-trend {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.metric-trend.up { color: var(--success); }
.metric-trend.down { color: var(--error); }

.content-card {
    background: white;
    border-radius: var(--radius-lg);
    border: 1px solid var(--gray-200);
    box-shadow: var(--shadow);
    overflow: hidden;
    margin-bottom: 2rem;
}

.card-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--gray-100);
    background: var(--gray-50);
}

.card-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--gray-900);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.card-content {
    padding: 2rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    font-size: 0.875rem;
    font-weight: 600;
    border: none;
    border-radius: var(--radius);
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s ease;
    line-height: 1;
}

.btn-primary {
    background: var(--primary);
    color: white;
    box-shadow: var(--shadow);
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-secondary {
    background: var(--gray-100);
    color: var(--gray-700);
    border: 1px solid var(--gray-300);
}

.btn-secondary:hover {
    background: var(--gray-200);
    border-color: var(--gray-400);
}

.btn-success {
    background: var(--success);
    color: white;
}

.btn-success:hover {
    background: #059669;
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: var(--radius);
    border: 1px solid;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.alert-success {
    background: var(--success-light);
    border-color: var(--success);
    color: #065f46;
}

.alert-error {
    background: var(--error-light);
    border-color: var(--error);
    color: #991b1b;
}

.alert-warning {
    background: var(--warning-light);
    border-color: var(--warning);
    color: #92400e;
}

.alert-info {
    background: var(--info-light);
    border-color: var(--info);
    color: #1e40af;
}

.table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.table th,
.table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--gray-200);
}

.table th {
    background: var(--gray-50);
    font-weight: 600;
    color: var(--gray-700);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.table tbody tr:hover {
    background: var(--gray-50);
}

.badge {
    display: inline-flex;
    align-items: center;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 9999px;
    line-height: 1;
}

.badge-success {
    background: var(--success-light);
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.badge-warning {
    background: var(--warning-light);
    color: #92400e;
    border: 1px solid #fcd34d;
}

.badge-error {
    background: var(--error-light);
    color: #991b1b;
    border: 1px solid #fca5a5;
}

.badge-info {
    background: var(--info-light);
    color: #1e40af;
    border: 1px solid #93c5fd;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 0.75rem 1rem;
    font-size: 0.875rem;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    background: white;
    transition: all 0.2s ease;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}

.loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid var(--gray-300);
    border-radius: 50%;
    border-top-color: var(--primary);
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--gray-500);
}

.empty-state-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: var(--gray-100);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--gray-400);
}

.empty-state-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.5rem;
}

.empty-state-description {
    margin-bottom: 2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .app-layout {
        flex-direction: column;
    }
    
    .sidebar {
        width: 100%;
        height: auto;
        position: fixed;
        bottom: 0;
        z-index: 1000;
        border-right: none;
        border-top: 1px solid var(--gray-200);
    }
    
    .sidebar-header {
        display: none;
    }
    
    .sidebar-nav {
        padding: 0.5rem 0;
        display: flex;
        overflow-x: auto;
    }
    
    .nav-section {
        display: flex;
        margin-bottom: 0;
    }
    
    .nav-section-title {
        display: none;
    }
    
    .nav-item {
        flex-direction: column;
        gap: 0.25rem;
        padding: 0.75rem 1rem;
        min-width: 80px;
        text-align: center;
        border-left: none;
        border-bottom: 3px solid transparent;
        font-size: 0.75rem;
    }
    
    .nav-item.active {
        border-left: none;
        border-bottom-color: var(--primary);
    }
    
    .main-content {
        padding-bottom: 80px;
    }
    
    .content-area {
        padding: 1rem;
    }
    
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
}
</style><?php