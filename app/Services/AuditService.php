<?php

namespace App\Services;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

class AuditService
{
    /**
     * Log a creation action.
     */
    public function logCreated(Model $model, ?string $reason = null): AuditLog
    {
        return AuditLog::log('created', $model, null, $model->toArray(), $reason);
    }

    /**
     * Log an update action.
     */
    public function logUpdated(Model $model, array $oldValues, ?string $reason = null): AuditLog
    {
        $newValues = $model->toArray();
        
        // Only log changed values
        $changes = array_diff_assoc($newValues, $oldValues);
        $originalChanges = array_intersect_key($oldValues, $changes);

        return AuditLog::log('updated', $model, $originalChanges, $changes, $reason);
    }

    /**
     * Log a deletion action.
     */
    public function logDeleted(Model $model, ?string $reason = null): AuditLog
    {
        return AuditLog::log('deleted', $model, $model->toArray(), null, $reason);
    }

    /**
     * Log an evaluation lock action.
     */
    public function logLocked(Model $model, ?string $reason = null): AuditLog
    {
        return AuditLog::log('locked', $model, ['is_locked' => false], ['is_locked' => true], $reason);
    }

    /**
     * Log an evaluation unlock action.
     */
    public function logUnlocked(Model $model, string $reason): AuditLog
    {
        return AuditLog::log('unlocked', $model, ['is_locked' => true], ['is_locked' => false], $reason);
    }

    /**
     * Log a permission change.
     */
    public function logPermissionChange(Model $role, string $action, array $permissions, ?string $reason = null): AuditLog
    {
        return AuditLog::log(
            $action === 'grant' ? 'permission_granted' : 'permission_revoked',
            $role,
            null,
            ['permissions' => $permissions],
            $reason
        );
    }

    /**
     * Log an impersonation action.
     */
    public function logImpersonation(Model $targetUser, ?string $reason = null): AuditLog
    {
        return AuditLog::log('impersonated', $targetUser, null, null, $reason);
    }
}
