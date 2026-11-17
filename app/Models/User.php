<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Every team account with a known role may sign in to the admin.
     * Without this, Filament refuses all accounts outside a local environment.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return array_key_exists($this->role ?? 'admin', self::ROLES);
    }

    /** Roles of the Coup de Pouce back-office (CDC Coup de Pouce §10). */
    public const ROLES = [
        'admin' => 'Administrateur technique',
        'administration' => 'Administration FEIRRY',
        'referent' => 'Référent technique',
        'communication' => 'Communication',
    ];

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role ?? 'admin', $roles, true);
    }

    /** May open Coup de Pouce requests and their private documents. */
    public function canSeeAssistanceRequests(): bool
    {
        return $this->hasRole('admin', 'administration', 'referent');
    }

    /** May see this particular request: referents only see what is assigned to them. */
    public function canSeeAssistanceRequest(AssistanceRequest $request): bool
    {
        if ($this->hasRole('admin', 'administration')) {
            return true;
        }

        return $this->hasRole('referent') && (int) $request->assigned_to === (int) $this->id;
    }
}
