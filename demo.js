// demo.js - Simulates database functionality for GitHub Pages demo

// Initialize localStorage if empty
if (!localStorage.getItem('patients')) {
    localStorage.setItem('patients', JSON.stringify([]));
}

// Save patient to localStorage (simulates database insert)
function savePatient() {
    // Get form values
    const patientId = document.getElementById('patientId').value.trim();
    const species = document.getElementById('species').value;
    const admissionDate = document.getElementById('admissionDate').value;
    const condition = parseInt(document.getElementById('condition').value);
    const status = document.getElementById('status').value;
    const commonName = document.getElementById('commonName').value.trim();
    const injuries = document.getElementById('injuries').value.trim();

    // Validation (simulating PHP validation)
    const errors = [];

    // Patient ID format validation
    if (!/^RSPCA-\d{4}-[A-Z0-9]{5}$/.test(patientId)) {
        errors.push('Invalid Patient ID format. Must be: RSPCA-YYYY-XXXXX');
    }

    // Check for duplicate Patient ID
    const patients = JSON.parse(localStorage.getItem('patients'));
    if (patients.some(p => p.patientId === patientId)) {
        errors.push(`Patient ID "${patientId}" already exists`);
    }

    // Species validation
    if (!species) {
        errors.push('Species selection is required');
    }

    // Date validation
    if (!admissionDate) {
        errors.push('Admission date is required');
    } else if (admissionDate > new Date().toISOString().split('T')[0]) {
        errors.push('Admission date cannot be in the future');
    }

    // Condition validation
    if (condition < 1 || condition > 5) {
        errors.push('Condition must be between 1 and 5');
    }

    // Status validation
    if (!status) {
        errors.push('Current status is required');
    }

    // Show errors or save
    if (errors.length > 0) {
        showMessage('❌ ' + errors.join('<br>'), 'error');
        
        // Update SQL demo for failed validation
        document.getElementById('sqlCode').textContent = `// VALIDATION FAILED - No database insertion
${errors.map(err => `// ❌ ${err}`).join('\n')}

// Result: Form shows error messages to user
// No SQL query executed
// Database remains unchanged`;
        
        return;
    }

    // Create patient object (simulating database row)
    const patient = {
        patientId,
        species,
        admissionDate,
        condition,
        status,
        commonName,
        injuries,
        createdAt: new Date().toISOString()
    };

    // Save to localStorage (simulating database insert)
    patients.push(patient);
    localStorage.setItem('patients', JSON.stringify(patients));

    // Show success message
    showMessage(`✅ Patient <strong>${patientId}</strong> saved successfully!`, 'success');
    
    // Update stats
    updateStats();

    // Update SQL demo for successful insertion
    document.getElementById('sqlCode').textContent = `// SQL INSERT statement executed:
INSERT INTO PATIENT (
    PatientID, 
    SpeciesID, 
    AdmissionDate, 
    ConditionOnArrival, 
    CurrentStatus, 
    CommonName, 
    Injuries
) VALUES (
    '${patientId}',
    '${species}', 
    '${admissionDate}', 
    ${condition}, 
    '${status}', 
    '${commonName}', 
    '${injuries}'
);

// Execution result:
✅ Query successful - 1 row affected
✅ Record inserted into PATIENT table
✅ User redirected with success message`;

    // Clear form
    document.getElementById('patientForm').reset();
    
    // Reset date to today
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('admissionDate').value = today;
    document.getElementById('conditionValue').textContent = '3';
}

// Helper function to show messages
function showMessage(text, type) {
    const messageDiv = document.getElementById('message');
    messageDiv.innerHTML = text;
    messageDiv.className = `message ${type}`;
    messageDiv.style.display = 'block';
    
    setTimeout(() => {
        messageDiv.style.display = 'none';
    }, 5000);
}

// Update statistics display
function updateStats() {
    const patients = JSON.parse(localStorage.getItem('patients'));
    const today = new Date().toISOString().split('T')[0];
    const todayPatients = patients.filter(p => p.admissionDate === today).length;
    
    if (document.getElementById('patientCount')) {
        document.getElementById('patientCount').textContent = patients.length;
        document.getElementById('todayCount').textContent = todayPatients;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateStats();
});
