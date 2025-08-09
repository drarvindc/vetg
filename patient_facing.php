<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Entry</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Roboto', sans-serif; }
        .prescription-container { max-width: 800px; margin: 0 auto; padding: 20px; border: 1px solid #ccc; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { font-size: 24px; margin: 0; }
        .header p { font-size: 14px; margin: 5px 0; }
        .details-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .details-table th, .details-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .details-table th { background-color: #f2f2f2; }
        .images { text-align: center; margin-bottom: 20px; }
        .images img { max-width: 150px; margin: 0 10px; }
        .footer { text-align: center; font-size: 12px; color: #666; margin-top: 20px; }
        @media print {
            body * { visibility: hidden; }
            .prescription-container, .prescription-container * { visibility: visible; }
            .prescription-container { position: absolute; top: 0; left: 0; width: 100%; border: none; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="container mt-5 no-print">
        <h2>Enter Mobile Number or Pet ID</h2>
        <form id="mobileForm">
            <div class="mb-3">
                <input type="text" class="form-control" id="search_term" placeholder="Mobile Number or Pet ID" maxlength="50" required autofocus>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <div id="errorArea" class="alert alert-danger mt-3" style="display:none;"></div>
    </div>
    <div id="printArea" class="prescription-container" style="display:none;">
        <div class="header">
            <h1>Veterinary Clinic</h1>
            <p>Date of Visit: <span id="visitDate"><?php echo date('Y-m-d'); ?></span></p>
        </div>
        <table class="details-table">
            <tr><th>Unique ID</th><td><span id="uniqueId"></span></td></tr>
            <tr><th>Pet Name</th><td><span id="petName"></span></td></tr>
            <tr><th>Species</th><td><span id="species"></span></td></tr>
            <tr><th>Breed</th><td><span id="breed"></span></td></tr>
            <tr><th>Gender</th><td><span id="gender"></span></td></tr>
            <tr><th>Age</th><td><span id="petAge"></span></td></tr>
            <tr><th>Owner Name</th><td><span id="ownerName"></span></td></tr>
            <tr><th>Mobile Numbers</th><td><span id="mobileNumbers"></span></td></tr>
        </table>
        <div class="images">
            <img id="qrCode" src="" alt="QR Code">
            <img id="barCode" src="" alt="Barcode">
        </div>
        <div class="footer">
            <p>Veterinary Clinic | Contact: info@vetclinic.com | Phone: (123) 456-7890</p>
        </div>
    </div>
    <script>
        document.getElementById('mobileForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const search_term = document.getElementById('search_term').value;
            const errorArea = document.getElementById('errorArea');

            if (!search_term) {
                errorArea.textContent = 'Enter a mobile number or Pet ID';
                errorArea.style.display = 'block';
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
                console.log('Raw response:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed response:', data);

                    if (data.success) {
                        document.getElementById('uniqueId').textContent = data.unique_id;
                        document.getElementById('qrCode').src = data.qr_path || '';
                        document.getElementById('barCode').src = data.barcode_path || '';
                        document.getElementById('petName').textContent = data.pet_name || '';
                        document.getElementById('species').textContent = data.species || '';
                        document.getElementById('breed').textContent = data.breed || '';
                        document.getElementById('gender').textContent = data.gender || '';
                        document.getElementById('ownerName').textContent = 
                            [data.first_name, data.middle_name, data.last_name].filter(Boolean).join(' ') || '';
                        document.getElementById('petAge').textContent = 
                            `${data.pet_age_years} yrs ${data.pet_age_months} mths ${data.pet_age_days} days`;
                        document.getElementById('mobileNumbers').textContent = data.mobile_numbers.join(', ') || '';
                        document.getElementById('visitDate').textContent = '<?php echo date('Y-m-d'); ?>';
                        document.getElementById('printArea').style.display = 'block';
                        errorArea.style.display = 'none';
                        setTimeout(() => window.print(), 500);
                    } else {
                        errorArea.textContent = 'Server error: ' + (data.message || 'Unknown error');
                        errorArea.style.display = 'block';
                    }
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    errorArea.textContent = 'Invalid server response: ' + text;
                    errorArea.style.display = 'block';
                }
            } catch (error) {
                console.error('Fetch error:', error);
                errorArea.textContent = 'Network error: ' + error.message;
                errorArea.style.display = 'block';
            }
        });
    </script>
</body>
</html>