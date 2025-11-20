document.addEventListener("DOMContentLoaded", function () {
  const carrousel = document.getElementById("carrouselLogos");
  if (!carrousel) return;

  let scrollPos = 0;
  const logoWidth = carrousel.querySelector('.logo-slide')?.offsetWidth || 140;
  let timer;

  function autoScroll() {
    // Si el scroll llega al final, vuelve al principio
    if (scrollPos + carrousel.offsetWidth >= carrousel.scrollWidth - 5) {
      scrollPos = 0;
    } else {
      scrollPos += logoWidth + 38; // 38px es el gap, ajústalo si cambias en CSS
    }
    carrousel.scrollTo({left: scrollPos, behavior: 'smooth'});
    timer = setTimeout(autoScroll, 2400);
  }

  // Para iniciar una vez cargado todo
  setTimeout(autoScroll, 2200);

  // Bonus: detener auto si el usuario pasa el mouse/touch
  carrousel.addEventListener("mouseenter", () => clearTimeout(timer));
  carrousel.addEventListener("mouseleave", () => autoScroll());
});