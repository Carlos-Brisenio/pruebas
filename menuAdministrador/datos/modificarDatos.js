const btnModify = document.getElementById('btn-modify');
const btnSave = document.getElementById('btn-save');
const inputs = document.querySelectorAll('input');
const profilePhoto = document.getElementById('profile-photo');
const photoUpload = document.getElementById('photo-upload');

// Habilitar edición de campos y foto
btnModify.addEventListener('click', () => {
  inputs.forEach(input => {
    input.disabled = false;
  });
  btnSave.style.display = 'block';
  photoUpload.disabled = false;
});

// Cargar foto del usuario
photoUpload.addEventListener('change', () => {
  const file = photoUpload.files[0];
  const reader = new FileReader();

  reader.onload = () => {
    profilePhoto.src = reader.result;
  };

  if (file) {
    reader.readAsDataURL(file);
  }
});

// Guardar cambios
btnSave.addEventListener('click', (e) => {
  e.preventDefault();
  guardarCambios();
  inputs.forEach(input => {
    input.disabled = true;
  });
  btnSave.style.display = 'none';
  photoUpload.disabled = true;
});

// Ocultar el botón "Guardar cambios" al cargar la página
btnSave.style.display = 'none';

function guardarCambios() {
  const nombre = document.getElementById('name').value;
  const email = document.getElementById('email').value;
  const telefono = document.getElementById('phone').value;

  //lógica para guardar los cambios
  // por ejemplo, enviar los datos al servidor o actualizar la base de datos
  console.log('Se han guardado los cambios:');
  console.log('Nombre:', nombre);
  console.log('Email:', email);
  console.log('Teléfono:', telefono);
}
