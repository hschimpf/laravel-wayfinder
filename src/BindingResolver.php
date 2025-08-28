<?php

namespace Laravel\Wayfinder;

use Illuminate\Database\Eloquent\Model;
use Throwable;

class BindingResolver
{
    protected static $booted = [];

    protected static $columns = [];

    public static function resolveTypeAndKey(string $routable, $key): array
    {
        $booted = self::$booted[$routable] ??= app($routable);

        $key ??= $booted->getRouteKeyName();

        if (! ($booted instanceof Model)) {
            return [null, $key];
        }

        try {
            self::$columns[$routable] ??= $booted->getConnection()->getSchemaBuilder()->getColumns($booted->getTable());

            return [
                collect(self::$columns[$routable])->first(
                    fn ($column) => $column['name'] === $key,
                )['type_name'] ?? null,
                $key,
            ];

        } catch (Throwable) {
            return [null, $key];
        }
    }
}
