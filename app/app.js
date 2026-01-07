const API_URL = 'https://nestorcornejo.com/carlos-inventarios/api/producto';
const CIERRE_API_URL = 'https://nestorcornejo.com/carlos-inventarios/api/cierre_inventario';
let productModal;
let cierreModal;
let currentPage = 1;
let currentCierrePage = 1;
let currentSearch = '';

// Inicialización al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    const productModalEl = document.getElementById('productModal');
    const cierreModalEl = document.getElementById('cierreModal');
    
    if (productModalEl && typeof bootstrap !== 'undefined') {
        productModal = new bootstrap.Modal(productModalEl);
    }
    if (cierreModalEl && typeof bootstrap !== 'undefined') {
        cierreModal = new bootstrap.Modal(cierreModalEl);
    }
    
    // Set default dates to today
    const fechaInicioEl = document.getElementById('fechaInicio');
    const fechaFinalEl = document.getElementById('fechaFinal');
    if (fechaInicioEl && fechaFinalEl) {
        const today = new Date().toISOString().split('T')[0];
        fechaInicioEl.value = today;
        fechaFinalEl.value = today;
    }
    
    loadProducts(1, currentSearch);
    
    // Navigation
    const dashboardLink = document.getElementById('dashboardLink');
    const productosLink = document.getElementById('productosLink');
    const cierresLink = document.getElementById('cierresLink');
    if (dashboardLink) {
        dashboardLink.addEventListener('click', (e) => {
            e.preventDefault();
            showSection('dashboard');
            loadDashboard();
        });
    }
    if (productosLink) {
        productosLink.addEventListener('click', (e) => {
            e.preventDefault();
            showSection('productos');
            loadProducts(1, currentSearch);
        });
    }
    if (cierresLink) {
        cierresLink.addEventListener('click', (e) => {
            e.preventDefault();
            showSection('cierres');
            loadCierres();
        });
    }

    // Cargar dashboard inicial
    loadDashboard();

    // Search on Enter
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                searchProducts();
            }
        });
    }
});

function showSection(section) {
    document.getElementById('dashboardSection').style.display = section === 'dashboard' ? 'block' : 'none';
    document.getElementById('productosSection').style.display = section === 'productos' ? 'block' : 'none';
    document.getElementById('cierresSection').style.display = section === 'cierres' ? 'block' : 'none';
    document.getElementById('pageTitle').textContent = section === 'dashboard' ? 'Dashboard' : (section === 'productos' ? 'Listado de Productos' : 'Listado de Cierres');
    document.getElementById('dashboardLink').classList.toggle('active', section === 'dashboard');
    document.getElementById('productosLink').classList.toggle('active', section === 'productos');
    document.getElementById('cierresLink').classList.toggle('active', section === 'cierres');
}

// Función para cargar datos del dashboard
async function loadDashboard() {
    try {
        // Fecha actual
        const today = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('currentDate').textContent = today.toLocaleDateString('es-ES', options);

        // Total productos
        const productosResponse = await fetch(`${API_URL}?all=true`);
        const productosData = await productosResponse.json();
        document.getElementById('totalProductos').textContent = productosData.state === 1 ? productosData.data.length : 'Error';

        // Cierres hoy
        const fechaHoy = today.toISOString().split('T')[0];
        const cierresHoyResponse = await fetch(`${CIERRE_API_URL}/rango?fecha_inicio=${fechaHoy}&fecha_final=${fechaHoy}&page=1&limit=10000`);
        const cierresHoyData = await cierresHoyResponse.json();
        document.getElementById('cierresHoy').textContent = cierresHoyData.state === 1 ? cierresHoyData.data.length : 'Error';

        // Total cierres
        const cierresResponse = await fetch(`${CIERRE_API_URL}?all=true`);
        const cierresData = await cierresResponse.json();
        document.getElementById('totalCierres').textContent = cierresData.state === 1 ? cierresData.data.length : 'Error';
    } catch (error) {
        console.error('Error cargando dashboard:', error);
    }
}

// Función para mostrar/ocultar loading
function setLoading(isLoading) {
    const loadingRow = document.getElementById('loadingRow');
    if (loadingRow) {
        loadingRow.style.display = isLoading ? 'table-row' : 'none';
    }
}

function setCierreLoading(isLoading) {
    const loadingRow = document.getElementById('cierreLoadingRow');
    if (loadingRow) {
        loadingRow.style.display = isLoading ? 'table-row' : 'none';
    }
}

// Función para obtener y listar productos (GET)
window.loadProducts = async function(page = 1, search = '') {
    currentPage = page;
    currentSearch = search;
    setLoading(true);
    let url = `${API_URL}?page=${page}`;
    if (search) {
        url += `&search=${encodeURIComponent(search)}`;
    }
    try {
        const response = await fetch(url);
        const result = await response.json();

        if (result.state === 1) {
            renderTable(result.data);
            renderPagination(result.pagination);
        } else {
            console.error('Error del servidor:', result.message);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar los productos: ' + result.message
            });
        }
    } catch (error) {
        console.error('Error de red:', error);
    } finally {
        setLoading(false);
    }
}

// Función para buscar productos
window.searchProducts = function() {
    const searchTerm = document.getElementById('searchInput').value.trim();
    loadProducts(1, searchTerm);
    Swal.fire({
        icon: 'success',
        title: 'Búsqueda realizada',
        text: 'Los productos han sido filtrados por el término de búsqueda.',
        timer: 2000,
        showConfirmButton: false
    });
}

// Renderizar la tabla HTML
function renderTable(products) {
    const tbody = document.getElementById('productTableBody');
    tbody.innerHTML = '';

    if (products.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay productos registrados</td></tr>';
        return;
    }

    products.forEach(product => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${product.id_producto}</td>
            <td>${product.nombre_producto}</td>
            <td>${product.unidad_medida_producto}</td>
            <td>${product.categoria_producto}</td>
            <td class="text-center">
                <button class="btn btn-sm btn-warning me-2" onclick="editProduct(${product.id_producto})"><i class="bi bi-pencil"></i> Editar</button>
                <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id_producto})"><i class="bi bi-trash"></i> Eliminar</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Renderizar controles de paginación
function renderPagination(pagination) {
    const container = document.getElementById('paginationContainer');
    container.innerHTML = '';

    if (pagination.total_pages <= 1) return;

    // Botón Anterior
    const prevLi = document.createElement('li');
    prevLi.className = `page-item ${pagination.current_page === 1 ? 'disabled' : ''}`;
    prevLi.innerHTML = `<a class="page-link" href="#" onclick="loadProducts(${pagination.current_page - 1})">Anterior</a>`;
    container.appendChild(prevLi);

    // Números de página
    for (let i = 1; i <= pagination.total_pages; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === pagination.current_page ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="#" onclick="loadProducts(${i})">${i}</a>`;
        container.appendChild(li);
    }

    // Botón Siguiente
    const nextLi = document.createElement('li');
    nextLi.className = `page-item ${pagination.current_page === pagination.total_pages ? 'disabled' : ''}`;
    nextLi.innerHTML = `<a class="page-link" href="#" onclick="loadProducts(${pagination.current_page + 1})">Siguiente</a>`;
    container.appendChild(nextLi);
}

// Abrir modal para crear
window.openModal = function() {
    if (!productModal) {
        console.error('Modal not initialized');
        return;
    }
    document.getElementById('productId').value = '';
    document.getElementById('nombre').value = '';
    document.getElementById('unidad').value = '';
    document.getElementById('categoria').value = '';
    document.getElementById('modalTitle').innerText = 'Nuevo Producto';
    productModal.show();
};

// Abrir modal para editar (llenar datos)
window.editProduct = async function(id) {
    if (!productModal) {
        console.error('Modal not initialized');
        return;
    }
    try {
        const response = await fetch(`${API_URL}/${id}`);
        const result = await response.json();
        if (result.state === 1) {
            document.getElementById('productId').value = result.data.id_producto;
            document.getElementById('nombre').value = result.data.nombre_producto;
            document.getElementById('unidad').value = result.data.unidad_medida_producto;
            document.getElementById('categoria').value = result.data.categoria_producto;
            document.getElementById('modalTitle').innerText = 'Editar Producto';
            productModal.show();
        }
    } catch (error) {
        console.error('Error cargando producto:', error);
    }
};

// Guardar producto (POST o PUT)
async function saveProduct() {
    const id = document.getElementById('productId').value;
    const data = {
        nombre_producto: document.getElementById('nombre').value.trim(),
        unidad_medida_producto: document.getElementById('unidad').value,
        categoria_producto: document.getElementById('categoria').value
    };

    if (!data.nombre_producto || !data.unidad_medida_producto || !data.categoria_producto) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Por favor, ingrese un nombre y seleccione una unidad de medida y una categoría.'
        });
        return;
    }

    const method = id ? 'PUT' : 'POST';
    const url = id ? `${API_URL}/${id}` : API_URL;

    try {
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();

        if (result.state === 1) {
            if (productModal) productModal.hide();
            loadProducts(currentPage, currentSearch); // Recargar tabla
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: result.message
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al guardar: ' + result.message
            });
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Eliminar producto (DELETE)
window.deleteProduct = async function(id) {
    const resultConfirm = await Swal.fire({
        title: '¿Está seguro?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminarlo!',
        cancelButtonText: 'Cancelar'
    });

    if (resultConfirm.isConfirmed) {
        try {
            const response = await fetch(`${API_URL}/${id}`, { method: 'DELETE' });
            const result = await response.json();

            if (result.state === 1) {
                loadProducts(currentPage, currentSearch);
                Swal.fire('Eliminado!', result.message, 'success');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al eliminar: ' + result.message
                });
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
}

// ==================== FUNCIONES PARA CIERRES ====================

// Función para obtener y listar cierres (GET)
window.loadCierres = async function(page = 1, showNotification = false) {
    currentCierrePage = page;
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFinal = document.getElementById('fechaFinal').value;
    setCierreLoading(true);
    try {
        const response = await fetch(`${CIERRE_API_URL}/rango?fecha_inicio=${fechaInicio}&fecha_final=${fechaFinal}&page=${page}`);
        const result = await response.json();

        if (result.state === 1) {
            renderCierreTable(result.data);
            renderCierrePagination(result.pagination);
            if (showNotification) {
                Swal.fire({
                    icon: 'success',
                    title: 'Filtrado realizado',
                    text: 'Los cierres han sido filtrados por el rango de fechas.',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        } else {
            console.error('Error del servidor:', result.message);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar los cierres: ' + result.message
            });
        }
    } catch (error) {
        console.error('Error de red:', error);
    } finally {
        setCierreLoading(false);
    }
}

// Función para exportar a PDF
window.exportToPDF = async function() {
    const fechaInicio = document.getElementById('fechaInicio').value;
    const fechaFinal = document.getElementById('fechaFinal').value;

    try {
        // Obtener todos los datos (asumiendo que la API soporta un límite alto)
        const response = await fetch(`${CIERRE_API_URL}/rango?fecha_inicio=${fechaInicio}&fecha_final=${fechaFinal}&page=1&limit=10000`);
        const result = await response.json();

        if (result.state !== 1) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron obtener los datos para exportar.'
            });
            return;
        }

        const data = result.data;

        // Procesar datos
        const processedData = data.map((item, index) => ({
            '#': index + 1,
            'ID': item.id_cierre_invetarios.toString().padStart(4, '0'),
            'Fecha': item.fecha,
            'Día': new Date(item.fecha + 'T00:00:00').toLocaleDateString('es-ES', { weekday: 'long' }),
            'Producto': item.nombre_producto,
            'Unidad de Medida': item.unidad_medida_producto,
            'Categoría': item.categoria_producto,
            'Cantidad': item.cantidad
        }));

        // Generar PDF
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('landscape');

        // Cabecera
        doc.setFontSize(16);
        doc.text(`Cierres de Inventario - Del ${fechaInicio} al ${fechaFinal}`, 10, 20);

        // Tabla
        doc.autoTable({
            head: [['#', 'ID', 'Fecha', 'Día', 'Producto', 'Unidad de Medida', 'Categoría', 'Cantidad']],
            body: processedData.map(item => [
                item['#'],
                item['ID'],
                item['Fecha'],
                item['Día'],
                item['Producto'],
                item['Unidad de Medida'],
                item['Categoría'],
                item['Cantidad']
            ]),
            startY: 30,
            styles: { fontSize: 8 },
            headStyles: { fillColor: [41, 128, 185] },
        });

        // Descargar
        doc.save(`cierres_${fechaInicio}_a_${fechaFinal}.pdf`);

        Swal.fire({
            icon: 'success',
            title: 'Exportado',
            text: 'El PDF se ha descargado correctamente.'
        });

    } catch (error) {
        console.error('Error exportando PDF:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Hubo un problema al exportar el PDF.'
        });
    }
}

// Renderizar la tabla de cierres
function renderCierreTable(cierres) {
    const tbody = document.getElementById('cierreTableBody');
    tbody.innerHTML = '';

    if (cierres.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay cierres registrados</td></tr>';
        return;
    }

    cierres.forEach(cierre => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>${cierre.id_cierre_invetarios}</td>
            <td>${cierre.nombre_producto}</td>
            <td>${cierre.fecha}</td>
            <td>${cierre.cantidad}</td>
            <td class="text-center">
                <button class="btn btn-warning btn-sm me-2" onclick="editCierre(${cierre.id_cierre_invetarios})"><i class="bi bi-pencil"></i> Editar</button>
                <button class="btn btn-danger btn-sm" onclick="deleteCierre(${cierre.id_cierre_invetarios})"><i class="bi bi-trash"></i> Eliminar</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
}

// Renderizar paginación para cierres
function renderCierrePagination(pagination) {
    const container = document.getElementById('cierrePaginationContainer');
    container.innerHTML = '';

    if (pagination.last_page <= 1) return;

    for (let i = 1; i <= pagination.last_page; i++) {
        const li = document.createElement('li');
        li.className = `page-item ${i === pagination.current_page ? 'active' : ''}`;
        li.innerHTML = `<a class="page-link" href="#" onclick="loadCierres(${i})">${i}</a>`;
        container.appendChild(li);
    }
}

// Abrir modal para crear/editar cierre
window.openCierreModal = async function(id = null) {
    if (!cierreModal) return;
    await loadProductosForSelect();
    document.getElementById('cierreId').value = id || '';
    document.getElementById('cierreModalTitle').textContent = id ? 'Editar Cierre' : 'Nuevo Cierre';
    
    if (id) {
        // Cargar datos para editar
        try {
            const response = await fetch(`${CIERRE_API_URL}/${id}`);
            const result = await response.json();
            if (result.state === 1) {
                document.getElementById('cierreProducto').value = result.data.fk_id_producto;
                document.getElementById('cierreFecha').value = result.data.fecha;
                document.getElementById('cierreCantidad').value = result.data.cantidad;
            }
        } catch (error) {
            console.error('Error cargando cierre:', error);
        }
    } else {
        // Resetear campos
        document.getElementById('cierreProducto').value = '';
        document.getElementById('cierreFecha').value = new Date().toISOString().split('T')[0];
        document.getElementById('cierreCantidad').value = '';
    }
    
    cierreModal.show();
}

// Cargar productos para el select
async function loadProductosForSelect() {
    try {
        const response = await fetch(`${API_URL}?all=true`);
        const result = await response.json();
        if (result.state === 1) {
            window.cierreProductosList = result.data;
            const input = document.getElementById('cierreProductoInput');
            const list = document.getElementById('cierreProductoList');
            input.value = '';
            document.getElementById('cierreProducto').value = '';
            list.innerHTML = '';
            list.style.display = 'none';

            input.addEventListener('input', function() {
                const term = input.value.trim().toLowerCase();
                list.innerHTML = '';
                if (!term) {
                    list.style.display = 'none';
                    return;
                }
                const filtered = window.cierreProductosList.filter(p => p.nombre_producto.toLowerCase().includes(term));
                if (filtered.length === 0) {
                    list.style.display = 'none';
                    return;
                }
                filtered.forEach(product => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = product.nombre_producto;
                    item.dataset.id = product.id_producto;
                    item.addEventListener('click', function() {
                        input.value = product.nombre_producto;
                        document.getElementById('cierreProducto').value = product.id_producto;
                        list.style.display = 'none';
                    });
                    list.appendChild(item);
                });
                list.style.display = 'block';
            });

            // Ocultar lista si se hace click fuera
            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !list.contains(e.target)) {
                    list.style.display = 'none';
                }
            });
        }
    } catch (error) {
        console.error('Error cargando productos:', error);
    }
}

// Guardar cierre (POST/PUT)
async function saveCierre() {
    const id = document.getElementById('cierreId').value;
    const data = {
        fk_id_producto: document.getElementById('cierreProducto').value,
        fecha: document.getElementById('cierreFecha').value,
        cantidad: document.getElementById('cierreCantidad').value
    };
    // Validar que el producto exista
    if (!data.fk_id_producto) {
        Swal.fire({
            icon: 'warning',
            title: 'Producto no seleccionado',
            text: 'Seleccione un producto válido de la lista.'
        });
        return;
    }

    if (!data.fk_id_producto || !data.fecha || !data.cantidad) {
        Swal.fire({
            icon: 'warning',
            title: 'Campos incompletos',
            text: 'Por favor, complete todos los campos.'
        });
        return;
    }

    const method = id ? 'PUT' : 'POST';
    const url = id ? `${CIERRE_API_URL}/${id}` : CIERRE_API_URL;

    try {
        const response = await fetch(url, {
            method: method,
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const result = await response.json();

        if (result.state === 1) {
            if (cierreModal) cierreModal.hide();
            loadCierres(); // Recargar tabla
            Swal.fire({
                icon: 'success',
                title: '¡Éxito!',
                text: result.message
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al guardar: ' + result.message
            });
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Editar cierre
window.editCierre = function(id) {
    openCierreModal(id);
}

// Eliminar cierre (DELETE)
window.deleteCierre = async function(id) {
    const resultConfirm = await Swal.fire({
        title: '¿Está seguro?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminarlo!',
        cancelButtonText: 'Cancelar'
    });

    if (resultConfirm.isConfirmed) {
        try {
            const response = await fetch(`${CIERRE_API_URL}/${id}`, { method: 'DELETE' });
            const result = await response.json();

            if (result.state === 1) {
                loadCierres();
                Swal.fire('Eliminado!', result.message, 'success');
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error al eliminar: ' + result.message
                });
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
}