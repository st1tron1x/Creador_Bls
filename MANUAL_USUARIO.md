# Manual de usuario — Plataforma BL

Para quienes crean, emiten y consultan Bills of Lading de **Trade & Business** y **Global Trade Business Corp US**.
Los campos marcados con **(*)** son obligatorios.

---

## 1. Roles: qué puede hacer cada uno

| Acción | Auxiliar | Coordinación | Gerencia | Admin |
|---|:--:|:--:|:--:|:--:|
| Ver / buscar BL, dashboard | ✔ | ✔ | ✔ | ✔ |
| Crear y editar borradores | ✔ | ✔ | ✔ | ✔ |
| Emitir un BL (genera el número) | ✔ | ✔ | ✔ | ✔ |
| Descargar el PDF de un BL emitido | ✔ | ✔ | ✔ | ✔ |
| Solicitar la anulación de un BL emitido | ✔ | ✔ | ✔ | ✔ (se aprueba sola) |
| Aprobar / rechazar anulaciones | ✘ | ✔ si su rango ≥ al del solicitante | ✔ | ✔ |
| Administrar usuarios (crear, rol, activar, eliminar) | ✘ | ✘ | ✔ | ✔ |

Reglas de la anulación:
- **Nunca** puedes aprobar tu propia solicitud.
- Un **auxiliar nunca aprueba**.
- Quien aprueba debe tener **rango igual o superior** al de quien solicita (auxiliar < coordinación < gerencia < admin).
- Si quien solicita es **admin**, la anulación queda aprobada de inmediato.

> La tabla refleja las reglas del sistema. Si algún botón no te aparece, tu rol no tiene ese permiso: consulta con un administrador.

---

## 2. Ingresar al sistema

1. Abre la dirección de la plataforma e ingresa tu **correo** y **contraseña**.
2. Si tu cuenta está **inactiva** verás el aviso "Tu cuenta está inactiva": contacta a un administrador.
3. Por seguridad, la sesión se cierra tras un tiempo sin actividad: si te pasa, vuelve a ingresar (el borrador que estabas llenando ya quedó guardado al avanzar de paso).

### 2.1 Elegir empresa
Después de ingresar aparece **"¿Con cuál empresa vas a trabajar?"**. Pulsa la empresa (Trade & Business o Global Trade Business). Todo lo que veas y crees desde ahí es de esa empresa. En la parte superior siempre se muestra el nombre de la empresa activa.

**Cambiar de empresa**: clic en tu nombre (arriba a la derecha) → **Cambiar empresa**.
Si solo perteneces a una empresa, entra directo y esa opción no aparece.

### 2.2 Menú de usuario (clic en tu nombre)
- **Usuarios** (solo gerencia/admin)
- **Mi perfil**: tus datos y cambio de contraseña
- **Cambiar empresa**
- **Cerrar sesión**

---

## 3. Dashboard (inicio)

Resumen de la empresa activa:
- Contadores por estado: **Borradores, Emitidos, Anulación solicitada, Anulados**, y tus BL creados.
- **Botón "+ Nuevo BL"**.
- **BL recientes** y **borradores antiguos** (con enlace "Continuar →").
- **Emitidos este mes**, **tendencia de los últimos 6 meses**, **promedio de horas** entre crear y emitir.
- **Top 5 shippers y consignees**.
- **Por puerto** (solo Trade & Business).
- **Pendientes de aprobar** (solo si tu rol puede aprobar anulaciones).

---

## 4. Listado de BL

Menú **BL**. Muestra los BL de la empresa activa, más recientes primero (20 por página).

Filtros (pulsa filtrar para aplicarlos):
- **Nº de BL** (búsqueda parcial: `BAQ`, `0004`, `BAQ-0004`…)
- **Shipper**, **Consignee** (búsqueda parcial, sin importar mayúsculas)
- **Puerto** (solo tiene sentido en Trade & Business)
- **Estado**: borrador, emitido, anulación solicitada, anulado
- **Desde / Hasta**: fecha de creación

Cada fila abre el detalle; los borradores abren directamente el asistente para continuar.

---

## 5. Crear un BL — Trade & Business (5 pasos)

Botón **+ Nuevo BL**. Arriba ves los 5 pasos; puedes **hacer clic en un paso ya recorrido** para volver. Al pulsar **Siguiente** se valida y se **guarda un borrador automáticamente**. Puedes salir cuando quieras con **Guardar borrador y salir** (lo continúas después desde el listado).

> En el borrador **no hay número de BL todavía**: se asigna al emitir.

### Paso 1 — Datos generales
| Campo | Qué escribir |
|---|---|
| **Puerto (*)** | Puerto de carga. **Define el prefijo del número**: BVT, CTG, BAQ, SMR, TBO. Cada puerto tiene su propio consecutivo. |
| Country Origin | País de origen de la mercancía. |
| Shipper's Ref | Referencia que da el embarcador. |
| Bill of Loading Nº | Número del BL de la **naviera**, solo si existe. **No es** el número que genera el sistema; es opcional y sale en la casilla "BILL OF LADING Nº" del PDF de Trade & Business. |
| **1. Shipper (*)** | Nombre y dirección del embarcador. Varias líneas con Enter. |
| **2. Consignee or Order (*)** | Consignatario o "TO ORDER". Varias líneas con Enter. |
| 3. Notify Party | Parte a notificar. |

### Paso 2 — Transporte
| Campo | Qué escribir |
|---|---|
| 4. Pre-carriage By | Transporte previo (ej. camión, tren). |
| 5. Place of Receipt | Lugar donde se recibe la carga. |
| 6. Ocean Vessel Voy. No. | Buque y viaje. |
| **8. Port of Discharge (*)** | Puerto de descarga. |
| 9. Place of Delivery | Lugar de entrega. |
| Final Destination | Destino final. |

El **Port of Loading** (casilla 7) sale automáticamente del puerto elegido en el paso 1 (ej. "Barranquilla, COLOMBIA").

### Paso 3 — Carga
Una o más **líneas de carga** (botón **+ Agregar línea de carga**; **Quitar línea** para borrar). Cada línea tiene:
| Campo | Qué escribir |
|---|---|
| Marks & Number / Container-Seal | Marcas y números. Admite varias líneas (Enter). |
| Container / Seal No. | Contenedor y sello; se imprime justo debajo de las marcas. |
| **Description of Goods (*)** | Descripción. Varias líneas con Enter. Obligatoria en cada línea. |
| No. of Pkgs | Cantidad y tipo de bultos (ej. `48 BALES`). Una sola línea. |
| Gross Weight | Peso bruto (ej. `20,000.000 KGS`). Una sola línea. |
| Measurement (CBM) | Volumen. Una sola línea. |

Al menos **una línea** con descripción. Las líneas con la descripción vacía se ignoran al guardar.
Ver la sección **7** para casos con varios contenedores (funciona en Trade & Business y en Global).

### Paso 4 — Flete y condiciones
| Campo | Qué escribir |
|---|---|
| Freight Payable At | Lugar donde se paga el flete. |
| Number of Original Bs/L | **Cuántos originales** se emiten (ej. `3` o `THREE`). No es el número del BL. |
| **Freight Type (*)** | **Prepaid** o **Collect**: define en cuál casilla del PDF sale el valor. |
| Freight Total (USD) | Valor numérico del flete (ej. `1250.00`). |
| On Board Date | Fecha de embarque (selector de fecha). |
| 14. Delivery Agent | Agente de entrega. |
| 11. Freight and Charges (detalle) | Texto libre del detalle de flete y cargos. |
| 10. Temperature Control Instructions | Instrucciones de temperatura, si aplica. |
| 12. Excess Value Declaration | Declaración de valor en exceso, si aplica. |

### Paso 5 — Revisión y emisión
Revisa el resumen. Botones:
- **Anterior** / **Guardar borrador y salir**
- **Emitir BL** (pide confirmación): asigna el número (ej. `BAQ-0004`), cambia el estado a *emitido* y **ya no se puede editar**. Solo con permiso de emitir.

---

## 6. Crear un BL — Global Trade Business Corp US (5 pasos)

No hay puertos ni catálogo: los lugares se escriben libres. El número es un **único consecutivo de la empresa**: `FMC OTI NO 0001`, `FMC OTI NO 0002`…

### Paso 1 — Partes involucradas
**Shipper / Exporter (*)**, **Consignee (*)**, **Notify Party**.

### Paso 2 — Referencias y ruteo
**Domestic Routing / Export Instructions**, **Export References**, **For Cargo Delivery Please Contact**, **Forwarding Agent – References**. Todos opcionales, multilínea.

### Paso 3 — Transporte
Pre-carriage By, Place of Receipt, **Exporting Carrier**, **Port of Loading (*)** (escritura libre, ej. `MIAMI`), **On-Carriage By**, **No. of Bills of Lading** (cantidad de originales), **Port of Discharge (*)**, Place of Delivery, Final Destination.

### Paso 4 — Carga
Líneas de carga: **Marks and Numbers**, **Description of Packages and Goods (*)**, **No. of Pkgs**, **Gross Weight**, **Measurement**. Más el campo **Net Weight (total)**, que sale en la fila "NET WT." al pie de la tabla.

### Paso 5 — Cargos y emisión
**Freight Type (*)** (Prepaid/Collect), **Freight Total (USD)**, **Freight Charges (detalle)**, **Excess Value Charge**, **Total Charges (USD)**. Luego **Emitir BL**.

El membrete (logo, dirección, teléfono, correo) sale de los datos de la empresa; si ves textos de relleno como "(tu direccion)", avisa a un administrador para que los actualice.

---

## 7. Varios contenedores en un mismo envío

Ejemplo real de otro agente de carga (RR Logistic): 5 contenedores de 40' HC, cada uno con su sello, bultos y peso, y **debajo** la descripción general de la mercancía.

El sistema permite hacerlo de **dos maneras**. En ambas, el formato imprime lo que escribas tal cual (no calcula totales).

### Opción A — Una línea de carga por contenedor (recomendada)
Úsala cuando **cada contenedor tiene su propio peso/bultos**. Pulsa **+ Agregar línea de carga** una vez por contenedor, y una línea final para la descripción general.

| Línea | Marks / Container-Seal | No. of Pkgs | Description | Gross Weight |
|---|---|---|---|---|
| 1 | `EMCU8748779/40H/EMCNWA4554` | `48 BALES` | `(HI-CUBE)` | `20,000.000 KGS` |
| 2 | `EMCU8810140/40H/EMCNWA4534` | `48 BALES` | `(HI-CUBE)` | `20,000.000 KGS` |
| 3 | `TGBU4556544/40H/EMCNWA4514` | `48 BALES` | `(HI-CUBE)` | `20,000.000 KGS` |
| 4 | `TIIU4039284/40H/EMCNWA4524` | `48 BALES` | `(HI-CUBE)` | `20,000.000 KGS` |
| 5 | `TXGU7803614/40H/EMCNWA4544` | `48 BALES` | `(HI-CUBE)` | `20,000.000 KGS` |
| 6 (general) | `NM` | `5 X 40H` | `WASTE PAPER, NEW DOUBLE LINE KRAFT CORRUGATED (VIRGIN) CONTAINERS`⏎`HS CODE: 47079000`⏎`"OCEAN FREIGHT PREPAID"`⏎`SHIPPER'S LOAD & COUNT`⏎`240 BALES` | `100,000.000 KGS` |

(⏎ = tecla Enter dentro de la misma casilla de descripción.)
Si el campo **Container / Seal No.** está disponible en tu formulario, también puedes poner ahí el contenedor/sello en vez de en "Marks".

### Opción B — Una sola línea con Enter
Úsala cuando el peso y los bultos son **un solo total** para todo el envío.
- En **Marks & Number / Container-Seal**, escribe un contenedor por renglón (Enter entre cada uno).
- En **Description**, escribe la descripción. Quedará **al lado** de los contenedores (empieza en el primer renglón). Si la necesitas **debajo** de la lista de contenedores, no dependas de renglones vacíos al inicio (el sistema puede recortarlos): usa la Opción A, con una línea de carga aparte para la descripción.
- En **No. of Pkgs** y **Gross Weight** (una sola línea) escribe los totales (`5 X 40H`, `100,000.000 KGS`).

**Reglas para que no se desalinee (Trade & Business):**
- Las columnas se alinean **renglón a renglón**: el renglón 1 de marcas queda frente al renglón 1 de la descripción.
- Cada renglón de **marcas** se corta a ~28 caracteres y cada renglón de **descripción** a ~68. Un renglón que se pase de ese largo se parte en dos y **corre todo lo que sigue**: mantén cada contenedor/sello en un renglón corto.
- Los bultos, el peso y el volumen salen en el **primer renglón** de la línea de carga.

**Hojas de continuación (Trade & Business):** caben ~24 renglones en la primera hoja. Si pasas de ahí, el sistema **crea hojas de continuación automáticamente** ("BILL OF LADING — CONTINUACIÓN, Página 2 de N") con la tabla de carga y el número del BL.

**Global:** sí se puede, con la **Opción A** (una línea por contenedor, escribiendo `contenedor/sello` en *Marks and Numbers*; Global no tiene un campo separado de contenedor/sello). Cada columna apila sus textos por línea de carga, así que las filas quedan parejas mientras cada línea de contenedor ocupe **un solo renglón** y el texto largo vaya en la **última** línea. La Opción B (una línea con Enter) es menos fiable en Global porque las columnas no se alinean renglón a renglón. El total en letras ("FIVE (5) CONTAINERS ONLY") **aún no tiene campo** en Global.

---

## 8. Detalle de un BL

Clic en el número en el listado. Muestra estado, datos generales, carga, quién lo creó/anuló, y al final la **Auditoría** (quién creó, editó, consultó, descargó, solicitó o resolvió anulación, con fecha y hora).

Botones según el estado:
- **Borrador**: *Continuar editando*.
- **Emitido**: *Ver PDF* (se abre en otra pestaña), *Descargar PDF*, y *Solicitar anulación*.
- **Anulación en revisión**: un aviso amarillo indica quién la solicitó y por qué.
- **Anulado**: se ve quién lo anuló, cuándo y el motivo.

El PDF sale con el formato de la empresa a la que pertenece el BL.

---

## 8.1 Duplicar un BL como borrador

Para no volver a digitar remitente, consignatario, mercancía y demás datos de un embarque parecido:
1. Abre el BL que quieres usar de base (en **cualquier estado**, incluso anulado) de la empresa activa.
2. Pulsa **Duplicar como borrador** y confirma.
3. Se abre el asistente con un **borrador nuevo**: revisa y cambia lo que haga falta (contenedores, pesos, fechas) y emítelo cuando esté listo.

Qué se copia: shipper, consignee, notify party, transporte, puertos, líneas de carga y flete.
Qué **no** se copia: el número del BL (se asigna al emitir, siempre uno nuevo), el estado, las anulaciones/aprobaciones, la bitácora, la **fecha de embarque**, la **referencia de la naviera (Bill of Loading Nº)** y el **Shipper's Ref** (cada embarque tiene los suyos: complétalos).
Solo se puede duplicar un BL de la empresa con la que estás trabajando; si es de la otra, primero cambia de empresa.

---

## 9. Anular un BL

### Quien solicita
1. Abre el BL **emitido**.
2. Escribe el **Motivo de la solicitud** — **mínimo 5 caracteres** (si es más corto la página parpadea y no pasa nada).
3. Pulsa **Solicitar anulación** y confirma.
   - Si eres **admin**: el BL queda **anulado** al instante.
   - Si no: el BL pasa a **"En revisión (anulación)"** y se notifica a quienes pueden aprobar.

### Quien aprueba (coordinación/gerencia/admin con rango suficiente)
1. Verás la solicitud en **Pendientes de aprobar** del dashboard o por la **notificación**.
2. En el detalle del BL: **✓ Aprobar anulación** (definitivo, no se deshace) o escribir una nota y **Rechazar**.
3. El solicitante recibe una notificación con el resultado. Si se rechaza, el BL vuelve a **emitido**.

Un BL anulado **no se reutiliza**: su número queda consumido.

---

## 10. Notificaciones
El icono/menú de notificaciones muestra las solicitudes de anulación enviadas y resueltas. Al abrir una se marca como leída; también puedes **marcar todas como leídas**.

## 11. Usuarios (gerencia y admin)
Menú de usuario → **Usuarios**.
- **Crear**: nombre, correo, contraseña y **rol** (admin, gerencia, coordinación, auxiliar).
- **Editar**: datos, rol y (opcional) nueva contraseña.
- **Activar / Desactivar**: un usuario inactivo no puede ingresar. No puedes desactivarte a ti mismo.
- **Eliminar**: solo si el usuario **no tiene BL asociados**; si los tiene, desactívalo. No puedes eliminarte a ti mismo.
- Todo usuario nuevo debe quedar asociado a las empresas con las que va a trabajar (si no ve la empresa en la pantalla de selección, pídelo a un administrador).

## 12. Mi perfil
Actualiza tus datos y **cambia tu contraseña** (usa una clave larga y única).

---

## 13. Preguntas frecuentes

**¿Por qué mi BL no tiene número?** Es un borrador; el número se asigna al **emitir**.

**¿Puedo editar un BL ya emitido?** No. Si hay un error, solicita su anulación y crea uno nuevo.

**¿Cada cuánto me pide ingresar de nuevo?** Tras unos 30 minutos sin actividad, o al cumplirse la jornada máxima configurada (p. ej. 10 horas) aunque estés trabajando. Lo que ya guardaste como borrador no se pierde.

**¿Se pierde mi trabajo si cierro la pestaña?** No: cada vez que pulsas *Siguiente* se guarda el borrador. Lo continúas desde el listado.

**"Number of Original Bs/L" y el número del BL, ¿son lo mismo?** No. El primero es *cuántos originales* se emiten (3); el segundo es el *identificador* (BAQ-0004), que genera el sistema.

**Cometí un error en el número/consecutivo.** No se edita a mano. Anula el BL y emite otro; consulta con coordinación/gerencia.

**El PDF sale en blanco o da error.** Avisa a TI con el número de BL; suele ser un problema del servidor de PDF.

**No me aparece "Cambiar empresa".** Solo aparece si perteneces a más de una empresa.

---
Dudas o fallas: Coordinación TI.
