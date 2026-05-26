<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.html");
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$business = trim($_POST["business"] ?? "");
$message = trim($_POST["message"] ?? "");

if ($name === "" || $email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Bitte gib deinen Namen und eine gültige E-Mail-Adresse ein.");
}

$to = "alexandra@a-class-marketing.at";
$subject = "Neue Workshop Anmeldung";

$emailBody = "
Neue Anmeldung zum Workshop: Positionierung & Zielgruppenansprache

Name: $name
E-Mail: $email
Telefon: $phone
Unternehmen / Branche: $business

Nachricht:
$message
";

$headers = "From: A-Class Marketing <alexandra@a-class-marketing.at>\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

$sent = mail($to, $subject, $emailBody, $headers);

if ($sent) {
    header("Location: danke.html");
    exit;
} else {
    echo "Leider konnte die Anmeldung nicht gesendet werden. Bitte versuche es später erneut.";
}
?>