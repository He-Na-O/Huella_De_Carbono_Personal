// Ruta base donde tienes los PHP
const API_BASE = '/brainer/huella-de-carbono-personal/php/';

// ================================
// -------- REGISTRO DE USUARIO ---
// ================================
async function registrarUsuario(e) {
  e.preventDefault();

  // Obtener datos del formulario (NOMBRES CORRECTOS DEL HTML)
  const firstName = document.getElementById('firstName').value.trim();
  const lastName = document.getElementById('lastName').value.trim();
  const email = document.getElementById('registerEmail').value.trim();
  const username = document.getElementById('username').value.trim();
  const password = document.getElementById('registerPassword').value;
  const confirmPassword = document.getElementById('confirmPassword').value;
  const age = parseInt(document.getElementById('age').value);
  const acceptTerms = document.getElementById('acceptTerms').checked;

  // Validaciones básicas
  if (!firstName || !email || !username || !password || !confirmPassword || !age) {
    alert('Por favor completa todos los campos obligatorios.');
    return;
  }

  if (!acceptTerms) {
    alert('Debes aceptar los términos y condiciones.');
    return;
  }

  if (password !== confirmPassword) {
    alert('Las contraseñas no coinciden.');
    return;
  }

  if (password.length < 8) {
    alert('La contraseña debe tener al menos 8 caracteres.');
    return;
  }

  if (age < 13 || age > 120) {
    alert('La edad debe estar entre 13 y 120 años.');
    return;
  }

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    alert('Por favor ingresa un email válido.');
    return;
  }

  // Datos CORRECTOS para enviar al PHP (coinciden con lo que espera register.php)
  const data = {
    nombre: firstName + (lastName ? ' ' + lastName : ''),
    correo: email,
    contrasena: password,
    edad: age,
    username: username
  };

  try {
    console.log('Enviando datos de registro:', data);
    
    const response = await fetch(API_BASE + 'register.php', {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(data)
    });

    const text = await response.text();
    console.log('Respuesta del servidor:', text);

    let json;
    try {
      json = JSON.parse(text);
    } catch (err) {
      console.error('Error al parsear JSON:', err);
      alert('Respuesta inválida del servidor:\n' + text);
      return;
    }

    if (json.success) {
      alert('¡Registro exitoso! Bienvenido a Latido Verde.');
      document.getElementById('registerForm').reset();
      setTimeout(() => {
        window.location.href = '/brainer/huella-de-carbono-personal/HTML/inicio_de_sesion.html';
      }, 2000);
    } else {
      alert('Error en el registro: ' + json.message);
    }

  } catch (error) {
    console.error('Error de conexión:', error);
    alert('Error de conexión con el servidor: ' + error.message);
  }
}

// ================================
// -------- CREAR RESEÑA ----------
// ================================
async function crearResena(e) {
  e.preventDefault();

  const data = {
    id_usuario: document.getElementById('id_usuario').value.trim(),
    titulo: document.getElementById('titulo').value.trim(),
    contenido: document.getElementById('contenido').value.trim(),
    calificacion: document.getElementById('calificacion').value
  };  

  if (!data.id_usuario || !data.titulo || !data.contenido || !data.calificacion) {
    alert('Por favor completa todos los campos.');
    return;
  }

  try {
    const response = await fetch(API_BASE + 'crear_resena.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(data)
    });

    const text = await response.text();
    console.log('Respuesta crear_resena:', text);

    let json;
    try {
      json = JSON.parse(text);
    } catch (err) {
      alert('Respuesta inválida del servidor:\n' + text);
      return;
    }

    if (json.success) {
      alert('Reseña guardada correctamente');
      document.getElementById('formResena').reset();
      cargarResenas();
    } else {
      alert('Error al guardar: ' + json.message);
    }
  } catch (error) {
    console.error('Error de conexión:', error);
    alert('Error de conexión: ' + error.message);
  }
}

// ================================
// -------- LISTAR RESEÑAS --------
// ================================
async function cargarResenas() {
  try {
    const response = await fetch(API_BASE + 'listar_resena.php');
    const text = await response.text();
    console.log('Respuesta listar_resena:', text);

    let json;
    try {
      json = JSON.parse(text);
    } catch (err) {
      alert('Respuesta inválida del servidor:\n' + text);
      return;
    }

    if (json.success) {
      const contenedor = document.getElementById('resenas');
      contenedor.innerHTML = '';
      json.resenas.forEach(r => {
        contenedor.innerHTML += `
          <div class="card mb-3">
            <div class="card-body">
              <h5 class="card-title">${r.Titulo} <small class="text-muted">por ${r.Autor}</small></h5>
              <p class="card-text">${r.Contenido}</p>
              <span>⭐ ${r.Calificacion}</span>
              <small class="text-muted float-end">${r.Fecha_Resena}</small>
            </div>
          </div>`;
      });
    } else {
      alert('Error al listar: ' + json.message);
    }
  } catch (error) {
    console.error('Error de conexión:', error);
    alert('Error de conexión: ' + error.message);
  }
}

// ================================
// -------- FUNCIONES AUXILIARES --
// ================================

function togglePasswordVisibility() {
  const passwordToggles = document.querySelectorAll('.password-toggle');
  
  passwordToggles.forEach(toggle => {
    const input = toggle.querySelector('input');
    const icon = toggle.querySelector('i');
    
    if (input && icon) {
      icon.addEventListener('click', function() {
        if (input.type === 'password') {
          input.type = 'text';
          this.classList.remove('bi-eye-slash');
          this.classList.add('bi-eye');
        } else {
          input.type = 'password';
          this.classList.remove('bi-eye');
          this.classList.add('bi-eye-slash');
        }
      });
    }
  });
}

function setupPasswordValidation() {
  const password = document.getElementById('registerPassword');
  const confirmPassword = document.getElementById('confirmPassword');
  
  if (password && confirmPassword) {
    confirmPassword.addEventListener('input', function() {
      if (password.value !== this.value) {
        this.setCustomValidity('Las contraseñas no coinciden');
        this.style.borderColor = '#f44336';
      } else {
        this.setCustomValidity('');
        this.style.borderColor = '#4caf50';
      }
    });
  }
}

// ================================
// -------- INICIALIZAR -----------
// ================================
document.addEventListener('DOMContentLoaded', function() {
  console.log('Inicializando sistema Latido Verde...');
  
  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', registrarUsuario);
    console.log('Formulario de registro conectado');
    togglePasswordVisibility();
    setupPasswordValidation();
  }
  
  const resenaForm = document.getElementById('formResena');
  if (resenaForm) {
    resenaForm.addEventListener('submit', crearResena);
    console.log('Formulario de reseñas conectado');
  }

  if (document.getElementById('resenas')) {
    cargarResenas();
    console.log('Cargando reseñas...');
  }
  
  console.log('Sistema inicializado correctamente');
});