<?php

namespace App\Http\Controllers;

use App\Models\CreativityType;
use App\Models\MasterClass;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CabinetController extends Controller
{
    private const ALL_SLOTS = [
        '09:00:00' => '09:00 - 11:00',
        '11:00:00' => '11:00 - 13:00',
        '13:00:00' => '13:00 - 15:00',
        '15:00:00' => '15:00 - 17:00',
    ];

    public function index(): View
    {
        $user = $this->currentMaster();

        $masterClasses = $user->masterClasses()
            ->with(['creativityType', 'participants'])
            ->orderBy('class_date')
            ->orderBy('start_time')
            ->get();

        $allTypes = CreativityType::orderBy('name')->get();

        return view('cabinet', compact('user', 'masterClasses', 'allTypes'));
    }

    public function create(Request $request): View
    {
        $user = $this->currentMaster();

        $types = CreativityType::orderBy('name')->get();

        $selectedDate = $request->get('class_date');
        $busySlots = [];

        if ($selectedDate) {
            $busySlots = MasterClass::where('master_id', $user->id)
                ->where('class_date', $selectedDate)
                ->pluck('start_time')
                ->toArray();
        }

        $allSlots = self::ALL_SLOTS;

        return view('master_classes.create', compact('types', 'allSlots', 'selectedDate', 'busySlots'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $this->currentMaster();

        $validated = $request->validate([
            'creativity_type_id' => ['required', 'exists:creativity_types,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'class_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'in:09:00:00,11:00:00,13:00:00,15:00:00'],
            'group_size' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
        ], [
            'creativity_type_id.required' => 'Выберите вид творчества.',
            'creativity_type_id.exists' => 'Выбран неверный вид творчества.',
            'title.required' => 'Введите название мастер-класса.',
            'description.required' => 'Введите описание мастер-класса.',
            'class_date.required' => 'Выберите дату.',
            'class_date.date' => 'Введите корректную дату.',
            'class_date.after_or_equal' => 'Дата мастер-класса не может быть в прошлом.',
            'start_time.required' => 'Выберите время.',
            'start_time.in' => 'Выберите время только из доступных слотов.',
            'group_size.required' => 'Введите количество человек в группе.',
            'group_size.integer' => 'Количество человек должно быть целым числом.',
            'group_size.min' => 'Количество человек должно быть не меньше 1.',
            'price.required' => 'Введите стоимость.',
            'price.numeric' => 'Стоимость должна быть числом.',
            'price.min' => 'Стоимость не может быть отрицательной.',
            'price.max' => 'Стоимость мастер-класса слишком большая.',
            'description.max' => 'Описание не должно превышать 2000 символов.',
        ]);

        $slotIsBusy = MasterClass::where('master_id', $user->id)
            ->where('class_date', $validated['class_date'])
            ->where('start_time', $validated['start_time'])
            ->exists();

        if ($slotIsBusy) {
            return back()
                ->withErrors(['start_time' => 'Это время на выбранную дату уже занято.'])
                ->withInput();
        }

        MasterClass::create([
            'creativity_type_id' => $validated['creativity_type_id'],
            'master_id' => $user->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'class_date' => $validated['class_date'],
            'start_time' => $validated['start_time'],
            'group_size' => $validated['group_size'],
            'price' => $validated['price'],
        ]);

        return redirect()
            ->route('cabinet')
            ->with('success', 'Мастер-класс успешно добавлен.');
    }

    public function edit(int $id): View
    {
        $user = $this->currentMaster();

        $masterClass = MasterClass::findOrFail($id);

        if ($masterClass->master_id !== $user->id) {
            abort(403, 'Вы не можете редактировать этот мастер-класс.');
        }

        return view('master_classes.edit', compact('masterClass'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $user = $this->currentMaster();

        $masterClass = MasterClass::findOrFail($id);

        if ($masterClass->master_id !== $user->id) {
            abort(403, 'Вы не можете редактировать этот мастер-класс.');
        }

        $validated = $request->validate([
            'description' => ['required', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999'],
        ], [
            'description.required' => 'Введите описание.',
            'price.required' => 'Введите стоимость.',
            'price.numeric' => 'Стоимость должна быть числом.',
            'price.min' => 'Стоимость не может быть отрицательной.',
            'price.max' => 'Стоимость мастер-класса слишком большая.',
            'description.max' => 'Описание не должно превышать 2000 символов.',
        ]);

        $masterClass->update($validated);

        return redirect()
            ->route('cabinet')
            ->with('success', 'Мастер-класс обновлен.');
    }

    private function currentMaster(): User
    {
        $user = auth()->user();

        if ($user->role !== 'master') {
            abort(403, 'Доступ разрешен только ведущему.');
        }

        return $user;
    }
}
