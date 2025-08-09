<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Facing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Pet Management</h2>
        <form id="searchForm" class="mb-3">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control" id="searchTerm" placeholder="Enter mobile number or unique ID (6 digits)" required>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </div>
        </form>
        <div id="petDetails" class="card mb-3" style="display:none;">
            <div class="card-body">
                <h5 class="card-title">Pet Details</h5>
                <div id="petInfo"></div>
                <img id="qrCode" class="img-fluid" style="max-width:100px; display:none;">
                <img id="barcode" class="img-fluid" style="max-width:200px; display:none;">
            </div>
        </div>
        <div id="scheduleArea" style="display:none;">
            <h3>Schedule</h3>
            <table class="table table-bordered" id="scheduleTable">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Treatment</th>
                        <th>Administered</th>
                        <th>Next Due</th>
                        <th>Notes</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <h3>Add Treatment</h3>
            <form id="addTreatmentForm">
                <input type="hidden" id="petId" name="pet_id">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="treatmentType" class="form-label">Type</label>
                        <select class="form-select" id="treatmentType" name="type" required>
                            <option value="vaccination">Vaccination</option>
                            <option value="deworming">Deworming</option>
                            <option value="tick_flea">Ectoparasite Rx</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="treatmentName" class="form-label">Treatment</label>
                        <select class="form-select" id="treatmentName" name="treatment_id" required>
                            <option value="">Select Treatment</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="treatmentNotes" class="form-label">Notes</label>
                        <textarea class="form-control" id="treatmentNotes" name="notes" maxlength="500"></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Add Treatment</button>
            </form>
            <h3>Generate Plan</h3>
            <form id="generatePlanForm">
                <input type="hidden" id="planPetId" name="pet_id">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="planName" class="form-label">Plan</label>
                        <select class="form-select" id="planName" name="plan_id" required>
                            <option value="">Select Plan</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="start_date" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Generate Plan</button>
            </form>
        </div>
        <div id="responseArea" class="alert mt-3" style="display:none;"></div>
    </div>
    <script>
        document.getElementById('searchForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const searchTerm = document.getElementById('searchTerm').value.trim();
            const responseArea = document.getElementById('responseArea');

            if (!searchTerm) {
                responseArea.className = 'alert alert-danger';
                responseArea.textContent = 'Please enter a mobile number or unique ID';
                responseArea.style.display = 'block';
                return;
            }

            const formData = new FormData();
            formData.append('search_term', searchTerm);
            // Auto-detect: 6 digits = unique_id, else mobile
            const searchType = (searchTerm.length === 6 && /^\d{6}$/.test(searchTerm)) ? 'unique_id' : 'mobile';
            formData.append('search_type', searchType);

            try {
                const response = await fetch('/backend/fetch_pet.php', {
                    method: 'POST',
                    body: formData
                });
                const text = await response.text();
                console.log('Fetch pet raw response:', text);

                let data;
                try {
                    data = JSON.parse(text);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    responseArea.className = 'alert alert-danger';
                    responseArea.textContent = 'Invalid server response';
                    responseArea.style.display = 'block';
                    return;
                }
                console.log('Fetch pet parsed response:', data);

                const petDetails = document.getElementById('petDetails');
                const petInfo = document.getElementById('petInfo');
                const qrCode = document.getElementById('qrCode');
                const barcode = document.getElementById('barcode');
                const scheduleArea = document.getElementById('scheduleArea');

                if (data.success && data.pet) {
                    petInfo.innerHTML = `
                        <p><strong>Name:</strong> ${data.pet.pet_name || 'N/A'}</p>
                        <p><strong>Unique ID:</strong> ${data.pet.unique_id || 'N/A'}</p>
                        <p><strong>Species:</strong> ${data.pet.species || 'N/A'}</p>
                        <p><strong>Breed:</strong> ${data.pet.breed || 'N/A'}</p>
                        <p><strong>Gender:</strong> ${data.pet.gender || 'N/A'}</p>
                        <p><strong>DOB:</strong> ${data.pet.dob || 'N/A'}</p>
                        <p><strong>Age:</strong> ${data.pet.pet_age_years || 0} years, ${data.pet.pet_age_months || 0} months</p>
                        <p><strong>Owner:</strong> ${data.pet.first_name || ''} ${data.pet.middle_name || ''} ${data.pet.last_name || ''}</p>
                        <p><strong>Locality:</strong> ${data.pet.locality || 'N/A'}</p>
                        <p><strong>Mobile:</strong> ${data.pet.mobile_numbers ? data.pet.mobile_numbers.join(', ') : 'N/A'}</p>
                    `;
                    qrCode.src = data.pet.qr_path || '';
                    qrCode.style.display = data.pet.qr_path ? 'block' : 'none';
                    barcode.src = data.pet.barcode_path || '';
                    barcode.style.display = data.pet.barcode_path ? 'block' : 'none';
                    petDetails.style.display = 'block';
                    scheduleArea.style.display = 'block';
                    document.getElementById('petId').value = data.pet.pet_id || '';
                    document.getElementById('planPetId').value = data.pet.pet_id || '';
                    await loadSchedules(data.pet.pet_id);
                    await loadTreatments(data.pet.species);
                    await loadPlans();
                    responseArea.style.display = 'none';
                } else {
                    responseArea.className = 'alert alert-danger';
                    responseArea.textContent = data.message || 'No pet found';
                    responseArea.style.display = 'block';
                    petDetails.style.display = 'none';
                    scheduleArea.style.display = 'none';
                }
            } catch (error) {
                console.error('Fetch error:', error);
                responseArea.className = 'alert alert-danger';
                responseArea.textContent = 'Network error: ' + error.message;
                responseArea.style.display = 'block';
                petDetails.style.display = 'none';
                scheduleArea.style.display = 'none';
            }
        });

        async function loadSchedules(petId) {
            try {
                const response = await fetch(`/backend/plans/get_schedules.php?pet_id=${petId}`);
                const text = await response.text();
                console.log('Schedules raw response:', text);
                let data;
                try {
                    data = JSON.parse(text);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    throw new Error('Invalid server response');
                }
                console.log('Schedules parsed response:', data);

                const tbody = document.querySelector('#scheduleTable tbody');
                tbody.innerHTML = '';
                if (data.success) {
                    data.schedules.forEach(schedule => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${schedule.type || 'N/A'}</td>
                            <td>${schedule.treatment_name || 'N/A'}</td>
                            <td>${schedule.date_administered || ''}</td>
                            <td>${schedule.next_due || ''}</td>
                            <td>${schedule.notes || ''}</td>
                            <td>
                                ${!schedule.date_administered && schedule.next_due ? `<button class="btn btn-sm btn-primary recordSchedule" data-schedule-id="${schedule.schedule_id}">Record</button>` : ''}
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    document.getElementById('responseArea').className = 'alert alert-danger';
                    document.getElementById('responseArea').textContent = data.message || 'Error loading schedules';
                    document.getElementById('responseArea').style.display = 'block';
                }
            } catch (error) {
                console.error('Load schedules error:', error);
                document.getElementById('responseArea').className = 'alert alert-danger';
                document.getElementById('responseArea').textContent = 'Error loading schedules: ' + error.message;
                document.getElementById('responseArea').style.display = 'block';
            }
        }

        async function loadTreatments(species) {
            try {
                const response = await fetch('/backend/plans/get_plans.php');
                const text = await response.text();
                console.log('Treatments raw response:', text);
                let data;
                try {
                    data = JSON.parse(text);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    throw new Error('Invalid server response');
                }
                console.log('Treatments parsed response:', data);

                const treatmentSelect = document.getElementById('treatmentName');
                treatmentSelect.innerHTML = '<option value="">Select Treatment</option>';
                if (data.success) {
                    data.plans.forEach(plan => {
                        if (!plan.plan_id) { // Standalone treatments
                            plan.steps.forEach(step => {
                                const tags = step.species_tags ? step.species_tags.split(',') : ['All'];
                                if (tags.includes('All') || tags.includes(species)) {
                                    const option = document.createElement('option');
                                    option.value = step.step_id;
                                    option.textContent = step.treatment_name || 'Unknown';
                                    treatmentSelect.appendChild(option);
                                }
                            });
                        }
                    });
                } else {
                    document.getElementById('responseArea').className = 'alert alert-danger';
                    document.getElementById('responseArea').textContent = data.message || 'Error loading treatments';
                    document.getElementById('responseArea').style.display = 'block';
                }
            } catch (error) {
                console.error('Load treatments error:', error);
                document.getElementById('responseArea').className = 'alert alert-danger';
                document.getElementById('responseArea').textContent = 'Error loading treatments: ' + error.message;
                document.getElementById('responseArea').style.display = 'block';
            }
        }

        async function loadPlans() {
            try {
                const response = await fetch('/backend/plans/get_plans.php');
                const text = await response.text();
                console.log('Plans raw response:', text);
                let data;
                try {
                    data = JSON.parse(text);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    throw new Error('Invalid server response');
                }
                console.log('Plans parsed response:', data);

                const planSelect = document.getElementById('planName');
                planSelect.innerHTML = '<option value="">Select Plan</option>';
                if (data.success) {
                    data.plans.forEach(plan => {
                        if (plan.plan_id) { // Actual plans
                            const option = document.createElement('option');
                            option.value = plan.plan_id;
                            option.textContent = plan.plan_name || 'Unknown';
                            planSelect.appendChild(option);
                        }
                    });
                } else {
                    document.getElementById('responseArea').className = 'alert alert-danger';
                    document.getElementById('responseArea').textContent = data.message || 'Error loading plans';
                    document.getElementById('responseArea').style.display = 'block';
                }
            } catch (error) {
                console.error('Load plans error:', error);
                document.getElementById('responseArea').className = 'alert alert-danger';
                document.getElementById('responseArea').textContent = 'Error loading plans: ' + error.message;
                document.getElementById('responseArea').style.display = 'block';
            }
        }

        document.getElementById('addTreatmentForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            try {
                const response = await fetch('/backend/plans/add_treatment.php', {
                    method: 'POST',
                    body: formData
                });
                const text = await response.text();
                console.log('Add treatment raw response:', text);
                let data;
                try {
                    data = JSON.parse(text);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    throw new Error('Invalid server response');
                }
                console.log('Add treatment parsed response:', data);

                const responseArea = document.getElementById('responseArea');
                responseArea.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger');
                responseArea.textContent = data.message;
                responseArea.style.display = 'block';
                if (data.success) {
                    document.getElementById('addTreatmentForm').reset();
                    loadSchedules(document.getElementById('petId').value);
                }
            } catch (error) {
                console.error('Fetch error:', error);
                document.getElementById('responseArea').className = 'alert alert-danger';
                document.getElementById('responseArea').textContent = 'Network error: ' + error.message;
                document.getElementById('responseArea').style.display = 'block';
            }
        });

        document.getElementById('generatePlanForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            try {
                const response = await fetch('/backend/plans/apply_plan.php', {
                    method: 'POST',
                    body: formData
                });
                const text = await response.text();
                console.log('Generate plan raw response:', text);
                let data;
                try {
                    data = JSON.parse(text);
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    throw new Error('Invalid server response');
                }
                console.log('Generate plan parsed response:', data);

                const responseArea = document.getElementById('responseArea');
                responseArea.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger');
                responseArea.textContent = data.message;
                responseArea.style.display = 'block';
                if (data.success) {
                    document.getElementById('generatePlanForm').reset();
                    loadSchedules(document.getElementById('planPetId').value);
                }
            } catch (error) {
                console.error('Fetch error:', error);
                document.getElementById('responseArea').className = 'alert alert-danger';
                document.getElementById('responseArea').textContent = 'Network error: ' + error.message;
                document.getElementById('responseArea').style.display = 'block';
            }
        });

        document.addEventListener('click', async (e) => {
            if (e.target.classList.contains('recordSchedule')) {
                const scheduleId = e.target.dataset.scheduleId;
                const date = prompt('Enter administered date (YYYY-MM-DD):');
                if (date) {
                    const formData = new FormData();
                    formData.append('schedule_id', scheduleId);
                    formData.append('date_administered', date);
                    try {
                        const response = await fetch('/backend/plans/record_schedule.php', {
                            method: 'POST',
                            body: formData
                        });
                        const text = await response.text();
                        console.log('Record schedule raw response:', text);
                        let data;
                        try {
                            data = JSON.parse(text);
                        } catch (parseError) {
                            console.error('JSON parse error:', parseError);
                            throw new Error('Invalid server response');
                        }
                        console.log('Record schedule parsed response:', data);

                        const responseArea = document.getElementById('responseArea');
                        responseArea.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger');
                        responseArea.textContent = data.message;
                        responseArea.style.display = 'block';
                        if (data.success) {
                            loadSchedules(document.getElementById('petId').value);
                        }
                    } catch (error) {
                        console.error('Fetch error:', error);
                        document.getElementById('responseArea').className = 'alert alert-danger';
                        document.getElementById('responseArea').textContent = 'Network error: ' + error.message;
                        responseArea.style.display = 'block';
                    }
                }
            }
        });
    </script>
</body>
</html>