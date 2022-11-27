<?php

/* --------------------------------------------------------------------

  Chevereto
  https://chevereto.com/

  @author	Rodolfo Berrios A. <http://rodolfoberrios.com/>
            <inbox@rodolfoberrios.com>

  Copyright (C) Rodolfo Berrios A. All rights reserved.

  BY USING THIS SOFTWARE YOU DECLARE TO ACCEPT THE CHEVERETO EULA
  https://chevereto.com/license

  --------------------------------------------------------------------- */

if (!defined('access') or !access) {
    die('This file cannot be directly accessed.');
}
$jobs = ['storageDelete', 'deleteExpiredImages', 'cleanUnconfirmedUsers', 'removeDeleteLog', 'tryForUpdates'];
shuffle($jobs);
foreach ($jobs as $job) {
    if (!CHV\isSafeToExecute()) {
        echo "[OK] Exit - (time is up)\n";
        die(0);
    }
    echo "* Processing $job\n";
    $job();
}
echo "--\n[OK] Cron tasks ran\n";
die();

function echoLocked() {
    echo "> Job locked, skipping\n";
}

function storageDelete()
{
    $lock = new CHV\Lock('storage-delete');
    if ($lock->create()) {
        CHV\Queue::process(['type' => 'storage-delete']);
        $lock->destroy();
    } else {
        echoLocked();
    }
}

function deleteExpiredImages()
{
    $lock = new CHV\Lock('delete-expired-images');
    if ($lock->create()) {
        CHV\Image::deleteExpired(50);
        $lock->destroy();
    } else {
        echoLocked();
    }
}

function cleanUnconfirmedUsers()
{
    $lock = new CHV\Lock('clean-unconfirmed-users');
    if ($lock->create()) {
        CHV\User::cleanUnconfirmed(5);
        $lock->destroy();
    } else {
        echoLocked();
    }
}

function removeDeleteLog()
{
    $lock = new CHV\Lock('remove-delete-log');
    if ($lock->create()) {
        $db = CHV\DB::getInstance();
        $db->query('DELETE FROM ' . CHV\DB::getTable('deletions') . ' WHERE deleted_date_gmt <= :time;');
        $db->bind(':time', G\datetime_sub(G\datetimegmt(), 'P3M'));
        $db->exec();
        $lock->destroy();
    } else {
        echoLocked();
    }
}

function tryForUpdates()
{
    if (
        is_null(CHV\Settings::get('update_check_datetimegmt'))
        || G\datetime_add(CHV\Settings::get('update_check_datetimegmt'), 'P1D') < G\datetimegmt()) {
        CHV\L10n::setLocale(CHV\Settings::get('default_language'));
        $lock = new CHV\Lock('check-updates');
        if ($lock->create()) {
            //CHV\checkUpdates();
            $lock->destroy();
        } else {
            echoLocked();
        }
    }
}
