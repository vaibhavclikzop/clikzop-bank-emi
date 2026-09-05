<?php

namespace App\Observers;

use App\Models\ActivityLog;

class ActivityLogObserver
{
    public function created($model)
    {
        $this->log($model, 'create');
    }

    public function updated($model)
    {
        $this->log($model, 'update', $model->getOriginal(), $model->getChanges());
    }

    public function deleted($model)
    {
        $this->log($model, 'delete', $model->getOriginal());
    }

    private function log($model, $action, $oldData = null, $newData = null)
    {
        // Avoid logging activity_logs itself (loop se bachne ke liye)
        if ($model->getTable() === 'activity_logs') {
            return;
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'tenant_id' => auth()->user()->tenant_id ?? null,
            'module' => $model->getTable(),
            'action' => $action,
            'record_id' => $model->id,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'ip' => request()->ip(),
        ]);
    }
}
