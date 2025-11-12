<style>
.card {
    margin-bottom: 30px;
    margin-left: 20px;
    margin-right: 20px;
}

.modal-body {
    max-width: 100%;
    padding: 1rem;
}

.modal-body img {
    width: 100%;
    max-width: 100%;
    height: auto;
    display: block;
}

.modal-body * {
    box-sizing: border-box;
}

#card-header-faq {
    background: #337AB7;
    color: white;
}
</style>

<!-- MODALES ADMINISTRADOR -->

<!-- Diagrama -->
<div class="modal fade" id="diagrama" tabindex="-1" aria-labelledby="diagramaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="diagramaLabel">Diagrama del Proceso Administrativo</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center">
                    <img src="{{ asset('images/diagrama-proceso.png') }}" alt="Diagrama del Proceso">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editarDiagramaModal">
                    <i class="bi bi-pencil"></i> Editar
                </button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Proceso -->
<div class="modal fade" id="proceso" tabindex="-1" aria-labelledby="procesoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title" id="procesoLabel">Proceso de Prácticas Profesionales (Admin)</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-center">
                    <img src="{{ asset('images/proceso-practicas.png') }}" alt="Proceso">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editarProcesoModal">
                    <i class="bi bi-pencil"></i> Editar
                </button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Detalles -->
<div class="modal fade" id="detalles" tabindex="-1" aria-labelledby="detallesLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, #00124E, #003B95); color:#ffffff">
                <h2 class="modal-title" id="detallesLabel">DETALLES DEL PROCESO</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="card border-0">1.- El alumno registra la solicitud de Prácticas Profesionales Si la empresa no esta registrada, selecciona la opción de Solicitar registro de empresa. (Llega un correo de notificación al Encargado), si ya esta registrada llega un correo de notificación al tutor académico para que autorice la solicitud.</div>
                <div class="card border-0">2.- Si el alumno, solicitó registro de empresa o de dirección de empresa, el Encargado de Prácticas Profesionales registra la empresa. (Llega un mail de notificación al alumno y al tutor académico)</div>
                <div class="card border-0">3.- El Tutor Académico autoriza la solicitud de Prácticas Profesionales. El jefe de área puede autorizar por ausencia,consultando al alumno por su clave única o nombre, entra en la opcion de "Autorizaciones", en la opción de "Tutor Académico" y da clic en el botón que dice "Autorizar por Ausencia". (Llega un correo de notificación al Coordinador de Carrera y al alumno.)</div>
                <div class="card border-0">4.- El Coordinador de Carrera autoriza la solicitud de Prácticas Profesionales. El jefe de área puede autorizar por ausencia,consultando al alumno por su clave única o nombre, entra en la opcion de "Autorizaciones", en la opción de "Coordinador" y da clic en el botón que dice "Autorizar por Ausencia". (Llega un correo de notificación al Encargado de Prácticas y al alumno.)</div>
                <div class="card border-0">5.- El Encargado de Prácticas autoriza la solicitud de Prácticas Profesionales. El jefe de área puede autorizar por ausencia,consultando al alumno por su clave única o nombre, entra en la opcion de "Autorizaciones", en la opción de "Encargado de Prácticas" y da clic en el botón que dice "Autorizar por Ausencia". (Llega un correo de notificación al alumno.)</div>
                <div class="card border-0">6.- El alumno sube reportes, tiene que subir el reporte final para seguir con el proceso.</div>
                <div class="card border-0">7.- El alumno evalua a la empresa. (Llega un correo al asesor externo para que evalue al alumno.)</div>
                <div class="card border-0">8.- El asesor externo (Jefe inmediato) evalua al alumno. En caso de que el asesor externo no lo pueda hacer, lo hace el Encargado de Prácticas.(Llega un correo de notificación al Encargado de Prácticas.)</div>
                <div class="card border-0">9.- El encargado de Prácticas Libera al Alumno. (Llega un correo de notificación al Secretario Académico.)</div>
                <div class="card border-0">10.- Secretaria Académica Genera la Constancia de Validación de Prácticas Profesionales. Se cargan los créditos al kardex del alumno.</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editarDetallesModal">
                    <i class="bi bi-pencil"></i> Editar
                </button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- FAQs -->
<div class="modal fade" id="faq" tabindex="-1" aria-labelledby="faqLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(90deg, #00124E, #003B95); color:#ffffff">
                <h2 class="modal-title" id="faqLabel">PREGUNTAS FRECUENTES</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="card">
                    <div class="card-header" id="card-header-faq">1.- ¿Cómo comienzo mi trámite de prácticas profesionales?</div>
                    <div class="card-body">Una vez que hablaste con tu tutor académico acerca de tus prácticas profesionales, ingresa al sitio <a href="http://imat.uaslp.mx/secacademica" class="link-primary">http://imat.uaslp.mx/secacademica</a> en el menu Alumnos, ingresa con tu clave única y tu contraseña (del DUI), e ingresa en la opción "Registrarse"/"Registrar Prácticas I" ó "Registrar Prácticas II" y llena la solicitud de Prácticas Profesionales.</div>
                </div>
                <div class="card">
                    <div class="card-header" id="card-header-faq">2.-¿Cómo entro al sistema de prácticas profesionales?</div>
                    <div class="card-body">Ingresa al sitio <a href="http://imat.uaslp.mx/secacademica" class="link-primary">http://imat.uaslp.mx/secacademica</a> en el menu Alumnos, ingresa con tu clave única y tu contraseña (del DUI). Para ingresar es necesario cumplir con los requisitos. (Tener créditos suficientes para hacer prácticas profesionales y estár activo dentro de la universidad, es decir, haber realizado el pago del periodo en curso).</div>
                </div>
                <div class="card">
                    <div class="card-header" id="card-header-faq">3.-Cómo registro mis datos?</div>
                    <div class="card-body">Selecciona la opción "Registrar Prácticas I" ó "Registrar Prácticas II" y llena la solicitud de Prácticas Profesionales. No deben quedar campos vacios, y si la empresa o la dirección de la empresa donde realizaras las prácticas profesionales no esta registrada deberas indicar en el combo (dirección de la empresa ) que solicitas alta de la empresa o alta de la dirección de empresa para que el encargado de prácticas la registre, una vez llenada correctamente la solicitud da click en el botón enviar, si elegiste la opción registrar Empresa ó dirección de Empresa le llegará un correo al Encargado de Prácticas de tu carrera para que la registre, después llegara un correo al tutor académico para que autorice tu solicitud de prácticas profesionales, posteriormente te autorizara el coordinador de carrera y el encargado de Prácticas Profesionales.</div>
                </div>
                <div class="card">
                    <div class="card-header" id="card-header-faq">4.-En caso de que mi asesor no lo localice como puedo avanzar en el trámite?</div>
                    <div class="card-body">Si tu tutor académico no esta disponible para autorizar tu solicitud de Prácticas Profesionales el Jefe de Área puede autorizar tu solicitud por ausencia del asesor.</div>
                </div>
                <div class="card">
                    <div class="card-header" id="card-header-faq">5.-Si el encargado de prácticas profesionales está ausente quien me puede autorizar el trámite?</div>
                    <div class="card-body">El Jefe de Área realizara las actividades propias del Encargado de Prácticas, por ausencia del mismo. El jefe de Área entra en su cuenta y tiene estas opciones disponibles.</div>
                </div>
                <div class="card">
                    <div class="card-header" id="card-header-faq">6.-Si mi asesor está ausente como continuo con el trámite?</div>
                    <div class="card-body">Si tu tutor académico no esta disponible para autorizar tu solicitud de Prácticas Profesionales el Jefe de Área puede autorizar tu solicitud por ausencia del asesor. El Jefe de Área entra al sistema con su rpe y respectiva contraseña, busca al alumno pr su clave única o nombre y en la opción que dice autorizaciones puede autorizar por ausencia del tutor académico, coordindor de carrera o encargado de Prácticas.</div>
                </div>
                <div class="card">
                    <div class="card-header" id="card-header-faq">7.-Si el coordinador está ausente cómo avanzo en mi trámite?</div>
                    <div class="card-body">Si el Coordinador de Carrera no esta disponible para autorizar tu solicitud de Prácticas Profesionales, el Jefe de Área puede autorizar tu solicitud por ausencia del coordinador de carrera.</div>
                </div>
                <div class="card">
                    <div class="card-header" id="card-header-faq">8.-En caso de que no pueda mi asesor externo evaluarme en el sistema cómo le hago?</div>
                    <div class="card-body">Tu asesor externo deberá evaluarte en el formato tradicional (papel) y deberás entregárselo al encargado de Prácticas Profesionales para que él registre tu evaluación en el sistema.</div>
                </div>
                <div class="card">
                    <div class="card-header" id="card-header-faq">9.-Cómo hago la evaluación en el sistema?</div>
                    <div class="card-body">Entra al sistema, en la opción "Evaluación", responder la encuesta de evaluación y "Enviar", en ese momento se enviara un link a tu asesor externo para que evalue tu desempeño durante tus prácticas profesionales.</div>
                </div>
                <div class="card">
                    <div class="card-header" id="card-header-faq">10.-Cómo se da de alta una nueva empresa en el sistema?</div>
                    <div class="card-body">Ingresa en la opción que dice "Registrar Nueva Empresa", llena el formulario con los datos de la empresa y da clic en el botón "Registrar Empresa".</div>
                </div>
                <div class="card">
                    <div class="card-header" id="card-header-faq">11.-Qué debo tomar en cuenta para elegir hacer prácticas profesionales en una empresa?</div>
                    <div class="card-body">Hay una opción que dice "Estadisticas de la Empresa", ahi podrás seleccionar cada una de las empresas que ya estan registradas y visualizar como fue evaluada la empresa por cada uno de los alumnos que ha hecho prácticas profesionales en dicha empresa, deberás considerar que la actividad que realizarás en tus prácticas profesionales este vinculada a las actividades propias de tu carrera.</div>
                </div>
                <div class="card">
                    <div class="card-header" id="card-header-faq">12.-Cómo debo subir mis reportes en el sistema?</div>
                    <div class="card-body">Ingresa al sistema y en la opción del menú "Solicitud de Prácticas I" ó "Solicitud de Prácticas II", en la pestaña "Nuevo Reporte", se desplegara un formulario en el cual podrás subir tus reportes, deberás indicar el periodo que comprende el reporte, puedes anexar un breve resumen de las actividades que realizaste en prácticas profesionales durante este periodo, puedes anexar un archivo (tu reporte en pdf) y es muy importante que si se trata de tu último reporte lo marques como "Reporte Final", para que de esta manera se habilite la opción para que evalues a la empresa.</div>
                </div>
                <div class="card">
                    <div class="card-header" id="card-header-faq">13.-Cuáles son los formatos válidos para subir mis reportes?</div>
                    <div class="card-body">Es altamente recomendable subir tus reportes en formato pdf y no de un tamaño superior a 10 MB.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editarFaqModal">
                    <i class="bi bi-pencil"></i> Editar
                </button>
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- MODALES DE EDICIÓN (placeholders, puedes personalizar su contenido después) -->

<div class="modal fade" id="editarDiagramaModal" tabindex="-1" aria-labelledby="editarDiagramaLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h2 class="modal-title" id="editarDiagramaLabel">Editar Diagrama</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form>
          <label class="form-label">Seleccionar nuevo diagrama</label>
          <input type="file" class="form-control" accept="image/*">
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success">Guardar Cambios</button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editarProcesoModal" tabindex="-1" aria-labelledby="editarProcesoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h2 class="modal-title" id="editarProcesoLabel">Editar Proceso</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form>
          <label class="form-label">Seleccionar nueva imagen del proceso</label>
          <input type="file" class="form-control" accept="image/*">
        </form>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success">Guardar Cambios</button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editarDetallesModal" tabindex="-1" aria-labelledby="editarDetallesLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h2 class="modal-title" id="editarDetallesLabel">EDITAR DETALLES DEL PROCESO</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <textarea class="form-control" rows="8">1.- El alumno registra la solicitud de Prácticas Profesionales Si la empresa no esta registrada, selecciona la opción de Solicitar registro de empresa. (Llega un correo de notificación al Encargado), si ya esta registrada llega un correo de notificación al tutor académico para que autorice la solicitud.

2.- Si el alumno, solicitó registro de empresa o de dirección de empresa, el Encargado de Prácticas Profesionales registra la empresa. (Llega un mail de notificación al alumno y al tutor académico)

3.- El Tutor Académico autoriza la solicitud de Prácticas Profesionales. El jefe de área puede autorizar por ausencia,consultando al alumno por su clave única o nombre, entra en la opcion de "Autorizaciones", en la opción de "Tutor Académico" y da clic en el botón que dice "Autorizar por Ausencia". (Llega un correo de notificación al Coordinador de Carrera y al alumno.)

4.- El Coordinador de Carrera autoriza la solicitud de Prácticas Profesionales. El jefe de área puede autorizar por ausencia,consultando al alumno por su clave única o nombre, entra en la opcion de "Autorizaciones", en la opción de "Coordinador" y da clic en el botón que dice "Autorizar por Ausencia". (Llega un correo de notificación al Encargado de Prácticas y al alumno.)

5.- El Encargado de Prácticas autoriza la solicitud de Prácticas Profesionales. El jefe de área puede autorizar por ausencia,consultando al alumno por su clave única o nombre, entra en la opcion de "Autorizaciones", en la opción de "Encargado de Prácticas" y da clic en el botón que dice "Autorizar por Ausencia". (Llega un correo de notificación al alumno.)

6.- El alumno sube reportes, tiene que subir el reporte final para seguir con el proceso.

7.- El alumno evalua a la empresa. (Llega un correo al asesor externo para que evalue al alumno.)

8.- El asesor externo (Jefe inmediato) evalua al alumno. En caso de que el asesor externo no lo pueda hacer, lo hace el Encargado de Prácticas.(Llega un correo de notificación al Encargado de Prácticas.)

9.- El encargado de Prácticas Libera al Alumno. (Llega un correo de notificación al Secretario Académico.)

10.- Secretaria Académica Genera la Constancia de Validación de Prácticas Profesionales. Se cargan los créditos al kardex del alumno.
        </textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success">Guardar Cambios</button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="editarFaqModal" tabindex="-1" aria-labelledby="editarFaqLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h2 class="modal-title" id="editarFaqLabel">EDITAR PREGUNTAS FRECUENTES</h2>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <textarea class="form-control" rows="8">1.- ¿Cómo comienzo mi trámite de prácticas profesionales?
2.-¿Cómo entro al sistema de prácticas profesionales?
3.-Cómo registro mis datos?
4.-En caso de que mi asesor no lo localice como puedo avanzar en el trámite?
5.-Si el encargado de prácticas profesionales está ausente quien me puede autorizar el trámite?
6.-Si mi asesor está ausente como continuo con el trámite?
7.-Si el coordinador está usente cómo avanzo en mi trámite?
8.-En caso de que no pueda mi asesor externo evaluarme en el sistema cómo le hago?
9.-Cómo hago la evaluación en el sistema?
10.-Cómo se da de alta una nueva empresa en el sistema?
11.-Qué debo tomar en cuenta para elegir hacer prácticas profesionales en una empresa?
12.-Cómo debo subir mis reportes en el sistema?
13.-Cuáles son los formatos válidos para subir mis reportes?</textarea>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success">Guardar Cambios</button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>
