<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Entry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #toggleEdit { margin-top: 15px; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Search and Update Pet and Owner Details</h2>
        <div class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" id="search_term" placeholder="Enter Mobile or Unique ID (6 digits)" maxlength="50" required autofocus>
                <button class="btn btn-primary" id="searchBtn">Search</button>
            </div>
        </div>
        <div class="mb-3">
            <h4>QR Code and Barcode</h4>
            <img id="qrCode" src="" alt="QR Code" style="max-width: 150px;">
            <img id="barCode" src="" alt="Barcode" style="max-width: 150px;">
        </div>
        <div id="displayArea" class="mb-3">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <p><strong>Unique ID:</strong> <span id="display_unique_id"></span></p>
                </div>
                <div class="col-md-6 mb-2">
                    <p><strong>Pet Name:</strong> <span id="display_pet_name"></span></p>
                </div>
                <div class="col-md-6 mb-2">
                    <p><strong>Species:</strong> <span id="display_species"></span></p>
                </div>
                <div class="col-md-6 mb-2">
                    <p><strong>Breed:</strong> <span id="display_breed"></span></p>
                </div>
                <div class="col-md-6 mb-2">
                    <p><strong>Gender:</strong> <span id="display_gender"></span></p>
                </div>
                <div class="col-md-6 mb-2">
                    <p><strong>Date of Birth:</strong> <span id="display_dob"></span></p>
                </div>
                <div class="col-md-6 mb-2">
                    <p><strong>Age:</strong> <span id="display_age"></span></p>
                </div>
                <div class="col-md-6 mb-2">
                    <p><strong>Owner Name:</strong> <span id="display_owner_name"></span></p>
                </div>
                <div class="col-md-6 mb-2">
                    <p><strong>Locality:</strong> <span id="display_locality"></span></p>
                </div>
                <div class="col-md-6 mb-2">
                    <p><strong>Mobile Numbers:</strong> <span id="display_mobile_numbers"></span></p>
                </div>
            </div>
        </div>
        <form id="updatePetForm" action="update_pet.php" method="POST" style="display:none;">
            <input type="hidden" id="unique_id" name="unique_id">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="pet_name" class="form-label">Pet Name</label>
                    <input type="text" class="form-control" id="pet_name" name="pet_name" maxlength="50">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="species" class="form-label">Species</label>
                    <select class="form-select" id="species" name="species" required>
                        <option value="Canine">Canine</option>
                        <option value="Feline">Feline</option>
                        <option value="Avian">Avian</option>
                        <option value="Tortoise">Tortoise</option>
                        <option value="Exotic">Exotic</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="breed" class="form-label">Breed</label>
                    <input type="text" class="form-control" id="breed" name="breed" maxlength="50">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="dob" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Age</label>
                    <div class="row">
                        <div class="col-4">
                            <input type="number" class="form-control" id="age_years" name="age_years" min="0" max="20" value="0">
                            <small>Years</small>
                        </div>
                        <div class="col-4">
                            <input type="number" class="form-control" id="age_months" name="age_months" min="0" max="11" value="0">
                            <small>Months</small>
                        </div>
                        <div class="col-4">
                            <input type="number" class="form-control" id="age_days" name="age_days" min="0" max="30" value="0">
                            <small>Days</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="first_name" class="form-label">Owner First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" maxlength="50" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="middle_name" class="form-label">Owner Middle Name</label>
                    <input type="text" class="form-control" id="middle_name" name="middle_name" maxlength="50">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="last_name" class="form-label">Owner Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" maxlength="50" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="locality" class="form-label">Locality</label>
                    <input type="text" class="form-control" id="locality" name="locality" maxlength="50" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Owner Mobile Numbers</label>
                <div id="mobileFields"></div>
                <button type="button" class="btn btn-secondary mt-2" id="addMobile">Add Mobile</button>
            </div>
        </form>
        <div class="mb-3">
            <h4>Schedules</h4>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#planModal">Generate Plan</button>
            <button type="button" class="btn btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#treatmentModal">Add Treatment</button>
            <table class="table table-bordered mt-3" id="schedulesTable">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Treatment</th>
                        <th>Administered</th>
                        <th>Next Due</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <button type="button" class="btn btn-primary" id="toggleEdit">Edit</button>
        <div id="responseArea" class="alert mt-3" style="display:none;"></div>

        <!-- Modal for Plan Generation -->
        <div class="modal fade" id="planModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Generate Plan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="applyPlanForm">
                            <input type="hidden" id="plan_pet_id" name="pet_id">
                            <div class="mb-3">
                                <label for="plan_id" class="form-label">Select Plan</label>
                                <select class="form-select" id="plan_id" name="plan_id" required>
                                    <option value="">Choose a plan</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Apply Plan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Recording Schedule -->
        <div class="modal fade" id="recordModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Record Administration</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="recordScheduleForm">
                            <input type="hidden" id="record_schedule_id" name="schedule_id">
                            <div class="mb-3">
                                <label for="record_date" class="form-label">Date Administered</label>
                                <input type="date" class="form-control" id="record_date" name="date_administered" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Record</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for Adding Treatment -->
        <div class="modal fade" id="treatmentModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Add Treatment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addTreatmentForm">
                            <input type="hidden" id="treatment_pet_id" name="pet_id">
                            <div class="mb-3">
                                <label for="treatment_type" class="form-label">Type</label>
                                <select class="form-select" id="treatment_type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="vaccination">Vaccination</option>
                                    <option value="deworming">Deworming</option>
                                    <option value="tick_flea">Ectoparasite Rx</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="treatment_id" class="form-label">Select Treatment</label>
                                <select class="form-select" id="treatment_id" name="treatment_id" required>
                                    <option value="">Select a type first</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="treatment_notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="treatment_notes" name="notes" maxlength="500"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Treatment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let isEditMode = false;
        let currentPetId = null;
        let currentPetSpecies = null;

        function toggleEditMode() {
            isEditMode = !isEditMode;
            document.getElementById('toggleEdit').textContent = isEditMode ? 'Save' : 'Edit';
            document.getElementById('displayArea').style.display = isEditMode ? 'none' : 'block';
            document.getElementById('updatePetForm').style.display = isEditMode ? 'block' : 'none';
            console.log('Toggled to ' + (isEditMode ? 'Edit' : 'Read-only') + ' mode');
            if (isEditMode) {
                document.querySelectorAll('#mobileFields input, #mobileFields button').forEach(el => el.disabled = false);
            } else {
                const formData = new FormData(document.getElementById('updatePetForm'));
                console.log('Submitting form data:', Array.from(formData.entries()));
                fetch('update_pet.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(text => {
                    console.log('Save raw response:', text);
                    try {
                        const data = JSON.parse(text);
                        console.log('Save parsed response:', data);
                        const responseArea = document.getElementById('responseArea');
                        responseArea.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger');
                        responseArea.textContent = data.message;
                        responseArea.style.display = 'block';
                        if (data.success) {
                            updateDisplayArea();
                        }
                    } catch (parseError) {
                        console.error('JSON parse error:', parseError);
                        document.getElementById('responseArea').className = 'alert alert-danger';
                        document.getElementById('responseArea').textContent = 'Invalid server response: ' + text;
                        document.getElementById('responseArea').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    document.getElementById('responseArea').className = 'alert alert-danger';
                    document.getElementById('responseArea').textContent = 'Network error: ' + error.message;
                    document.getElementById('responseArea').style.display = 'block';
                });
            }
        }

        function updateDisplayArea() {
            document.getElementById('display_unique_id').textContent = document.getElementById('unique_id').value;
            document.getElementById('display_pet_name').textContent = document.getElementById('pet_name').value;
            document.getElementById('display_species').textContent = document.getElementById('species').value;
            document.getElementById('display_breed').textContent = document.getElementById('breed').value;
            document.getElementById('display_gender').textContent = document.getElementById('gender').value;
            document.getElementById('display_dob').textContent = document.getElementById('dob').value;
            document.getElementById('display_age').textContent = 
                `${document.getElementById('age_years').value} yrs ${document.getElementById('age_months').value} mths ${document.getElementById('age_days').value} days`;
            document.getElementById('display_owner_name').textContent = 
                [document.getElementById('first_name').value, 
                 document.getElementById('middle_name').value, 
                 document.getElementById('last_name').value].filter(Boolean).join(' ');
            document.getElementById('display_locality').textContent = document.getElementById('locality').value;
            document.getElementById('display_mobile_numbers').textContent = 
                Array.from(document.querySelectorAll('input[name="mobile_numbers[]"]')).map(input => input.value).filter(Boolean).join(', ');
        }

        document.getElementById('toggleEdit').addEventListener('click', toggleEditMode);
        console.log('Edit button initialized');

        document.getElementById('search_term').focus();
        document.getElementById('search_term').addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('searchBtn').click();
            }
        });

        document.getElementById('searchBtn').addEventListener('click', async () => {
            const search_term = document.getElementById('search_term').value;
            const responseArea = document.getElementById('responseArea');

            if (!search_term) {
                responseArea.className = 'alert alert-warning';
                responseArea.textContent = 'Enter a search term';
                responseArea.style.display = 'block';
                return;
            }

            const search_type = /^\d{6}$/.test(search_term) ? 'unique_id' : 'mobile';

            try {
                const formData = new FormData();
                formData.append('search_term', search_term);
                formData.append('search_type', search_type);

                const response = await fetch('fetch_pet.php', {
                    method: 'POST',
                    body: formData
                });
                const text = await response.text();
                console.log('Search raw response:', text);
                const data = JSON.parse(text);
                console.log('Search parsed response:', data);

                if (data.success) {
                    currentPetId = data.pet_id;
                    currentPetSpecies = data.species;
                    console.log('Set currentPetId:', currentPetId, 'Species:', currentPetSpecies);
                    document.getElementById('unique_id').value = data.unique_id;
                    document.getElementById('display_unique_id').textContent = data.unique_id;
                    document.getElementById('pet_name').value = data.pet_name || '';
                    document.getElementById('display_pet_name').textContent = data.pet_name || '';
                    document.getElementById('species').value = data.species || 'Canine';
                    document.getElementById('display_species').textContent = data.species || 'Canine';
                    document.getElementById('breed').value = data.breed || '';
                    document.getElementById('display_breed').textContent = data.breed || '';
                    document.getElementById('gender').value = data.gender || 'Male';
                    document.getElementById('display_gender').textContent = data.gender || 'Male';
                    document.getElementById('dob').value = data.dob || '';
                    document.getElementById('display_dob').textContent = data.dob || '';
                    document.getElementById('age_years').value = data.pet_age_years || 0;
                    document.getElementById('age_months').value = data.pet_age_months || 0;
                    document.getElementById('age_days').value = data.pet_age_days || 0;
                    document.getElementById('display_age').textContent = 
                        `${data.pet_age_years || 0} yrs ${data.pet_age_months || 0} mths ${data.pet_age_days || 0} days`;
                    document.getElementById('first_name').value = data.first_name || '';
                    document.getElementById('middle_name').value = data.middle_name || '';
                    document.getElementById('last_name').value = data.last_name || '';
                    document.getElementById('display_owner_name').textContent = 
                        [data.first_name, data.middle_name, data.last_name].filter(Boolean).join(' ') || '';
                    document.getElementById('locality').value = data.locality || '';
                    document.getElementById('display_locality').textContent = data.locality || '';
                    document.getElementById('qrCode').src = data.qr_path || '';
                    document.getElementById('barCode').src = data.barcode_path || '';

                    const mobileFields = document.getElementById('mobileFields');
                    mobileFields.innerHTML = '';
                    const displayMobiles = document.getElementById('display_mobile_numbers');
                    displayMobiles.textContent = data.mobile_numbers.join(', ') || '';
                    data.mobile_numbers.forEach((mobile) => {
                        const div = document.createElement('div');
                        div.className = 'input-group mb-2';
                        div.innerHTML = `
                            <input type="text" class="form-control" name="mobile_numbers[]" value="${mobile}" maxlength="50">
                            <button type="button" class="btn btn-danger removeMobile">Remove</button>
                        `;
                        mobileFields.appendChild(div);
                    });

                    loadSchedules(data.pet_id);
                    loadPlans();
                    document.getElementById('treatment_type').value = ''; // Reset type dropdown
                    loadTreatments('');

                    responseArea.style.display = 'none';
                    document.getElementById('search_term').focus();
                } else {
                    responseArea.className = 'alert alert-danger';
                    responseArea.textContent = data.message;
                    responseArea.style.display = 'block';
                }
            } catch (error) {
                console.error('Fetch error:', error);
                responseArea.className = 'alert alert-danger';
                responseArea.textContent = 'Network error: ' + error.message;
                responseArea.style.display = 'block';
            }
        });

        document.getElementById('addMobile').addEventListener('click', () => {
            const mobileFields = document.getElementById('mobileFields');
            const div = document.createElement('div');
            div.className = 'input-group mb-2';
            div.innerHTML = `
                <input type="text" class="form-control" name="mobile_numbers[]" value="" maxlength="50">
                <button type="button" class="btn btn-danger removeMobile">Remove</button>
            `;
            mobileFields.appendChild(div);
        });

        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('removeMobile') && isEditMode) {
                e.target.parentElement.remove();
            }
            if (e.target.classList.contains('recordBtn')) {
                const scheduleId = e.target.dataset.scheduleId;
                document.getElementById('record_schedule_id').value = scheduleId;
                const modal = new bootstrap.Modal(document.getElementById('recordModal'));
                modal.show();
            }
            if (e.target.classList.contains('deleteBtn')) {
                if (confirm('Are you sure you want to delete this schedule?')) {
                    const scheduleId = e.target.dataset.scheduleId;
                    const formData = new FormData();
                    formData.append('schedule_id', scheduleId);
                    fetch('/backend/plans/delete_schedule.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(text => {
                        console.log('Delete schedule raw response:', text);
                        const data = JSON.parse(text);
                        console.log('Delete schedule parsed response:', data);
                        const responseArea = document.getElementById('responseArea');
                        responseArea.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger');
                        responseArea.textContent = data.message;
                        responseArea.style.display = 'block';
                        if (data.success) {
                            loadSchedules(currentPetId);
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        document.getElementById('responseArea').className = 'alert alert-danger';
                        document.getElementById('responseArea').textContent = 'Network error: ' + error.message;
                        document.getElementById('responseArea').style.display = 'block';
                    });
                }
            }
        });

        document.getElementById('dob').addEventListener('change', (e) => {
            const dob = e.target.value;
            if (dob) {
                const today = new Date();
                const birthDate = new Date(dob);
                let ageYears = today.getFullYear() - birthDate.getFullYear();
                let ageMonths = today.getMonth() - birthDate.getMonth();
                if (ageMonths < 0 || (ageMonths === 0 && today.getDate() < birthDate.getDate())) {
                    ageYears--;
                    ageMonths += 12;
                }
                let ageDays = today.getDate() - birthDate.getDate();
                if (ageDays < 0) {
                    ageMonths--;
                    ageDays += new Date(today.getFullYear(), today.getMonth(), 0).getDate();
                }
                document.getElementById('age_years').value = ageYears;
                document.getElementById('age_months').value = ageMonths;
                document.getElementById('age_days').value = ageDays;
            } else {
                document.getElementById('age_years').value = 0;
                document.getElementById('age_months').value = 0;
                document.getElementById('age_days').value = 0;
            }
        });

        function updateDOBFromAge() {
            const ageYears = parseInt(document.getElementById('age_years').value) || 0;
            const ageMonths = parseInt(document.getElementById('age_months').value) || 0;
            const ageDays = parseInt(document.getElementById('age_days').value) || 0;
            if (ageYears > 0 || ageMonths > 0 || ageDays > 0) {
                const today = new Date();
                today.setFullYear(today.getFullYear() - ageYears);
                today.setMonth(today.getMonth() - ageMonths);
                today.setDate(today.getDate() - ageDays);
                const dob = today.toISOString().split('T')[0];
                document.getElementById('dob').value = dob;
            } else {
                document.getElementById('dob').value = '';
            }
        }

        document.getElementById('age_years').addEventListener('input', updateDOBFromAge);
        document.getElementById('age_months').addEventListener('input', updateDOBFromAge);
        document.getElementById('age_days').addEventListener('input', updateDOBFromAge);

        async function loadPlans() {
            try {
                const response = await fetch('/backend/plans/get_plans.php');
                const text = await response.text();
                console.log('Plans raw response:', text);
                const data = JSON.parse(text);
                console.log('Plans parsed response:', data);

                if (!data.success) {
                    throw new Error(data.message || 'Failed to load plans');
                }

                const select = document.getElementById('plan_id');
                select.innerHTML = '<option value="">Choose a plan</option>';
                data.plans.forEach(plan => {
                    if (plan.plan_id) { // Only actual plans
                        const hasCompatibleSteps = plan.steps.some(step => {
                            const tags = step.species_tags ? step.species_tags.split(',') : ['All'];
                            return tags.includes('All') || tags.includes(currentPetSpecies);
                        });
                        if (hasCompatibleSteps) {
                            const option = document.createElement('option');
                            option.value = plan.plan_id;
                            option.textContent = plan.plan_name;
                            select.appendChild(option);
                        }
                    }
                });
            } catch (error) {
                console.error('Load plans error:', error);
                document.getElementById('responseArea').className = 'alert alert-danger';
                document.getElementById('responseArea').textContent = 'Error loading plans: ' + error.message;
                document.getElementById('responseArea').style.display = 'block';
            }
        }

        async function loadTreatments(type = '') {
            try {
                const response = await fetch('/backend/plans/get_plans.php');
                const text = await response.text();
                console.log('Treatments raw response:', text);
                const data = JSON.parse(text);
                console.log('Treatments parsed response:', data);

                if (!data.success) {
                    throw new Error(data.message || 'Failed to load treatments');
                }

                const select = document.getElementById('treatment_id');
                select.innerHTML = '<option value="">Select a treatment</option>';
                data.plans.forEach(plan => {
                    if (!plan.plan_id) { // Standalone treatments
                        plan.steps.forEach(treatment => {
                            if (!treatment.treatment_name) {
                                console.warn('Treatment with step_id=' + treatment.step_id + ' has no treatment_name');
                                return;
                            }
                            const tags = treatment.species_tags ? treatment.species_tags.split(',') : ['All'];
                            if ((type === '' || treatment.type === type) && (tags.includes('All') || tags.includes(currentPetSpecies))) {
                                const option = document.createElement('option');
                                option.value = treatment.step_id;
                                option.textContent = `${treatment.treatment_name} (${treatment.type}${treatment.duration_months ? `, ${treatment.duration_months} months` : ''})`;
                                select.appendChild(option);
                            }
                        });
                    }
                });
            } catch (error) {
                console.error('Load treatments error:', error);
                document.getElementById('responseArea').className = 'alert alert-danger';
                document.getElementById('responseArea').textContent = 'Error loading treatments: ' + error.message;
                document.getElementById('responseArea').style.display = 'block';
            }
        }

        document.getElementById('treatment_type').addEventListener('change', (e) => {
            const type = e.target.value;
            loadTreatments(type);
        });

        async function loadSchedules(pet_id) {
            try {
                const formData = new FormData();
                formData.append('pet_id', pet_id);
                const response = await fetch('/backend/plans/get_schedules.php', {
                    method: 'POST',
                    body: formData
                });
                const text = await response.text();
                console.log('Schedules raw response:', text);
                const data = JSON.parse(text);
                console.log('Schedules parsed response:', data);

                if (!data.success) {
                    throw new Error(data.message || 'Failed to load schedules');
                }

                const tbody = document.querySelector('#schedulesTable tbody');
                tbody.innerHTML = '';
                data.schedules.forEach(schedule => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${schedule.type}</td>
                        <td>${schedule.treatment_name}</td>
                        <td>${schedule.date_administered || ''}</td>
                        <td>${schedule.next_due || ''}</td>
                        <td>
                            ${schedule.next_due && !schedule.date_administered ? `<button class="btn btn-sm btn-primary recordBtn" data-schedule-id="${schedule.schedule_id}">Record</button>` : ''}
                            <button class="btn btn-sm btn-danger deleteBtn" data-schedule-id="${schedule.schedule_id}">Delete</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } catch (error) {
                console.error('Load schedules error:', error);
                document.getElementById('responseArea').className = 'alert alert-danger';
                document.getElementById('responseArea').textContent = 'Error loading schedules: ' + error.message;
                document.getElementById('responseArea').style.display = 'block';
            }
        }

        document.getElementById('applyPlanForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('pet_id', currentPetId);
            console.log('Submitting plan with pet_id:', currentPetId);
            const responseArea = document.getElementById('responseArea');

            try {
                const response = await fetch('/backend/plans/apply_plan.php', {
                    method: 'POST',
                    body: formData
                });
                const text = await response.text();
                console.log('Apply plan raw response:', text);
                const data = JSON.parse(text);
                console.log('Apply plan parsed response:', data);

                if (data.success) {
                    responseArea.className = 'alert alert-success';
                    responseArea.textContent = data.message;
                    responseArea.style.display = 'block';
                    loadSchedules(currentPetId);
                    bootstrap.Modal.getInstance(document.getElementById('planModal')).hide();
                } else {
                    responseArea.className = 'alert alert-danger';
                    responseArea.textContent = data.message;
                    responseArea.style.display = 'block';
                }
            } catch (error) {
                console.error('Fetch error:', error);
                responseArea.className = 'alert alert-danger';
                responseArea.textContent = 'Network error: ' + error.message;
                responseArea.style.display = 'block';
            }
        });

        document.getElementById('recordScheduleForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const responseArea = document.getElementById('responseArea');

            try {
                const response = await fetch('/backend/plans/record_schedule.php', {
                    method: 'POST',
                    body: formData
                });
                const text = await response.text();
                console.log('Record schedule raw response:', text);
                const data = JSON.parse(text);
                console.log('Record schedule parsed response:', data);

                if (data.success) {
                    responseArea.className = 'alert alert-success';
                    responseArea.textContent = data.message;
                    responseArea.style.display = 'block';
                    loadSchedules(currentPetId);
                    bootstrap.Modal.getInstance(document.getElementById('recordModal')).hide();
                } else {
                    responseArea.className = 'alert alert-danger';
                    responseArea.textContent = data.message;
                    responseArea.style.display = 'block';
                }
            } catch (error) {
                console.error('Fetch error:', error);
                responseArea.className = 'alert alert-danger';
                responseArea.textContent = 'Network error: ' + error.message;
                responseArea.style.display = 'block';
            }
        });

        document.getElementById('addTreatmentForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('pet_id', currentPetId);
            console.log('Submitting treatment with pet_id:', currentPetId, 'Species:', currentPetSpecies);
            const responseArea = document.getElementById('responseArea');

            try {
                const response = await fetch('/backend/plans/add_treatment.php', {
                    method: 'POST',
                    body: formData
                });
                const text = await response.text();
                console.log('Add treatment raw response:', text);
                const data = JSON.parse(text);
                console.log('Add treatment parsed response:', data);

                if (data.success) {
                    responseArea.className = 'alert alert-success';
                    responseArea.textContent = data.message;
                    responseArea.style.display = 'block';
                    loadSchedules(currentPetId);
                    bootstrap.Modal.getInstance(document.getElementById('treatmentModal')).hide();
                } else {
                    responseArea.className = 'alert alert-danger';
                    responseArea.textContent = data.message;
                    responseArea.style.display = 'block';
                }
            } catch (error) {
                console.error('Fetch error:', error);
                responseArea.className = 'alert alert-danger';
                responseArea.textContent = 'Network error: ' + error.message;
                responseArea.style.display = 'block';
            }
        });

        document.getElementById('treatment_type').addEventListener('change', (e) => {
            const type = e.target.value;
            loadTreatments(type);
        });
    </script>
</body>
</html>