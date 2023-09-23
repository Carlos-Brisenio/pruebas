const buttons = document.querySelectorAll('.user-type button');
const forms = document.querySelectorAll('.login-form form');

buttons.forEach(button => {
  button.addEventListener('click', () => {
    // Obtener el formulario objetivo a través del atributo data-target
    const targetFormId = button.getAttribute('data-target');
    const targetForm = document.getElementById(targetFormId);

    // Mostrar el formulario objetivo
    forms.forEach(form => {
      form.classList.remove('active');
    });
    targetForm.classList.add('active');

    // Establecer el botón activo
    buttons.forEach(btn => {
      btn.classList.remove('active');
    });
    button.classList.add('active');
  });
});
