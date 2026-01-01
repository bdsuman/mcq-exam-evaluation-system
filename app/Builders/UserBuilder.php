<?php

namespace App\Builders;

use Illuminate\Database\Eloquent\Builder;

class UserBuilder extends Builder
{
    // You can add custom query methods here
    public function filterByStatus($status)
    {
        return $this->where('status', $status);
    }

    public function filterByRole($role)
    {
        return $this->where('role', $role);
    }
}
