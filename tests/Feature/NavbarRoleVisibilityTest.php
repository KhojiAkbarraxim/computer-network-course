<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavbarRoleVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_navbar_shows_public_links_and_auth_actions(): void
    {
        $navbar = $this->mainNavbar($this->get(route('home'))->assertOk()->getContent());

        $this->assertStringContainsString('Bosh sahifa', $navbar);
        $this->assertStringContainsString('Kurs', $navbar);
        $this->assertStringContainsString('Namuna dars', $navbar);
        $this->assertStringContainsString('Nazorat', $navbar);
        $this->assertStringContainsString('href="'.route('quizzes.index').'"', $navbar);
        $this->assertStringContainsString('Loyiha haqida', $navbar);
        $this->assertStringContainsString('Kirish', $navbar);
        $this->assertStringContainsString("Ro'yxatdan o'tish", $navbar);

        $this->assertStringNotContainsString("O'quv panel", $navbar);
        $this->assertStringNotContainsString('Admin panel', $navbar);
        $this->assertStringNotContainsString('Profil', $navbar);
        $this->assertStringNotContainsString('Chiqish', $navbar);
    }

    public function test_normal_user_navbar_shows_learner_links_profile_and_logout(): void
    {
        $user = User::factory()->create();

        $navbar = $this->mainNavbar(
            $this->actingAs($user)->get(route('home'))->assertOk()->getContent()
        );

        $this->assertStringContainsString('Bosh sahifa', $navbar);
        $this->assertStringContainsString('Kurs', $navbar);
        $this->assertStringContainsString('Namuna dars', $navbar);
        $this->assertStringContainsString('Nazorat', $navbar);
        $this->assertStringContainsString('href="'.route('quizzes.index').'"', $navbar);
        $this->assertStringContainsString("O'quv panel", $navbar);
        $this->assertStringContainsString('Loyiha haqida', $navbar);
        $this->assertStringContainsString('Profil', $navbar);
        $this->assertStringContainsString('Chiqish', $navbar);

        $this->assertStringNotContainsString('Admin panel', $navbar);
    }

    public function test_admin_navbar_shows_only_admin_panel_profile_and_logout(): void
    {
        $admin = User::factory()->admin()->create();

        $navbar = $this->mainNavbar(
            $this->actingAs($admin)->get(route('home'))->assertOk()->getContent()
        );

        $this->assertStringContainsString('Admin panel', $navbar);
        $this->assertStringContainsString('href="'.route('admin.dashboard').'"', $navbar);
        $this->assertStringContainsString('Profil', $navbar);
        $this->assertStringContainsString('href="'.route('profile.edit').'"', $navbar);
        $this->assertStringContainsString('Chiqish', $navbar);
        $this->assertStringContainsString('action="'.route('logout').'"', $navbar);
        $this->assertStringContainsString('method="POST"', $navbar);

        $this->assertStringNotContainsString('Bosh sahifa', $navbar);
        $this->assertStringNotContainsString('Kurs', $navbar);
        $this->assertStringNotContainsString('Namuna dars', $navbar);
        $this->assertStringNotContainsString('Nazorat', $navbar);
        $this->assertStringNotContainsString("O'quv panel", $navbar);
        $this->assertStringNotContainsString('Loyiha haqida', $navbar);
    }

    private function mainNavbar(string $html): string
    {
        $this->assertSame(1, preg_match('/<nav\b.*?<\/nav>/s', $html, $matches));

        return html_entity_decode($matches[0], ENT_QUOTES | ENT_HTML5);
    }
}
