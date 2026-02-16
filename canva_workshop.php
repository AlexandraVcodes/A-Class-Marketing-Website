<?php
declare(strict_types=1);

/**
 * send.php – Formularversand für Workshop-Anmeldung
 * - Validiert Pflichtfelder
 * - Honeypot gegen Bots
 * - Sendet E-Mail an alexandra@a-class-marketing.at
 * - Gibt eine schlanke HTML-Erfolgs-/Fehlerseite zurück
 */

header('Content-Type: text/html; charset=utf-8');

function h(string $v): string {
  return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function post(string $key): string {
  return isset($_POST[$key]) ? trim((string)$_POST[$key]) : '';
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo "<h1>405 – Method Not Allowed</h1>";
  exit;
}

// Honeypot: muss leer bleiben
if (post('website') !== '') {
  http_response_code(200);
  echo "<h1>Danke!</h1><p>Deine Anfrage wurde gespeichert.</p>";
  exit;
}

// Pflichtfelder
$confirm  = post('confirm'); // checkbox -> "on" oder "1"
$vorname  = post('vorname');
$nachname = post('nachname');
$email    = post('email');
$street   = post('street');
$house    = post('house');
$zip      = post('zip');
$city     = post('city');
$attendance = post('attendance');


// Freiwillig
$company  = post('company');
$phone    = post('phone');
$questions = post('questions');

// Workshop meta
$workshop_title = post('workshop_title') ?: 'Canva Advanced Workshop – Social-Media-Edition';
$workshop_date  = post('workshop_date') ?: '19.03.2026';
$workshop_time  = post('workshop_time') ?: '17:30';

// Server-seitige Validierung
$errors = [];

if ($confirm === '') $errors[] = "Bitte bestätige die Anmeldung mit dem Häkchen.";
if ($vorname === '') $errors[] = "Vorname ist erforderlich.";
if ($nachname === '') $errors[] = "Nachname ist erforderlich.";
if ($email === '') $errors[] = "E-Mailadresse ist erforderlich.";
if ($street === '') $errors[] = "Straße ist erforderlich.";
if ($house === '') $errors[] = "Hausnummer ist erforderlich.";
if ($zip === '') $errors[] = "PLZ ist erforderlich.";
if ($city === '') $errors[] = "Ort ist erforderlich.";
if ($attendance === '') $errors[] = "Bitte wähle aus, ob du vor Ort oder online teilnimmst.";


// E-Mail validieren
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $errors[] = "Bitte gib eine gültige E-Mailadresse an.";
}

// Minimale Länge / Plausibilität
if ($zip !== '' && !preg_match('/^[0-9]{4,6}$/', $zip)) {
  $errors[] = "Bitte gib eine gültige PLZ an.";
}

if (!empty($errors)) {
  http_response_code(400);
  echo "<h1>Bitte prüfe deine Angaben</h1><ul>";
  foreach ($errors as $e) echo "<li>" . h($e) . "</li>";
  echo "</ul><p><a href=\"javascript:history.back()\">Zurück zum Formular</a></p>";
  exit;
}

// Zieladresse
$to = 'alexandra@a-class-marketing.at';

// Betreff
$subject = "Anmeldung: {$workshop_title} – {$workshop_date} {$workshop_time}";

// Nachricht
$messageLines = [
  "Neue Workshop-Anmeldung",
  "=======================",
  "",
  "Workshop: {$workshop_title}",
  "Datum/Uhrzeit: {$workshop_date} {$workshop_time}",
  "",
  "Teilnehmer/in",
  "------------",
  "Vorname: {$vorname}",
  "Nachname: {$nachname}",
  "E-Mail: {$email}",
  "Telefon: " . ($phone !== '' ? $phone : '-'),
  "Firma: " . ($company !== '' ? $company : '-'),
  "",
  "Rechnungs-/Anschrift",
  "-------------------",
  "Straße: {$street}",
  "Hausnummer: {$house}",
  "PLZ: {$zip}",
  "Ort: {$city}",
  "",
  "Weitere Fragen",
  "-------------",
  ($questions !== '' ? $questions : '-'),
  "",
  "Meta",
  "----",
  "IP: " . ($_SERVER['REMOTE_ADDR'] ?? '-'),
  "User-Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? '-'),
  "Zeit: " . date('c'),
];

$body = implode("\r\n", $messageLines);

// Header (Reply-To auf Teilnehmer, From auf Server-Domain Adresse)
$fromEmail = 'no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'example.com');
$headers = [];
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/plain; charset=UTF-8";
$headers[] = "From: Workshop Anmeldung <{$fromEmail}>";
$headers[] = "Reply-To: " . $email;
$headers[] = "X-Content-Type-Options: nosniff";

$success = mail($to, $subject, $body, implode("\r\n", $headers));

if (!$success) {
  http_response_code(500);
  echo "<h1>Leider hat der Versand nicht funktioniert</h1>
        <p>Bitte versuche es später erneut oder schreibe direkt an <a href=\"mailto:alexandra@a-class-marketing.at\">alexandra@a-class-marketing.at</a>.</p>";
  exit;
}

http_response_code(200);
?>
<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Danke für deine Anmeldung</title>
  <meta name="robots" content="noindex,nofollow" />
  <style>
    :root{
      --primary:#68247a;
      --secondary:#fae6c3;
      --border:#e2e8f0;
      --ink:#0f172a;
      --sub:#475569;
      --radius:22px;
      --shadow:0 10px 24px rgba(0,0,0,.12);
    }
    body{
      font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;
      margin:0;
      padding:24px;
      background: linear-gradient(100deg, var(--secondary), #fff 40%, #fdfdfd);
      color:var(--ink);
    }
    .card{
      max-width:780px;
      margin:0 auto;
      background:#fff;
      border:1px solid var(--border);
      border-radius:var(--radius);
      padding:22px;
      box-shadow:var(--shadow);
    }
    h1{
      margin:0 0 10px;
      color:var(--primary);
      letter-spacing:-0.3px;
    }
    p{
      margin:10px 0;
      color:var(--sub);
      line-height:1.6;
      font-size:16px;
    }
    .meta{
      margin-top:14px;
      padding:12px 14px;
      border-radius:16px;
      background:rgba(104,36,122,.06);
      border:1px solid rgba(104,36,122,.18);
      color:var(--sub);
      font-size:14px;
    }
    a{
      color:var(--primary);
      font-weight:800;
      text-decoration:none;
    }
    a:hover{ text-decoration:underline; }
  </style>
</head>
<body>
  <div class="card">
    <h1>Vielen Dank für deine Anmeldung.</h1>
    <p>
      Du bekommst umgehend eine Bestätigung für die erfolgreiche Anmeldung zum
      Canva-Advanced-Workshop am 19.03.2026.
    </p>

    <div class="meta">
      <strong>Deine Angaben:</strong><br />
      <?= h($vorname) ?> <?= h($nachname) ?> · <?= h($email) ?>
    </div>

    <p style="margin-top:16px;">
      <a href="https://www.a-class-marketing.at/">Zurück zur Website</a>
    </p>
  </div>
</body>
</html>
