<?php

namespace Tests\Feature;

use App\Models\CreativityType;
use App\Models\MasterClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MasterClassTest extends TestCase
{
    use RefreshDatabase;

    private function createMaster(): User
    {
        return User::create([
            'fio' => 'Мастер Иванов',
            'email' => 'master@example.com',
            'password' => Hash::make('password'),
            'phone' => '+79991111111',
            'role' => 'master',
        ]);
    }

    private function createVisitor(): User
    {
        return User::create([
            'fio' => 'Пользователь Петров',
            'email' => 'visitor@example.com',
            'password' => Hash::make('password'),
            'phone' => '+79992222222',
            'role' => 'visitor',
        ]);
    }

    private function createType(): CreativityType
    {
        return CreativityType::create([
            'name' => 'Кулинария',
            'description' => 'Описание кулинарии',
        ]);
    }

    public function test_master_can_open_cabinet(): void
    {
        $master = $this->createMaster();

        $response = $this->actingAs($master)->get(route('cabinet'));

        $response->assertOk();
        $response->assertSee('Личный кабинет');
    }

    public function test_visitor_cannot_open_cabinet(): void
    {
        $visitor = $this->createVisitor();

        $response = $this->actingAs($visitor)->get(route('cabinet'));

        $response->assertForbidden();
    }

    public function test_master_can_create_master_class(): void
    {
        $master = $this->createMaster();
        $type = $this->createType();

        $response = $this->actingAs($master)->post(route('master-classes.store'), [
            'creativity_type_id' => $type->id,
            'title' => 'Готовим стейк',
            'description' => 'Описание мастер-класса',
            'class_date' => now()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'group_size' => 10,
            'price' => 1500,
        ]);

        $response->assertRedirect(route('cabinet'));

        $this->assertDatabaseHas('master_classes', [
            'creativity_type_id' => $type->id,
            'master_id' => $master->id,
            'title' => 'Готовим стейк',
            'start_time' => '09:00:00',
        ]);
    }

    public function test_visitor_cannot_create_master_class(): void
    {
        $visitor = $this->createVisitor();
        $type = $this->createType();

        $response = $this->actingAs($visitor)->post(route('master-classes.store'), [
            'creativity_type_id' => $type->id,
            'title' => 'Готовим стейк',
            'description' => 'Описание мастер-класса',
            'class_date' => now()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'group_size' => 10,
            'price' => 1500,
        ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('master_classes', [
            'title' => 'Готовим стейк',
        ]);
    }

    public function test_master_cannot_create_class_in_busy_slot(): void
    {
        $master = $this->createMaster();
        $type = $this->createType();
        $date = now()->addDay()->toDateString();

        MasterClass::create([
            'creativity_type_id' => $type->id,
            'master_id' => $master->id,
            'title' => 'Первый мастер-класс',
            'description' => 'Описание',
            'class_date' => $date,
            'start_time' => '09:00:00',
            'group_size' => 10,
            'price' => 1000,
        ]);

        $response = $this->actingAs($master)->from(route('master-classes.create'))->post(route('master-classes.store'), [
            'creativity_type_id' => $type->id,
            'title' => 'Второй мастер-класс',
            'description' => 'Описание',
            'class_date' => $date,
            'start_time' => '09:00:00',
            'group_size' => 10,
            'price' => 1200,
        ]);

        $response->assertRedirect(route('master-classes.create'));
        $response->assertSessionHasErrors('start_time');

        $this->assertDatabaseMissing('master_classes', [
            'title' => 'Второй мастер-класс',
        ]);
    }

    public function test_master_can_update_description_and_price(): void
    {
        $master = $this->createMaster();
        $type = $this->createType();

        $masterClass = MasterClass::create([
            'creativity_type_id' => $type->id,
            'master_id' => $master->id,
            'title' => 'Старое название',
            'description' => 'Старое описание',
            'class_date' => now()->addDay()->toDateString(),
            'start_time' => '11:00:00',
            'group_size' => 10,
            'price' => 1000,
        ]);

        $response = $this->actingAs($master)->post(route('master-classes.update', $masterClass->id), [
            'description' => 'Новое описание',
            'price' => 2000,
        ]);

        $response->assertRedirect(route('cabinet'));

        $this->assertDatabaseHas('master_classes', [
            'id' => $masterClass->id,
            'description' => 'Новое описание',
            'price' => 2000,
        ]);
    }
}
