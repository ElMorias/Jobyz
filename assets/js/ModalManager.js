class ModalManager {
  constructor() {
    this.modal = null;
  }

  //esto sirve si el contenido esta dentro de la pagina por ejeplo
  crearModal(contenidoHtml) {
    // Si ya hay una modal abierta, la cerramos
    if (this.modal) this.cerrarModal();

    // Crear fondo
    const fondo = document.createElement('div');
    fondo.classList.add('modal-fondo');

    // Crear contenedor
    const contenedor = document.createElement('div')
    contenedor.classList.add('modal-contenedor');

    // Botón de cerrar
    const cerrar = document.createElement('button');
    cerrar.classList.add('modal-cerrar');
    cerrar.innerText = '×';
    cerrar.onclick = () => this.cerrarModal();

    // Meter contenido
    contenedor.innerHTML = contenidoHtml;
    contenedor.appendChild(cerrar);
    fondo.appendChild(contenedor);
    document.body.appendChild(fondo);

    this.modal = fondo;
  }

  //metodo que lo que necesita es la url del archivo que tiene el contenido
  crearModalDesdeUrl(url) {
    fetch(url)
      .then(res => res.text())
      .then(html => {
        this.crearModal(html);
      })
      .catch(err => {
        console.error('Error al cargar la plantilla:', err);
      });
  }


  cerrarModal() {
    if (this.modal) {
      this.modal.remove();
      this.modal = null;
    }
  }
}