<?php

function enviarTelegram($mensaje) {
    $token = "8657639866:AAHeCj9Q5zxRFnSYR5LZqFFbbV1IkjgXpiM";
    $chat_id = "8466418643";

    $url = "https://api.telegram.org/bot$token/sendMessage";

    $data = [
        'chat_id' => $chat_id,
        'text' => $mensaje
    ];

    file_get_contents($url . "?" . http_build_query($data));
}