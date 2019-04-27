<?php
/**
 * Parser: JSON lang files
 * Author: Alexey Schebelev
 * Version: 1.0
 */

$filetype = 'json|jsn';
$website = 'http://json.org/';

$sample = <<< EOT
{
    "app": {
        "name": "TODO List",
        "login": {
            "text": "Enter your credentials below to login",
            "message": {
                "success": "Login successful !\nWelcome back %s !",
                "error": "Make sure you've entered the correct username and password"
            },
            "password": {
                "forget": "I forgot my password",
                "reset": "Enter your address in the field below. A new password will be sent to your inbox."
            },
            "user": "Username",
            "pass": "Password"
        },
        "register": {
            "text": "Sign up for free! No credit card required!",
            "message": {
                "success": "Thank you for signing up !\nPlease check your email address to activate your account.",
                "error": "We were unable to sign you up.\nPlease correct the marked fields."
            }
        },
        "menu": {
            "terms": "Terms and conditions"
        }
    }
}
EOT;

?>