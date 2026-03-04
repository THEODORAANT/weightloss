<?php

function perch_sendgrid_subscribe($data)
{
    $Factory = new PerchSendGrid_Factory();
    return $Factory->upsert_contact($data);
}

function perch_sendgrid_update_contact($data)
{
    $Factory = new PerchSendGrid_Factory();
    return $Factory->upsert_contact($data);
}
