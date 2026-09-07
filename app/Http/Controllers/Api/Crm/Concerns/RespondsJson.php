<?php

namespace App\Http\Controllers\Api\Crm\Concerns;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

trait RespondsJson
{
    protected function ok(mixed $data = null, string $message = 'success', int $status = 200): JsonResponse
    {
        $payload = ['status' => 'success', 'message' => $message];

        if ($data !== null) {
            $payload['data'] = $data;
        }

        return response()->json($payload, $status);
    }

    protected function created(mixed $data = null, string $message = 'Created successfully'): JsonResponse
    {
        return $this->ok($data, $message, 201);
    }

    protected function paginated(LengthAwarePaginator $paginator, ?callable $map = null): JsonResponse
    {
        $items = $map ? $paginator->getCollection()->map($map)->values() : $paginator->items();

        return response()->json([
            'status' => 'success',
            'data' => $items,
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
        ]);
    }

    protected function enumOptions(array $labels): array
    {
        return collect($labels)
            ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();
    }

    protected function userSummary($user): ?array
    {
        if (! $user) {
            return null;
        }

        return [
            'id' => $user->id,
            'name' => $user->full_name,
            'phone' => $user->phone,
            'email' => $user->email,
            'role' => $user->role,
        ];
    }
}
