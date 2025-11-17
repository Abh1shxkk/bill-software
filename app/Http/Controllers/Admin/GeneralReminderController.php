<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralReminder;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GeneralReminderController extends Controller
{
    use CrudNotificationTrait;

    public function index(Request $request)
    {
        $reminders = GeneralReminder::orderBy('id', 'desc')->paginate(10);
        
        // Handle AJAX requests for infinite scroll
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('admin.general-reminders.index', compact('reminders'))->render();
        }
        
        return view('admin.general-reminders.index', compact('reminders'));
    }

    public function create()
    {
        return view('admin.general-reminders.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string|max:255',
        ]);

        try {
            $generalReminder = GeneralReminder::create($validated);
            $this->notifyCreated($generalReminder->name ?? 'General Reminder');
            return redirect()->route('admin.general-reminders.index');
        } catch (\Exception $e) {
            $this->notifyError('Error creating reminder: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function edit(GeneralReminder $generalReminder)
    {
        return view('admin.general-reminders.edit', compact('generalReminder'));
    }

    public function update(Request $request, GeneralReminder $generalReminder)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'due_date' => 'nullable|date',
            'status' => 'nullable|string|max:255',
        ]);

        try {
            $generalReminder->update($validated);
            $this->notifyUpdated($generalReminder->name ?? 'General Reminder');
            return redirect()->route('admin.general-reminders.index');
        } catch (\Exception $e) {
            $this->notifyError('Error updating reminder: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy(GeneralReminder $generalReminder)
    {
        try {
            $generalReminderName = $generalReminder->name ?? 'General Reminder';
            $generalReminder->delete();
            $this->notifyDeleted($generalReminderName);
            return back();
        } catch (\Exception $e) {
            $this->notifyError('Error deleting reminder: ' . $e->getMessage());
            return back();
        }
    }

    public function multipleDelete(Request $request)
    {
        $request->merge([
            'general_reminder_ids' => $request->input('general_reminder_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'general_reminder_ids' => 'required|array|min:1',
            'general_reminder_ids.*' => 'required|integer|exists:general_reminders,id'
        ]);

        try {
            $ids = $request->general_reminder_ids;
            $reminders = GeneralReminder::whereIn('id', $ids)->get();

            if ($reminders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No general reminders found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            foreach ($reminders as $reminder) {
                $reminder->delete();
                $deletedCount++;
            }

            $message = $deletedCount === 1
                ? '1 general reminder deleted successfully.'
                : $deletedCount . ' general reminders deleted successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Multiple general reminders deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete general reminders. Please try again.'
            ], 500);
        }
    }
}
