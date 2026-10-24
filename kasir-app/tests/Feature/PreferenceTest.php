<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PreferenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_sees_indonesian_by_default(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('Masuk')
            ->assertSee('Belum punya akun?');
    }

    public function test_language_cookie_switches_locale_to_english(): void
    {
        $this->withCookie('locale', 'en')
            ->get('/login')
            ->assertOk()
            ->assertSee('Sign in to your account')
            ->assertSee('Email or username');
    }

    public function test_language_route_sets_cookie_and_redirects(): void
    {
        $this->withHeader('Referer', 'http://localhost:8000/kasir')
            ->get('/bahasa/en')
            ->assertRedirect('http://localhost:8000/kasir')
            ->assertCookie('locale', 'en');

        $this->withHeader('Referer', 'http://localhost:8000/kasir')
            ->get('/bahasa/id')
            ->assertRedirect('http://localhost:8000/kasir')
            ->assertCookie('locale', 'id');
    }

    public function test_invalid_locale_is_ignored(): void
    {
        $this->withCookie('locale', 'jp')
            ->get('/login')
            ->assertOk()
            ->assertSee('Masuk');

        $this->get('/bahasa/jp')
            ->assertNotFound();
    }

    public function test_theme_cookie_sets_dark_theme_on_html(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('data-theme="light"', false);

        $this->withCookie('theme', 'dark')
            ->get('/login')
            ->assertOk()
            ->assertSee('data-theme="dark"', false);
    }

    public function test_theme_route_sets_cookie_and_redirects(): void
    {
        $this->get('/tema/dark')
            ->assertRedirect('/')
            ->assertCookie('theme', 'dark');
    }

    public function test_invalid_theme_is_ignored(): void
    {
        $this->withCookie('theme', 'neon')
            ->get('/login')
            ->assertOk()
            ->assertSee('data-theme="light"', false);
    }

    public function test_sidebar_collapse_cookie_adds_body_class(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_KASIR,
        ]);

        $this->actingAs($user)
            ->get('/kasir')
            ->assertOk()
            ->assertDontSee('sidebar-collapsed', false);

        $this->actingAs($user)
            ->withCookie('sidebar', 'collapsed')
            ->get('/kasir')
            ->assertOk()
            ->assertSee('sidebar-collapsed', false);
    }

    public function test_sidebar_toggle_route_flips_state(): void
    {
        $user = User::factory()->create([
            'role' => User::ROLE_KASIR,
        ]);

        $this->actingAs($user)
            ->get('/sidebar/atur')
            ->assertRedirect()
            ->assertCookie('sidebar', 'collapsed');

        $this->actingAs($user)
            ->withCookie('sidebar', 'collapsed')
            ->get('/sidebar/atur')
            ->assertRedirect()
            ->assertCookie('sidebar', 'expanded');
    }
}
