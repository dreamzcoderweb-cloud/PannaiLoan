<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Trait to scope queries by the authenticated employee's branch_id and route_id
 * 
 * This trait should be used in models that need to be filtered by employee's
 * assigned branch and route (e.g., Customer, LoanAssign, etc.)
 */
trait FilterByEmployeeRoute
{
    /**
     * Scope to filter records by the authenticated employee's branch and route
     * 
     * @param Builder $query
     * @return Builder
     */
    

    /**
     * Scope to filter records by a specific branch and route
     * 
     * @param Builder $query
     * @param int $branch_id
     * @param int $route_id
     * @return Builder
     */
    public function scopeByBranchRoute(Builder $query, int $branch_id, int $route_id): Builder
    {
        return $query->where('branch_id', $branch_id)
                     ->where('route_id', $route_id);
    }

    /**
     * Scope to filter records by the authenticated employee's branch only
     * 
     * @param Builder $query
     * @return Builder
     */
    public function scopeByEmployeeBranch(Builder $query): Builder
    {
        $user = request()->user();

        if (!$user || !isset($user->branch_id)) {
            return $query;
        }

        return $query->where('branch_id', $user->branch_id);
    }

    /**
     * Scope to filter records by the authenticated employee's route only
     * 
     * @param Builder $query
     * @return Builder
     */
     public function scopeByEmployeeRoute(Builder $query): Builder
    {
    $user = request()->user();

    if (!$user || !isset($user->branch_id) || !isset($user->route_id)) {
        return $query;
    }

    $table = $query->getModel()->getTable();

    return $query->where('branch_id', $user->branch_id)
                 ->where(function ($q) use ($user, $table) {
                     
                     if ($table === 'routes') {
                        
                         $q->where('id', $user->route_id);
                     } else {
                        
                         $q->where('route_id', $user->route_id);
                     }

                 });
}

    /**
     * Check if the given resource belongs to the authenticated employee
     * 
     * @return bool
     */
    public function belongsToAuthenticatedEmployee(): bool
    {
        $user = request()->user();

        if (!$user || !isset($user->branch_id) || !isset($user->route_id)) {
            return false;
        }

        return $this->branch_id === $user->branch_id && 
               $this->route_id === $user->route_id;
    }
}
