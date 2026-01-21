<?php
// submit.php - English only
require_once 'config.php';

// Set timezone
date_default_timezone_set('Africa/Addis_Ababa');

// Create uploads directory
if (!is_dir('uploads')) {
    mkdir('uploads', 0755, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate form data
    $errors = [];
    
    // Required fields
    $required = ['full_name', 'father_name', 'mother_name', 'gender', 'date_of_birth',
                'age', 'marital_status', 'phone_number', 'country', 'city',
                'national_id_front', 'national_id_back', 'selfie', 'applicant_name', 'declaration_date'];
    
    foreach ($required as $field) {
        if (empty($_POST[$field]) && $field != 'national_id_front' && $field != 'national_id_back') {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . " is required.";
        }
    }
    
    // File validation
    if (!isset($_FILES['national_id_front']) || $_FILES['national_id_front']['error'] != 0) {
        $errors[] = "National ID Front image is required.";
    }
    
    if (!isset($_FILES['national_id_back']) || $_FILES['national_id_back']['error'] != 0) {
        $errors[] = "National ID Back image is required.";
    }

    if (!isset($_FILES['selfie']) || $_FILES['selfie']['error'] != 0) {
        $errors[] = "Selfie image is required.";
    }
    
    // Check file types and sizes
    $allowed_extensions = ['jpg', 'jpeg', 'png'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    $files_to_check = [
        'national_id_front' => $_FILES['national_id_front'],
        'national_id_back' => $_FILES['national_id_back'],
        'selfie' => $_FILES['selfie']
    ];
    
    foreach ($files_to_check as $field => $file) {
        if ($file && $file['error'] == 0) {
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($file_ext, $allowed_extensions)) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . " must be JPG or PNG.";
            }
            
            if ($file['size'] > $max_size) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . " must be less than 2MB.";
            }
        }
    }
    
    // Show errors if any
    if (!empty($errors)) {
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Application Error</title>
            <link rel="stylesheet" href="styles.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        </head>
        <body>
            <div class="container" style="max-width: 600px; margin-top: 50px;">
                <div class="form-container">
                    <h2 style="color: #dc3545;"><i class="fas fa-exclamation-circle"></i> Application Errors</h2>
                    <div class="alert alert-warning">
                        <ul>';
        foreach ($errors as $error) {
            echo '<li>' . htmlspecialchars($error) . '</li>';
        }
        echo '</ul>
                    </div>
                    <a href="index.html" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back to Application Form
                    </a>
                </div>
            </div>
        </body>
        </html>';
        exit;
    }
    
    // Process file uploads
    $uploaded_files = [];
    
    foreach ($files_to_check as $field => $file) {
        if ($file && $file['error'] == 0) {
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $new_filename = uniqid('id_', true) . '_' . time() . '.' . $file_ext;
            $upload_path = 'uploads/' . $new_filename;
            
            if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                $uploaded_files[$field] = $upload_path;
            } else {
                $errors[] = "Failed to upload " . str_replace('_', ' ', $field);
            }
        }
    }
    
    // Insert into database
    try {
        $sql = "INSERT INTO applications (full_name, father_name, mother_name, gender, date_of_birth,
                age, marital_status, relationship_status, phone_number, country, city, email,
                postal_zip, national_id_front, national_id_back, selfie, declaration_accepted, application_date)
                VALUES (:full_name, :father_name, :mother_name, :gender, :date_of_birth,
                :age, :marital_status, :relationship_status, :phone_number, :country, :city, :email,
                :postal_zip, :national_id_front, :national_id_back, :selfie, :declaration_accepted, :application_date)";
        
        $stmt = $pdo->prepare($sql);
        
        $data = [
            'full_name' => htmlspecialchars($_POST['full_name']),
            'father_name' => htmlspecialchars($_POST['father_name']),
            'mother_name' => htmlspecialchars($_POST['mother_name']),
            'gender' => htmlspecialchars($_POST['gender']),
            'date_of_birth' => htmlspecialchars($_POST['date_of_birth']),
            'age' => intval($_POST['age']),
            'marital_status' => htmlspecialchars($_POST['marital_status']),
            'relationship_status' => htmlspecialchars($_POST['relationship_status'] ?? ''),
            'phone_number' => htmlspecialchars($_POST['phone_number']),
            'country' => htmlspecialchars($_POST['country']),
            'city' => htmlspecialchars($_POST['city']),
            'email' => htmlspecialchars($_POST['email'] ?? ''),
            'postal_zip' => htmlspecialchars($_POST['postal_zip'] ?? ''),
            'national_id_front' => $uploaded_files['national_id_front'],
            'national_id_back' => $uploaded_files['national_id_back'],
            'selfie' => $uploaded_files['selfie'],
            'declaration_accepted' => isset($_POST['accept_declaration']) ? 1 : 0,
            'application_date' => date('Y-m-d H:i:s')
        ];
        
        $stmt->execute($data);
        $application_id = $pdo->lastInsertId();
        
        // Success page
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>Application Submitted</title>
            <link rel="stylesheet" href="styles.css">
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        </head>
        <body>
            <div class="container" style="max-width: 700px; margin-top: 50px;">
                <div class="form-container">
                    <div class="success-header">
                        <div class="success-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h2 style="color: #28a745;">Application Submitted Successfully!</h2>
                        <p class="subtitle">Your work visa application has been received.</p>
                    </div>
                    
                    <div class="application-summary">
                        <h3><i class="fas fa-file-alt"></i> Application Details</h3>
                        <div class="summary-grid">
                            <div class="summary-item">
                                <span class="summary-label">Application ID:</span>
                                <span class="summary-value">ET-CA-' . str_pad($application_id, 6, "0", STR_PAD_LEFT) . '</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Applicant Name:</span>
                                <span class="summary-value">' . htmlspecialchars($_POST['full_name']) . '</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Phone Number:</span>
                                <span class="summary-value">' . htmlspecialchars($_POST['phone_number']) . '</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Submission Date:</span>
                                <span class="summary-value">' . date('F j, Y, g:i a') . '</span>
                            </div>
                            <div class="summary-item">
                                <span class="summary-label">Status:</span>
                                <span class="summary-value"><span class="status-badge pending">Pending Review</span></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="next-steps">
                        <h3><i class="fas fa-list-check"></i> What Happens Next?</h3>
                        <ol>
                            <li>Save your Application ID for future reference</li>
                            <li>Check your email for confirmation (if provided)</li>
                            <li>Our team will review your application within 5-7 business days</li>
                            <li>You may be contacted for additional information if needed</li>
                        </ol>
                    </div>
                    
                    <div class="form-actions">
                        <a href="index.html" class="btn btn-primary">
                            <i class="fas fa-home"></i> Return to Home
                        </a>
                        <button onclick="window.print()" class="btn btn-secondary">
                            <i class="fas fa-print"></i> Print Confirmation
                        </button>
                    </div>
                    
                    <div class="form-footer">
                        <p><i class="fas fa-question-circle"></i> Need help? Contact us at visa-support@ethiocanada.com</p>
                    </div>
                </div>
            </div>
            <style>
                .success-header { text-align: center; margin-bottom: 30px; }
                .success-icon { font-size: 4rem; color: #28a745; margin-bottom: 20px; }
                .application-summary { background: #f8f9fa; padding: 25px; border-radius: 10px; margin: 25px 0; }
                .summary-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-top: 20px; }
                .summary-item { display: flex; flex-direction: column; }
                .summary-label { font-weight: 600; color: #555; font-size: 0.9rem; }
                .summary-value { font-size: 1.1rem; color: #333; margin-top: 5px; }
                .status-badge { padding: 5px 15px; border-radius: 20px; font-size: 0.9rem; font-weight: 600; }
                .status-badge.pending { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
                .next-steps { background: #e7f4ff; padding: 25px; border-radius: 10px; margin: 25px 0; }
                .next-steps ol { margin-left: 20px; margin-top: 15px; }
                .next-steps li { margin-bottom: 10px; line-height: 1.5; }
            </style>
        </body>
        </html>';
        
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>