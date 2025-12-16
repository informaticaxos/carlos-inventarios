# Dashboard de Inventarios - Frontend

Este es el frontend del sistema de inventarios, construido con Bootstrap 5, jQuery y SweetAlert2.

## Características

- **Interfaz responsiva**: Diseñada para funcionar en desktop y móviles
- **Gestión de Productos**: CRUD completo para productos
- **Gestión de Cierres de Inventario**: CRUD completo para cierres de inventario
- **Tablas interactivas**: Con DataTables para búsqueda, ordenamiento y paginación
- **Modales**: Formularios modales para crear y editar registros
- **Notificaciones**: SweetAlert2 para feedback al usuario

## Estructura de archivos

```
app/
├── index.html          # Archivo principal del dashboard
└── README.md          # Este archivo
```

## Configuración

### 1. URL de la API

En el archivo `index.html`, modifica la variable `API_BASE_URL` en la línea 178:

```javascript
const API_BASE_URL = 'https://nestorcornejo.com/carlos-inventarios/api'; // Cambia esto por tu URL real
```

Por ejemplo:
- Desarrollo local: `http://localhost/carlos-inventarios/api`
- Producción: `https://nestorcornejo.com/carlos-inventarios/api`

### 2. Despliegue

Sube la carpeta `app/` completa a tu servidor web. El archivo `index.html` debe estar accesible desde el navegador.

## Funcionalidades

### Gestión de Productos

- **Ver productos**: Tabla con todos los productos registrados
- **Crear producto**: Modal con formulario para nuevo producto
- **Editar producto**: Modal con datos precargados para edición
- **Eliminar producto**: Confirmación con SweetAlert antes de eliminar

### Gestión de Cierres de Inventario

- **Ver cierres**: Tabla con todos los cierres registrados
- **Crear cierre**: Modal con formulario para nuevo cierre (seleccionar producto, fecha, cantidad)
- **Editar cierre**: Modal con datos precargados para edición
- **Eliminar cierre**: Confirmación con SweetAlert antes de eliminar

## Campos de formularios

### Producto
- **Nombre del Producto** (requerido): Nombre descriptivo del producto
- **Unidad de Medida** (requerido): Unidad en la que se mide el producto (kg, litros, unidades, etc.)
- **Categoría** (requerido): Categoría o tipo de producto

### Cierre de Inventario
- **Producto** (requerido): Seleccionar de la lista de productos existentes
- **Fecha** (requerido): Fecha del cierre de inventario
- **Cantidad** (requerido): Cantidad registrada en el cierre

## Tecnologías utilizadas

- **Bootstrap 5**: Framework CSS para diseño responsivo
- **jQuery**: Para manipulación del DOM y AJAX
- **DataTables**: Para tablas interactivas
- **SweetAlert2**: Para notificaciones y confirmaciones
- **Bootstrap Icons**: Para iconos

## Navegación

- **Sidebar**: Navegación lateral con opciones de Productos y Cierres de Inventario
- **Responsive**: En móviles, el sidebar se oculta y se muestra con un botón hamburguesa
- **Secciones**: Solo una sección visible a la vez

## API Endpoints utilizados

El frontend consume los siguientes endpoints del backend:

### Productos
- `GET /api/producto` - Obtener todos los productos
- `GET /api/producto/{id}` - Obtener producto específico
- `POST /api/producto` - Crear nuevo producto
- `PUT /api/producto/{id}` - Actualizar producto
- `DELETE /api/producto/{id}` - Eliminar producto

### Cierres de Inventario
- `GET /api/cierre_inventario` - Obtener todos los cierres
- `GET /api/cierre_inventario/{id}` - Obtener cierre específico
- `POST /api/cierre_inventario` - Crear nuevo cierre
- `PUT /api/cierre_inventario/{id}` - Actualizar cierre
- `DELETE /api/cierre_inventario/{id}` - Eliminar cierre

## Notas importantes

1. **Dependencias del backend**: Asegúrate de que el backend esté funcionando correctamente antes de usar el frontend
2. **CORS**: Si hay problemas de CORS, configura el servidor para permitir requests desde el dominio del frontend
3. **Seguridad**: Considera implementar autenticación si es necesario para producción
4. **Validación**: La validación se hace tanto en frontend (HTML5) como en backend

## Soporte móvil

El dashboard está completamente optimizado para dispositivos móviles:
- Sidebar colapsable
- Tablas responsivas con scroll horizontal
- Botones y formularios adaptados al tamaño de pantalla
- Toque optimizado para interacciones
