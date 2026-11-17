<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Tests\TestCase;

class AuthenticatedSessionControllerTest extends TestCase
{
    public function test_guest_user_can_view_login_form(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
    }

    public function test_authenticated_user_visiting_login_is_redirected_to_admin_ktm(): void
    {
        $this->actingAs(User::factory()->make());

        $response = $this->get(route('login'));

        $response->assertRedirect(route('admin.ktm.index'));
    }
}
