<?php

$mailbox = "{imap.gmail.com:993/imap/ssl}INBOX";
$mailbox_username = "a1063321@mail.nuk.edu.tw";
$mailbox_password = "s124069936";

echo "Trying to connect to '$mailbox'...<br>";

$mbox_connection = imap_open($mailbox, $mailbox_username, $mailbox_password);

$mailsIds = imap_search($mbox_connection, 'SUBJECT "Calculus"', SE_UID, "UTF-8");

if(!$mailsIds) {
    echo "No emails found!<br>";
    imap_close($mbox_connection);
    die();
}

echo "Found " . count($mailsIds) . " email(s)...<br>";

foreach($mailsIds as $mailId) {
    echo "+------ P A R S I N G ------+<br>";

    $headersRaw = imap_fetchheader($mbox_connection, $mailId, FT_UID);
    $header = imap_rfc822_parse_headers($headersRaw);

    echo "From: " . imap_utf8($header->fromaddress) . "<br>";
    echo "Subject: " . imap_utf8($header->subject) . "<br>";
}



?>