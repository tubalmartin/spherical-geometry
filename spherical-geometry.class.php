<?php

/*!
 * Spherical Geometry PHP Library v1.1
 * http://tubalmartin.github.com/spherical-geometry-php/
 *
 * Copyright 2012, Túbal Martín
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * This code is a port of some classes from the Google Maps Javascript API version 3.x
 */
 


/** 
 * Static class SphericalGeometry
 * Utility functions for computing geodesic angles, distances and areas.
 */  
class SphericalGeometry
{
    const EQUALS_MARGIN_ERROR = 1.0E-9;
    
    // Earth's radius (at the Ecuator) of 6378137 meters.
    const EARTH_RADIUS = 6378137; 
        
    
    public static function getEarthRadius()
    {
        return self::EARTH_RADIUS;
    }
    
    /**
     * Computes a bounding rectangle (LatLngBounds instance) from a point and a given radius.
     * Reference: http://www.movable-type.co.uk/scripts/latlong-db.html
     *
     *  -------------NE
     * |              |
     * |        radius|
     * |       o------|
     * |              |
     * |              |
     * SW-------------
     *
     * @param object $LatLng
     * @param int|float $radius (In meters)
     */
    public static function computeBounds($LatLng, $radius)
    {
        $latRadiansDistance = $radius / self::EARTH_RADIUS;
        $latDegreesDistance = rad2deg($latRadiansDistance);
        $lngDegreesDistance = rad2deg($latRadiansDistance / cos(deg2rad($LatLng->getLat())));
    
        // SW point
        $swLat = $LatLng->getLat() - $latDegreesDistance;
        $swLng = $LatLng->getLng() - $lngDegreesDistance;
        $sw = new LatLng($swLat, $swLng);
        
        // NE point
        $neLat = $LatLng->getLat() + $latDegreesDistance;
        $neLng = $LatLng->getLng() + $lngDegreesDistance;
        $ne = new LatLng($neLat, $neLng);
    
        return new LatLngBounds($sw, $ne);
    }
    
    public static function computeHeading($fromLatLng, $toLatLng)
    {
        $fromLat = deg2rad($fromLatLng->getLat());
        $toLat = deg2rad($toLatLng->getLat());
        $lng = deg2rad($toLatLng->getLng()) - deg2rad($fromLatLng->getLng());
        
        return self::wrapLongitude(rad2deg(atan2(sin($lng) * cos($toLat), cos($fromLat) 
            * sin($toLat) - sin($fromLat) * cos($toLat) * cos($lng))));
    }
    
    public static function computeOffset($fromLatLng, $distance, $heading) 
    {
        $distance /= self::EARTH_RADIUS;
        $heading = deg2rad($heading);
        $fromLat = deg2rad($fromLatLng->getLat());
        $cosDistance = cos($distance);
        $sinDistance = sin($distance);
        $sinFromLat = sin($fromLat);
        $cosFromLat = cos($fromLat);
        $sc = $cosDistance * $sinFromLat + $sinDistance * $cosFromLat * cos($heading);
        
        $lat = rad2deg(asin($sc));
        $lng = rad2deg(deg2rad($fromLatLng->getLng()) + atan2($sinDistance * $cosFromLat 
            * sin($heading), $cosDistance - $sinFromLat * $sc));
        
        return new LatLng($lat, $lng);
    }
    
    public static function interpolate($fromLatLng, $toLatLng, $fraction)
    {
        $radFromLat = deg2rad($fromLatLng->getLat());
        $radFromLng = deg2rad($fromLatLng->getLng());
        $radToLat = deg2rad($toLatLng->getLat());
        $radToLng = deg2rad($toLatLng->getLng());
        $cosFromLat = cos($radFromLat);
        $cosToLat = cos($radToLat);
        $radDist = self::_computeDistanceInRadiansBetween($fromLatLng, $toLatLng);
        $sinRadDist = sin($radDist);
        
        if ($sinRadDist < 1.0E-6)
        {
            return new LatLng($fromLatLng->getLat(), $fromLatLng->getLng());
        }
        
        $a = sin((1 - $fraction) * $radDist) / $sinRadDist;
        $b = sin($fraction * $radDist) / $sinRadDist;
        $c = $a * $cosFromLat * cos($radFromLng) + $b * $cosToLat * cos($radToLng);
        $d = $a * $cosFromLat * sin($radFromLng) + $b * $cosToLat * sin($radToLng);
        
        $lat = rad2deg(atan2($a * sin($radFromLat) + $b * sin($radToLat), sqrt(pow($c,2) + pow($d,2))));
        $lng = rad2deg(atan2($d, $c));
        
        return new LatLng($lat, $lng);
    }

    public static function computeDistanceBetween($LatLng1, $LatLng2)
    {
        return self::_computeDistanceInRadiansBetween($LatLng1, $LatLng2) * self::EARTH_RADIUS;
    }
    
    public static function computeLength($LatLngsArray) 
    {
        $length = 0;
        
        for ($i = 0, $l = count($LatLngsArray) - 1; $i < $l; ++$i) 
        {
            $length += self::computeDistanceBetween($LatLngsArray[$i], $LatLngsArray[$i + 1]);
        }    
        
        return $length;
    }
    
    public static function computeArea($LatLngsArray)
    {
        return abs(self::computeSignedArea($LatLngsArray, false));
    }
    
    public static function computeSignedArea($LatLngsArray, $signed = true)
    {
        if (empty($LatLngsArray) || count($LatLngsArray) < 3) return 0;
        
        $e = 0;
        $r2 = pow(self::EARTH_RADIUS, 2);
        
        for ($i = 1, $l = count($LatLngsArray) - 1; $i < $l; ++$i) 
        {
            $e += self::_computeSphericalExcess($LatLngsArray[0], $LatLngsArray[$i], $LatLngsArray[$i + 1], $signed);
        }
           
        return $e * $r2;
    }
    
    // Clamp latitude
    public static function clampLatitude($lat)
    {
        return min(max($lat, -90), 90); 
    }
    
    // Wrap longitude
    public static function wrapLongitude($lng)
    {
        return fmod((fmod(($lng - -180), 360) + 360), 360) + -180;
    }
    
    /**
     * Computes the great circle distance (in radians) between two points.
     * Uses the Haversine formula.
     */
    protected static function _computeDistanceInRadiansBetween($LatLng1, $LatLng2)
    {
        $p1RadLat = deg2rad($LatLng1->getLat());
        $p1RadLng = deg2rad($LatLng1->getLng());
        $p2RadLat = deg2rad($LatLng2->getLat());
        $p2RadLng = deg2rad($LatLng2->getLng());
        return 2 * asin(sqrt(pow(sin(($p1RadLat - $p2RadLat) / 2), 2) + cos($p1RadLat) 
            * cos($p2RadLat) * pow(sin(($p1RadLng - $p2RadLng) / 2), 2)));
    }
    
    /**
     * Computes the spherical excess.
     * Uses L'Huilier's Theorem.
     */
    protected static function _computeSphericalExcess($LatLng1, $LatLng2, $LatLng3, $signed)
    {
        $latLngsArray = array($LatLng1, $LatLng2, $LatLng3, $LatLng1);
        $distances = array();
        $sumOfDistances = 0;
        
        for ($i = 0; $i < 3; ++$i) 
        {
            $distances[$i] = self::_computeDistanceInRadiansBetween($latLngsArray[$i], $latLngsArray[$i + 1]);
            $sumOfDistances += $distances[$i];
        }
            
        $semiPerimeter = $sumOfDistances / 2;
        $tan = tan($semiPerimeter / 2);
        
        for ($i = 0; $i < 3; ++$i) 
        { 
            $tan *= tan(($semiPerimeter - $distances[$i]) / 2);
        }
            
        $sphericalExcess = 4 * atan(sqrt(abs($tan)));
        
        if (!$signed) 
        {
            return $sphericalExcess;
        }
        
        // Negative or positive sign?
        array_pop($latLngsArray);
        
        $v = array();
        
        for ($i = 0; $i < 3; ++$i) 
        { 
            $LatLng = $latLngsArray[$i];
            $lat = deg2rad($LatLng->getLat());
            $lng = deg2rad($LatLng->getLng());
            
            $v[$i] = array();
            $v[$i][0] = cos($lat) * cos($lng);
            $v[$i][1] = cos($lat) * sin($lng);
            $v[$i][2] = sin($lat);
        }
        
        $sign = ($v[0][0] * $v[1][1] * $v[2][2] 
            + $v[1][0] * $v[2][1] * $v[0][2] 
            + $v[2][0] * $v[0][1] * $v[1][2] 
            - $v[0][0] * $v[2][1] * $v[1][2] 
            - $v[1][0] * $v[0][1] * $v[2][2] 
            - $v[2][0] * $v[1][1] * $v[0][2]) > 0 ? 1 : -1;
            
        return $sphericalExcess * $sign;
    }
}



class LatLng
{
    protected $_lat;
    protected $_lng;
    
    public function __construct($lat, $lng, $noWrap = false)
    {
        $lat = (float) $lat;
        $lng = (float) $lng;
        
        if (is_nan($lat) || is_nan($lng))
        {
            trigger_error('LatLng class -> Invalid float numbers: ('. $lat .', '. $lng .')', E_USER_ERROR);
        }
        
        if ($noWrap !== true) 
        {
            $lat = SphericalGeometry::clampLatitude($lat);
            $lng = SphericalGeometry::wrapLongitude($lng);
        }
        
        $this->_lat = $lat;
        $this->_lng = $lng;
    }
    
    public function getLat()
    {
        return $this->_lat;
    }
    
    public function getLng()
    {
        return $this->_lng;
    }
    
    public function equals($LatLng)
    {
        if (!is_object($LatLng) || !($LatLng instanceof self))
        {
            return false;
        }
        
        return abs($this->_lat - $LatLng->getLat()) <= SphericalGeometry::EQUALS_MARGIN_ERROR 
            && abs($this->_lng - $LatLng->getLng()) <= SphericalGeometry::EQUALS_MARGIN_ERROR;             
    }
    
    public function toString()
    {
        return '('. $this->_lat .', '. $this->_lng .')';
    }
    
    public function toUrlValue($precision = 6)
    {
        $precision = (int) $precision;
        return round($this->_lat, $precision) .','. round($this->_lng, $precision);
    }
}



class LatLngBounds
{
    protected $_LatBounds;
    protected $_LngBounds;
    
    /**
     * $LatLngSw South West LatLng object
     * $LatLngNe North East LatLng object
     */
    public function __construct($LatLngSw = null, $LatLngNe = null) 
    {   
        if ((!is_null($LatLngSw) && !($LatLngSw instanceof LatLng))
            || (!is_null($LatLngNe) && !($LatLngNe instanceof LatLng)))
        {
            trigger_error('LatLngBounds class -> Invalid LatLng object.', E_USER_ERROR);
        }
        
        if ($LatLngSw && !$LatLngNe) 
        {
            $LatLngNe = $LatLngSw;
        }
        
        if ($LatLngSw)
        {
            $sw = SphericalGeometry::clampLatitude($LatLngSw->getLat());
            $ne = SphericalGeometry::clampLatitude($LatLngNe->getLat());
            $this->_LatBounds = new LatBounds($sw, $ne);
            
            $sw = $LatLngSw->getLng();
            $ne = $LatLngNe->getLng();
            
            if ($ne - $sw >= 360) 
            {
                $this->_LngBounds = new LngBounds(-180, 180);
            }
            else 
            {
                $sw = SphericalGeometry::wrapLongitude($LatLngSw->getLng());
                $ne = SphericalGeometry::wrapLongitude($LatLngNe->getLng());
                $this->_LngBounds = new LngBounds($sw, $ne);
            }
        } 
        else 
        {
            $this->_LatBounds = new LatBounds(1, -1);
            $this->_LngBounds = new LngBounds(180, -180);
        }
    }
    
    public function getLatBounds()
    {
        return $this->_LatBounds;
    }
    
    public function getLngBounds()
    {
        return $this->_LngBounds;
    }
    
    public function getCenter()
    {
        return new LatLng($this->_LatBounds->getMidpoint(), $this->_LngBounds->getMidpoint());
    }
    
    public function isEmpty()
    {
        return $this->_LatBounds->isEmpty() || $this->_LngBounds->isEmpty();
    }
    
    public function getSouthWest()
    {
        return new LatLng($this->_LatBounds->getSw(), $this->_LngBounds->getSw(), true);
    }
    
    public function getNorthEast()
    {
        return new LatLng($this->_LatBounds->getNe(), $this->_LngBounds->getNe(), true);
    }
    
    public function toSpan()
    {
        $lat = $this->_LatBounds->isEmpty() ? 0 : $this->_LatBounds->getNe() - $this->_LatBounds->getSw();
        $lng = $this->_LngBounds->isEmpty() 
            ? 0 
            : ($this->_LngBounds->getSw() > $this->_LngBounds->getNe() 
                ? 360 - ($this->_LngBounds->getSw() - $this->_LngBounds->getNe())
                : $this->_LngBounds->getNe() - $this->_LngBounds->getSw());
        
        return new LatLng($lat, $lng, true);
    }
    
    public function toString()
    {
        return '('. $this->getSouthWest()->toString() .', '. $this->getNorthEast()->toString() .')';
    }
    
    public function toUrlValue($precision = 6)
    {
        return $this->getSouthWest()->toUrlValue($precision) .','. 
            $this->getNorthEast()->toUrlValue($precision);
    }
    
    public function equals($LatLngBounds)
    {
        return !$LatLngBounds 
            ? false 
            : $this->_LatBounds->equals($LatLngBounds->getLatBounds()) 
                && $this->_LngBounds->equals($LatLngBounds->getLngBounds());
    }
    
    public function intersects($LatLngBounds)
    {
        return $this->_LatBounds->intersects($LatLngBounds->getLatBounds()) 
            && $this->_LngBounds->intersects($LatLngBounds->getLngBounds());
    }
    
    public function union($LatLngBounds)
    {
        $this->extend($LatLngBounds->getSouthWest());
        $this->extend($LatLngBounds->getNorthEast());
        return $this;
    }
    
    public function contains($LatLng)
    {
        return $this->_LatBounds->contains($LatLng->getLat()) 
            && $this->_LngBounds->contains($LatLng->getLng());
    }
    
    public function extend($LatLng)
    {
        $this->_LatBounds->extend($LatLng->getLat());
        $this->_LngBounds->extend($LatLng->getLng());
        return $this;    
    }
}


// DO NOT USE THE CLASSES BELOW DIRECTLY


class LatBounds
{
    protected $_swLat;
    protected $_neLat;
    
    public function __construct($swLat, $neLat) 
    {
        $this->_swLat = $swLat;
        $this->_neLat = $neLat;
    }
    
    public function getSw()
    {
        return $this->_swLat;
    }
    
    public function getNe()
    {
        return $this->_neLat;
    }
    
    public function getMidpoint()
    {
        return ($this->_swLat + $this->_neLat) / 2;
    }
    
    public function isEmpty()
    {
        return $this->_swLat > $this->_neLat;
    }
    
    public function intersects($LatBounds)
    {
        return $this->_swLat <= $LatBounds->getSw() 
            ? $LatBounds->getSw() <= $this->_neLat && $LatBounds->getSw() <= $LatBounds->getNe() 
            : $this->_swLat <= $LatBounds->getNe() && $this->_swLat <= $this->_neLat;
    }
    
    public function equals($LatBounds)
    {
        return $this->isEmpty() 
            ? $LatBounds->isEmpty() 
            : abs($LatBounds->getSw() - $this->_swLat) 
                + abs($this->_neLat - $LatBounds->getNe()) 
                <= SphericalGeometry::EQUALS_MARGIN_ERROR;
    }
    
    public function contains($lat)
    {
        return $lat >= $this->_swLat && $lat <= $this->_neLat;
    }
    
    public function extend($lat)
    {
        if ($this->isEmpty()) 
        {
            $this->_neLat = $this->_swLat = $lat;
        }
        else if ($lat < $this->_swLat) 
        { 
            $this->_swLat = $lat;
        }
        else if ($lat > $this->_neLat) 
        {
            $this->_neLat = $lat;
        }
    }
}



class LngBounds
{
    protected $_swLng;
    protected $_neLng;
    
    public function __construct($swLng, $neLng) 
    {
        $swLng = $swLng == -180 && $neLng != 180 ? 180 : $swLng;
        $neLng = $neLng == -180 && $swLng != 180 ? 180 : $neLng;
        
        $this->_swLng = $swLng;
        $this->_neLng = $neLng;
    }
    
    public function getSw()
    {
        return $this->_swLng;
    }
    
    public function getNe()
    {
        return $this->_neLng;
    }
    
    public function getMidpoint()
    {
        $midPoint = ($this->_swLng + $this->_neLng) / 2;
        
        if ($this->_swLng > $this->_neLng) 
        {
            $midPoint = SphericalGeometry::wrapLongitude($midPoint + 180);
        }
        
        return $midPoint;
    }
    
    public function isEmpty()
    {
        return $this->_swLng - $this->_neLng == 360;
    }
    
    public function intersects($LngBounds)
    {
        if ($this->isEmpty() || $LngBounds->isEmpty()) 
        {
            return false;
        }
        else if ($this->_swLng > $this->_neLng) 
        {
            return $LngBounds->getSw() > $LngBounds->getNe() 
                || $LngBounds->getSw() <= $this->_neLng 
                || $LngBounds->getNe() >= $this->_swLng;
        }
        else if ($LngBounds->getSw() > $LngBounds->getNe()) 
        {
            return $LngBounds->getSw() <= $this->_neLng || $LngBounds->getNe() >= $this->_swLng;
        }
        else 
        {
            return $LngBounds->getSw() <= $this->_neLng && $LngBounds->getNe() >= $this->_swLng;
        }
    }
    
    public function equals($LngBounds)
    {
        return $this->isEmpty() 
            ? $LngBounds->isEmpty() 
            : fmod(abs($LngBounds->getSw() - $this->_swLng), 360) 
                + fmod(abs($LngBounds->getNe() - $this->_neLng), 360) 
                <= SphericalGeometry::EQUALS_MARGIN_ERROR;   
    }
    
    public function contains($lng)
    {
        $lng = $lng == -180 ? 180 : $lng;
        
        return $this->_swLng > $this->_neLng 
            ? ($lng >= $this->_swLng || $lng <= $this->_neLng) && !$this->isEmpty()
            : $lng >= $this->_swLng && $lng <= $this->_neLng;
    }
    
    public function extend($lng)
    {
        if ($this->contains($lng)) 
        {
            return;
        }
        
        if ($this->isEmpty())
        {
            $this->_swLng = $this->_neLng = $lng;
        }
        else 
        {
            $swLng = $this->_swLng - $lng;
            $swLng = $swLng >= 0 ? $swLng : $this->_swLng + 180 - ($lng - 180);
            $neLng = $lng - $this->_neLng;
            $neLng = $neLng >= 0 ? $neLng : $lng + 180 - ($this->_neLng - 180);
            
            if ($swLng < $neLng) 
            {
                $this->_swLng = $lng;
            }
            else 
            {
                $this->_neLng = $lng;
            }
        }
    }
}