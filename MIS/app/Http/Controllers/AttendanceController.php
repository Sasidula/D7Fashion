<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{

    public function index()
    {
        $employees = User::where('role', 'employee')->get();
        return view('pages.attendance', compact('employees'));
    }

    public function check(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'nullable|date',
        ]);

        $date = $validated['date'] ?? now()->toDateString();
        $userId = $validated['user_id'];

        $attendance = Attendance::where('user_id', $userId)->latest()->first();

        if ($attendance) {
            return redirect()->back()->with([
                'status' => 'found',
                'data' => $attendance,
            ]);
        }

        return redirect()->back()->with('status', 'not found');
    }


    public function mark(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'nullable|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
        ]);

        $userId = $validated['user_id'];
        $date = $validated['date'] ?? now()->toDateString();
        $checkIn = $validated['check_in'] ?? null;
        $checkOut = $validated['check_out'] ?? null;

        // If both check_in and check_out are provided: create a custom entry
        if ($checkIn && $checkOut) {
            Attendance::create([
                'user_id' => $userId,
                'date' => $date,
                'check_in' => $checkIn,
                'check_out' => $checkOut,
            ]);

            return back()->with('status', 'Custom attendance recorded.');
        }

        // Auto check-in/check-out flow for today
        $today = now()->toDateString();
        $attendance = Attendance::where('user_id', $userId)
            ->latest()
            ->first();

        if ($attendance) {
            // If already checked in but not out, mark check_out
            if ($attendance->check_in && !$attendance->check_out) {
                $attendance->check_out = now()->format('H:i');
                $attendance->save();

                return back()->with('status', 'Check-out recorded.');
            }

            if ($attendance->check_in && $attendance->check_out) {

                // If not checked in yet, create a new entry
                Attendance::create([
                    'user_id' => $userId,
                    'date' => $today,
                    'check_in' => now()->format('H:i'),
                ]);

                return back()->with('status', 'Check-in recorded.');
            }

            return back()->with('status', 'failed(not found).');

        }

        Attendance::create([
            'user_id' => $userId,
            'date' => $today,
            'check_in' => now()->format('H:i'),
        ]);

        return back()->with('status', 'for a new employee Check-in recorded.');
    }


//    public function mark(Request $request)
//    {
//        $validated = $request->validate([
//            'user_id' => 'required|exists:users,id',
//            'check_in' => 'nullable|date_format:H:i:s',
//            'check_out' => 'nullable|date_format:H:i:s',
//            'date' => 'nullable|date'
//        ]);
//
//        $user_id = $validated['user_id'];
//        $in = $validated['check_in'] ?? null;
//        $out = $validated['check_out'] ?? null;
//        $date = $validated['date'] ?? now()->toDateString();
//
//        $latest = Attendance::where('user_id', $user_id)->latest()->first();
//
//        $canNew = !$latest || ($latest->check_in !== null && $latest->check_out !== null);
//        $canCheckOut = $latest && $latest->check_in !== null && $latest->check_out === null;
//
//
//        if ($canNew && !$in || !$out || !$date) {
//            $attendance = Attendance::create([
//                'user_id' => $user_id,
//                'date' => carbon::now()->toDateString(),
//                'check_in' => carbon::now()->toTimeString(),
//            ]);
//
//            return redirect()->back()->with([
//                'saved' => 'saved successfully',
//                'data' => $attendance,
//            ]);
//        }
//
//        if ($canNew && $in && $out && $date) {
//            $attendance = Attendance::create([
//                'user_id' => $user_id,
//                'date' => $date,
//                'check_in' => $in,
//                'check_out' => $out
//            ]);
//
//            return redirect()->back()->with([
//                'saved' => 'saved successfully',
//                'data' => $attendance,
//            ]);
//        }
//
//        if ($canCheckOut) {
//            $latest->check_out = carbon::now()->toTimeString();
//            $latest->save();
//            return redirect()->back()->with([
//                'saved' => 'saved successfully',
//                'data' => $latest,
//            ]);
//        }
//
//        return redirect()->back()->with('status', 'cant create new record');
//    }











    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::all();
        return view('attendances.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        Attendance::create($request->only(['user_id', 'date', 'check_in', 'check_out']));
        return redirect()->route('attendances.index')->with('success', 'Attendance recorded.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        return view('attendances.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        $users = User::all();
        return view('attendances.edit', compact('attendance', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i|after:check_in',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $attendance->update($request->only(['user_id', 'date', 'check_in', 'check_out']));
        return redirect()->route('attendances.index')->with('success', 'Attendance updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();
        return redirect()->route('attendances.index')->with('success', 'Attendance deleted.');
    }
}
