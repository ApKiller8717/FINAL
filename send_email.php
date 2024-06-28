<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input data
    $name = htmlspecialchars(strip_tags(trim($_POST["Name"])));
    $email = filter_var(trim($_POST["Email"]), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(strip_tags(trim($_POST["phone"])));
    $message = htmlspecialchars(strip_tags(trim($_POST["message"])));
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Validate input data
    if (empty($name) || empty($email) || empty($phone) || empty($message) || empty($recaptcha_response)) {
        echo json_encode(["status" => "error", "message" => "Please fill in all fields."]);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Please enter a valid email address."]);
        exit;
    }

    // Verify reCAPTCHA
    $recaptcha_secret = "6LeolQIqAAAAAONugrk_DfkceOSKpqKfZFcnxqRc";
    $recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptcha_data = [
        'secret' => $recaptcha_secret,
        'response' => $recaptcha_response
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($recaptcha_data)
        ]
    ];
    $context  = stream_context_create($options);
    $recaptcha_verify = file_get_contents($recaptcha_url, false, $context);
    $recaptcha_success = json_decode($recaptcha_verify);

    if (!$recaptcha_success->success) {
        echo json_encode(["status" => "error", "message" => "reCAPTCHA verification failed."]);
        exit;
    }

    // Email settings
    $to = "ankitpanchal8717@gmail.com";
    $subject = "New Contact Form Submission from $name";
    $email_message = "Name: $name\nEmail: $email\nPhone: $phone\n\nMessage:\n$message";
    $headers = "From: $email";

    // Send email
    if (mail($to, $subject, $email_message, $headers)) {
        echo json_encode(["status" => "success", "message" => "Your message has been sent successfully."]);
    } else {
        echo json_encode(["status" => "error", "message" => "There was an error sending your message."]);
    }
}
?>
