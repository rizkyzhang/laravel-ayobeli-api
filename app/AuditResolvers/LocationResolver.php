<?php

namespace App\AuditResolvers;

use Exception;
use GeoIp2\Database\Reader;
use GeoIp2\Exception\AddressNotFoundException;
use Illuminate\Support\Facades\Request;
use MaxMind\Db\Reader\InvalidDatabaseException;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\Resolver;

require_once(realpath("geoip/geoip2.phar"));

/**
 * Class LocationResolver
 *  - This class is used to resolve the location of the user based on the IP address.
 * @package App\AuditResolvers
 */
class LocationResolver implements Resolver
{
    /**
     * Resolve the location of the user based on the IP address.
     *
     * @param Auditable $auditable The auditable instance.
     * @return string The resolved location in the format "City, Country" or an error message.
     */
    public static function resolve(Auditable $auditable): string
    {
        $dbPath = (string)realpath("geoip/GeoLite2-Country.mmdb");
        try {
            $reader = new Reader($dbPath);
            $record = $reader->country((string)Request::ip());
            $cityName = $record->city->name ?? 'Unknown City';
            $countryName = $record->country->name ?? 'Unknown Country';

            return $cityName . ", " . $countryName;
        } catch (AddressNotFoundException $e) {
            return "Address not found";
        } catch (InvalidDatabaseException $e) {
            return "Invalid database";
        }
    }
}
