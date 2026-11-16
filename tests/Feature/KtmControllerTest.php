<?php

namespace Tests\Feature;

use Tests\TestCase;

class KtmControllerTest extends TestCase
{
    public function test_backend_validation_messages_mark_invalid_fields_red_and_valid_fields_green(): void
    {
        $response = $this->from(route('ktm.create'))
            ->followingRedirects()
            ->post(route('ktm.store'), [
                'nama' => 'Nama Valid',
            ]);

        $response->assertOk()
            ->assertSeeText('Nama Valid')
            ->assertSeeText('NIM harus diisi.')
            ->assertSeeText('Pas foto harus diunggah.')
            ->assertSee('aria-invalid="true"', false)
            ->assertSee('border-red-500', false)
            ->assertSee('border-green-500', false);
    }
}
