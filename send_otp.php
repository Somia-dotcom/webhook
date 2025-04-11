<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Load PHPMailer

function sendOTP($email, $otp) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';  // Replace with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'cssomiab@gmail.com';  // Your email
        $mail->Password = 'dlbz cvsl uzep exev';  // Your email password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('cssomiab@gmail.com', 'AgroBazaar');
        $mail->addAddress($email);

        $mail->Subject = 'Your OTP for Login';
        $mail->Body = "Your OTP for login is: $otp";

        if ($mail->send()) {
            echo "OTP Sent Successfully!";
        } else {
            echo "Failed to send OTP!";
        }
    } catch (Exception $e) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    }
}

?>
