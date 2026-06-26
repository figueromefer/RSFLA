# RSFLA Reporting Platform - Guía de uso para presentación al cliente

## 1. Objetivo del sistema

Este sistema reemplaza el proceso manual de crear y actualizar reportes HTML por propiedad. La idea principal es que el equipo de RSFLA pueda capturar y actualizar la información del día a día en una plataforma web, mientras que el cliente propietario de cada inmueble puede consultar un reporte vivo cuando lo necesite.

Antes, el reporte era un documento estático que se tenía que preparar y actualizar manualmente. Ahora, el reporte se alimenta directamente de la información que el equipo carga en el sistema: prospectos, tours, propuestas, leases, actividades de marketing, documentos y equipo asignado.

El objetivo de la demostración es presentar esto no solo como un reporte digital, sino como una primera versión de una plataforma de inteligencia y seguimiento para propiedades comerciales.

---

## 2. Concepto general para explicar al cliente

La forma más sencilla de explicarlo es:

> Creamos una plataforma donde cada propiedad tiene su propio dashboard vivo. El equipo de RSFLA puede administrar la información comercial y operativa de cada propiedad, y el propietario puede entrar con usuario y contraseña para consultar el estado actualizado del reporte en cualquier momento.

El sistema está dividido en dos experiencias:

1. **Vista interna RSFLA**
   - Para administración diaria.
   - La usa el equipo de RSFLA.
   - Permite administrar propiedades, prospectos, marketing, documentos, equipo y usuarios.

2. **Vista cliente / propietario**
   - Para consultar el reporte vivo.
   - El cliente solo ve sus propiedades asignadas.
   - No ve información marcada como interna.

---

## 3. Accesos de prueba

Todas las cuentas sembradas usan la contraseña:

```txt
password
```

### Administrador

```txt
admin@rsfla.test
password
```

Uso recomendado para una demo interna completa.

### Staff RSFLA

```txt
maya@rsfla.test
password
```

Uso recomendado para mostrar cómo trabajaría un miembro del equipo.

### Cliente / propietario

```txt
client@utahcampus.test
password
```

Uso recomendado para mostrar la experiencia del cliente final.

### Cliente secundario

```txt
owner@utahcampus.test
password
```

---

## 4. Flujo recomendado para la presentación

### Paso 1: Explicar el problema actual

Comenzar explicando el flujo anterior:

- RSFLA genera un reporte HTML por propiedad.
- Ese reporte requiere actualizaciones manuales.
- La información puede cambiar durante la semana.
- El cliente no tiene una forma directa de consultar el estado vivo.
- El reporte depende de que alguien lo edite, lo exporte o lo envíe.

Mensaje sugerido:

> El objetivo fue convertir el reporte mensual en una plataforma viva. En lugar de actualizar manualmente un HTML, ahora el equipo captura la información operativa en el sistema y el cliente consulta un reporte actualizado en tiempo real.

---

### Paso 2: Entrar como cliente

Entrar con:

```txt
client@utahcampus.test
password
```

Mostrar que el cliente entra directamente a su propiedad o a la lista de propiedades asignadas.

Puntos importantes:

- El cliente no ve el panel administrativo.
- El cliente solo ve las propiedades que tiene asignadas.
- Si una propiedad está inactiva o no asignada, el cliente no puede consultarla.

Mensaje sugerido:

> Cada propietario tiene acceso únicamente a sus propiedades. Esto permite que RSFLA maneje varias propiedades y varios clientes dentro del mismo sistema sin exponer información cruzada.

---

### Paso 3: Mostrar el reporte del cliente

En el reporte del cliente, explicar las secciones principales.

#### Header ejecutivo

Muestra:

- Nombre de la propiedad.
- Título del reporte.
- Fecha de última actualización.
- Fecha de generación del reporte.
- Branding de RSFLA.

Mensaje sugerido:

> Esta pantalla es el reemplazo natural del reporte mensual, pero con la ventaja de que se mantiene actualizada con la información que RSFLA captura en el sistema.

#### Executive Summary

Resumen automático generado a partir de los datos disponibles.

Sirve para que el cliente entienda rápidamente qué está pasando con la propiedad.

Mensaje sugerido:

> El resumen ejecutivo ayuda al propietario a leer el estado general sin tener que revisar todas las tablas.

#### KPIs principales

Muestra indicadores como:

- Active Prospects.
- Tours.
- Proposals.
- Leases.
- Marketing Activity.

Mensaje sugerido:

> Estos indicadores permiten ver rápidamente la salud comercial de la propiedad.

#### Pipeline Detail

Agrupa los prospectos por etapa:

- Lease.
- Proposals.
- Tours.
- Active Prospects.
- New Leads.
- Inactive.

Cada registro puede mostrar:

- Suite.
- Tenant.
- Use.
- Timing.
- RSF.
- Broker.

Mensaje sugerido:

> En lugar de una tabla plana, el pipeline está organizado por etapa para que el cliente entienda dónde está cada oportunidad.

#### Marketing Activity

Muestra las actividades visibles al cliente:

- Broadcast emails.
- Campaigns.
- Listing updates.
- Broker outreach.
- Flyers.
- Signage.
- Other.

Mensaje sugerido:

> Aquí RSFLA puede documentar qué acciones de marketing se han realizado para la propiedad.

#### Documents / Property Links

Muestra enlaces visibles al cliente, por ejemplo:

- Dropbox folder.
- Broadcast email archive.
- Digital brochure.
- Property files.

Mensaje sugerido:

> Los archivos importantes de la propiedad pueden centralizarse en el reporte para que el cliente no tenga que buscarlos en correos o mensajes anteriores.

#### Team

Muestra miembros del equipo asignados a la propiedad.

Mensaje sugerido:

> Cada propiedad puede tener su equipo asignado, y el cliente puede identificar fácilmente quién está involucrado.

#### Print / Export

El reporte tiene botón para imprimir o exportar desde el navegador.

Mensaje sugerido:

> Aunque el sistema es vivo, también se puede generar una versión imprimible para juntas, archivos internos o envío en PDF desde el navegador.

---

## 5. Entrar como administrador

Entrar con:

```txt
admin@rsfla.test
password
```

Mostrar el dashboard interno.

Puntos importantes:

- El dashboard interno es para RSFLA.
- Desde aquí se accede a los módulos principales.
- El cliente no ve esta parte.

Módulos principales:

- Dashboard.
- Properties.
- Pipeline.
- Marketing.
- Reports.
- Documents.
- Team.
- Users.

---

## 6. Módulo Properties

Ruta:

```txt
/properties
```

Sirve para administrar propiedades.

Permite:

- Ver propiedades activas/inactivas.
- Ver total de prospects.
- Ver prospects visibles al cliente.
- Ver última actividad.
- Crear propiedades.
- Editar propiedades.
- Abrir el detalle interno de una propiedad.
- Abrir el reporte de cliente.

### Property Detail

Al abrir una propiedad, se muestra una vista centralizada con:

- Overview.
- Pipeline.
- Marketing.
- Documents.
- Activity.
- Team.
- Report.

Mensaje sugerido:

> La propiedad es el centro del sistema. Todo lo relacionado con esa propiedad vive aquí: pipeline, documentos, marketing, actividad y reporte.

---

## 7. Módulo Pipeline

Ruta:

```txt
/pipeline
```

Sirve para administrar prospectos y oportunidades.

Permite:

- Crear prospectos.
- Editar prospectos.
- Cambiar status.
- Filtrar por propiedad.
- Filtrar por status.
- Buscar por tenant, broker, use, suite o contacto.
- Marcar si un prospecto es visible o no para el cliente.

### Estados del pipeline

- New Lead.
- Active Prospect.
- Tour.
- Proposal.
- Lease.
- Inactive.

### Punto importante para explicar

Cada vez que se crea un prospecto, se actualiza o cambia de estado, el sistema genera actividad relacionada.

Mensaje sugerido:

> Pipeline es donde RSFLA mantiene vivo el reporte. Lo que se actualiza aquí se refleja en la vista del cliente, siempre respetando que se puede ocultar información interna.

---

## 8. Módulo Marketing

Ruta:

```txt
/marketing
```

Sirve para registrar acciones de marketing por propiedad.

Permite crear actividades como:

- Broadcast email.
- Campaign.
- Social post.
- Listing update.
- Flyer.
- Signage.
- Broker outreach.
- Other.

Cada actividad puede tener:

- Propiedad.
- Tipo.
- Título.
- Descripción.
- Fecha.
- Métrica opcional.
- URL opcional.
- Visibilidad para el cliente.

Mensaje sugerido:

> Este módulo permite que RSFLA documente el trabajo de marketing que se realiza para cada propiedad y decida qué se muestra al propietario.

---

## 9. Módulo Documents

Ruta:

```txt
/documents
```

Sirve para administrar enlaces y documentos por propiedad.

Permite:

- Crear links.
- Editar links.
- Eliminar links.
- Filtrar por propiedad.
- Filtrar por visibilidad.
- Marcar si un documento es visible para el cliente o interno.

Ejemplos:

- Dropbox Leasing Folder.
- Broadcast Email Archive.
- Digital Brochure.
- Property Files.

Mensaje sugerido:

> Documents centraliza recursos que normalmente terminan dispersos en correos, carpetas o mensajes.

---

## 10. Módulo Team

Ruta:

```txt
/team
```

Sirve para administrar miembros del equipo.

Permite:

- Crear miembros.
- Editar miembros.
- Activar/inactivar miembros.
- Asignarlos a propiedades.

Campos principales:

- Name.
- DRE.
- Phone.
- Email.
- Bio URL.
- Photo.
- Active/inactive.

Mensaje sugerido:

> Cada propiedad puede tener miembros de equipo asignados, y solo los miembros activos aparecen en el reporte del cliente.

---

## 11. Módulo Users

Ruta:

```txt
/users
```

Sirve para administrar usuarios del sistema.

Roles:

- Admin.
- Staff.
- Client.

Permite:

- Crear usuarios.
- Editar usuarios.
- Activar/inactivar usuarios.
- Asignar propiedades a clientes.
- Controlar acceso.

Reglas importantes:

- Admin puede administrar todos.
- Staff no puede crear, editar ni eliminar admins.
- Un usuario inactivo no puede iniciar sesión.
- Un cliente solo ve propiedades asignadas.

Mensaje sugerido:

> Este módulo permite que RSFLA gestione qué clientes tienen acceso a qué propiedades sin depender de cambios manuales en código.

---

## 12. Módulo Reports

Ruta:

```txt
/reports
```

Sirve para acceder rápidamente a reportes por propiedad desde la vista interna.

Permite:

- Ver propiedades.
- Revisar estado activo/inactivo.
- Ver prospects visibles.
- Ver marketing visible.
- Abrir reporte.

Mensaje sugerido:

> Reports es un acceso rápido para revisar cómo verá el cliente el reporte de cada propiedad.

---

## 13. Conceptos clave para explicar al cliente

### No es solo un reporte

El sistema evolucionó de un reporte HTML a una plataforma viva por propiedad.

### La información se actualiza desde RSFLA

El equipo administra pipeline, marketing, documentos y equipo.

### El cliente ve solo lo autorizado

Cada prospecto, documento o actividad puede ser visible para el cliente o mantenerse interno.

### El reporte es consultable en cualquier momento

El cliente no depende de esperar el envío mensual.

### Puede imprimirse/exportarse

El reporte mantiene una salida práctica para juntas o archivo.

### Puede crecer a futuro

La arquitectura permite agregar:

- Gráficas.
- Comparativos mensuales.
- Reportes históricos.
- Importador CSV.
- Insights automáticos.
- Notificaciones.
- Multiempresa.

---

## 14. Guion breve de presentación

1. **Contexto**
   - Actualmente el reporte se genera manualmente.
   - Queremos convertirlo en una herramienta viva.

2. **Vista cliente**
   - Entrar como cliente.
   - Mostrar reporte de Utah Campus.
   - Explicar Executive Summary, KPIs, Pipeline Detail, Marketing, Documents y Team.

3. **Vista interna RSFLA**
   - Entrar como admin.
   - Mostrar Properties.
   - Abrir detalle de propiedad.
   - Mostrar Pipeline.
   - Editar un prospecto.
   - Regresar al reporte y mostrar que cambia.

4. **Control de visibilidad**
   - Mostrar que un prospecto puede marcarse como interno.
   - Explicar que el cliente solo ve información aprobada.

5. **Valor para el cliente**
   - Menos trabajo manual.
   - Información más actual.
   - Mayor transparencia.
   - Mejor experiencia que un PDF o HTML estático.

6. **Futuro**
   - Gráficas, tendencias, comparativos y reportes históricos.

---

## 15. Demo sugerida paso a paso

### Demo como cliente

1. Ir al login.
2. Entrar con `client@utahcampus.test` / `password`.
3. Mostrar el reporte.
4. Explicar Executive Summary.
5. Mostrar Pipeline Detail.
6. Mostrar Marketing Activity.
7. Mostrar Documents.
8. Mostrar Team.
9. Usar Print / Export.

### Demo como admin

1. Cerrar sesión.
2. Entrar con `admin@rsfla.test` / `password`.
3. Abrir Properties.
4. Abrir Utah Campus.
5. Mostrar Overview.
6. Abrir Pipeline.
7. Crear o editar un prospecto.
8. Cambiar status.
9. Marcar visible/interno.
10. Abrir Reports y ver el reporte actualizado.

---

## 16. Advertencias para la presentación

Este sistema está en versión inicial/MVP funcional.

Evitar prometer como terminado lo siguiente si todavía no se ha implementado completamente:

- Gráficas avanzadas.
- Comparativos históricos.
- Importador automático de CSV.
- Envío automático por email.
- PDF generado desde backend.
- Notificaciones.
- Multiempresa.
- Integraciones externas.

Forma recomendada de decirlo:

> Esta primera versión ya permite administrar la información principal y mostrar un reporte vivo. Las siguientes fases pueden enfocarse en automatización, gráficas, históricos e integraciones.

---

## 17. Siguiente fase recomendada

Para que el sistema se vea más premium ante el cliente, la siguiente fase recomendada es UX/UI polish:

- Mejorar dashboard ejecutivo.
- Agregar gráficas simples.
- Mejorar responsive.
- Refinar branding visual RSFLA.
- Mejorar experiencia de impresión.
- Crear comparativos mensuales.
- Preparar importador CSV.

---

## 18. Mensaje final para cliente

> Este proyecto convierte el reporte mensual de RSFLA en una plataforma viva de seguimiento por propiedad. El equipo puede actualizar información operativa y comercial en tiempo real, mientras que el propietario accede a un reporte ejecutivo actualizado, organizado y exportable. La primera versión ya cubre propiedades, pipeline, marketing, documentos, equipo, usuarios y reportes, y deja la base lista para evolucionar hacia analítica, automatización e inteligencia de negocio.
