<?php

function validateCors()
{
    $allowedDomains = [
        'http://localhost:8080',
        'http://security.westfrome.ltd'
    ];

    if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedDomains)) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Max-Age: 86400'); // cache for 1 day
    }
}

function render($content, $status = 200)
{
    http_response_code($status);
    header('Content-Type: application/json');
    header('Vary: Origin');

    validateCors();

    if (!empty($content)) {
        echo json_encode($content);
    }
}

function renderValidationError($errors)
{
    render(['errors' => $errors], 400);
}

function validate($rules, $values)
{
    $errors = [];

    foreach ($rules as $field => $fieldRules) {
        $fieldRules = explode('|', $fieldRules);

        foreach ($fieldRules as $rule) {
            $ruleName = substr($rule, 0, 4) === 'min:' ? 'min' : $rule;

            switch ($ruleName) {
                case 'required':
                    if (empty($values[$field])) {
                        $errors[$field][] = "Please enter your $field";
                        break 2;
                    }
                    break;
                case 'email':
                    if (!filter_var($values[$field], FILTER_VALIDATE_EMAIL)) {
                        $errors[$field][] = "Must be a valid email address";
                    }
                    break;
                case 'min':
                    $min = substr($rule, 4);
                    if (strlen($values[$field]) < $min) {
                        $errors[$field][] = "Must be at least $min characters";
                    }
            }
        }
    }

    return $errors;
}
