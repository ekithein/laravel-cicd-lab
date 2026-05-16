<?php

namespace Tests\Feature;

use App\Models\CreativityType;
use App\Models\Enrollment;
use App\Models\MasterClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    private function createVisitor(string $email = 'visitor@example.com'): User
    {
        return User::create([
            'fio' => 'Пользователь Петров',
            'email' => $email,
            'password' => Hash::make('password'),
            'phone' => '+79992222222',
            'role' => 'visitor',
        ]);
    }

    private function createMaster(string $email = 'master@example.com'): User
    {
        return User::create([
            'fio' => 'Мастер Иванов',
            'email' => $email,
            'password' => Hash::make('password'),
            'phone' => '+79991111111',
            'role' => 'master',
        ]);
    }

    private function createType(): CreativityType
    {
        return CreativityType::create([
            'name' => 'Кулинария',
            'description' => 'Описание кулинарии',
        ]);
    }

    private function createMasterClass(?User $master = null, ?CreativityType $type = null, array $data = []): MasterClass
    {
        $master ??= $this->createMaster();
        $type ??= $this->createType();

        return MasterClass::create(array_merge([
            'creativity_type_id' => $type->id,
            'master_id' => $master->id,
            'title' => 'Готовим стейк',
            'description' => 'Описание мастер-класса',
            'class_date' => now()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'group_size' => 10,
            'price' => 1500,
        ], $data));
    }

    public function test_visitor_can_open_enrollment_confirmation_page(): void
    {
        $visitor = $this->createVisitor();
        $masterClass = $this->createMasterClass();

        $response = $this->actingAs($visitor)
            ->get(route('enrollments.confirm', $masterClass->id));

        $response->assertOk();
        $response->assertSee('Подтверждение записи');
        $response->assertSee($visitor->fio);
        $response->assertSee($masterClass->creativityType->name);
        $response->assertSee($masterClass->master->fio);
    }

    public function test_master_cannot_open_enrollment_confirmation_page(): void
    {
        $master = $this->createMaster();
        $masterClass = $this->createMasterClass($master);

        $response = $this->actingAs($master)
            ->get(route('enrollments.confirm', $masterClass->id));

        $response->assertForbidden();
    }

    public function test_visitor_can_enroll_to_master_class(): void
    {
        $visitor = $this->createVisitor();
        $masterClass = $this->createMasterClass();

        $response = $this->actingAs($visitor)
            ->post(route('enrollments.store', $masterClass->id));

        $response->assertRedirect(route('creativity.show', $masterClass->creativity_type_id));

        $this->assertDatabaseHas('enrollments', [
            'user_id' => $visitor->id,
            'master_class_id' => $masterClass->id,
        ]);
    }

    public function test_visitor_cannot_enroll_twice_to_same_master_class(): void
    {
        $visitor = $this->createVisitor();
        $masterClass = $this->createMasterClass();

        Enrollment::create([
            'user_id' => $visitor->id,
            'master_class_id' => $masterClass->id,
        ]);

        $response = $this->actingAs($visitor)
            ->post(route('enrollments.store', $masterClass->id));

        $response->assertRedirect(route('creativity.show', $masterClass->creativity_type_id));
        $response->assertSessionHas('error', 'Вы уже записаны на этот мастер-класс.');

        $this->assertEquals(1, Enrollment::where('user_id', $visitor->id)->count());
    }

    public function test_visitor_cannot_enroll_to_two_classes_at_same_time(): void
    {
        $visitor = $this->createVisitor();
        $type = $this->createType();

        $firstMaster = $this->createMaster('master1@example.com');
        $secondMaster = $this->createMaster('master2@example.com');

        $date = now()->addDay()->toDateString();

        $firstClass = $this->createMasterClass($firstMaster, $type, [
            'title' => 'Первый мастер-класс',
            'class_date' => $date,
            'start_time' => '11:00:00',
        ]);

        $secondClass = $this->createMasterClass($secondMaster, $type, [
            'title' => 'Второй мастер-класс',
            'class_date' => $date,
            'start_time' => '11:00:00',
        ]);

        Enrollment::create([
            'user_id' => $visitor->id,
            'master_class_id' => $firstClass->id,
        ]);

        $response = $this->actingAs($visitor)
            ->post(route('enrollments.store', $secondClass->id));

        $response->assertRedirect(route('creativity.show', $secondClass->creativity_type_id));
        $response->assertSessionHas('error', 'Вы уже записаны на мастер-класс в это время.');

        $this->assertDatabaseMissing('enrollments', [
            'user_id' => $visitor->id,
            'master_class_id' => $secondClass->id,
        ]);
    }

    public function test_visitor_cannot_enroll_if_no_free_places(): void
    {
        $firstVisitor = $this->createVisitor('first@example.com');
        $secondVisitor = $this->createVisitor('second@example.com');

        $masterClass = $this->createMasterClass(null, null, [
            'group_size' => 1,
        ]);

        Enrollment::create([
            'user_id' => $firstVisitor->id,
            'master_class_id' => $masterClass->id,
        ]);

        $response = $this->actingAs($secondVisitor)
            ->post(route('enrollments.store', $masterClass->id));

        $response->assertRedirect(route('creativity.show', $masterClass->creativity_type_id));
        $response->assertSessionHas('error', 'Свободных мест больше нет.');

        $this->assertDatabaseMissing('enrollments', [
            'user_id' => $secondVisitor->id,
            'master_class_id' => $masterClass->id,
        ]);
    }

    public function test_cancel_enrollment_returns_to_creativity_page(): void
    {
        $visitor = $this->createVisitor();
        $masterClass = $this->createMasterClass();

        $response = $this->actingAs($visitor)
            ->post(route('enrollments.cancel', $masterClass->id));

        $response->assertRedirect(route('creativity.show', $masterClass->creativity_type_id));
        $response->assertSessionHas('success', 'Запись отменена.');
    }
}
