<?php

use WestFrome\Mailer;

require_once 'Mailer.php';
require_once 'functions.php';
require_once 'config.php';

switch ($_SERVER['REQUEST_METHOD']) {
    case 'OPTIONS':
        validateCors();
        break;
    case 'POST':
        $rules = [
            'name' => 'required',
            'email' => 'required|email',
            'message' => 'required|min:20'
        ];

        $errors = validate($rules, $_POST);

        if (!empty($errors)) {
            renderValidationError($errors);
            exit();
        }

        try {
            $mailer = new Mailer($config);
            $mailer->send($_POST['name'], $_POST['email'], $_POST['message']);
            render('', 204);
        } catch (Swift_TransportException $e) {
            render($e->getMessage(), 500);
        }
        break;
    default:
        render('', 405);
}

