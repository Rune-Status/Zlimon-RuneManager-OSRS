<?php

namespace OSRSCM\Helpers;

use DateTime;

class Helper
{
    /**
     * Calculates the next hour.
     *
     * @return
     */
    public static function roundToNextHour() {
        $dateString = date("H:i:s");
        $date = new DateTime($dateString);

        $nextHour = (intval($date->format('H'))+1) % 24 . ":00";
        
        return $nextHour;
    }

    /**
     * Generates a valid random item ID.
     *
     * @return
     */
    public static function randomItemId() {
        $randomItemId = rand(0,15000);

        if (self::verifyItem($randomItemId)) {
            return $randomItemId;
        } else {
            return self::randomItemId();
        }
    }

    /**
     * Verifies wheter the URL exists or not.
     *
     * @return
     */
    public static function verifyUrl($url) {
        $handle = curl_init($url);
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);

        /* Get the content of $url. */
        $response = curl_exec($handle);

        /* Check for errors (content not found). */
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        curl_close($handle);

        /* If the document has loaded successfully without any redirection or error */
        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Verifies wheter the item exists or not.
     *
     * @return
     */
    public static function verifyItem($itemId) {
        $itemData = 'https://www.osrsbox.com/osrsbox-db/items-json/'.$itemId.'.json';

        if (self::verifyUrl($itemData)) {
            $itemData = file_get_contents($itemData);
            $itemData = json_decode($itemData, true);

            if (!$itemData['noted']) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}