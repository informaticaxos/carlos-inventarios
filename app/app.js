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
        dashboardLink.addEventListener('click', () => {
            showSection('dashboard');
        });
    }
    if (productosLink) {
        productosLink.addEventListener('click', () => {
            showSection('productos');
            loadProducts(1, currentSearch);
        });
    }
    if (cierresLink) {
        cierresLink.addEventListener('click', () => {
            showSection('cierres');
            loadCierres();
        });
    }

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
async function loadProducts(page = 1, search = '') {
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
                <button class="btn btn-sm btn-warning me-2" onclick='editProduct(${JSON.stringify(product)})'><i class="bi bi-pencil"></i> Editar</button>
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
function openModal() {
    if (!productModal) return;
    document.getElementById('productId').value = '';
    document.getElementById('nombre').value = '';
    document.getElementById('unidad').value = '';
    document.getElementById('categoria').value = '';
    document.getElementById('modalTitle').innerText = 'Nuevo Producto';
    productModal.show();
}

// Abrir modal para editar (llenar datos)
function editProduct(product) {
    if (!productModal) return;
    document.getElementById('productId').value = product.id_producto;
    document.getElementById('nombre').value = product.nombre_producto;
    document.getElementById('unidad').value = product.unidad_medida_producto;
    document.getElementById('categoria').value = product.categoria_producto;
    document.getElementById('modalTitle').innerText = 'Editar Producto';
    productModal.show();
}

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
async function deleteProduct(id) {
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
async function loadCierres(page = 1) {
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
async function exportToPDF() {
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
            'Día': new Date(item.fecha).toLocaleDateString('es-ES', { weekday: 'long' }),
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
                <button class="btn btn-warning btn-sm" onclick="editCierre(${cierre.id_cierre_invetarios})">Editar</button>
                <button class="btn btn-danger btn-sm" onclick="deleteCierre(${cierre.id_cierre_invetarios})">Eliminar</button>
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
async function openCierreModal(id = null) {
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
        const response = await fetch(`${API_URL}?page=1`);
        const result = await response.json();
        if (result.state === 1) {
            const select = document.getElementById('cierreProducto');
            select.innerHTML = '<option value="">Seleccione...</option>';
            result.data.forEach(product => {
                const option = document.createElement('option');
                option.value = product.id_producto;
                option.textContent = product.nombre_producto;
                select.appendChild(option);
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
function editCierre(id) {
    openCierreModal(id);
}

// Eliminar cierre (DELETE)
async function deleteCierre(id) {
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