<?php
/**
 * Envoi SMS (Twilio) et email transactionnel (SendGrid HTTP API ou mail()).
 * Les cles se definissent dans config/notifications.local.php et config/email.local.php.
 */

declare(strict_types=1);

function studyhubTwilioConfigured(): bool
{
    $sid = studyhubGetOutboundConfig('TWILIO_ACCOUNT_SID');
    $token = studyhubGetOutboundConfig('TWILIO_AUTH_TOKEN');
    $from = studyhubGetOutboundConfig('TWILIO_FROM_NUMBER');
    return $sid !== '' && $token !== '' && $from !== '';
}

/**
 * Si true et Twilio absent : le code OTP est affiche sur la page (tests locaux uniquement).
 * Definir SMS_DEV_SHOW_CODE dans config/notifications.local.php — jamais en production.
 */
function studyhubSmsDevModeEnabled(): bool
{
    if (defined('SMS_DEV_SHOW_CODE') && SMS_DEV_SHOW_CODE) {
        return true;
    }
    $v = strtolower(trim(studyhubGetOutboundConfig('SMS_DEV_SHOW_CODE', '')));
    return $v === '1' || $v === 'true' || $v === 'yes';
}

function studyhubSmsCanProceed(): bool
{
    return studyhubTwilioConfigured() || studyhubSmsDevModeEnabled();
}

/**
 * Message si SID/token presents mais numero From absent (Twilio exige un numero actif).
 */
function studyhubTwilioMissingFromHint(): string
{
    $sid = studyhubGetOutboundConfig('TWILIO_ACCOUNT_SID');
    $token = studyhubGetOutboundConfig('TWILIO_AUTH_TOKEN');
    $from = studyhubGetOutboundConfig('TWILIO_FROM_NUMBER');
    if ($sid !== '' && $token !== '' && $from === '') {
        return 'Ajoutez votre numero Twilio dans TWILIO_FROM_NUMBER (Console Twilio > Phone Numbers > Active numbers, format E.164 comme +12025551234).';
    }
    return '';
}

function studyhubSendgridConfigured(): bool
{
    return studyhubGetOutboundConfig('SENDGRID_API_KEY') !== '';
}

function studyhubSmtpConfigured(): bool
{
    $host = studyhubGetOutboundConfig('SMTP_HOST');
    $user = studyhubGetOutboundConfig('SMTP_USER');
    $pass = studyhubGetOutboundConfig('SMTP_PASS');
    return $host !== '' && $user !== '' && $pass !== '';
}

function studyhubGetOutboundConfig(string $key, string $default = ''): string
{
    $env = getenv($key);
    if (is_string($env) && trim($env) !== '') {
        return trim($env);
    }
    if (defined($key)) {
        $v = constant($key);
        if (is_string($v) && trim($v) !== '') {
            return trim($v);
        }
    }
    return $default;
}

/**
 * Numero local 8 chiffres (Tunisie) -> E.164 +216XXXXXXXX
 */
function studyhubPhoneToE164Tn(string $eightDigits): string
{
    return '+216' . $eightDigits;
}

/**
 * True si le numero Twilio « From » est le meme que le destinataire tunisien (8 chiffres stockes).
 * Twilio interdit From == To ; le From doit etre le numero achete chez Twilio, pas le portable du client.
 */
function studyhubTwilioFromEqualsRecipientTn(string $twilioFrom, string $normalizedEightDigitsTn): bool
{
    $fromDigits = preg_replace('/\D+/', '', $twilioFrom);
    $local = preg_replace('/\D+/', '', $normalizedEightDigitsTn);
    if (strlen($local) !== 8) {
        return false;
    }
    return $fromDigits === ('216' . $local);
}

function studyhubTwilioHumanizeSmsError(string $msg): string
{
    if (stripos($msg, 'cannot be the same') !== false) {
        return "Twilio refuse : expediteur et destinataire identiques. Dans config/notifications.local.php, TWILIO_FROM_NUMBER doit etre le numero attribue par Twilio (Develop > Phone Numbers > Active numbers), souvent +1..., pas le numero mobile du client.";
    }
    if (stripos($msg, '21601') !== false || stripos($msg, 'Trial accounts') !== false || stripos($msg, 'unverified') !== false) {
        return $msg . ' — En compte Test Twilio, ajoutez le destinataire dans Phone Numbers > Verified Caller IDs, ou passez un compte payant pour envoyer vers n importe quel numero.';
    }
    return $msg;
}

/**
 * Envoie un SMS via l API REST Twilio.
 *
 * @return array{ok:bool, error?:string, http?:int}
 */
function studyhubSendSmsTwilio(string $e164To, string $body): array
{
    if (!studyhubTwilioConfigured()) {
        return ['ok' => false, 'error' => 'Twilio non configure (TWILIO_ACCOUNT_SID, TWILIO_AUTH_TOKEN, TWILIO_FROM_NUMBER).'];
    }

    $sid = studyhubGetOutboundConfig('TWILIO_ACCOUNT_SID');
    $token = studyhubGetOutboundConfig('TWILIO_AUTH_TOKEN');
    $from = studyhubGetOutboundConfig('TWILIO_FROM_NUMBER');

    $url = 'https://api.twilio.com/2010-04-01/Accounts/' . rawurlencode($sid) . '/Messages.json';
    $post = http_build_query([
        'From' => $from,
        'To' => $e164To,
        'Body' => $body,
    ], '', '&');

    $ch = curl_init($url);
    if ($ch === false) {
        return ['ok' => false, 'error' => 'curl_init a echoue.'];
    }

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $post,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => $sid . ':' . $token,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
        CURLOPT_TIMEOUT => 20,
    ]);

    $raw = curl_exec($ch);
    $errno = curl_errno($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($errno !== 0) {
        return ['ok' => false, 'error' => 'Erreur reseau SMS (cURL ' . $errno . ').', 'http' => $http];
    }

    if ($http >= 200 && $http < 300) {
        return ['ok' => true, 'http' => $http];
    }

    $msg = 'Twilio HTTP ' . $http;
    if (is_string($raw) && $raw !== '') {
        $decoded = json_decode($raw, true);
        if (is_array($decoded) && isset($decoded['message'])) {
            $msg .= ': ' . (string)$decoded['message'];
        }
    }
    return ['ok' => false, 'error' => studyhubTwilioHumanizeSmsError($msg), 'http' => $http];
}

/**
 * Envoie un email via SMTP (Gmail, Brevo, OVH, etc.) — necessaire sur XAMPP/Windows ou mail() ne delivre pas.
 *
 * @return array{ok:bool, error?:string}
 */
function studyhubSendEmailSmtp(
    string $toEmail,
    string $subject,
    string $textBody,
    ?string $htmlBody,
    string $fromEmail,
    string $fromName
): array {
    $host = studyhubGetOutboundConfig('SMTP_HOST');
    $port = (int)studyhubGetOutboundConfig('SMTP_PORT', '587');
    if ($port <= 0) {
        $port = 587;
    }
    $user = studyhubGetOutboundConfig('SMTP_USER');
    $pass = studyhubGetOutboundConfig('SMTP_PASS');
    $enc = strtolower(studyhubGetOutboundConfig('SMTP_ENCRYPTION', 'tls'));
    $verifyPeer = studyhubGetOutboundConfig('SMTP_VERIFY_PEER', '1') !== '0';

    $remote = ($enc === 'ssl' ? 'ssl' : 'tcp') . '://' . $host . ':' . $port;
    $ctx = stream_context_create([
        'ssl' => [
            'verify_peer' => $verifyPeer,
            'verify_peer_name' => $verifyPeer,
            'allow_self_signed' => !$verifyPeer,
        ],
    ]);

    $fp = @stream_socket_client(
        $remote,
        $errno,
        $errstr,
        25,
        STREAM_CLIENT_CONNECT,
        $ctx
    );
    if ($fp === false) {
        return ['ok' => false, 'error' => "Connexion SMTP impossible ($errno): $errstr"];
    }

    stream_set_timeout($fp, 25);

    $readFullResponse = static function ($fp): string {
        $out = '';
        while (($line = fgets($fp, 8192)) !== false) {
            $out .= $line;
            if (strlen($line) >= 4 && $line[3] === ' ') {
                break;
            }
        }
        return $out;
    };

    $expect = static function (string $resp, array $okCodes, string $ctxLabel) use ($readFullResponse): ?string {
        $code = (int)substr(trim($resp), 0, 3);
        if (!in_array($code, $okCodes, true)) {
            return $ctxLabel . ': ' . trim(str_replace(["\r", "\n"], ' ', $resp));
        }
        return null;
    };

    $sendCmd = static function ($fp, string $cmd) use ($readFullResponse): string {
        fwrite($fp, $cmd . "\r\n");
        return $readFullResponse($fp);
    };

    $greeting = $readFullResponse($fp);
    if ($err = $expect($greeting, [220], 'SMTP greeting')) {
        fclose($fp);
        return ['ok' => false, 'error' => $err];
    }

    $ehloId = preg_replace('/[^\x20-\x7E]/', '', (string)(gethostname() ?: 'localhost'));
    if ($ehloId === '') {
        $ehloId = 'localhost';
    }

    $ehlo = $sendCmd($fp, 'EHLO ' . $ehloId);
    if ($err = $expect($ehlo, [250], 'EHLO')) {
        fclose($fp);
        return ['ok' => false, 'error' => $err];
    }

    if ($enc === 'tls' && $port !== 465) {
        $start = $sendCmd($fp, 'STARTTLS');
        if ($err = $expect($start, [220], 'STARTTLS')) {
            fclose($fp);
            return ['ok' => false, 'error' => $err];
        }
        $tlsOk = false;
        $tlsVariants = [];
        if (\defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) {
            $tlsVariants[] = \STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
        }
        $tlsVariants[] = \STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
        $tlsVariants[] = \STREAM_CRYPTO_METHOD_TLS_CLIENT;

        foreach ($tlsVariants as $meth) {
            if (@stream_socket_enable_crypto($fp, true, $meth)) {
                $tlsOk = true;
                break;
            }
        }
        if (!$tlsOk) {
            fclose($fp);
            return ['ok' => false, 'error' => 'STARTTLS : negociation TLS echouee (essayez define(\'SMTP_VERIFY_PEER\', \'0\') dans email.local.php sous Windows).'];
        }
        $ehlo2 = $sendCmd($fp, 'EHLO ' . $ehloId);
        if ($err = $expect($ehlo2, [250], 'EHLO apres TLS')) {
            fclose($fp);
            return ['ok' => false, 'error' => $err];
        }
    }

    $auth = $sendCmd($fp, 'AUTH LOGIN');
    if ($err = $expect($auth, [334], 'AUTH LOGIN')) {
        fclose($fp);
        return ['ok' => false, 'error' => $err];
    }
    $u = $sendCmd($fp, base64_encode($user));
    if ($err = $expect($u, [334], 'SMTP user')) {
        fclose($fp);
        return ['ok' => false, 'error' => $err];
    }
    $p = $sendCmd($fp, base64_encode($pass));
    if ($err = $expect($p, [235], 'SMTP mot de passe')) {
        fclose($fp);
        return ['ok' => false, 'error' => $err . ' (verifiez SMTP_USER / SMTP_PASS, Gmail = mot de passe d\'application)'];
    }

    $mf = $sendCmd($fp, 'MAIL FROM:<' . $fromEmail . '>');
    if ($err = $expect($mf, [250], 'MAIL FROM')) {
        fclose($fp);
        return ['ok' => false, 'error' => $err];
    }
    $rcpt = $sendCmd($fp, 'RCPT TO:<' . $toEmail . '>');
    if ($err = $expect($rcpt, [250, 251], 'RCPT TO')) {
        fclose($fp);
        return ['ok' => false, 'error' => $err];
    }
    $data = $sendCmd($fp, 'DATA');
    if ($err = $expect($data, [354], 'DATA')) {
        fclose($fp);
        return ['ok' => false, 'error' => $err];
    }

    $boundary = 'b_' . bin2hex(random_bytes(12));
    $headers = [];
    $headers[] = 'Date: ' . gmdate('D, d M Y H:i:s') . ' +0000';
    $headers[] = 'From: ' . sprintf('=?UTF-8?B?%s?= <%s>', base64_encode($fromName), $fromEmail);
    $headers[] = 'To: <' . $toEmail . '>';
    $headers[] = 'Subject: =?UTF-8?B?' . base64_encode($subject) . '?=';
    $headers[] = 'MIME-Version: 1.0';

    if ($htmlBody !== null && $htmlBody !== '') {
        $headers[] = 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';
        $body = "--{$boundary}\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n" . str_replace("\n.", "\n..", $textBody) . "\r\n";
        $body .= "--{$boundary}\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: 8bit\r\n\r\n" . str_replace("\n.", "\n..", $htmlBody) . "\r\n";
        $body .= "--{$boundary}--\r\n";
    } else {
        $headers[] = 'Content-Type: text/plain; charset=UTF-8';
        $headers[] = 'Content-Transfer-Encoding: 8bit';
        $body = str_replace("\n.", "\n..", $textBody);
    }

    $raw = implode("\r\n", $headers) . "\r\n\r\n" . $body;
    $raw = preg_replace("/^\./m", '..', $raw);

    fwrite($fp, $raw . "\r\n.\r\n");
    $afterData = $readFullResponse($fp);
    if ($err = $expect($afterData, [250], 'fin DATA')) {
        fclose($fp);
        return ['ok' => false, 'error' => $err];
    }

    fwrite($fp, "QUIT\r\n");
    fclose($fp);
    return ['ok' => true];
}

/**
 * Email via SendGrid v3 (API), SMTP, ou repli mail().
 *
 * @return array{ok:bool, error?:string, http?:int}
 */
function studyhubSendEmailTransactional(string $toEmail, string $subject, string $textBody, ?string $htmlBody = null): array
{
    $fromEmail = studyhubGetOutboundConfig('MAIL_FROM_EMAIL', 'no-reply@studyhub.local');
    $fromName = studyhubGetOutboundConfig('MAIL_FROM_NAME', 'StudyHub');

    if (studyhubSendgridConfigured()) {
        $apiKey = studyhubGetOutboundConfig('SENDGRID_API_KEY');
        $payload = [
            'personalizations' => [
                [
                    'to' => [
                        ['email' => $toEmail],
                    ],
                ],
            ],
            'from' => ['email' => $fromEmail, 'name' => $fromName],
            'subject' => $subject,
            'content' => [
                ['type' => 'text/plain', 'value' => $textBody],
            ],
        ];
        if ($htmlBody !== null && $htmlBody !== '') {
            $payload['content'][] = ['type' => 'text/html', 'value' => $htmlBody];
        }

        $ch = curl_init('https://api.sendgrid.com/v3/mail/send');
        if ($ch === false) {
            return ['ok' => false, 'error' => 'curl_init a echoue.'];
        }

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 25,
        ]);

        $raw = curl_exec($ch);
        $errno = curl_errno($ch);
        $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno !== 0) {
            return ['ok' => false, 'error' => 'Erreur reseau email (cURL ' . $errno . ').', 'http' => $http];
        }

        if ($http >= 200 && $http < 300) {
            return ['ok' => true, 'http' => $http];
        }

        return ['ok' => false, 'error' => 'SendGrid HTTP ' . $http . ($raw ? ': ' . substr((string)$raw, 0, 500) : ''), 'http' => $http];
    }

    $smtpHost = studyhubGetOutboundConfig('SMTP_HOST');
    $smtpUser = studyhubGetOutboundConfig('SMTP_USER');
    $smtpPass = studyhubGetOutboundConfig('SMTP_PASS');
    if ($smtpHost !== '' && $smtpUser !== '' && $smtpPass === '') {
        return ['ok' => false, 'error' => "SMTP_PASS est vide dans config/email.local.php. Google : https://myaccount.google.com/apppasswords — collez le mot de passe d application (16 caracteres) sans espaces."];
    }

    if (studyhubSmtpConfigured()) {
        return studyhubSendEmailSmtp($toEmail, $subject, $textBody, $htmlBody, $fromEmail, $fromName);
    }

    $headers = 'From: ' . $fromName . ' <' . $fromEmail . ">\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    if ($htmlBody !== null && $htmlBody !== '') {
        $boundary = 'bnd_' . bin2hex(random_bytes(8));
        $headers .= 'Content-Type: multipart/alternative; boundary="' . $boundary . "\"\r\n";
        $message = "--{$boundary}\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n{$textBody}\r\n";
        $message .= "--{$boundary}\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n{$htmlBody}\r\n";
        $message .= "--{$boundary}--\r\n";
    } else {
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message = $textBody;
    }

    $ok = @mail($toEmail, '=?UTF-8?B?' . base64_encode($subject) . '?=', $message, $headers);
    return $ok ? ['ok' => true] : ['ok' => false, 'error' => "Aucun envoi email configure. Creez config/email.local.php avec SENDGRID_API_KEY ou SMTP_* (voir email.local.example.php). Sur XAMPP, mail() ne livre presque jamais vers Gmail."];
}
