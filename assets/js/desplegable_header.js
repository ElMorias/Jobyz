window.addEventListener('load', function () {
  const profileMenus = document.querySelectorAll('.profile-menu');
  profileMenus.forEach(function(menu){
      const button = menu.querySelector('.profile-icon');
      const dropdown = menu.querySelector('.menu-dropdown');
      if (!button || !dropdown) return;
      button.addEventListener('click', function(e){
        e.stopPropagation();
        dropdown.classList.toggle('active');
      });
      document.addEventListener('click', function(){
        dropdown.classList.remove('active');
      });
  });
});