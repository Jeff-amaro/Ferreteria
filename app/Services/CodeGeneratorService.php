<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CodeGeneratorService
{
    public function next(string $prefix, string $modelClass, string $column = 'code', int $padding = 5): string
    {
        /** @var Model $model */
        $model = new $modelClass;

        return DB::transaction(function () use ($prefix, $model, $column, $padding) {
            $last = $model->newQuery()
                ->when(method_exists($model, 'trashed'), fn ($q) => $q->withTrashed())
                ->where($column, 'like', $prefix.'-%')
                ->lockForUpdate()
                ->orderByDesc('id')
                ->value($column);

            $number = 1;

            if ($last && preg_match('/^'.preg_quote($prefix, '/').'-(\d+)$/', $last, $matches)) {
                $number = (int) $matches[1] + 1;
            }

            return $prefix.'-'.str_pad((string) $number, $padding, '0', STR_PAD_LEFT);
        });
    }
}
