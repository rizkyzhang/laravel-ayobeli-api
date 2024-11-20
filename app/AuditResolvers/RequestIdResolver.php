<?php

namespace App\AuditResolvers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\Resolver;

class RequestIdResolver implements Resolver
{
    public static function resolve(Auditable $auditable): string
    {
      return Request::header('X-Request-ID') ?? (string) Str::ulid();
    }
}
