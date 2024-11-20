<?php

namespace App\AuditResolvers;

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
        $dbPath = realpath("geoip/GeoLite2-Country.mmdb");
        try {
            $reader = new Reader($dbPath);
            $record = $reader->country(Request::ip());
            return $record->city->name . ", " . $record->country->name;
        } catch (AddressNotFoundException $e) {
            return "Address not found";
        } catch (InvalidDatabaseException $e) {
            return "Invalid database";
        }
    }
}
