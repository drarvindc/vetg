<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Plan Maker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 id="formTitle">Add New Plan</h2>
        <form id="planForm">
            <input type="hidden" id="plan_id" name="plan_id">
            <div class="mb-3">
                <label for="plan_name" class="form-label">Plan Name</label>
                <input type="text" class="form-control" id="plan_name" name="plan_name" maxlength="50" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" maxlength="500"></textarea>
            </div>
            <div class="mb-3">
                <h4>Treatment Steps</h4>
                <div id="stepsContainer">
                    <div class="step row mb-2">
                        <div class="col-md-3">
                            <select class="form-select" name="steps[0][type]" required>
                                <option value="vaccination">Vaccination</option>
                                <option value="deworming">Deworming</option>
                                <option value="tick_flea">Ectoparasite Rx</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="steps[0][treatment_name]" placeholder="Treatment Name" maxlength="50" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" name="steps[0][spacing_days]" placeholder="Spacing Days" min="0" required>
                        </div>
                        <div class="col-md-2">
                            <input type="number" class="form-control" name="steps[0][duration_months]" placeholder="Duration Months" min="0">
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" name="steps[0][species_tags][]" multiple required>
                                <option value="Canine">Canine</option>
                                <option value="Feline">Feline</option>
                                <option value="Avian">Avian</option>
                                <option value="Tortoise">Tortoise</option>
                                <option value="Exotic">Exotic</option>
                                <option value="All">All</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-secondary mt-2" id="addStep">Add Step</button>
            </div>
            <button type="submit" class="btn btn-primary" id="saveBtn">Save Plan</button>
            <button type="button" class="btn btn-secondary ms-2" id="resetForm" style="display:none;">Add New Plan</button>
        </form>
        <div id="responseArea" class="alert mt-3" style="display:none;"></div>
        <h3 class="mt-5">Existing Plans</h3>
        <table class="table table-bordered" id="plansTable">
            <thead>
                <tr>
                    <th>Plan Name</th>
                    <th>Description</th>
                    <th>Steps</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>

        <h2 class="mt-5">Treatment Maker</h2>
        <form id="treatmentForm">
            <input type="hidden" id="treatment_step_id" name="step_id">
            <div class="mb-3">
                <label for="treatment_type" class="form-label">Type</label>
                <select class="form-select" id="treatment_type" name="type" required>
                    <option value="vaccination">Vaccination</option>
                    <option value="deworming">Deworming</option>
                    <option value="tick_flea">Ectoparasite Rx</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="treatment_name" class="form-label">Treatment Name</label>
                <input type="text" class="form-control" id="treatment_name" name="treatment_name" maxlength="50" required>
            </div>
            <div class="mb-3">
                <label for="treatment_duration" class="form-label">Duration Months</label>
                <input type="number" class="form-control" id="treatment_duration" name="duration_months" min="0">
            </div>
            <div class="mb-3">
                <label for="treatment_species" class="form-label">Species Tags</label>
                <select class="form-select" id="treatment_species" name="species_tags[]" multiple required>
                    <option value="Canine">Canine</option>
                    <option value="Feline">Feline</option>
                    <option value="Avian">Avian</option>
                    <option value="Tortoise">Tortoise</option>
                    <option value="Exotic">Exotic</option>
                    <option value="All">All</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" id="saveTreatmentBtn">Save Treatment</button>
            <button type="button" class="btn btn-secondary ms-2" id="resetTreatmentForm" style="display:none;">Add New Treatment</button>
        </form>
        <div id="treatmentResponseArea" class="alert mt-3" style="display:none;"></div>
        <h3 class="mt-5">Existing Treatments</h3>
        <table class="table table-bordered" id="treatmentsTable">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Treatment Name</th>
                    <th>Duration Months</th>
                    <th>Species Tags</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <script>
        let stepIndex = 1;
        let editMode = false;
        let editTreatmentMode = false;

        function resetPlanForm() {
            document.getElementById('planForm').reset();
            document.getElementById('plan_id').value = '';
            document.getElementById('formTitle').textContent = 'Add New Plan';
            document.getElementById('saveBtn').textContent = 'Save Plan';
            document.getElementById('resetForm').style.display = 'none';
            document.getElementById('stepsContainer').innerHTML = `
                <div class="step row mb-2">
                    <div class="col-md-3">
                        <select class="form-select" name="steps[0][type]" required>
                            <option value="vaccination">Vaccination</option>
                            <option value="deworming">Deworming</option>
                            <option value="tick_flea">Ectoparasite Rx</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="steps[0][treatment_name]" placeholder="Treatment Name" maxlength="50" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="steps[0][spacing_days]" placeholder="Spacing Days" min="0" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="steps[0][duration_months]" placeholder="Duration Months" min="0">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="steps[0][species_tags][]" multiple required>
                            <option value="Canine">Canine</option>
                            <option value="Feline">Feline</option>
                            <option value="Avian">Avian</option>
                            <option value="Tortoise">Tortoise</option>
                            <option value="Exotic">Exotic</option>
                            <option value="All">All</option>
                        </select>
                    </div>
                </div>
            `;
            stepIndex = 1;
            editMode = false;
        }

        function resetTreatmentForm() {
            document.getElementById('treatmentForm').reset();
            document.getElementById('treatment_step_id').value = '';
            document.getElementById('saveTreatmentBtn').textContent = 'Save Treatment';
            document.getElementById('resetTreatmentForm').style.display = 'none';
            editTreatmentMode = false;
        }

        document.getElementById('addStep').addEventListener('click', () => {
            const container = document.getElementById('stepsContainer');
            const step = document.createElement('div');
            step.className = 'step row mb-2';
            step.innerHTML = `
                <div class="col-md-3">
                    <select class="form-select" name="steps[${stepIndex}][type]" required>
                        <option value="vaccination">Vaccination</option>
                        <option value="deworming">Deworming</option>
                        <option value="tick_flea">Ectoparasite Rx</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="steps[${stepIndex}][treatment_name]" placeholder="Treatment Name" maxlength="50" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="steps[${stepIndex}][spacing_days]" placeholder="Spacing Days" min="0" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="steps[${stepIndex}][duration_months]" placeholder="Duration Months" min="0">
                </div>
                <div class="col-md-2">
                    <select class="form-select" name="steps[${stepIndex}][species_tags][]" multiple required>
                        <option value="Canine">Canine</option>
                        <option value="Feline">Feline</option>
                        <option value="Avian">Avian</option>
                        <option value="Tortoise">Tortoise</option>
                        <option value="Exotic">Exotic</option>
                        <option value="All">All</option>
                    </select>
                </div>
                <button type="button" class="btn btn-danger btn-sm removeStep">Remove</button>
            `;
            container.appendChild(step);
            stepIndex++;
        });

        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('removeStep')) {
                e.target.parentElement.remove();
            }
            if (e.target.classList.contains('editPlan')) {
                const planId = e.target.dataset.planId;
                fetch('/backend/plans/get_plans.php')
                    .then(response => response.text())
                    .then(text => {
                        console.log('Get plans raw response:', text);
                        const data = JSON.parse(text);
                        console.log('Get plans parsed response:', data);
                        if (data.success) {
                            const plan = data.plans.find(p => p.plan_id == planId);
                            if (plan) {
                                document.getElementById('plan_id').value = plan.plan_id;
                                document.getElementById('plan_name').value = plan.plan_name;
                                document.getElementById('description').value = plan.description || '';
                                document.getElementById('formTitle').textContent = 'Edit Plan';
                                document.getElementById('saveBtn').textContent = 'Update Plan';
                                document.getElementById('resetForm').style.display = 'inline-block';
                                const container = document.getElementById('stepsContainer');
                                container.innerHTML = '';
                                plan.steps.forEach((step, index) => {
                                    const tags = step.species_tags ? step.species_tags.split(',') : ['All'];
                                    const stepDiv = document.createElement('div');
                                    stepDiv.className = 'step row mb-2';
                                    stepDiv.innerHTML = `
                                        <div class="col-md-3">
                                            <select class="form-select" name="steps[${index}][type]" required>
                                                <option value="vaccination" ${step.type === 'vaccination' ? 'selected' : ''}>Vaccination</option>
                                                <option value="deworming" ${step.type === 'deworming' ? 'selected' : ''}>Deworming</option>
                                                <option value="tick_flea" ${step.type === 'tick_flea' ? 'selected' : ''}>Ectoparasite Rx</option>
                                                <option value="other" ${step.type === 'other' ? 'selected' : ''}>Other</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="steps[${index}][treatment_name]" value="${step.treatment_name}" maxlength="50" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control" name="steps[${index}][spacing_days]" value="${step.spacing_days}" min="0" required>
                                        </div>
                                        <div class="col-md-2">
                                            <input type="number" class="form-control" name="steps[${index}][duration_months]" value="${step.duration_months || ''}" min="0">
                                        </div>
                                        <div class="col-md-2">
                                            <select class="form-select" name="steps[${index}][species_tags][]" multiple required>
                                                <option value="Canine" ${tags.includes('Canine') ? 'selected' : ''}>Canine</option>
                                                <option value="Feline" ${tags.includes('Feline') ? 'selected' : ''}>Feline</option>
                                                <option value="Avian" ${tags.includes('Avian') ? 'selected' : ''}>Avian</option>
                                                <option value="Tortoise" ${tags.includes('Tortoise') ? 'selected' : ''}>Tortoise</option>
                                                <option value="Exotic" ${tags.includes('Exotic') ? 'selected' : ''}>Exotic</option>
                                                <option value="All" ${tags.includes('All') ? 'selected' : ''}>All</option>
                                            </select>
                                        </div>
                                        ${index > 0 ? '<button type="button" class="btn btn-danger btn-sm removeStep">Remove</button>' : ''}
                                    `;
                                    container.appendChild(stepDiv);
                                });
                                stepIndex = plan.steps.length;
                                editMode = true;
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        document.getElementById('responseArea').className = 'alert alert-danger';
                        document.getElementById('responseArea').textContent = 'Error loading plan: ' + error.message;
                        document.getElementById('responseArea').style.display = 'block';
                    });
            }
            if (e.target.classList.contains('editTreatment')) {
                const stepId = e.target.dataset.stepId;
                fetch('/backend/plans/get_plans.php')
                    .then(response => response.text())
                    .then(text => {
                        console.log('Get treatments raw response:', text);
                        const data = JSON.parse(text);
                        console.log('Get treatments parsed response:', data);
                        if (data.success) {
                            const treatmentPlan = data.plans.find(p => !p.plan_id);
                            if (treatmentPlan) {
                                const treatment = treatmentPlan.steps.find(t => t.step_id == stepId);
                                if (treatment) {
                                    document.getElementById('treatment_step_id').value = treatment.step_id;
                                    document.getElementById('treatment_type').value = treatment.type;
                                    document.getElementById('treatment_name').value = treatment.treatment_name;
                                    document.getElementById('treatment_duration').value = treatment.duration_months || '';
                                    const select = document.getElementById('treatment_species');
                                    const tags = treatment.species_tags ? treatment.species_tags.split(',') : ['All'];
                                    Array.from(select.options).forEach(option => {
                                        option.selected = tags.includes(option.value);
                                    });
                                    document.getElementById('saveTreatmentBtn').textContent = 'Update Treatment';
                                    document.getElementById('resetTreatmentForm').style.display = 'inline-block';
                                    editTreatmentMode = true;
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        document.getElementById('treatmentResponseArea').className = 'alert alert-danger';
                        document.getElementById('treatmentResponseArea').textContent = 'Error loading treatment: ' + error.message;
                        document.getElementById('treatmentResponseArea').style.display = 'block';
                    });
            }
            if (e.target.classList.contains('deleteTreatment')) {
                if (confirm('Are you sure you want to delete this treatment?')) {
                    const stepId = e.target.dataset.stepId;
                    const formData = new FormData();
                    formData.append('step_id', stepId);
                    fetch('/backend/plans/delete_treatment.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(text => {
                        console.log('Delete treatment raw response:', text);
                        const data = JSON.parse(text);
                        console.log('Delete treatment parsed response:', data);
                        const responseArea = document.getElementById('treatmentResponseArea');
                        responseArea.className = 'alert ' + (data.success ? 'alert-success' : 'alert-danger');
                        responseArea.textContent = data.message;
                        responseArea.style.display = 'block';
                        if (data.success) {
                            loadTreatments();
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        document.getElementById('treatmentResponseArea').className = 'alert alert-danger';
                        document.getElementById('treatmentResponseArea').textContent = 'Network error: ' + error.message;
                        document.getElementById('treatmentResponseArea').style.display = 'block';
                    });
                }
            }
        });

        document.getElementById('resetForm').addEventListener('click', resetPlanForm);
        document.getElementById('resetTreatmentForm').addEventListener('click', resetTreatmentForm);

        document.getElementById('planForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const responseArea = document.getElementById('responseArea');

            try {
                const response = await fetch('/backend/plans/add_plan.php', {
                    method: 'POST',
                    body: formData
                });
                const text = await response.text();
                console.log('Add plan raw response:', text);
                const data = JSON.parse(text);
                console.log('Add plan parsed response:', data);

                if (data.success) {
                    responseArea.className = 'alert alert-success';
                    responseArea.textContent = data.message;
                    responseArea.style.display = 'block';
                    resetPlanForm();
                    loadPlans();
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

        document.getElementById('treatmentForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const responseArea = document.getElementById('treatmentResponseArea');
            const url = editTreatmentMode ? '/backend/plans/edit_treatment.php' : '/backend/plans/add_treatment.php';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                const text = await response.text();
                console.log('Treatment raw response:', text);
                const data = JSON.parse(text);
                console.log('Treatment parsed response:', data);

                if (data.success) {
                    responseArea.className = 'alert alert-success';
                    responseArea.textContent = data.message;
                    responseArea.style.display = 'block';
                    resetTreatmentForm();
                    loadTreatments();
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

                const tbody = document.querySelector('#plansTable tbody');
                tbody.innerHTML = '';
                data.plans.forEach(plan => {
                    if (plan.plan_id) { // Only show actual plans
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${plan.plan_name}</td>
                            <td>${plan.description || ''}</td>
                            <td>${plan.steps.map(step => `${step.treatment_name} (${step.type}, ${step.spacing_days} days${step.duration_months ? `, ${step.duration_months} months` : ''}, ${step.species_tags || 'All'})`).join('<br>')}</td>
                            <td><button class="btn btn-sm btn-primary editPlan" data-plan-id="${plan.plan_id}">Edit</button></td>
                        `;
                        tbody.appendChild(row);
                    }
                });
            } catch (error) {
                console.error('Load plans error:', error);
                document.getElementById('responseArea').className = 'alert alert-danger';
                document.getElementById('responseArea').textContent = 'Error loading plans: ' + error.message;
                document.getElementById('responseArea').style.display = 'block';
            }
        }

        async function loadTreatments() {
            try {
                const response = await fetch('/backend/plans/get_plans.php');
                const text = await response.text();
                console.log('Treatments raw response:', text);
                const data = JSON.parse(text);
                console.log('Treatments parsed response:', data);

                if (!data.success) {
                    throw new Error(data.message || 'Failed to load treatments');
                }

                const tbody = document.querySelector('#treatmentsTable tbody');
                tbody.innerHTML = '';
                data.plans.forEach(plan => {
                    if (!plan.plan_id) { // Show standalone treatments (plan_id = NULL)
                        plan.steps.forEach(treatment => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${treatment.type}</td>
                                <td>${treatment.treatment_name}</td>
                                <td>${treatment.duration_months || ''}</td>
                                <td>${treatment.species_tags || 'All'}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary editTreatment" data-step-id="${treatment.step_id}">Edit</button>
                                    <button class="btn btn-sm btn-danger deleteTreatment" data-step-id="${treatment.step_id}">Delete</button>
                                </td>
                            `;
                            tbody.appendChild(row);
                        });
                    }
                });
            } catch (error) {
                console.error('Load treatments error:', error);
                document.getElementById('treatmentResponseArea').className = 'alert alert-danger';
                document.getElementById('treatmentResponseArea').textContent = 'Error loading treatments: ' + error.message;
                document.getElementById('treatmentResponseArea').style.display = 'block';
            }
        }

        window.onload = () => {
            loadPlans();
            loadTreatments();
        };
    </script>
</body>
</html>