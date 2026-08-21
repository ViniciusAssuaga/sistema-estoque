<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserPermissionTest extends TestCase
{
    public function test_default_user_type_is_normal(): void
    {
        $user = User::factory()->make();

        $this->assertSame(0, $user->tipo);
        $this->assertFalse($user->canCreateRecords());
        $this->assertFalse($user->canEditRecords());
        $this->assertFalse($user->canDeleteRecords());
    }

    public function test_admin_can_create_and_edit_but_not_delete(): void
    {
        $user = User::factory()->make(['tipo' => 1]);

        $this->assertTrue($user->canCreateRecords());
        $this->assertTrue($user->canEditRecords());
        $this->assertFalse($user->canDeleteRecords());
    }

    public function test_super_admin_can_create_edit_and_delete(): void
    {
        $user = User::factory()->make(['tipo' => 2]);

        $this->assertTrue($user->canCreateRecords());
        $this->assertTrue($user->canEditRecords());
        $this->assertTrue($user->canDeleteRecords());
    }
}
