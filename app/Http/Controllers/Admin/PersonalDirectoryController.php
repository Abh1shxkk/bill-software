<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PersonalDirectory;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PersonalDirectoryController extends Controller
{
    use CrudNotificationTrait;

    public function index(Request $request)
    {
        $entries = PersonalDirectory::orderBy('id', 'desc')->paginate(10);
        
        // Handle AJAX requests for infinite scroll
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('admin.personal-directory.index', compact('entries'))->render();
        }
        
        return view('admin.personal-directory.index', compact('entries'));
    }

    public function create()
    {
        return view('admin.personal-directory.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'alt_code' => 'nullable|string|max:255',
            'address_office' => 'nullable|string',
            'address_residence' => 'nullable|string',
            'tel_office' => 'nullable|string|max:255',
            'tel_residence' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'status' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'anniversary' => 'nullable|date',
            'spouse' => 'nullable|string|max:255',
            'spouse_dob' => 'nullable|date',
            'child_1' => 'nullable|string|max:255',
            'child_1_dob' => 'nullable|date',
            'child_2' => 'nullable|string|max:255',
            'child_2_dob' => 'nullable|date',
        ]);

        try {
            $personalDirectory = PersonalDirectory::create($validated);
            $this->notifyCreated($personalDirectory->name ?? 'Personal Directory Entry');
            return redirect()->route('admin.personal-directory.index');
        } catch (\Exception $e) {
            $this->notifyError('Error creating entry: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(PersonalDirectory $personalDirectory)
    {
        if (request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json($personalDirectory);
        }
        return view('admin.personal-directory.show', compact('personalDirectory'));
    }

    public function edit(PersonalDirectory $personalDirectory)
    {
        return view('admin.personal-directory.edit', compact('personalDirectory'));
    }

    public function update(Request $request, PersonalDirectory $personalDirectory)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'alt_code' => 'nullable|string|max:255',
            'address_office' => 'nullable|string',
            'address_residence' => 'nullable|string',
            'tel_office' => 'nullable|string|max:255',
            'tel_residence' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:255',
            'fax' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'status' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'birthday' => 'nullable|date',
            'anniversary' => 'nullable|date',
            'spouse' => 'nullable|string|max:255',
            'spouse_dob' => 'nullable|date',
            'child_1' => 'nullable|string|max:255',
            'child_1_dob' => 'nullable|date',
            'child_2' => 'nullable|string|max:255',
            'child_2_dob' => 'nullable|date',
        ]);

        try {
            $personalDirectory->update($validated);
            $this->notifyUpdated($personalDirectory->name ?? 'Personal Directory Entry');
            return redirect()->route('admin.personal-directory.index');
        } catch (\Exception $e) {
            $this->notifyError('Error updating entry: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy(PersonalDirectory $personalDirectory)
    {
        try {
            $personalDirectoryName = $personalDirectory->name ?? 'Personal Directory Entry';
            $personalDirectory->delete();
            $this->notifyDeleted($personalDirectoryName);
            return back();
        } catch (\Exception $e) {
            $this->notifyError('Error deleting entry: ' . $e->getMessage());
            return back();
        }
    }

    public function multipleDelete(Request $request)
    {
        $request->merge([
            'personal_directory_ids' => $request->input('personal_directory_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'personal_directory_ids' => 'required|array|min:1',
            'personal_directory_ids.*' => 'required|integer|exists:personal_directories,id'
        ]);

        try {
            $ids = $request->personal_directory_ids;
            $entries = PersonalDirectory::whereIn('id', $ids)->get();

            if ($entries->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No personal directory entries found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            foreach ($entries as $entry) {
                $entry->delete();
                $deletedCount++;
            }

            $message = $deletedCount === 1
                ? '1 personal directory entry deleted successfully.'
                : $deletedCount . ' personal directory entries deleted successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Multiple personal directory entries deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete personal directory entries. Please try again.'
            ], 500);
        }
    }
}
