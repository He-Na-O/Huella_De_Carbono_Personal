// admin_functions.js - Funciones para conectar el admin con la base de datos

// URL base de la API
const API_URL = '/brainer/huella-de-carbono-personal/php/admin_data.php';

// Al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Admin cargado, conectando con BD...');
    cargarDashboard();
});

// 📊 FUNCIONES PARA EL DASHBOARD
async function cargarDashboard() {
    try {
        console.log('📊 Cargando datos del dashboard...');
        
        const respuesta = await fetch(`${API_URL}?accion=dashboard`);
        const datos = await respuesta.json();
        
        if (datos.exito) {
            actualizarEstadisticas(datos.datos);
            cargarActividadReciente();
            console.log('✅ Dashboard cargado correctamente');
        } else {
            console.error('❌ Error al cargar dashboard:', datos);
        }
        
    } catch (error) {
        console.error('❌ Error de conexión:', error);
        mostrarError('Error de conexión con el servidor');
    }
}

function actualizarEstadisticas(stats) {
    const tarjetas = document.querySelectorAll('.stats-number');
    
    if (tarjetas.length >= 4) {
        tarjetas[0].textContent = (stats.usuarios || 0).toLocaleString();
        tarjetas[1].textContent = (stats.evaluaciones || 0).toLocaleString();
        tarjetas[2].textContent = (stats.resenas || 0).toLocaleString();
        tarjetas[3].textContent = (stats.contactos || 0).toLocaleString();
    }
}

async function cargarActividadReciente() {
    try {
        const respuesta = await fetch(`${API_URL}?accion=actividad`);
        const datos = await respuesta.json();
        
        if (datos.exito && datos.actividades) {
            actualizarTablaActividad(datos.actividades);
        }
    } catch (error) {
        console.error('Error cargando actividad:', error);
    }
}

function actualizarTablaActividad(actividades) {
    const tabla = document.querySelector('#dashboard table tbody');
    
    if (tabla && actividades.length > 0) {
        tabla.innerHTML = '';
        
        actividades.slice(0, 4).forEach(actividad => {
            const fila = `
                <tr>
                    <td>${formatearFecha(actividad.fecha)}</td>
                    <td>${actividad.usuario}</td>
                    <td>${actividad.accion}</td>
                    <td><span class="badge bg-success">${actividad.estado}</span></td>
                </tr>
            `;
            tabla.innerHTML += fila;
        });
    }
}

// 👥 FUNCIONES PARA USUARIOS
async function cargarUsuarios() {
    try {
        console.log('👥 Cargando usuarios...');
        
        const respuesta = await fetch(`${API_URL}?accion=usuarios`);
        const datos = await respuesta.json();
        
        if (datos.exito) {
            actualizarTablaUsuarios(datos.usuarios);
            console.log('✅ Usuarios cargados:', datos.usuarios.length);
        }
    } catch (error) {
        console.error('❌ Error cargando usuarios:', error);
    }
}

function actualizarTablaUsuarios(usuarios) {
    const tabla = document.getElementById('usersTableBody');
    
    if (tabla) {
        tabla.innerHTML = '';
        
        usuarios.forEach(usuario => {
            const fila = `
                <tr>
                    <td>${String(usuario.id).padStart(3, '0')}</td>
                    <td>${usuario.nombre}</td>
                    <td>${usuario.email}</td>
                    <td>${formatearFecha(usuario.fecha)}</td>
                    <td><span class="badge bg-success">${usuario.estado}</span></td>
                    <td>
                        <button class="action-btn btn-view" title="Ver" onclick="verUsuario(${usuario.id})">
                            <i class="bi bi-eye"></i>
                        </button>
                        <button class="action-btn btn-edit" title="Editar" onclick="editarUsuario(${usuario.id})">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="action-btn btn-delete" title="Eliminar" onclick="eliminarUsuario(${usuario.id})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
            tabla.innerHTML += fila;
        });
    }
}

// 📧 FUNCIONES PARA CONTACTOS
async function cargarContactos() {
    try {
        console.log('📧 Cargando contactos...');
        
        const respuesta = await fetch(`${API_URL}?accion=contactos`);
        const datos = await respuesta.json();
        
        if (datos.exito) {
            mostrarContactos(datos.contactos);
            console.log('✅ Contactos cargados:', datos.contactos.length);
        }
    } catch (error) {
        console.error('❌ Error cargando contactos:', error);
    }
}

function mostrarContactos(contactos) {
    const seccionContactos = document.getElementById('contacts');
    
    if (seccionContactos && contactos.length > 0) {
        const tablaHTML = `
            <div class="dashboard-card">
                <h5><i class="bi bi-envelope me-2"></i>Mensajes de Contacto</h5>
                <div class="custom-alert alert-info mb-3">
                    <i class="bi bi-info-circle me-2"></i>
                    Total de mensajes: ${contactos.length}
                </div>
                <div class="admin-table">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Mensaje</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${contactos.map(contacto => `
                                <tr>
                                    <td>${contacto.id}</td>
                                    <td>${contacto.nombre}</td>
                                    <td>${contacto.email}</td>
                                    <td>${contacto.asunto}...</td>
                                    <td>${formatearFecha(contacto.fecha)}</td>
                                    <td><span class="badge bg-primary">${contacto.estado}</span></td>
                                    <td>
                                        <button class="action-btn btn-view" title="Ver completo" onclick='verContactoCompleto(${JSON.stringify(contacto)})'>
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
        
        seccionContactos.innerHTML = tablaHTML;
    } else if (seccionContactos) {
        seccionContactos.innerHTML = `
            <div class="dashboard-card">
                <h5><i class="bi bi-envelope me-2"></i>Mensajes de Contacto</h5>
                <div class="custom-alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    No hay mensajes de contacto aún.
                </div>
            </div>
        `;
    }
}

function verContactoCompleto(contacto) {
    alert(`
        Nombre: ${contacto.nombre}
        Email: ${contacto.email}
        Fecha: ${formatearFecha(contacto.fecha)}
        
        Mensaje:
        ${contacto.mensaje_completo || contacto.asunto}
    `);
}

// ⭐ FUNCIONES PARA RESEÑAS
async function cargarResenas() {
    try {
        console.log('⭐ Cargando reseñas...');
        
        const respuesta = await fetch(`${API_URL}?accion=resenas`);
        const datos = await respuesta.json();
        
        if (datos.exito) {
            mostrarResenas(datos.resenas);
            console.log('✅ Reseñas cargadas:', datos.resenas.length);
        }
    } catch (error) {
        console.error('❌ Error cargando reseñas:', error);
    }
}

function mostrarResenas(resenas) {
    const seccionResenas = document.getElementById('reviews');
    
    if (seccionResenas && resenas.length > 0) {
        const tablaHTML = `
            <div class="dashboard-card">
                <h5><i class="bi bi-chat-dots me-2"></i>Reseñas de Usuarios</h5>
                <div class="custom-alert alert-info mb-3">
                    <i class="bi bi-star-fill me-2"></i>
                    Total de reseñas: ${resenas.length}
                </div>
                <div class="admin-table">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Calificación</th>
                                <th>Comentario</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${resenas.map(resena => `
                                <tr>
                                    <td>${resena.id}</td>
                                    <td>${resena.usuario}</td>
                                    <td>${'⭐'.repeat(parseInt(resena.calificacion))}</td>
                                    <td>${resena.comentario.substring(0, 50)}...</td>
                                    <td>${formatearFecha(resena.fecha)}</td>
                                    <td>
                                        <span class="badge ${resena.estado === 'activo' ? 'bg-success' : 'bg-warning'}">
                                            ${resena.estado}
                                        </span>
                                    </td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
        
        seccionResenas.innerHTML = tablaHTML;
    } else if (seccionResenas) {
        seccionResenas.innerHTML = `
            <div class="dashboard-card">
                <h5><i class="bi bi-chat-dots me-2"></i>Reseñas de Usuarios</h5>
                <div class="custom-alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    No hay reseñas aún.
                </div>
            </div>
        `;
    }
}

// 🧮 FUNCIONES PARA CÁLCULOS
async function cargarCalculos() {
    try {
        console.log('🧮 Cargando cálculos...');
        
        const respuesta = await fetch(`${API_URL}?accion=calculos`);
        const datos = await respuesta.json();
        
        if (datos.exito) {
            mostrarCalculos(datos.calculos);
            console.log('✅ Cálculos cargados:', datos.calculos.length);
        }
    } catch (error) {
        console.error('❌ Error cargando cálculos:', error);
    }
}

function mostrarCalculos(calculos) {
    const seccionCalculos = document.getElementById('calculations');
    
    if (seccionCalculos && calculos.length > 0) {
        const tablaHTML = `
            <div class="dashboard-card">
                <h5><i class="bi bi-calculator me-2"></i>Cálculos de CO2 Realizados</h5>
                <div class="custom-alert alert-success mb-3">
                    <i class="bi bi-check-circle me-2"></i>
                    Total de cálculos: ${calculos.length}
                </div>
                <div class="admin-table">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Tipo</th>
                                <th>Resultado</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${calculos.map(calculo => `
                                <tr>
                                    <td>${calculo.id}</td>
                                    <td>${calculo.usuario}</td>
                                    <td>${calculo.tipo}</td>
                                    <td><strong class="text-success">${calculo.resultado}</strong></td>
                                    <td>${formatearFecha(calculo.fecha)}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            </div>
        `;
        
        seccionCalculos.innerHTML = tablaHTML;
    } else if (seccionCalculos) {
        seccionCalculos.innerHTML = `
            <div class="dashboard-card">
                <h5><i class="bi bi-calculator me-2"></i>Cálculos de CO2</h5>
                <div class="custom-alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    No hay cálculos realizados aún.
                </div>
            </div>
        `;
    }
}

// 🔄 MEJORAR LA FUNCIÓN showSection
function showSection(sectionId) {
    const sections = document.querySelectorAll('.admin-section');
    sections.forEach(section => {
        section.style.display = 'none';
    });
    
    document.getElementById(sectionId).style.display = 'block';
    
    // Cargar datos específicos según la sección
    switch(sectionId) {
        case 'dashboard':
            cargarDashboard();
            break;
        case 'users':
            cargarUsuarios();
            break;
        case 'contacts':
            cargarContactos();
            break;
        case 'reviews':
            cargarResenas();
            break;
        case 'calculations':
            cargarCalculos();
            break;
    }
    
    const navLinks = document.querySelectorAll('.sidebar-nav a');
    navLinks.forEach(link => link.classList.remove('active'));
    
    if (event && event.target) {
        event.target.classList.add('active');
    }
}

// 🛠 FUNCIONES DE UTILIDAD
function formatearFecha(fecha) {
    if (!fecha) return 'Sin fecha';
    
    const date = new Date(fecha);
    const dia = String(date.getDate()).padStart(2, '0');
    const mes = String(date.getMonth() + 1).padStart(2, '0');
    const anio = date.getFullYear();
    const hora = String(date.getHours()).padStart(2, '0');
    const minuto = String(date.getMinutes()).padStart(2, '0');
    
    return `${dia}/${mes}/${anio} ${hora}:${minuto}`;
}

function mostrarError(mensaje) {
    console.error('❌', mensaje);
    alert('Error: ' + mensaje);
}

function mostrarExito(mensaje) {
    console.log('✅', mensaje);
    alert('Éxito: ' + mensaje);
}

// Funciones de acción para botones
function verUsuario(id) {
    alert('Ver detalles del usuario ID: ' + id);
}

function editarUsuario(id) {
    alert('Editar usuario ID: ' + id);
}

function eliminarUsuario(id) {
    if (confirm('¿Estás seguro de eliminar este usuario?')) {
        alert('Usuario eliminado (funcionalidad pendiente)');
    }
}

// Hacer disponibles las funciones globalmente
window.showSection = showSection;
window.cargarDashboard = cargarDashboard;
window.verContactoCompleto = verContactoCompleto;
window.verUsuario = verUsuario;
window.editarUsuario = editarUsuario;
window.eliminarUsuario = eliminarUsuario;