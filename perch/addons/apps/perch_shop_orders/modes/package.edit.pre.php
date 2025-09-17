<?php

	$Packages   = new PerchShop_Packages($API);
	$PackageItems = new PerchShop_PackageItems($API);
	$Customers  = new PerchShop_Customers($API);


        $Form = $API->get('Form');

        $message = false;

        if (PerchUtil::get('id')) {

                if (!$CurrentUser->has_priv('perch_shop.orders.edit')) {
                    PerchUtil::redirect($API->app_path());
                }

                $shop_id = PerchUtil::get('id');

                $Package     = $Packages->find($shop_id);

                if (!$Package) {
                    PerchUtil::redirect($API->app_path());
                }

                $Customer    = $Customers->find($Package->customerID());


                if (!PerchSession::get('csrf_token')) {
                    PerchSession::set('csrf_token', md5(uniqid('csrf', true)));
                }

                $status_choices = ['confirmed', 'completed', 'cancelled'];

                if (PerchUtil::post('formaction') === 'update_status') {
                    $token          = PerchUtil::post('token');
                    $session_token  = PerchSession::get('csrf_token');

                    if (!$token || !$session_token || $token !== $session_token) {
                        $message = $HTML->failure_message($Lang->get('Sorry, that request could not be authorised. Please try again.'));
                        PerchSession::set('csrf_token', md5(uniqid('csrf', true)));
                    } else {
                        PerchSession::set('csrf_token', md5(uniqid('csrf', true)));

                        $status = trim((string)PerchUtil::post('status'));

                        if (in_array($status, $status_choices, true)) {
                            if ($Package->update(['status' => $status])) {
                                $message = $HTML->success_message($Lang->get('Package status updated successfully.'));
                            } else {
                                $message = $HTML->failure_message($Lang->get('Sorry, that update was not successful.'));
                            }
                        } else {
                            $message = $HTML->failure_message($Lang->get('Please select a valid status.'));
                        }
                    }
                }


                if (PerchUtil::post('formaction') === 'update_billing_date') {
                    $token          = PerchUtil::post('token');
                    $session_token  = PerchSession::get('csrf_token');

                    if (!$token || !$session_token || $token !== $session_token) {
                        $message = $HTML->failure_message($Lang->get('Sorry, that request could not be authorised. Please try again.'));
                        PerchSession::set('csrf_token', md5(uniqid('csrf', true)));
                    } else {
                        PerchSession::set('csrf_token', md5(uniqid('csrf', true)));

                        $itemID      = (int)PerchUtil::post('itemID');
                        $billingDate = trim((string)PerchUtil::post('billingDate'));

                        if ($itemID > 0 && $billingDate !== '') {
                            $date = DateTime::createFromFormat('Y-m-d', $billingDate);

                            if ($date && $date->format('Y-m-d') === $billingDate) {
                                $Item = $PackageItems->find($itemID);

                                if ($Item && $Item->packageID() == $Package->uuid()) {
                                    if ((int)$Item->month() === 1) {

                                        $update_success = $Item->update(['billingDate' => $billingDate]);

                                        if ($update_success) {
                                            $package_items = $PackageItems->get_for_package($Package->uuid());

                                            if (PerchUtil::count($package_items)) {
                                                $base_date = clone $date;
                                                foreach ($package_items as $package_item) {
                                                    if ((int)$package_item->itemID() === (int)$Item->itemID()) {
                                                        continue;
                                                    }

                                                    $month_number = (int)$package_item->month();

                                                    if ($month_number > 1) {
                                                        $month_offset = $month_number - 1;
                                                        $target_date  = clone $base_date;
                                                        $target_date->modify('+' . $month_offset . ' month');

                                                        if (!$package_item->update(['billingDate' => $target_date->format('Y-m-d')])) {
                                                            $update_success = false;
                                                            break;
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        if ($update_success) {

                                        if ($Item->update(['billingDate' => $billingDate])) {

                                            $message = $HTML->success_message($Lang->get('Billing date updated successfully.'));
                                        } else {
                                            $message = $HTML->failure_message($Lang->get('Sorry, that update was not successful.'));
                                        }
                                    } else {
                                        $message = $HTML->failure_message($Lang->get('Billing date can only be edited for the first month.'));
                                    }
                                } else {
                                    $message = $HTML->failure_message($Lang->get('Sorry, that package item could not be found.'));
                                }
                            } else {
                                $message = $HTML->failure_message($Lang->get('Please enter a valid billing date (YYYY-MM-DD).'));
                            }
                        } else {
                            $message = $HTML->failure_message($Lang->get('Billing date and item information are required.'));
                        }
                    }
                }

                $items = $PackageItems->get_for_admin($Package->uuid());


        }else{
            PerchUtil::redirect($API->app_path());
        }


