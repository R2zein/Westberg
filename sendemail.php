<?php
require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form fields and remove whitespace
    $name = strip_tags(trim($_POST["name"]));
    $name = str_replace(array("\r","\n"),array(" "," "),$name);
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $subject = trim($_POST["subject"]);
    $message = trim($_POST["message"]);

    // Check that data was submitted to the mailer
    if (empty($name) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Set a 400 (bad request) response code and exit
        http_response_code(400);
        echo "Please complete the form and try again.";
        exit;
    }

    // Set the recipient email address
    $recipient = "officialwestberg@gmail.com";

    // Set the email subject
    $subject = "New contact from $name: $subject";

    // Build the email content
    $email_content = "Name: $name\n";
    $email_content .= "Email: $email\n\n";
    $email_content .= "Message:\n$message\n";

    // Build the email headers
    $email_headers = "From: $name <$email>";

    // Send the email
    try {
        $mail = new PHPMailer(true);

        //Server settings
        $mail->isSMTP();                                           
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;                                  
        $mail->Username   = 'officialwestberg@gmail.com';
        $mail->Password   = 'gehukarapitan3';
        $mail->SMTPSecure = 'tls';         
        $mail->Port       = 587;

        //Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress($recipient);

        // Content
        $mail->isHTML(false);                                 
        $mail->Subject = $subject;
        $mail->Body    = $email_content;

        $mail->send();
        // Set a 200 (okay) response code
        http_response_code(200);
        echo "Thank you! Your message has been sent.";
    } catch (Exception $e) {
        // Set a 500 (internal server error) response code
        http_response_code(500);
        echo "Oops! Something went wrong and we couldn't send your message. Mailer Error: {$mail->ErrorInfo}";
    }

} else {
    // Not a POST request, set a 403 (forbidden) response code
    http_response_code(403);
    echo "There was a problem with your submission, please try again.";
}