<?php

namespace App\Helpers;

class StateHelper
{
    /**
     * Get all GST states with their codes
     */
    public static function getStates()
    {
        return [
            '01' => 'Jammu & Kashmir',
            '02' => 'Himachal Pradesh',
            '03' => 'Punjab',
            '04' => 'Chandigarh',
            '05' => 'Uttarakhand',
            '06' => 'Haryana',
            '07' => 'Delhi',
            '08' => 'Rajasthan',
            '09' => 'Uttar Pradesh',
            '10' => 'Bihar',
            '11' => 'Sikkim',
            '12' => 'Arunachal Pradesh',
            '13' => 'Nagaland',
            '14' => 'Manipur',
            '15' => 'Mizoram',
            '16' => 'Tripura',
            '17' => 'Meghalaya',
            '18' => 'Assam',
            '19' => 'West Bengal',
            '20' => 'Jharkhand',
            '21' => 'Odisha',
            '22' => 'Chhattisgarh',
            '23' => 'Madhya Pradesh',
            '24' => 'Gujarat',
            '25' => 'Daman & Diu (merged with Dadra & Nagar Haveli)',
            '26' => 'Dadra & Nagar Haveli and Daman & Diu',
            '27' => 'Maharashtra',
            '28' => 'Andhra Pradesh (Before bifurcation)',
            '29' => 'Karnataka',
            '30' => 'Goa',
            '31' => 'Lakshadweep',
            '32' => 'Kerala',
            '33' => 'Tamil Nadu',
            '34' => 'Puducherry',
            '35' => 'Andaman & Nicobar Islands',
            '36' => 'Telangana',
            '37' => 'Andhra Pradesh (New)',
            '38' => 'Ladakh',
        ];
    }

    /**
     * Get state name by code
     */
    public static function getStateName($code)
    {
        $states = self::getStates();
        return $states[$code] ?? null;
    }

    /**
     * Get state code by name
     */
    public static function getStateCode($name)
    {
        $states = self::getStates();
        return array_search($name, $states) ?: null;
    }
}
