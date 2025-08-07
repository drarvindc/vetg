# Veterinary Clinic Management System

## Setup Instructions

1. **Install MySQL and create a database:**
   - Install MySQL and create a database named `vet_clinic`.
   - Import the `database/vet_clinic.sql` file: 
     ```
     mysql -u your_username -p vet_clinic < database/vet_clinic.sql
     ```

2. **Set up the Python environment:**
   - Create a virtual environment: `python -m venv venv`
   - Activate it: `venv\Scripts\activate` (Windows) or `source venv/bin/activate` (Linux/Mac)
   - Install dependencies: `pip install -r backend/requirements.txt`

3. **Configure the database connection:**
   - Edit `backend/app.py` and update the MySQL connection details (`your_username` and `your_password`).

4. **Run the application:**
   - Navigate to the `backend` directory: `cd backend`
   - Run the app: `python app.py`
   - Access it at `https://localhost:5000`

## API Endpoints

- **POST /api/upload_image**: Upload a pet image.
  - Form data: `image` (file), `pet_id` (string)
- **GET /api/get_pet/<unique_id>**: Get pet details by unique ID.

## Project Structure

```
vet_clinic_management/
├── backend/
│   ├── app.py
│   ├── requirements.txt
│   ├── templates/
│   │   ├── patient.html
│   │   ├── admin.html
│   │   └── settings.html
│   └── static/
│       └── uploads/
│           └── .gitkeep
├── database/
│   └── vet_clinic.sql
└── docs/
    └── README.md
```

## Notes

- Ensure SSL is configured for production use (currently uses Flask's adhoc SSL for development).
- Add custom CSS/JS in `backend/static` as needed.
- Expand API endpoints and templates for additional features.