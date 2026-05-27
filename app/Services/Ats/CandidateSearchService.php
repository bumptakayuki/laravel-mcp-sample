<?php

namespace App\Services\Ats;

use App\Models\Candidate;
use Illuminate\Support\Str;

/**
 * 候補者検索（MCP / 画面の共通ユースケース）。
 *
 * セキュリティ方針:
 * - AI に DB / Eloquent を直接触らせず、許可された検索条件のみ適用する。
 * - 返却は PII 最小限（email は返さない）・最大 10 件。
 * - 本デモでは更新系は実装しない。
 */
class CandidateSearchService
{
    private const MAX_RESULTS = 10;

    public function search(array $filters): array
    {
        $allowed = ['skill', 'status', 'position'];
        $filters = array_intersect_key($filters, array_flip($allowed));

        $query = Candidate::query();

        if (! empty($filters['skill']) && is_string($filters['skill'])) {
            $needle = Str::limit(trim($filters['skill']), 100, '');
            if ($needle !== '') {
                $escaped = addcslashes($needle, '%_\\');
                $query->where('skills', 'like', '%'.$escaped.'%');
            }
        }

        if (! empty($filters['status']) && is_string($filters['status'])) {
            $status = trim($filters['status']);
            if (in_array($status, Candidate::STATUSES, true)) {
                $query->where('status', $status);
            }
        }

        if (! empty($filters['position']) && is_string($filters['position'])) {
            $pos = Str::limit(trim($filters['position']), 100, '');
            if ($pos !== '') {
                $escaped = addcslashes($pos, '%_\\');
                $query->where('current_position', 'like', '%'.$escaped.'%');
            }
        }

        return $query
            ->orderBy('id')
            ->limit(self::MAX_RESULTS)
            ->get()
            ->map(fn (Candidate $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'current_position' => $c->current_position,
                'skills' => $c->skills ?? [],
                'status' => $c->status,
            ])
            ->all();
    }
}
