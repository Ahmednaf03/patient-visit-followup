<?php

function validatePatient(array $data): array
{
    $errors = [];

    // Name
    $name = trim($data['name'] ?? '');
    if ($name === '') {
        $errors[] = 'Name is required.';
    } elseif (strlen($name) < 3) {
        $errors[] = 'Name must be at least 3 characters long.';
    } elseif (!preg_match('/^[a-zA-Z\s]+$/', $name)) {
        $errors[] = 'Name can only contain letters and spaces.';
    }

    // DOB
    $dob = $data['dob'] ?? '';
    if ($dob === '') {
        $errors[] = 'Date of birth is required.';
    } elseif (!validateDate($dob)) {
        $errors[] = 'Date of birth is invalid.';
    } elseif ($dob > date('Y-m-d')) {
        $errors[] = 'Date of birth cannot be in the future.';
    }

    // Join date
    $joinDate = $data['join_date'] ?? '';
    if ($joinDate === '') {
        $errors[] = 'Join date is required.';
    } elseif (!validateDate($joinDate)) {
        $errors[] = 'Join date is invalid.';
    } elseif ($joinDate > date('Y-m-d')) {
        $errors[] = 'Join date cannot be in the future.';
    }

    // Phone (optional)
    $phone = trim($data['phone'] ?? '');
    if ($phone !== '' && !preg_match('/^[0-9]{10}$/', $phone)) {
        $errors[] = 'Phone number must be 10 digits.';
    }

    // Address (optional)
    $address = trim($data['address'] ?? '');
    if ($address !== '' && strlen($address) < 5) {
        $errors[] = 'Address must be at least 5 characters long.';
    }

    return $errors;
}

function validateVisit(array $data): array
{
    $errors = [];

    // Patient
    if (empty($data['patient_id']) || !is_numeric($data['patient_id'])) {
        $errors[] = 'Valid patient is required.';
    }

    // Visit date
    $visitDate = $data['visit_date'] ?? '';
    if ($visitDate === '') {
        $errors[] = 'Visit date is required.';
    } elseif (!validateDate($visitDate)) {
        $errors[] = 'Visit date is invalid.';
    } elseif ($visitDate > date('Y-m-d')) {
        $errors[] = 'Visit date cannot be in the future.';
    }

    // Consultation fee
    if (!isset($data['consultation_fee']) || $data['consultation_fee'] === '') {
        $errors[] = 'Consultation fee is required.';
    } elseif (!is_numeric($data['consultation_fee']) || $data['consultation_fee'] < 0) {
        $errors[] = 'Consultation fee must be a positive number.';
    }

    // Lab fee
    if (!isset($data['lab_fee']) || $data['lab_fee'] === '') {
        $errors[] = 'Lab fee is required.';
    } elseif (!is_numeric($data['lab_fee']) || $data['lab_fee'] < 0) {
        $errors[] = 'Lab fee must be a positive number.';
    }

    return $errors;
}


function validateDate(string $date): bool
{
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}
