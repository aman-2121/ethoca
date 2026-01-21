 // script.js - Ethio-Canada Visa Website JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Initialize camera functionality (face detection optional)
    initializeCameraSystem();

    // Form validation and preview functionality
    const visaForm = document.getElementById('visaForm');
    const previewBtn = document.getElementById('previewBtn');
    const previewModal = document.getElementById('previewModal');
    const closeModal = document.querySelector('.close-modal');
    const previewContent = document.getElementById('previewContent');

    // Preview button functionality
    if (previewBtn) {
        previewBtn.addEventListener('click', function() {
            showPreview();
        });
    }

    // Modal close functionality
    const closeModalSpan = document.querySelector('span.close-modal');
    const closeModalBtn = document.querySelector('button.close-modal');

    if (closeModalSpan) {
        closeModalSpan.addEventListener('click', function() {
            previewModal.style.display = 'none';
        });
    }

    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', function() {
            window.location.href = 'index.html';
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target === previewModal) {
            previewModal.style.display = 'none';
        }
    });

    // Form submission with validation
    if (visaForm) {
        visaForm.addEventListener('submit', function(e) {
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
        });
    }

    // Phone number formatting for Ethiopian numbers
    const phoneInput = document.getElementById('phone_number');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.startsWith('251')) {
                value = value.substring(3);
            }
            if (value.length >= 9) {
                value = value.substring(0, 9);
                e.target.value = '+251 ' + value.substring(0, 1) + value.substring(1, 4) + ' ' + value.substring(4, 7) + ' ' + value.substring(7);
            }
        });
    }

    // Age calculation from date of birth
    const dobInput = document.getElementById('date_of_birth');
    const ageInput = document.getElementById('age');

    if (dobInput && ageInput) {
        dobInput.addEventListener('change', function() {
            const today = new Date();
            const birthDate = new Date(this.value);
            let age = today.getFullYear() - birthDate.getFullYear();
            const monthDiff = today.getMonth() - birthDate.getMonth();

            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }

            ageInput.value = age;
        });
    }

    // File upload preview
    const nationalIdFront = document.getElementById('national_id_front');
    const nationalIdBack = document.getElementById('national_id_back');
    const selfie = document.getElementById('selfie');
    const previewFront = document.getElementById('previewFront');
    const previewBack = document.getElementById('previewBack');
    const previewSelfie = document.getElementById('previewSelfie');

    if (nationalIdFront && previewFront) {
        nationalIdFront.addEventListener('change', function(e) {
            previewFile(e.target, previewFront);
        });
    }

    if (nationalIdBack && previewBack) {
        nationalIdBack.addEventListener('change', function(e) {
            previewFile(e.target, previewBack);
        });
    }

    if (selfie && previewSelfie) {
        selfie.addEventListener('change', function(e) {
            previewFile(e.target, previewSelfie);
        });
    }

    // Camera functionality
    const takePhotoFront = document.getElementById('takePhotoFront');
    const takePhotoBack = document.getElementById('takePhotoBack');
    const takeSelfie = document.getElementById('takeSelfie');
    const videoFront = document.getElementById('videoFront');
    const videoBack = document.getElementById('videoBack');
    const videoSelfie = document.getElementById('videoSelfie');
    const canvasFront = document.getElementById('canvasFront');
    const canvasBack = document.getElementById('canvasBack');
    const canvasSelfie = document.getElementById('canvasSelfie');

    let streamFront = null;
    let streamBack = null;
    let streamSelfie = null;

    // Debug: Check if buttons are found
    console.log('takePhotoFront:', takePhotoFront);
    console.log('takePhotoBack:', takePhotoBack);
    console.log('takeSelfie:', takeSelfie);

    if (takePhotoFront) {
        takePhotoFront.addEventListener('click', function() {
            console.log('takePhotoFront clicked');
            startCamera('front');
        });
    }

    if (takePhotoBack) {
        takePhotoBack.addEventListener('click', function() {
            console.log('takePhotoBack clicked');
            startCamera('back');
        });
    }

    if (takeSelfie) {
        takeSelfie.addEventListener('click', function() {
            console.log('takeSelfie clicked');
            startCamera('selfie');
        });
    }

    function startCamera(side) {
        let video, canvas, streamRef, facingMode;

        if (side === 'front') {
            video = videoFront;
            canvas = canvasFront;
            streamRef = 'streamFront';
            facingMode = 'environment';
        } else if (side === 'back') {
            video = videoBack;
            canvas = canvasBack;
            streamRef = 'streamBack';
            facingMode = 'environment';
        } else if (side === 'selfie') {
            video = videoSelfie;
            canvas = canvasSelfie;
            streamRef = 'streamSelfie';
            facingMode = 'user'; // Front-facing camera for selfie
        }



        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: facingMode } })
                .then(function(stream) {
                    window[streamRef] = stream;
                    video.srcObject = stream;
                    video.style.display = 'block';
                    video.play();

                    // Add capture button
                    const captureBtn = document.createElement('button');
                    captureBtn.textContent = 'Capture Photo';
                    captureBtn.className = 'btn btn-primary btn-sm';
                    captureBtn.style.marginTop = '10px';
                    captureBtn.onclick = function() {
                        capturePhoto(side);
                    };
                    video.parentNode.insertBefore(captureBtn, video.nextSibling);

                    // Add stop camera button
                    const stopBtn = document.createElement('button');
                    stopBtn.textContent = 'Stop Camera';
                    stopBtn.className = 'btn btn-outline btn-sm';
                    stopBtn.style.marginTop = '10px';
                    stopBtn.onclick = function() {
                        stopCamera(side);
                    };
                    captureBtn.parentNode.insertBefore(stopBtn, captureBtn.nextSibling);
                })
                .catch(function(error) {
                    console.error('Camera error:', error);
                    let errorMessage = 'Error accessing camera: ';
                    switch(error.name) {
                        case 'NotAllowedError':
                            errorMessage += 'Camera permission denied. Please allow camera access and try again.';
                            break;
                        case 'NotFoundError':
                            errorMessage += 'No camera found on this device.';
                            break;
                        case 'NotReadableError':
                            errorMessage += 'Camera is already in use by another application.';
                            break;
                        case 'OverconstrainedError':
                            errorMessage += 'Camera constraints not supported.';
                            break;
                        case 'SecurityError':
                            errorMessage += 'Camera access blocked due to security restrictions. Ensure the site uses HTTPS.';
                            break;
                        default:
                            errorMessage += error.message;
                    }
                    alert(errorMessage);
                });
        } else {
            alert('Camera not supported on this device/browser. Please use a modern browser like Chrome, Firefox, or Safari.');
        }
    }

    function capturePhoto(side) {
        const video = side === 'front' ? videoFront : (side === 'back' ? videoBack : videoSelfie);
        const canvas = side === 'front' ? canvasFront : (side === 'back' ? canvasBack : canvasSelfie);
        const preview = side === 'front' ? previewFront : (side === 'back' ? previewBack : previewSelfie);
        const input = side === 'front' ? nationalIdFront : (side === 'back' ? nationalIdBack : selfie);

        const context = canvas.getContext('2d');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);

        canvas.toBlob(function(blob) {
            const file = new File([blob], `${side === 'selfie' ? 'selfie' : 'national_id_' + side}.jpg`, { type: 'image/jpeg' });

            // Create a new DataTransfer to preserve existing files if any
            const dt = new DataTransfer();
            // If there are existing files, preserve them
            if (input.files && input.files.length > 0) {
                for (let i = 0; i < input.files.length; i++) {
                    dt.items.add(input.files[i]);
                }
            }
            // Add the new captured file
            dt.items.add(file);
            input.files = dt.files;

            // Preview the captured image
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Captured Photo" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; margin-top: 10px;">';
            };
            reader.readAsDataURL(file);

            stopCamera(side);
        }, 'image/jpeg');
    }

    function stopCamera(side) {
        let video, streamRef;

        if (side === 'front') {
            video = videoFront;
            streamRef = 'streamFront';
        } else if (side === 'back') {
            video = videoBack;
            streamRef = 'streamBack';
        } else if (side === 'selfie') {
            video = videoSelfie;
            streamRef = 'streamSelfie';
        }

        if (window[streamRef]) {
            window[streamRef].getTracks().forEach(track => track.stop());
            window[streamRef] = null;
        }
        video.style.display = 'none';
        video.srcObject = null;

        // Remove capture and stop buttons
        const buttons = video.parentNode.querySelectorAll('button');
        buttons.forEach(btn => {
            if (btn.textContent === 'Capture Photo' || btn.textContent === 'Stop Camera') {
                btn.remove();
            }
        });
    }
});

function validateForm() {
    const requiredFields = document.querySelectorAll('input[required], select[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = '#dc3545';
            isValid = false;
        } else {
            field.style.borderColor = '#28a745';
        }
    });

    // Check file uploads
    const frontFile = document.getElementById('national_id_front');
    const backFile = document.getElementById('national_id_back');
    const selfieFile = document.getElementById('selfie');

    if (frontFile && frontFile.files.length === 0) {
        frontFile.style.borderColor = '#dc3545';
        isValid = false;
    }

    if (backFile && backFile.files.length === 0) {
        backFile.style.borderColor = '#dc3545';
        isValid = false;
    }

    if (selfieFile && selfieFile.files.length === 0) {
        selfieFile.style.borderColor = '#dc3545';
        isValid = false;
    }

    // Check declaration checkbox
    const declarationCheckbox = document.getElementById('accept_declaration');
    if (declarationCheckbox && !declarationCheckbox.checked) {
        alert('Please accept the declaration to proceed.');
        isValid = false;
    }

    if (!isValid) {
        alert('Please fill in all required fields and upload the necessary documents.');
    }

    return isValid;
}

function showPreview() {
    const previewContent = document.getElementById('previewContent');
    const modal = document.getElementById('previewModal');

    let previewHTML = '<div class="preview-section"><h3>Application Preview</h3>';

    // Personal Information
    previewHTML += '<h4>Personal Information</h4>';
    previewHTML += '<p><strong>Full Name:</strong> ' + (document.getElementById('full_name').value || 'Not provided') + '</p>';
    previewHTML += '<p><strong>Father\'s Name:</strong> ' + (document.getElementById('father_name').value || 'Not provided') + '</p>';
    previewHTML += '<p><strong>Mother\'s Name:</strong> ' + (document.getElementById('mother_name').value || 'Not provided') + '</p>';
    previewHTML += '<p><strong>Gender:</strong> ' + (document.getElementById('gender').value || 'Not provided') + '</p>';
    previewHTML += '<p><strong>Date of Birth:</strong> ' + (document.getElementById('date_of_birth').value || 'Not provided') + '</p>';
    previewHTML += '<p><strong>Age:</strong> ' + (document.getElementById('age').value || 'Not provided') + '</p>';
    previewHTML += '<p><strong>Marital Status:</strong> ' + (document.getElementById('marital_status').value || 'Not provided') + '</p>';
    previewHTML += '<p><strong>Phone Number:</strong> ' + (document.getElementById('phone_number').value || 'Not provided') + '</p>';

    // Address Information
    previewHTML += '<h4>Address Information</h4>';
    previewHTML += '<p><strong>Country:</strong> ' + (document.getElementById('country').value || 'Not provided') + '</p>';
    previewHTML += '<p><strong>City:</strong> ' + (document.getElementById('city').value || 'Not provided') + '</p>';
    previewHTML += '<p><strong>Email:</strong> ' + (document.getElementById('email').value || 'Not provided') + '</p>';

    // Declaration
    previewHTML += '<h4>Declaration</h4>';
    previewHTML += '<p><strong>Applicant Name:</strong> ' + (document.getElementById('applicant_name').value || 'Not provided') + '</p>';
    previewHTML += '<p><strong>Date:</strong> ' + (document.getElementById('declaration_date').value || 'Not provided') + '</p>';

    previewHTML += '</div>';

    previewContent.innerHTML = previewHTML;
    modal.style.display = 'flex';
}

function previewFile(input, previewDiv) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            previewDiv.innerHTML = '<img src="' + e.target.result + '" alt="Preview" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; margin-top: 10px;">';
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        previewDiv.innerHTML = '';
    }
}

// Camera System Initialization
function initializeCameraSystem() {
    console.log('📷 Camera system initialized - selfie capture ready');
}

// Navigation hover effects
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.navbar a');

    navLinks.forEach(link => {
        link.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });

        link.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
