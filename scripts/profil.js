document.getElementById('profileForm').addEventListener('submit', function(event) {
    let isValid = true;

    // Réinitialisation des messages d'erreur
    document.getElementById('lastNameError').textContent = '';
    document.getElementById('firstNameError').textContent = '';
    document.getElementById('employeeNumberError').textContent = '';

    // Validation du nom de famille
    if (!document.getElementById('lastName').value.trim()) {
        document.getElementById('lastNameError').textContent = 'Le nom de famille est requis.';
        isValid = false;
    }

    // Validation du prénom
    if (!document.getElementById('firstName').value.trim()) {
        document.getElementById('firstNameError').textContent = 'Le prénom est requis.';
        isValid = false;
    }

    // Validation du numéro d'employé (facultatif mais doit être un nombre si rempli)
    let employeeNumber = document.getElementById('employeeNumber').value.trim();
    if (employeeNumber && isNaN(employeeNumber)) {
        document.getElementById('employeeNumberError').textContent = 'Le numéro d\'employé doit être un nombre.';
        isValid = false;
    }

    if (!isValid) {
        event.preventDefault();
    }
});