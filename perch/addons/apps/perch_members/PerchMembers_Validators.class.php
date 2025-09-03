<?php

class PerchMembers_Validators
{
function isStrongPassword($password) {
    // Minimum length check
    if (strlen($password) < 8) {
        return false;
    }

    // Check for at least one uppercase letter
    if (!preg_match('/[A-Z]/', $password)) {
        return false;
    }

    // Check for at least one lowercase letter
    if (!preg_match('/[a-z]/', $password)) {
        return false;
    }

    // Check for at least one number
    if (!preg_match('/\d/', $password)) {
        return false;
    }

    // Check for at least one special character
    if (!preg_match('/[\W_]/', $password)) {
        return false;
    }

    // If all checks pass, return true
    return true;
}

   }
