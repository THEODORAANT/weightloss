<?php

require_once __DIR__ . '/comms_service.php';

function comms_sync_format_datetime(?string $value): string
{
    if ($value === null || $value === '') {
        return '';
    }

    $timestamp = strtotime($value);
    if ($timestamp === false) {
        return '';
    }

    return gmdate('Y-m-d\\TH:i:s.000\\Z', $timestamp);
}

function comms_sync_member(int $memberID): bool
{
    $memberData = perch_member_profile($memberID);
    if (!is_array($memberData)) {
        $memberData = [];
    }

    $addressData = [];
    $Customer = null;
    // echo "sync_comms_member2";
    if (class_exists('PerchAPI') && class_exists('PerchShop_Customers')) {
        $API = new PerchAPI(1.0, 'perch_shop');
        $Customers = new PerchShop_Customers($API);
        $Customer = $Customers->find_by_memberID($memberID);
        if ($Customer instanceof PerchShop_Customer) {
            $Addresses = new PerchShop_Addresses($API);
            $Address = $Addresses->find_for_customer($Customer->id(), 'default');
            if (!$Address instanceof PerchShop_Address) {
                $Address = $Addresses->find_for_customer($Customer->id(), 'shipping');
            }
            if ($Address instanceof PerchShop_Address) {
                $addressData = $Address->to_array();
            }
        }
    }

    $firstName = trim((string)($memberData['first_name'] ?? ''));
    $lastName = trim((string)($memberData['last_name'] ?? ''));
    $name = trim($firstName . ' ' . $lastName);
    if ($name === '') {
        $name = (string)($memberData['name'] ?? '');
    }

    $email = (string)($memberData['memberEmail'] ?? ($memberData['email'] ?? ''));
    $dob = (string)($memberData['dob'] ?? '');
    $phone = (string)($memberData['phone'] ?? '');
    $gender = (string)($memberData['gender'] ?? '');
    $address1 = (string)($addressData['address_1'] ?? ($memberData['address1'] ?? ($memberData['address_1'] ?? '')));
    $city = (string)($addressData['city'] ?? ($memberData['city'] ?? ''));
    $zip = (string)($addressData['postcode'] ?? ($memberData['zip'] ?? ($memberData['postcode'] ?? '')));
    $country = (string)($addressData['country_name'] ?? ($addressData['country'] ?? ($memberData['country'] ?? '')));
    $createdAt = comms_sync_format_datetime(
        (string)($memberData['memberCreated'] ?? ($memberData['createdAt'] ?? ''))
    );
    $updatedAt = comms_sync_format_datetime(
        (string)($memberData['memberUpdated'] ?? ($memberData['updatedAt'] ?? $memberData['memberCreated'] ?? ''))
    );

    $payload =  [
      'first_name' =>$firstName,
       'last_name' =>$lastName,
        'name' => $name,
        'email' => $email,
        'dob' => $dob,
        'phone' => $phone,
        'gender' => $gender,
        'addressLine1' => $address1,
        'city' => $city,
        'postCode' => $zip,
        'country' => "UK",
        'createdAt' => $createdAt,
        'updatedAt' => $updatedAt,
    ];
 //echo "sync_comms_member3";
    $response = comms_service_link_member_response($memberID, $payload);
    //print_r($response );
    if ($response !== null) {
        $customerId = comms_service_extract_customer_id($response);
         //echo "customerId";  print_r($customerId );
        if ($customerId !== '' && $Customer instanceof PerchShop_Customer) {
            $Customer->update(['pharmacy_refid' => $customerId]);
        }
    }

    return $response !== null;
}

function comms_sync_order(int $orderID, ?int $memberID = null): bool
{
    $API = new PerchAPI(1.0, 'perch_shop');
    $Orders = new PerchShop_Orders($API);
    $Order = $Orders->find($orderID);
    if (!$Order instanceof PerchShop_Order) {
        return false;
    }

    $orderData = $Order->to_array();
    if ($memberID !== null) {
        $orderData['memberID'] = $memberID;
    }

    return comms_service_link_order($orderID, $orderData);
}
