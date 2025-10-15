// ===== FUNCIONALIDADES PARA LATIDO VERDE =====

// Función para alternar tema oscuro/claro
function toggleTheme() {
  const body = document.body;
  const themeIcon = document.getElementById('theme-icon');
  
  if (body.getAttribute('data-theme') === 'dark') {
    body.removeAttribute('data-theme');
    themeIcon.className = 'bi bi-moon-fill';
    localStorage.setItem('theme', 'light');
    
    // Efecto de transición suave
    body.style.transition = 'all 0.3s ease';
  } else {
    body.setAttribute('data-theme', 'dark');
    themeIcon.className = 'bi bi-sun-fill';
    localStorage.setItem('theme', 'dark');
    
    // Efecto de transición suave
    body.style.transition = 'all 0.3s ease';
  }
  
  // Animación del botón
  const themeButton = document.querySelector('.theme-toggle');
  themeButton.style.transform = 'scale(0.9)';
  setTimeout(() => {
    themeButton.style.transform = 'scale(1)';
  }, 150);
}

// Crear partículas de fondo animadas
function createParticles() {
  // Crear contenedor de partículas si no existe
  let particlesContainer = document.getElementById('bg-particles');
  if (!particlesContainer) {
    particlesContainer = document.createElement('div');
    particlesContainer.id = 'bg-particles';
    particlesContainer.className = 'bg-particles';
    document.body.appendChild(particlesContainer);
  }
  
  const particleCount = window.innerWidth < 768 ? 25 : 50; // Menos partículas en móvil
  
  // Limpiar partículas existentes
  particlesContainer.innerHTML = '';
  
  for (let i = 0; i < particleCount; i++) {
    const particle = document.createElement('div');
    particle.className = 'particle';
    particle.style.left = Math.random() * 100 + '%';
    particle.style.top = Math.random() * 100 + '%';
    particle.style.animationDelay = Math.random() * 6 + 's';
    particle.style.animationDuration = (Math.random() * 3 + 3) + 's';
    particle.style.opacity = Math.random() * 0.5 + 0.1;
    particlesContainer.appendChild(particle);
  }
}

// Funciones de animaciones de entrada
function initializeAnimations() {
  // Añadir animaciones de entrada escalonadas
  setTimeout(() => {
    const elementsToAnimate = document.querySelectorAll('.fade-in-up');
    elementsToAnimate.forEach((el, index) => {
      setTimeout(() => {
        el.style.opacity = '1';
        el.style.transform = 'translateY(0)';
      }, index * 200);
    });
  }, 100);
}

// Funcionalidad del carrusel mejorada
function initializeCarousel() {
  const carouselElement = document.getElementById('carouselLatido');
  if (carouselElement) {
    const myCarousel = new bootstrap.Carousel(carouselElement, {
      interval: 3050,
      wrap: true,
      keyboard: true,
      pause: 'hover'
    });

    // Pausar carrusel cuando está fuera de la vista
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          myCarousel.cycle();
        } else {
          myCarousel.pause();
        }
      });
    }, { threshold: 0.3 });

    observer.observe(carouselElement);

    // Añadir controles de teclado personalizados
    document.addEventListener('keydown', (e) => {
      if (carouselElement.matches(':hover')) {
        if (e.key === 'ArrowLeft') {
          myCarousel.prev();
        } else if (e.key === 'ArrowRight') {
          myCarousel.next();
        }
      }
    });
  }
}

// Funcionalidad de búsqueda
function initializeSearch() {
  const searchInput = document.querySelector('.search-container input');
  const searchBtn = document.querySelector('.search-btn');

  if (searchInput && searchBtn) {
    // Búsqueda al presionar Enter
    searchInput.addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        performSearch(searchInput.value);
      }
    });

    // Búsqueda al hacer clic en el botón
    searchBtn.addEventListener('click', () => {
      performSearch(searchInput.value);
    });

    // Autocompletado simple (opcional)
    const suggestions = [
      'misión', 'visión', 'valores', 'política', 'calculadora', 
      'reseñas', 'contacto', 'reciclaje', 'sostenibilidad', 'medio ambiente'
    ];

    searchInput.addEventListener('input', (e) => {
      const value = e.target.value.toLowerCase();
      // Aquí podrías implementar un sistema de sugerencias
      if (value.length > 2) {
        const matches = suggestions.filter(s => s.includes(value));
        // console.log('Sugerencias:', matches); // Para desarrollo
      }
    });
  }
}

// Función para realizar búsqueda
function performSearch(query) {
  if (!query.trim()) {
    alert('Por favor, ingresa un término de búsqueda');
    return;
  }

  // Mapeo de términos a páginas
  const pageMapping = {
    'mision': 'HTML/mision_vision.html',
    'misión': 'HTML/mision_vision.html',
    'vision': 'HTML/mision_vision.html',
    'visión': 'HTML/mision_vision.html',
    'valores': 'HTML/valores.html',
    'politica': 'HTML/politica_de_calidad.html',
    'política': 'HTML/politica_de_calidad.html',
    'calidad': 'HTML/politica_de_calidad.html',
    'reseñas': 'HTML/reseñas.html',
    'resenas': 'HTML/reseñas.html',
    'calculadora': 'HTML/calculadora.html',
    'contacto': 'HTML/contactanos.html',
    'contactanos': 'HTML/contactanos.html',
    'contáctanos': 'HTML/contactanos.html'
  };

  const searchTerm = query.toLowerCase().trim();
  const targetPage = pageMapping[searchTerm];

  if (targetPage) {
    // Efecto de búsqueda exitosa
    const searchInput = document.querySelector('.search-container input');
    searchInput.style.background = 'rgba(76, 175, 80, 0.3)';
    setTimeout(() => {
      window.location.href = targetPage;
    }, 500);
  } else {
    // Buscar en el contenido de la página actual
    const found = highlightSearchTerm(searchTerm);
    if (!found) {
      alert(`No se encontraron resultados para: "${query}"`);
    }
  }
}

// Función para resaltar términos de búsqueda en la página
function highlightSearchTerm(term) {
  const content = document.body.innerText.toLowerCase();
  if (content.includes(term)) {
    // Scroll suave hacia el carrusel si se buscan términos relacionados
    const relatedTerms = ['verde', 'tierra', 'reciclaje', 'sostenible', 'planeta'];
    if (relatedTerms.some(t => term.includes(t))) {
      document.getElementById('carouselLatido')?.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'center' 
      });
    }
    return true;
  }
  return false;
}

// Funcionalidad del botón "Ir arriba"
function initializeGoTopButton() {
  const goTopBtn = document.getElementById('goTopBtn');
  
  if (goTopBtn) {
    // Mostrar/ocultar botón según scroll
    window.addEventListener('scroll', () => {
      if (window.scrollY > 250) {
        goTopBtn.classList.add('visible');
      } else {
        goTopBtn.classList.remove('visible');
      }
    });

    // Funcionalidad de clic
    goTopBtn.addEventListener('click', () => {
      window.scrollTo({ 
        top: 0, 
        behavior: 'smooth' 
      });
    });

    // Accesibilidad: soporte para teclado
    goTopBtn.addEventListener('keydown', (e) => {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        window.scrollTo({ 
          top: 0, 
          behavior: 'smooth' 
        });
      }
    });
  }
}

// Efecto parallax suave
function initializeParallax() {
  let ticking = false;
  
  function updateParallax() {
    const scrolled = window.pageYOffset;
    const carousel = document.getElementById('carouselLatido');
    
    if (carousel) {
      // Efecto parallax más sutil
      carousel.style.transform = `translateY(${scrolled * 0.05}px)`;
    }
    
    ticking = false;
  }
  
  window.addEventListener('scroll', () => {
    if (!ticking) {
      requestAnimationFrame(updateParallax);
      ticking = true;
    }
  });
}

// Mejoras de accesibilidad para redes sociales
function enhanceSocialBar() {
  const socialLinks = document.querySelectorAll('.social-bar a');
  
  socialLinks.forEach(link => {
    // Mejorar feedback visual
    link.addEventListener('mouseenter', () => {
      link.style.transform = 'scale(1.1) rotate(5deg)';
    });
    
    link.addEventListener('mouseleave', () => {
      link.style.transform = 'scale(1) rotate(0deg)';
    });
    
    // Soporte para teclado mejorado
    link.addEventListener('keydown', (e) => {
      if (e.key === 'Enter') {
        link.style.transform = 'scale(0.95)';
        setTimeout(() => {
          link.style.transform = 'scale(1.1)';
        }, 100);
      }
    });
  });
}

// Animación suave para navegación
function enhanceNavigation() {
  const navLinks = document.querySelectorAll('nav a');
  
  navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
      // Si es un enlace interno, añadir efecto de carga
      if (link.getAttribute('href').startsWith('HTML/')) {
        // Efecto de carga suave
        document.body.style.opacity = '0.7';
        document.body.style.transition = 'opacity 0.3s ease';
      }
    });
  });
}

// Función de carga diferida para imágenes
function lazyLoadImages() {
  const images = document.querySelectorAll('img');
  const imageObserver = new IntersectionObserver((entries, observer) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        const img = entry.target;
        img.style.transition = 'opacity 0.3s ease';
        img.style.opacity = '0';
        
        img.addEventListener('load', () => {
          img.style.opacity = '1';
        });
        
        observer.unobserve(img);
      }
    });
  });

  images.forEach(img => {
    imageObserver.observe(img);
  });
}

// Manejo de errores de carga de imágenes
function handleImageErrors() {
  const images = document.querySelectorAll('img');
  
  images.forEach(img => {
    img.addEventListener('error', () => {
      img.style.background = 'linear-gradient(45deg, #e8f5e8, #c8e6c9)';
      img.style.display = 'flex';
      img.style.alignItems = 'center';
      img.style.justifyContent = 'center';
      img.innerHTML = '<i class="bi bi-image text-muted fs-1"></i>';
      img.alt = 'Imagen no disponible';
    });
  });
}

// Función de optimización de rendimiento
function optimizePerformance() {
  // Reducir animaciones en dispositivos con poca batería
  if ('getBattery' in navigator) {
    navigator.getBattery().then((battery) => {
      if (battery.level < 0.2) {
        document.documentElement.style.setProperty('--animation-duration', '0.1s');
      }
    });
  }
  
  // Reducir efectos en dispositivos lentos
  if ('deviceMemory' in navigator && navigator.deviceMemory < 4) {
    const particles = document.querySelectorAll('.particle');
    particles.forEach((particle, index) => {
      if (index % 2 === 0) particle.remove(); // Remover la mitad de las partículas
    });
  }
}

// INICIALIZACIÓN PRINCIPAL
document.addEventListener('DOMContentLoaded', function() {
  console.log('🌱 Latido Verde - Inicializando...');
  
  // Cargar tema guardado
  const savedTheme = localStorage.getItem('theme');
  const themeIcon = document.getElementById('theme-icon');
  
  if (savedTheme === 'dark') {
    document.body.setAttribute('data-theme', 'dark');
    if (themeIcon) themeIcon.className = 'bi bi-sun-fill';
  }
  
  // Inicializar todas las funcionalidades
  try {
    createParticles();
    initializeAnimations();
    initializeCarousel();
    initializeSearch();
    initializeGoTopButton();
    initializeParallax();
    enhanceSocialBar();
    enhanceNavigation();
    lazyLoadImages();
    handleImageErrors();
    optimizePerformance();
    
    console.log('✅ Latido Verde - Todas las funcionalidades cargadas correctamente');
  } catch (error) {
    console.warn('⚠️ Error al cargar algunas funcionalidades:', error);
  }
});

// Manejo de redimensionamiento de ventana
window.addEventListener('resize', () => {
  // Recrear partículas con nueva cantidad según tamaño de pantalla
  createParticles();
});

// Manejo de visibilidad de página (optimización)
document.addEventListener('visibilitychange', () => {
  const carousel = bootstrap.Carousel.getInstance(document.getElementById('carouselLatido'));
  if (carousel) {
    if (document.hidden) {
      carousel.pause();
    } else {
      carousel.cycle();
    }
  }
});

// Prevención de errores de consola
window.addEventListener('error', (e) => {
  console.warn('🌿 Latido Verde - Error capturado:', e.message);
});

// Función utilitaria para debugging (solo en desarrollo)
function debugMode() {
  if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
    console.log('🔧 Modo debug activado');
    window.latidoVerde = {
      toggleTheme,
      createParticles,
      performSearch,
      version: '1.0.0'
    };
  }
}