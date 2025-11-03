class ModalManager {
  constructor() {
    this.modals = [];
  }
  

  //esto sirve si el contenido esta dentro de la pagina por ejeplo
  crearModal(contenidoHtml) {
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

    this.modals.push(fondo);
  }

  //metodo que lo que necesita es la url del archivo que tiene el contenido
crearModalDesdeUrl(url, callback) {
    fetch(url)
      .then(res => res.text())
      .then(html => {
        this.crearModal(html);
        if (typeof callback === "function") callback();
      })
      .catch(err => {
        console.error('Error al cargar la plantilla:', err);
      });
}


  cerrarModal() {
    if (this.modals.length > 0) {
      const fondo = this.modals.pop();
      fondo.remove();
    }
  }
}

window.modalManager = new ModalManager();