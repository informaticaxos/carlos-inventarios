const API_URL = 'https://nestorcornejo.com/carlos-inventarios/api/producto';
let productModal;
let currentPage = 1;

// Inicialización al cargar la página
document.addEventListener('DOMContentLoaded', () => {
    productModal = new bootstrap.Modal(document.getElementById('productModal'));
    loadProducts();
});

// Función para obtener y listar productos (GET)
async function loadProducts(page = 1) {
    currentPage = page;
    try {
        const response = await fetch(`${API_URL}?page=${page}`);
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
                <button class="btn btn-sm btn-warning me-2" onclick='editProduct(${JSON.stringify(product)})'>Editar</button>
                <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id_producto})">Eliminar</button>
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
    document.getElementById('productId').value = '';
    document.getElementById('nombre').value = '';
    document.getElementById('unidad').value = '';
    document.getElementById('categoria').value = '';
    document.getElementById('modalTitle').innerText = 'Nuevo Producto';
    productModal.show();
}

// Abrir modal para editar (llenar datos)
function editProduct(product) {
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
            productModal.hide();
            loadProducts(); // Recargar tabla
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
                loadProducts();
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