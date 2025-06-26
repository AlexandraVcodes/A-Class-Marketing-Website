<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST["name"]));
    $company = htmlspecialchars(trim($_POST["company"]));
    $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($_POST["phone"]));
    $message = htmlspecialchars(trim($_POST["message"]));
    $captcha = trim($_POST["captcha"]);

    if (empty($name) || empty($email)) {
        die("Bitte fülle die Pflichtfelder Name und E-Mail-Adresse aus.");
    }

    // Captcha validieren: 3 + 4 = 7
    if ($captcha !== "7") {
        die("Captcha falsch. Bitte gib das richtige Ergebnis ein.");
    }

    // Zieladresse
    $to = "alexandra@a-class-marketing.at";
    $subject = "Neue Kontaktanfrage von $name";
    $body = "Name: $name\n";
    $body .= "Unternehmen: $company\n";
    $body .= "E-Mail: $email\n";
    $body .= "Telefon: $phone\n\n";
    $body .= "Nachricht:\n$message";

    $headers = "From: $email";

    if (mail($to, $subject, $body, $headers)) {
        echo "<p style='text-align:center; font-family:sans-serif; font-size:1.2rem; margin-top:2rem;'>Vielen Dank für deine Nachricht!<br>Ich werde dich so schnell als möglich kontaktieren.</p>";
    } else {
        echo "<p style='color:red; text-align:center;'>Fehler beim Senden der Nachricht. Bitte versuche es später erneut.</p>";
    }
} else {
    http_response_code(403);
    echo "Unzulässige Anfrage.";
}
?>
