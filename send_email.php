<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["Name"]);
    $email = trim($_POST["Email"]);
    $phone = trim($_POST["phone"]);
    $message = trim($_POST["message"]);

    // Validate reCAPTCHA
    $recaptcha_secret = 'YOUR_RECAPTCHA_SECRET_KEY';
    $recaptcha_response = $_POST['g-recaptcha-response'];
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha = file_get_contents($recaptcha_url . '?secret=' . $recaptcha_secret . '&response=' . $recaptcha_response);
    $recaptcha_data = json_decode($recaptcha);
    
    if (!$recaptcha_data->success) {
        echo json_encode(array('status' => 'error', 'message' => 'reCAPTCHA verification failed.'));
        exit;
    }

    // Send email
    $to = "ankitpanchal8717@gmail.com";
    $subject = "New Contact Form Submission";
    $message_body = "Name: $name\nEmail: $email\nPhone: $phone\n\n$message";
    $headers = "From: $email";

    if (mail($to, $subject, $message_body, $headers)) {
        echo json_encode(array('status' => 'success', 'message' => 'Your message has been sent successfully.'));
    } else {
        echo json_encode(array('status' => 'error', 'message' => 'Failed to send your message. Please try again later.'));
    }
} else {
    echo json_encode(array('status' => 'error', 'message' => 'Method not allowed.'));
}
?>
