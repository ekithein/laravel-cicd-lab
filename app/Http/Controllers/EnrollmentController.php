<?php

namespace App\Http\Controllers;

use App\Models\MasterClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class EnrollmentController extends Controller
{
    public function confirm(int $id): View
    {
        $user = $this->currentVisitor();

        $masterClass = MasterClass::with(['creativityType', 'master', 'enrollments'])
            ->findOrFail($id);

        return view('enrollments.confirm', compact('user', 'masterClass'));
    }

    public function store(int $id): RedirectResponse
    {
        $user = $this->currentVisitor();

        $masterClass = MasterClass::with(['creativityType', 'enrollments'])
            ->findOrFail($id);

        $alreadyEnrolled = $user->enrollments()
            ->where('master_class_id', $masterClass->id)
            ->exists();

        if ($alreadyEnrolled) {
            return redirect()
                ->route('creativity.show', $masterClass->creativity_type_id)
                ->with('error', 'Вы уже записаны на этот мастер-класс.');
        }

        $hasClassAtSameTime = $user->enrollments()
            ->whereHas('masterClass', function ($query) use ($masterClass) {
                $query->where('class_date', $masterClass->class_date)
                    ->where('start_time', $masterClass->start_time);
            })
            ->exists();

        if ($hasClassAtSameTime) {
            return redirect()
                ->route('creativity.show', $masterClass->creativity_type_id)
                ->with('error', 'Вы уже записаны на мастер-класс в это время.');
        }

        $freePlaces = $masterClass->group_size - $masterClass->enrollments->count();

        if ($freePlaces <= 0) {
            return redirect()
                ->route('creativity.show', $masterClass->creativity_type_id)
                ->with('error', 'Свободных мест больше нет.');
        }

        $user->enrollments()->create([
            'master_class_id' => $masterClass->id,
        ]);

        return redirect()
            ->route('creativity.show', $masterClass->creativity_type_id)
            ->with('success', 'Вы успешно записались на мастер-класс.');
    }

    public function cancel(int $id): RedirectResponse
    {
        $this->currentVisitor();

        $masterClass = MasterClass::findOrFail($id);

        return redirect()
            ->route('creativity.show', $masterClass->creativity_type_id)
            ->with('success', 'Запись отменена.');
    }

    private function currentVisitor(): User
    {
        $user = auth()->user();

        if ($user->role !== 'visitor') {
            abort(403, 'Запись доступна только пользователю.');
        }

        return $user;
    }
}
