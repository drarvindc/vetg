from flask import Flask, request, jsonify, render_template
import mysql.connector
from werkzeug.utils import secure_filename
import os

app = Flask(__name__)

# MySQL configuration
db = mysql.connector.connect(
    host="localhost",
    user="your_username",
    password="your_password",
    database="vet_clinic"
)
cursor = db.cursor()

# Directory to save uploaded images
UPLOAD_FOLDER = 'static/uploads'
app.config['UPLOAD_FOLDER'] = UPLOAD_FOLDER
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

# Home page - Patient facing
@app.route('/')
def patient_home():
    return render_template('patient.html')

# Admin page
@app.route('/admin')
def admin_home():
    return render_template('admin.html')

# Settings page
@app.route('/settings')
def settings_page():
    return render_template('settings.html')

# API endpoint to upload pet image
@app.route('/api/upload_image', methods=['POST'])
def upload_image():
    if 'image' not in request.files:
        return jsonify({'error': 'No image provided'}), 400
    
    image = request.files['image']
    pet_id = request.form.get('pet_id')
    
    if image and pet_id:
        filename = secure_filename(image.filename)
        image_path = os.path.join(app.config['UPLOAD_FOLDER'], filename)
        image.save(image_path)
        
        # Update database with image path
        query = "UPDATE Pets SET image_path = %s WHERE pet_id = %s"
        cursor.execute(query, (filename, pet_id))
        db.commit()
        
        return jsonify({'message': 'Image uploaded successfully'}), 200
    return jsonify({'error': 'Invalid data'}), 400

# API endpoint to get pet details by ID
@app.route('/api/get_pet/<string:unique_id>', methods=['GET'])
def get_pet(unique_id):
    query = """
    SELECT p.*, o.first_name, o.last_name, o.locality 
    FROM Pets p 
    JOIN Owners o ON p.owner_id = o.owner_id 
    WHERE p.unique_id = %s
    """
    cursor.execute(query, (unique_id,))
    pet = cursor.fetchone()
    if pet:
        pet_dict = {
            'pet_id': pet[0],
            'owner_id': pet[1],
            'unique_id': pet[2],
            'pet_name': pet[3],
            'species': pet[4],
            'breed': pet[5],
            'gender': pet[6],
            'dob': pet[7].isoformat(),
            'image_path': pet[8],
            'owner_first_name': pet[9],
            'owner_last_name': pet[10],
            'locality': pet[11]
        }
        return jsonify(pet_dict), 200
    else:
        return jsonify({'error': 'Pet not found'}), 404

if __name__ == '__main__':
    app.run(debug=True, ssl_context='adhoc')