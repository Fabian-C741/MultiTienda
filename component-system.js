/**
 * 🧩 MultiTienda Pro - Sistema de Componentes Enterprise
 * Biblioteca de componentes modulares y reutilizables
 */

class ComponentSystem {
    constructor() {
        this.components = new Map();
        this.globalState = new Map();
        this.eventBus = new EventTarget();
    }

    // Registrar un componente
    register(name, component) {
        this.components.set(name, component);
    }

    // Crear instancia de componente
    create(name, config = {}) {
        const Component = this.components.get(name);
        if (!Component) {
            throw new Error(`Componente '${name}' no encontrado`);
        }
        return new Component(config, this);
    }
}

// Componente base
class BaseComponent {
    constructor(config = {}, system) {
        this.config = { ...this.defaultConfig, ...config };
        this.system = system;
        this.element = null;
        this.isVisible = false;
        this.events = new Map();
    }

    get defaultConfig() {
        return {};
    }

    // Crear el DOM del componente
    render() {
        throw new Error('Método render debe ser implementado');
    }

    // Montar el componente en el DOM
    mount(container) {
        if (typeof container === 'string') {
            container = document.querySelector(container);
        }
        if (!container) {
            throw new Error('Container no encontrado');
        }
        
        this.element = this.render();
        container.appendChild(this.element);
        this.bindEvents();
        this.onMount();
        return this;
    }

    // Desmontar componente
    unmount() {
        if (this.element) {
            this.unbindEvents();
            this.element.remove();
            this.element = null;
            this.onUnmount();
        }
        return this;
    }

    // Hooks del ciclo de vida
    onMount() {}
    onUnmount() {}

    // Gestión de eventos
    bindEvents() {}
    unbindEvents() {
        this.events.forEach((handler, event) => {
            this.element?.removeEventListener(event, handler);
        });
        this.events.clear();
    }

    on(event, handler) {
        this.events.set(event, handler);
        this.element?.addEventListener(event, handler);
    }

    emit(event, data) {
        this.system.eventBus.dispatchEvent(new CustomEvent(event, { detail: data }));
    }
}

// Modal Component Enterprise
class Modal extends BaseComponent {
    get defaultConfig() {
        return {
            title: '',
            content: '',
            size: 'md', // sm, md, lg, xl
            closable: true,
            backdrop: true,
            keyboard: true,
            animation: true,
            className: '',
            buttons: []
        };
    }

    render() {
        const modal = document.createElement('div');
        modal.className = `modal-overlay ${this.config.animation ? 'modal-animated' : ''} ${this.config.className}`;
        modal.innerHTML = `
            <div class="modal-backdrop ${this.config.backdrop ? 'modal-backdrop-enabled' : ''}"></div>
            <div class="modal-container">
                <div class="modal modal-${this.config.size}">
                    ${this.config.closable ? '<button class="modal-close" type="button">&times;</button>' : ''}
                    ${this.config.title ? `<div class="modal-header"><h3 class="modal-title">${this.config.title}</h3></div>` : ''}
                    <div class="modal-body">${this.config.content}</div>
                    ${this.config.buttons.length ? `<div class="modal-footer">${this.renderButtons()}</div>` : ''}
                </div>
            </div>
        `;

        // CSS del modal
        if (!document.querySelector('#modal-styles')) {
            const style = document.createElement('style');
            style.id = 'modal-styles';
            style.textContent = `
                .modal-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    z-index: 1000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 1rem;
                }
                
                .modal-overlay.modal-animated {
                    animation: modalFadeIn 0.3s ease-out;
                }
                
                .modal-backdrop {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.5);
                    backdrop-filter: blur(10px);
                    -webkit-backdrop-filter: blur(10px);
                }
                
                .modal-container {
                    position: relative;
                    z-index: 1001;
                    max-width: 100%;
                    max-height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                
                .modal {
                    background: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(20px);
                    -webkit-backdrop-filter: blur(20px);
                    border-radius: 1.5rem;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                    border: 1px solid rgba(255, 255, 255, 0.2);
                    position: relative;
                    width: 100%;
                    max-height: 90vh;
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                }
                
                .modal.modal-animated {
                    animation: modalSlideIn 0.3s ease-out;
                }
                
                .modal-sm { max-width: 400px; }
                .modal-md { max-width: 600px; }
                .modal-lg { max-width: 800px; }
                .modal-xl { max-width: 1200px; }
                
                .modal-close {
                    position: absolute;
                    top: 1rem;
                    right: 1rem;
                    width: 2.5rem;
                    height: 2.5rem;
                    border: none;
                    background: rgba(0, 0, 0, 0.1);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.5rem;
                    cursor: pointer;
                    transition: all 0.2s;
                    z-index: 1002;
                    color: #6b7280;
                }
                
                .modal-close:hover {
                    background: rgba(239, 68, 68, 0.1);
                    color: #ef4444;
                    transform: scale(1.1);
                }
                
                .modal-header {
                    padding: 2rem 2rem 0;
                    flex-shrink: 0;
                }
                
                .modal-title {
                    font-size: 1.5rem;
                    font-weight: 700;
                    color: #111827;
                    margin: 0;
                }
                
                .modal-body {
                    padding: 2rem;
                    flex: 1;
                    overflow-y: auto;
                }
                
                .modal-footer {
                    padding: 0 2rem 2rem;
                    display: flex;
                    gap: 1rem;
                    justify-content: flex-end;
                    flex-shrink: 0;
                }
                
                @keyframes modalFadeIn {
                    from { opacity: 0; }
                    to { opacity: 1; }
                }
                
                @keyframes modalSlideIn {
                    from { 
                        opacity: 0;
                        transform: scale(0.9) translateY(-20px);
                    }
                    to { 
                        opacity: 1;
                        transform: scale(1) translateY(0);
                    }
                }
                
                @media (max-width: 768px) {
                    .modal-overlay {
                        padding: 0;
                        align-items: flex-end;
                    }
                    
                    .modal {
                        border-radius: 1.5rem 1.5rem 0 0;
                        max-height: 90vh;
                        width: 100%;
                    }
                }
            `;
            document.head.appendChild(style);
        }

        return modal;
    }

    renderButtons() {
        return this.config.buttons.map(button => 
            `<button type="button" class="btn ${button.className || 'btn-secondary'}" data-action="${button.action || ''}">${button.text}</button>`
        ).join('');
    }

    bindEvents() {
        if (this.config.closable) {
            const closeBtn = this.element.querySelector('.modal-close');
            const backdrop = this.element.querySelector('.modal-backdrop');
            
            this.on('click', (e) => {
                if (e.target === closeBtn || (this.config.backdrop && e.target === backdrop)) {
                    this.hide();
                }
            });
        }

        if (this.config.keyboard) {
            this.on('keydown', (e) => {
                if (e.key === 'Escape' && this.config.closable) {
                    this.hide();
                }
            });
        }

        // Botones personalizados
        const buttons = this.element.querySelectorAll('[data-action]');
        buttons.forEach(button => {
            button.addEventListener('click', (e) => {
                const action = e.target.getAttribute('data-action');
                this.emit('button-click', { action, button: e.target });
                
                if (action === 'close') {
                    this.hide();
                }
            });
        });
    }

    show() {
        document.body.appendChild(this.element);
        document.body.style.overflow = 'hidden';
        this.isVisible = true;
        this.emit('show');
        
        // Focus trap
        const focusableElements = this.element.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        if (focusableElements.length > 0) {
            focusableElements[0].focus();
        }
        
        return this;
    }

    hide() {
        if (this.config.animation) {
            this.element.style.animation = 'modalFadeIn 0.3s ease-out reverse';
            setTimeout(() => {
                this.unmount();
                document.body.style.overflow = '';
                this.isVisible = false;
                this.emit('hide');
            }, 300);
        } else {
            this.unmount();
            document.body.style.overflow = '';
            this.isVisible = false;
            this.emit('hide');
        }
        return this;
    }

    setContent(content) {
        const body = this.element?.querySelector('.modal-body');
        if (body) {
            body.innerHTML = content;
        }
        return this;
    }

    setTitle(title) {
        const titleEl = this.element?.querySelector('.modal-title');
        if (titleEl) {
            titleEl.textContent = title;
        }
        return this;
    }
}

// Table Component Enterprise
class DataTable extends BaseComponent {
    get defaultConfig() {
        return {
            data: [],
            columns: [],
            pagination: true,
            pageSize: 10,
            sortable: true,
            filterable: true,
            selectable: false,
            actions: [],
            className: '',
            emptyMessage: 'No hay datos disponibles',
            loadingMessage: 'Cargando...'
        };
    }

    constructor(config, system) {
        super(config, system);
        this.currentPage = 1;
        this.sortColumn = null;
        this.sortDirection = 'asc';
        this.filters = new Map();
        this.selectedRows = new Set();
        this.isLoading = false;
    }

    render() {
        const table = document.createElement('div');
        table.className = `datatable ${this.config.className}`;
        table.innerHTML = `
            <div class="datatable-header">
                ${this.config.filterable ? this.renderFilters() : ''}
                <div class="datatable-actions">
                    ${this.config.actions.map(action => 
                        `<button class="btn ${action.className || 'btn-secondary'}" data-action="${action.name}">${action.label}</button>`
                    ).join('')}
                </div>
            </div>
            <div class="datatable-wrapper">
                <div class="datatable-loading" style="display: none;">
                    <div class="loading-spinner"></div>
                    <span>${this.config.loadingMessage}</span>
                </div>
                <table class="table datatable-table">
                    ${this.renderHeaders()}
                    ${this.renderBody()}
                </table>
            </div>
            ${this.config.pagination ? this.renderPagination() : ''}
        `;

        // CSS del datatable
        if (!document.querySelector('#datatable-styles')) {
            const style = document.createElement('style');
            style.id = 'datatable-styles';
            style.textContent = `
                .datatable {
                    background: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(20px);
                    -webkit-backdrop-filter: blur(20px);
                    border-radius: 1rem;
                    border: 1px solid rgba(255, 255, 255, 0.2);
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                    overflow: hidden;
                }
                
                .datatable-header {
                    padding: 1.5rem;
                    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95) 0%, rgba(248, 250, 252, 0.95) 100%);
                    border-bottom: 1px solid rgba(229, 231, 235, 0.8);
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    gap: 1rem;
                    flex-wrap: wrap;
                }
                
                .datatable-filters {
                    display: flex;
                    gap: 1rem;
                    align-items: center;
                    flex-wrap: wrap;
                }
                
                .datatable-search {
                    position: relative;
                }
                
                .datatable-search input {
                    padding: 0.5rem 1rem 0.5rem 2.5rem;
                    border: 1px solid #d1d5db;
                    border-radius: 0.5rem;
                    font-size: 0.875rem;
                    min-width: 250px;
                }
                
                .datatable-search::before {
                    content: '🔍';
                    position: absolute;
                    left: 0.75rem;
                    top: 50%;
                    transform: translateY(-50%);
                    font-size: 0.875rem;
                }
                
                .datatable-actions {
                    display: flex;
                    gap: 0.5rem;
                    align-items: center;
                }
                
                .datatable-wrapper {
                    position: relative;
                    overflow-x: auto;
                }
                
                .datatable-loading {
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(255, 255, 255, 0.9);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 1rem;
                    z-index: 10;
                }
                
                .datatable-table {
                    margin: 0;
                }
                
                .datatable-table th {
                    background: rgba(249, 250, 251, 0.8);
                    font-weight: 600;
                    cursor: pointer;
                    user-select: none;
                    position: relative;
                    transition: all 0.2s;
                }
                
                .datatable-table th:hover {
                    background: rgba(243, 244, 246, 0.8);
                }
                
                .datatable-table th.sortable::after {
                    content: '↕️';
                    position: absolute;
                    right: 0.5rem;
                    opacity: 0.5;
                    font-size: 0.75rem;
                }
                
                .datatable-table th.sorted-asc::after {
                    content: '↑';
                    opacity: 1;
                    color: #0ea5e9;
                }
                
                .datatable-table th.sorted-desc::after {
                    content: '↓';
                    opacity: 1;
                    color: #0ea5e9;
                }
                
                .datatable-table tbody tr {
                    transition: all 0.2s;
                }
                
                .datatable-table tbody tr:hover {
                    background: rgba(240, 249, 255, 0.8);
                    transform: translateX(4px);
                    box-shadow: 4px 0 0 rgba(14, 165, 233, 0.3);
                }
                
                .datatable-table tbody tr.selected {
                    background: rgba(219, 234, 254, 0.8);
                }
                
                .datatable-pagination {
                    padding: 1rem 1.5rem;
                    background: rgba(249, 250, 251, 0.8);
                    border-top: 1px solid rgba(229, 231, 235, 0.8);
                    display: flex;
                    justify-content: between;
                    align-items: center;
                }
                
                .datatable-pagination-info {
                    font-size: 0.875rem;
                    color: #6b7280;
                }
                
                .datatable-pagination-controls {
                    display: flex;
                    gap: 0.25rem;
                    align-items: center;
                }
                
                .datatable-pagination-btn {
                    padding: 0.5rem 0.75rem;
                    border: 1px solid #d1d5db;
                    background: white;
                    border-radius: 0.375rem;
                    cursor: pointer;
                    font-size: 0.875rem;
                    transition: all 0.2s;
                }
                
                .datatable-pagination-btn:hover:not(:disabled) {
                    background: #f3f4f6;
                }
                
                .datatable-pagination-btn:disabled {
                    opacity: 0.5;
                    cursor: not-allowed;
                }
                
                .datatable-pagination-btn.active {
                    background: #0ea5e9;
                    color: white;
                    border-color: #0ea5e9;
                }
            `;
            document.head.appendChild(style);
        }

        return table;
    }

    renderFilters() {
        return `
            <div class="datatable-filters">
                <div class="datatable-search">
                    <input type="text" placeholder="Buscar..." class="datatable-search-input">
                </div>
            </div>
        `;
    }

    renderHeaders() {
        return `
            <thead>
                <tr>
                    ${this.config.selectable ? '<th><input type="checkbox" class="select-all"></th>' : ''}
                    ${this.config.columns.map(col => `
                        <th class="${this.config.sortable && col.sortable !== false ? 'sortable' : ''}" 
                            data-column="${col.key}">
                            ${col.label}
                        </th>
                    `).join('')}
                </tr>
            </thead>
        `;
    }

    renderBody() {
        if (this.isLoading) {
            return '<tbody><tr><td colspan="100%" class="text-center">Cargando...</td></tr></tbody>';
        }

        const data = this.getFilteredAndSortedData();
        const paginatedData = this.config.pagination ? this.getPaginatedData(data) : data;

        if (paginatedData.length === 0) {
            return `<tbody><tr><td colspan="100%" class="text-center">${this.config.emptyMessage}</td></tr></tbody>`;
        }

        return `
            <tbody>
                ${paginatedData.map((row, index) => `
                    <tr data-index="${index}" class="${this.selectedRows.has(index) ? 'selected' : ''}">
                        ${this.config.selectable ? `<td><input type="checkbox" class="row-select" ${this.selectedRows.has(index) ? 'checked' : ''}></td>` : ''}
                        ${this.config.columns.map(col => `
                            <td>${this.formatCellValue(row[col.key], col, row)}</td>
                        `).join('')}
                    </tr>
                `).join('')}
            </tbody>
        `;
    }

    renderPagination() {
        const totalPages = Math.ceil(this.getFilteredAndSortedData().length / this.config.pageSize);
        const startRecord = (this.currentPage - 1) * this.config.pageSize + 1;
        const endRecord = Math.min(this.currentPage * this.config.pageSize, this.getFilteredAndSortedData().length);

        return `
            <div class="datatable-pagination">
                <div class="datatable-pagination-info">
                    Mostrando ${startRecord}-${endRecord} de ${this.getFilteredAndSortedData().length} registros
                </div>
                <div class="datatable-pagination-controls">
                    <button class="datatable-pagination-btn" data-page="prev" ${this.currentPage === 1 ? 'disabled' : ''}>←</button>
                    ${this.renderPageNumbers(totalPages)}
                    <button class="datatable-pagination-btn" data-page="next" ${this.currentPage === totalPages ? 'disabled' : ''}>→</button>
                </div>
            </div>
        `;
    }

    renderPageNumbers(totalPages) {
        const pages = [];
        const maxVisible = 5;
        let start = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
        let end = Math.min(totalPages, start + maxVisible - 1);

        if (end - start + 1 < maxVisible) {
            start = Math.max(1, end - maxVisible + 1);
        }

        for (let i = start; i <= end; i++) {
            pages.push(`
                <button class="datatable-pagination-btn ${i === this.currentPage ? 'active' : ''}" 
                        data-page="${i}">${i}</button>
            `);
        }

        return pages.join('');
    }

    formatCellValue(value, column, row) {
        if (column.render && typeof column.render === 'function') {
            return column.render(value, row);
        }
        
        if (column.type === 'currency') {
            return '$' + parseFloat(value || 0).toLocaleString();
        }
        
        if (column.type === 'date') {
            return new Date(value).toLocaleDateString();
        }
        
        if (column.type === 'badge') {
            const badgeClass = column.badgeClass?.(value) || 'badge-secondary';
            return `<span class="badge ${badgeClass}">${value}</span>`;
        }

        return value || '';
    }

    bindEvents() {
        // Búsqueda
        const searchInput = this.element.querySelector('.datatable-search-input');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.filter('search', e.target.value);
            });
        }

        // Ordenamiento
        if (this.config.sortable) {
            const headers = this.element.querySelectorAll('th.sortable');
            headers.forEach(header => {
                header.addEventListener('click', () => {
                    const column = header.getAttribute('data-column');
                    this.sort(column);
                });
            });
        }

        // Paginación
        if (this.config.pagination) {
            const paginationBtns = this.element.querySelectorAll('.datatable-pagination-btn');
            paginationBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const page = btn.getAttribute('data-page');
                    this.goToPage(page);
                });
            });
        }

        // Selección
        if (this.config.selectable) {
            const selectAll = this.element.querySelector('.select-all');
            if (selectAll) {
                selectAll.addEventListener('change', (e) => {
                    this.selectAll(e.target.checked);
                });
            }

            const rowSelects = this.element.querySelectorAll('.row-select');
            rowSelects.forEach((select, index) => {
                select.addEventListener('change', (e) => {
                    this.selectRow(index, e.target.checked);
                });
            });
        }

        // Acciones
        const actionBtns = this.element.querySelectorAll('[data-action]');
        actionBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const action = btn.getAttribute('data-action');
                this.emit('action', { action, selectedRows: Array.from(this.selectedRows) });
            });
        });
    }

    // Métodos públicos
    setData(data) {
        this.config.data = data;
        this.refresh();
        return this;
    }

    filter(key, value) {
        if (value) {
            this.filters.set(key, value);
        } else {
            this.filters.delete(key);
        }
        this.currentPage = 1;
        this.refresh();
        return this;
    }

    sort(column) {
        if (this.sortColumn === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortColumn = column;
            this.sortDirection = 'asc';
        }
        this.refresh();
        return this;
    }

    goToPage(page) {
        if (page === 'prev') {
            this.currentPage = Math.max(1, this.currentPage - 1);
        } else if (page === 'next') {
            const totalPages = Math.ceil(this.getFilteredAndSortedData().length / this.config.pageSize);
            this.currentPage = Math.min(totalPages, this.currentPage + 1);
        } else {
            this.currentPage = parseInt(page);
        }
        this.refresh();
        return this;
    }

    selectRow(index, selected) {
        if (selected) {
            this.selectedRows.add(index);
        } else {
            this.selectedRows.delete(index);
        }
        this.emit('selection-change', { selectedRows: Array.from(this.selectedRows) });
        return this;
    }

    selectAll(selected) {
        this.selectedRows.clear();
        if (selected) {
            const data = this.getPaginatedData(this.getFilteredAndSortedData());
            data.forEach((_, index) => this.selectedRows.add(index));
        }
        this.refresh();
        this.emit('selection-change', { selectedRows: Array.from(this.selectedRows) });
        return this;
    }

    refresh() {
        if (!this.element) return;
        
        const tbody = this.element.querySelector('tbody');
        const pagination = this.element.querySelector('.datatable-pagination');
        
        if (tbody) {
            tbody.outerHTML = this.renderBody();
        }
        
        if (pagination && this.config.pagination) {
            pagination.outerHTML = this.renderPagination();
        }

        // Re-bind eventos
        this.unbindEvents();
        this.bindEvents();
        
        return this;
    }

    showLoading() {
        this.isLoading = true;
        const loading = this.element?.querySelector('.datatable-loading');
        if (loading) {
            loading.style.display = 'flex';
        }
        return this;
    }

    hideLoading() {
        this.isLoading = false;
        const loading = this.element?.querySelector('.datatable-loading');
        if (loading) {
            loading.style.display = 'none';
        }
        return this;
    }

    // Métodos privados
    getFilteredAndSortedData() {
        let data = [...this.config.data];

        // Aplicar filtros
        this.filters.forEach((value, key) => {
            if (key === 'search' && value) {
                data = data.filter(row => 
                    this.config.columns.some(col => 
                        String(row[col.key] || '').toLowerCase().includes(value.toLowerCase())
                    )
                );
            }
        });

        // Aplicar ordenamiento
        if (this.sortColumn) {
            data.sort((a, b) => {
                const aVal = a[this.sortColumn];
                const bVal = b[this.sortColumn];
                
                if (aVal < bVal) return this.sortDirection === 'asc' ? -1 : 1;
                if (aVal > bVal) return this.sortDirection === 'asc' ? 1 : -1;
                return 0;
            });
        }

        return data;
    }

    getPaginatedData(data) {
        if (!this.config.pagination) return data;
        
        const start = (this.currentPage - 1) * this.config.pageSize;
        const end = start + this.config.pageSize;
        return data.slice(start, end);
    }
}

// Toast/Notification Component
class Toast extends BaseComponent {
    get defaultConfig() {
        return {
            message: '',
            type: 'info', // success, error, warning, info
            duration: 5000,
            position: 'top-right', // top-left, top-right, bottom-left, bottom-right
            closable: true,
            icon: true
        };
    }

    render() {
        const toast = document.createElement('div');
        toast.className = `toast toast-${this.config.type} toast-${this.config.position}`;
        
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };

        toast.innerHTML = `
            <div class="toast-content">
                ${this.config.icon ? `<span class="toast-icon">${icons[this.config.type] || icons.info}</span>` : ''}
                <span class="toast-message">${this.config.message}</span>
                ${this.config.closable ? '<button class="toast-close">&times;</button>' : ''}
            </div>
            <div class="toast-progress"></div>
        `;

        // CSS del toast
        if (!document.querySelector('#toast-styles')) {
            const style = document.createElement('style');
            style.id = 'toast-styles';
            style.textContent = `
                .toast {
                    position: fixed;
                    z-index: 1100;
                    min-width: 300px;
                    max-width: 500px;
                    background: rgba(255, 255, 255, 0.95);
                    backdrop-filter: blur(20px);
                    -webkit-backdrop-filter: blur(20px);
                    border-radius: 0.75rem;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                    border: 1px solid rgba(255, 255, 255, 0.2);
                    overflow: hidden;
                    animation: toastSlideIn 0.3s ease-out;
                }
                
                .toast-top-right { top: 1rem; right: 1rem; }
                .toast-top-left { top: 1rem; left: 1rem; }
                .toast-bottom-right { bottom: 1rem; right: 1rem; }
                .toast-bottom-left { bottom: 1rem; left: 1rem; }
                
                .toast-content {
                    padding: 1rem 1.5rem;
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                }
                
                .toast-icon {
                    font-size: 1.25rem;
                    flex-shrink: 0;
                }
                
                .toast-message {
                    flex: 1;
                    font-weight: 500;
                    color: #374151;
                }
                
                .toast-close {
                    background: none;
                    border: none;
                    font-size: 1.25rem;
                    cursor: pointer;
                    padding: 0;
                    color: #9ca3af;
                    transition: color 0.2s;
                }
                
                .toast-close:hover {
                    color: #374151;
                }
                
                .toast-progress {
                    height: 3px;
                    background: rgba(0, 0, 0, 0.1);
                    position: relative;
                    overflow: hidden;
                }
                
                .toast-progress::after {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    height: 100%;
                    background: currentColor;
                    width: 100%;
                    animation: toastProgress var(--duration, 5000ms) linear;
                }
                
                .toast-success { color: #22c55e; }
                .toast-error { color: #ef4444; }
                .toast-warning { color: #f59e0b; }
                .toast-info { color: #0ea5e9; }
                
                @keyframes toastSlideIn {
                    from {
                        opacity: 0;
                        transform: translateX(100%);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(0);
                    }
                }
                
                @keyframes toastProgress {
                    from { width: 100%; }
                    to { width: 0; }
                }
            `;
            document.head.appendChild(style);
        }

        return toast;
    }

    bindEvents() {
        if (this.config.closable) {
            const closeBtn = this.element.querySelector('.toast-close');
            closeBtn?.addEventListener('click', () => this.hide());
        }

        // Auto-hide
        if (this.config.duration > 0) {
            this.element.style.setProperty('--duration', this.config.duration + 'ms');
            this.autoHideTimer = setTimeout(() => {
                this.hide();
            }, this.config.duration);
        }
    }

    show() {
        document.body.appendChild(this.element);
        this.isVisible = true;
        this.emit('show');
        return this;
    }

    hide() {
        if (this.autoHideTimer) {
            clearTimeout(this.autoHideTimer);
        }
        
        this.element.style.animation = 'toastSlideIn 0.3s ease-out reverse';
        setTimeout(() => {
            this.unmount();
            this.isVisible = false;
            this.emit('hide');
        }, 300);
        return this;
    }
}

// Inicializar sistema de componentes
window.addEventListener('DOMContentLoaded', function() {
    window.componentSystem = new ComponentSystem();
    
    // Registrar componentes
    window.componentSystem.register('Modal', Modal);
    window.componentSystem.register('DataTable', DataTable);
    window.componentSystem.register('Toast', Toast);
    
    // Funciones de utilidad globales
    window.showModal = function(config) {
        const modal = window.componentSystem.create('Modal', config);
        modal.show();
        return modal;
    };
    
    window.showToast = function(message, type = 'info', options = {}) {
        const toast = window.componentSystem.create('Toast', {
            message,
            type,
            ...options
        });
        toast.show();
        return toast;
    };
    
    window.createDataTable = function(container, config) {
        const table = window.componentSystem.create('DataTable', config);
        table.mount(container);
        return table;
    };
});

// Exportar para uso global
window.ComponentSystem = ComponentSystem;
window.BaseComponent = BaseComponent;
window.Modal = Modal;
window.DataTable = DataTable;
window.Toast = Toast;