# Support Ticket System

Sistema interno de soporte técnico desarrollado para la Municipalidad Distrital de Breña, orientado a la gestión de incidencias, seguimiento operativo de tickets y control de atención entre usuarios, técnicos y administradores.

El sistema permite centralizar solicitudes de soporte, asignación de técnicos, control de estados, tiempos de atención, comentarios, evidencias y métricas operativas en una interfaz moderna, responsive y con soporte dark mode.

---

# Características principales

- Gestión completa de tickets
- Roles y permisos (Usuario / Técnico / Administrador)
- Dashboard operativo
- Bandeja general de tickets
- Asignación y toma de tickets
- Estados operativos:
  - Abierto
  - En atención
  - Resuelto
  - Cerrado
- Timeline operativo del ticket
- Métricas de atención:
  - Tiempo en cola
  - Tiempo en atención
  - Tiempo total
- Comentarios internos
- Adjuntos y evidencias
- Indicadores visuales de atención
- Interfaz institucional moderna
- Responsive design
- Dark mode

---

# Tecnologías utilizadas

- Laravel 12
- PHP 8.3
- Blade
- Tailwind CSS
- Vite
- MySQL
- Spatie Laravel Permission

---

# Módulos implementados

## Autenticación
- Login seguro
- Recuperación de contraseña
- Gestión de perfil
- Roles y permisos

## Tickets
- Creación de tickets
- Bandeja general
- Historial
- Tickets asignados
- Seguimiento operativo
- Gestión de estados
- Línea de tiempo
- Comentarios

## Administración
- Gestión de usuarios
- Gestión de técnicos
- Dashboard administrativo
- Indicadores operativos

## Evidencias
- Adjuntos por ticket
- Vista previa de imágenes
- Gestión de archivos

---

# Roles del sistema

## Usuario
- Registrar tickets
- Ver seguimiento
- Agregar comentarios
- Adjuntar evidencias

## Técnico
- Tomar tickets
- Gestionar atención
- Resolver tickets
- Registrar comentarios

## Administrador
- Control total del sistema
- Gestión de usuarios
- Cierre de tickets
- Supervisión operativa

---

# Instalación

```bash
git clone https://github.com/marcelovelasco96/support-ticket-system.git
```

```bash
composer install
```

```bash
npm install
```

```bash
cp .env.example .env
```

```bash
php artisan key:generate
```

```bash
php artisan migrate --seed
```

```bash
npm run dev
```

```bash
php artisan serve
```

---

# Estado del proyecto

Proyecto actualmente en desarrollo y mejora continua para entorno institucional.

---

# Autor

**Marcelo Velasco**  
Ingeniero de Sistemas  
Backend Developer | Laravel | SQL Server | Power BI
