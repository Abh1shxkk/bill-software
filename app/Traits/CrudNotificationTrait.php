<?php

namespace App\Traits;

trait CrudNotificationTrait
{
    /**
     * Set success notification for item creation
     *
     * @param string $itemName
     * @return void
     */
    protected function notifyCreated($itemName)
    {
        session()->flash('item_created', $itemName);
    }

    /**
     * Set success notification for item update
     *
     * @param string $itemName
     * @return void
     */
    protected function notifyUpdated($itemName)
    {
        session()->flash('item_updated', $itemName);
    }

    /**
     * Set success notification for item deletion
     *
     * @param string $itemName
     * @return void
     */
    protected function notifyDeleted($itemName)
    {
        session()->flash('item_deleted', $itemName);
    }

    /**
     * Set general success notification
     *
     * @param string $message
     * @return void
     */
    protected function notifySuccess($message)
    {
        session()->flash('crud_success', $message);
    }

    /**
     * Set error notification
     *
     * @param string $message
     * @return void
     */
    protected function notifyError($message)
    {
        session()->flash('crud_error', $message);
    }

    /**
     * Set warning notification
     *
     * @param string $message
     * @return void
     */
    protected function notifyWarning($message)
    {
        session()->flash('crud_warning', $message);
    }

    /**
     * Set info notification
     *
     * @param string $message
     * @return void
     */
    protected function notifyInfo($message)
    {
        session()->flash('crud_info', $message);
    }

    /**
     * Set multiple notifications at once
     *
     * @param array $notifications
     * @return void
     */
    protected function notifyMultiple(array $notifications)
    {
        foreach ($notifications as $type => $message) {
            session()->flash("crud_{$type}", $message);
        }
    }

    /**
     * Convenience method to set notification based on operation result
     *
     * @param bool $success
     * @param string $itemName
     * @param string $operation (created, updated, deleted)
     * @param string $errorMessage
     * @return void
     */
    protected function notifyOperation($success, $itemName, $operation, $errorMessage = null)
    {
        if ($success) {
            switch ($operation) {
                case 'created':
                    $this->notifyCreated($itemName);
                    break;
                case 'updated':
                    $this->notifyUpdated($itemName);
                    break;
                case 'deleted':
                    $this->notifyDeleted($itemName);
                    break;
                default:
                    $this->notifySuccess("{$itemName} has been successfully {$operation}!");
            }
        } else {
            $this->notifyError($errorMessage ?: "Failed to {$operation} {$itemName}. Please try again.");
        }
    }
}
