<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TransportMaster;
use App\Traits\CrudNotificationTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TransportMasterController extends Controller
{
    use CrudNotificationTrait;

    public function index(Request $request)
    {
        $query = TransportMaster::query();
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $searchField = $request->search_field ?? 'all';
            
            if ($searchField === 'all') {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%")
                      ->orWhere('trans_mode', 'like', "%{$search}%")
                      ->orWhere('status', 'like', "%{$search}%");
                });
            } else {
                $query->where($searchField, 'like', "%{$search}%");
            }
        }
        
        $transports = $query->orderBy('id', 'desc')->paginate(10);
        
        // Handle AJAX requests for infinite scroll
        if ($request->ajax() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
            return view('admin.transport-master.index', compact('transports'))->render();
        }
        
        return view('admin.transport-master.index', compact('transports'));
    }

    public function create()
    {
        return view('admin.transport-master.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'alter_code' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:255',
            'gst_no' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'vehicle_no' => 'nullable|string|max:255',
            'trans_mode' => 'nullable|string|max:255',
        ]);

        try {
            $transportMaster = TransportMaster::create($validated);
            $this->notifyCreated($transportMaster->name ?? 'Transport Master');
            return redirect()->route('admin.transport-master.index');
        } catch (\Exception $e) {
            $this->notifyError('Error creating transport: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function show(TransportMaster $transportMaster)
    {
        if (request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            return response()->json($transportMaster);
        }
        return view('admin.transport-master.show', compact('transportMaster'));
    }

    public function edit(TransportMaster $transportMaster)
    {
        return view('admin.transport-master.edit', compact('transportMaster'));
    }

    public function update(Request $request, TransportMaster $transportMaster)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'alter_code' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'mobile' => 'nullable|string|max:255',
            'gst_no' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'vehicle_no' => 'nullable|string|max:255',
            'trans_mode' => 'nullable|string|max:255',
        ]);

        try {
            $transportMaster->update($validated);
            $this->notifyUpdated($transportMaster->name ?? 'Transport Master');
            return redirect()->route('admin.transport-master.index');
        } catch (\Exception $e) {
            $this->notifyError('Error updating transport: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function destroy(TransportMaster $transportMaster)
    {
        try {
            $transportMasterName = $transportMaster->name ?? 'Transport Master';
            $transportMaster->delete();
            $this->notifyDeleted($transportMasterName);
            return back();
        } catch (\Exception $e) {
            $this->notifyError('Error deleting transport: ' . $e->getMessage());
            return back();
        }
    }

    public function multipleDelete(Request $request)
    {
        $request->merge([
            'transport_master_ids' => $request->input('transport_master_ids', $request->input('item_ids', []))
        ]);

        $request->validate([
            'transport_master_ids' => 'required|array|min:1',
            'transport_master_ids.*' => 'required|integer|exists:transport_masters,id'
        ]);

        try {
            $ids = $request->transport_master_ids;
            $transports = TransportMaster::whereIn('id', $ids)->get();

            if ($transports->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No transport masters found to delete.'
                ], 404);
            }

            $deletedCount = 0;
            foreach ($transports as $transport) {
                $transport->delete();
                $deletedCount++;
            }

            $message = $deletedCount === 1
                ? '1 transport master deleted successfully.'
                : $deletedCount . ' transport masters deleted successfully.';

            return response()->json([
                'success' => true,
                'message' => $message,
                'deleted_count' => $deletedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Multiple transport masters deletion failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete transport masters. Please try again.'
            ], 500);
        }
    }
}
