<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class TaskArchivesBuilder extends Builder
{
    /**
     * Search by title
     */
    public function searchFilter($search): self
    {
        return $this->when($search, fn($q) => $q->whereAny(['task_archives.title', 'task_archives.description'], 'LIKE', "%$search%"));
    }

    public function isCompletedFilter($isCompleted): self
    {
        $isCompletedArray = array_filter(explode(',', $isCompleted), fn($v) => $v !== '');

        $isCompletedArray = array_map(fn($v) => filter_var($v, FILTER_VALIDATE_BOOLEAN), $isCompletedArray);
        return $this->when(count($isCompletedArray), fn($q) => $q->whereIn('task_archives.is_completed', $isCompletedArray));
    }
}
