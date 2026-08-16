<?php

// Eingaben bereinigen
function clear_user_input($value) {
    return str_replace(["\n", "\r"], '', trim($value));
}

// Honeypot prüfen
if (!empty($_POST['hp_field'])) {
    http_response_code(400);
    exit;
}

// Zeitprüfung (mindestens 5 Sekunden zwischen Formularladezeit und Absenden)
if (!isset($_POST['form_start_time']) || (time() - (int)$_POST['form_start_time']) < 5) {
    http_response_code(400);
    exit;
}

// Pflichtfeld E-Mail prüfen
if (!isset($_POST['email']) || empty($_POST['email'])) {
    die('E-Mail-Adresse fehlt.');
}

$email = clear_user_input($_POST['email']);

// E-Mail-Adresse validieren
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die('Ungültige E-Mail-Adresse.');
}

// Konfiguration
$mailto = "ihr.name@ihrserver.de"; // DIES MUSS EINE GÜLTIGE MAILADRESSE VON IHREM SERVER SEIN, AN DIE DIE MAIL VERSCHICKT WIRD
$mailsubj = "=?UTF-8?B?" . base64_encode("Kontaktanfrage von der Website") . "?=";

$mailhead = "From: <$email>\r\n";
$mailhead .= "Reply-To: <$email>\r\n";
$mailhead .= "MIME-Version: 1.0\r\n";
$mailhead .= "Content-Type: text/plain; charset=UTF-8\r\n";

// Mailinhalt zusammenstellen
$body = "Werte, die von der Website übermittelt wurden:\n";

foreach ($_POST as $key => $value) {
    if (in_array($key, ['hp_field', 'form_start_time'])) {
        continue;
    }
    $key = clear_user_input($key);

    if ($key === 'extras' && is_array($value)) {
        $cleaned_extras = array_map('clear_user_input', $value);
        $body .= "$key: " . implode(", ", $cleaned_extras) . "\n";
    } else {
        $value = clear_user_input($value);
        $body .= "$key: $value\n";
    }
}

// Mailversand
if (!mail($mailto, $mailsubj, $body, $mailhead)) {
    die('Fehler beim Senden der Nachricht.');
}
?>


<!DOCTYPE html>
<html>
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="user-scalable=no, width=
       device-width">
      <title>Arztpraxis Dr. Udo Bloemkamp - Kontaktformular Hausarzt München
      </title>
      <link rel="stylesheet" type="text/css" media="screen"
      href="stylesheet.css">
     <link rel="stylesheet" type="text/css" media="print" href="print.css">
     <link rel="stylesheet" type="text/css" media="screen and (max-device-  
width: 480px)" href="mobil.css">
   </head>
   <body>
   </body>
</html>