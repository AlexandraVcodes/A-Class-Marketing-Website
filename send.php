<?php
declare(strict_types=1);

/**
 * send.php – Workshop Anmeldung
 * - Validiert Pflichtfelder + Checkbox
 * - Honeypot gegen Bots
 * - Sendet Mail an alexandra@a-class-marketing.at
 * - Zeigt eine Dankesseite (unter /send.php) nach Erfolg
 */

header('Content-Type: text/html; charset=utf-8');

function h(string $v): string {
  return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function post(string $key): string {
  return isset($_POST[$key]) ? trim((string)$_POST[$key]) : '';
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
  http_response_code(405);
  echo "<h1>405 – Method Not Allowed</h1>";
  exit;
}

/** Honeypot: muss leer sein */
if (post('website') !== '') {
  // bewusst "ok" ausgeben, damit Bots keine Rückschlüsse ziehen
  http_response_code(200);
  echo "<h1>Danke!</h1><p>Deine Anfrage wurde gespeichert.</p>";
  exit;
}

/** Pflichtfelder */
$confirm  = post('confirm'); // Checkbox (on/1)
$vorname  = post('vorname');
$nachname = post('nachname');
$email    = post('email');
$attendance = post('attendance');
$street   = post('street');
$house    = post('house');
$zip      = post('zip');
$city     = post('city');

/** Optional */
$company   = post('company');
$phone     = post('phone');
$questions = post('questions');

/** Meta (Hidden Fields, optional) */
$workshop_title = post('workshop_title') ?: 'Canva Advanced Workshop – Social-Media-Edition';
$workshop_date  = post('workshop_date')  ?: '19.03.2026';
$workshop_time  = post('workshop_time')  ?: '17:30';

$errors = [];

/** Server-seitige Validierung */
if ($confirm === '')  $errors[] = "Bitte bestätige die Anmeldung mit dem Häkchen.";
if ($vorname === '')  $errors[] = "Vorname ist erforderlich.";
if ($nachname === '') $errors[] = "Nachname ist erforderlich.";
if ($email === '')    $errors[] = "E-Mailadresse ist erforderlich.";
if ($street === '')   $errors[] = "Straße ist erforderlich.";
if ($house === '')    $errors[] = "Hausnummer ist erforderlich.";
if ($zip === '')      $errors[] = "PLZ ist erforderlich.";
if ($city === '')     $errors[] = "Ort ist erforderlich.";
if ($attendance === '') $errors[] = "Bitte wähle aus, ob du vor Ort oder online teilnimmst.";


/** E-Mail prüfen */
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  $errors[] = "Bitte gib eine gültige E-Mailadresse an.";
}

/** PLZ grob prüfen (AT meist 4-stellig, wir erlauben 4–6) */
if ($zip !== '' && !preg_match('/^[0-9]{4,6}$/', $zip)) {
  $errors[] = "Bitte gib eine gültige PLZ an.";
}

/** Optional: Telefonnummer etwas säubern */
if ($phone !== '') {
  $phone = preg_replace('/[^\d\+\s\/\-\(\)]/', '', $phone);
}

if (!empty($errors)) {
  http_response_code(400);
  ?>
  <!doctype html>
  <html lang="de">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Fehler – Anmeldung</title>
    <meta name="robots" content="noindex,nofollow" />
    <style>
      body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;margin:0;padding:24px;background:#fff7e8;color:#0f172a}
      .card{max-width:780px;margin:0 auto;background:#fff;border:1px solid #e2e8f0;border-radius:18px;padding:18px;box-shadow:0 10px 24px rgba(0,0,0,.12)}
      h1{margin:0 0 10px;color:#68247a}
      ul{margin:10px 0 0;padding-left:18px;color:#475569;line-height:1.6}
      a{color:#68247a;font-weight:800;text-decoration:none}
      a:hover{text-decoration:underline}
      .btn{display:inline-block;margin-top:14px;padding:12px 14px;border-radius:14px;background:#68247a;color:#fff}
    </style>
  </head>
  <body>
    <div class="card">
      <h1>Bitte prüfe deine Angaben</h1>
      <ul>
        <?php foreach ($errors as $e): ?>
          <li><?= h($e) ?></li>
        <?php endforeach; ?>
      </ul>
      <a class="btn" href="javascript:history.back()">Zurück zum Formular</a>
    </div>
  </body>
  </html>
  <?php
  exit;
}

/** Zieladresse */
$to = 'alexandra@a-class-marketing.at';

/** Betreff */
$subject = "Anmeldung: {$workshop_title} – {$workshop_date} {$workshop_time}";

/** Nachricht */
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
  "Teilnahme: {$attendance}",
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

/**
 * Header / Zustellbarkeit:
 * From sollte zur Domain passen. Optimal ist eine echte Mailbox am Hosting.
 */
$host = $_SERVER['HTTP_HOST'] ?? 'a-class-marketing.at';
$host = preg_replace('/^www\./', '', $host);
$fromEmail = "no-reply@{$host}";

$headers = [];
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/plain; charset=UTF-8";
$headers[] = "From: Workshop Anmeldung <{$fromEmail}>";
$headers[] = "Reply-To: {$email}";
$headers[] = "X-Content-Type-Options: nosniff";

/** Versand */
$success = mail($to, $subject, $body, implode("\r\n", $headers));

if (!$success) {
  http_response_code(500);
  ?>
  <!doctype html>
  <html lang="de">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Versand fehlgeschlagen</title>
    <meta name="robots" content="noindex,nofollow" />
    <style>
      body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;margin:0;padding:24px;background:#fff7e8;color:#0f172a}
      .card{max-width:780px;margin:0 auto;background:#fff;border:1px solid #e2e8f0;border-radius:18px;padding:18px;box-shadow:0 10px 24px rgba(0,0,0,.12)}
      h1{margin:0 0 10px;color:#68247a}
      p{margin:10px 0;color:#475569;line-height:1.6}
      a{color:#68247a;font-weight:800;text-decoration:none}
      a:hover{text-decoration:underline}
    </style>
  </head>
  <body>
    <div class="card">
      <h1>Leider hat der Versand nicht funktioniert</h1>
      <p>
        Bitte versuche es später erneut oder schreibe direkt an
        <a href="mailto:alexandra@a-class-marketing.at">alexandra@a-class-marketing.at</a>.
      </p>
      <p><a href="javascript:history.back()">Zurück</a></p>
    </div>
  </body>
  </html>
  <?php
  exit;
}

/** ✅ Erfolg: Dankesseite unter /send.php */
http_response_code(200);
?>
<!doctype html>
<html lang="de">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Vielen Dank für deine Anmeldung</title>
  <meta name="robots" content="noindex,nofollow" />
  <style>
    :root{
      --primary:#68247a;
      --secondary:#fae6c3;
      --border:#e2e8f0;
      --ink:#0f172a;
      --sub:#475569;
      --muted:#64748b;
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
      color:var(--muted);
      font-size:14px;
    }
    a{
      color:var(--primary);
      font-weight:800;
      text-decoration:none;
    }
    a:hover{ text-decoration:underline; }
    .btn{
      display:inline-block;
      margin-top:16px;
      padding:12px 14px;
      border-radius:14px;
      background:var(--primary);
      color:#fff;
    }
  </style>
</head>
<body>
  <div class="card">
    <h1>Vielen Dank für deine Anmeldung.</h1>
    <p>
      Du bekommst umgehend eine Bestätigung für deine erfolgreiche Anmeldung zum
      Canva-Advanced-Workshop am 19.03.2026 um 17:30 Uhr.
    </p>
    <p>
    Liebe Grüße, Alexandra. 
    </p>


    <div class="meta">
      <strong>Deine Angaben:</strong><br />
      <?= h($vorname) ?> <?= h($nachname) ?> · <?= h($email) ?>
    </div>

    <a class="btn" href="https://www.a-class-marketing.at/">Zurück zur Website</a>
  </div>
</body>
</html>
