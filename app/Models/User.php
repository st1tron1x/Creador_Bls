<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

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

    public function roleRank(): int
    {
        $ranks = config('role_ranks');

        return $this->getRoleNames()
            ->map(fn ($roleName) => $ranks[$roleName] ?? -1)
            ->max() ?? -1;
    }

    public function canApproveVoidRequestFrom(User $requester): bool
    {
        if ($this->id === $requester->id) {
            return false; // no puede aprobar su propia solicitud
        }

        if ($this->hasRole('auxiliar')) {
            return false; // un auxiliar nunca puede aprobar
        }

        return $this->roleRank() >= $requester->roleRank();
    }

    public function companies(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Company::class);
    }

    /**
     * La empresa con la que el usuario está trabajando en esta sesión.
     * Si solo pertenece a una, esa es siempre la activa (sin necesidad de elegir).
     */
    public function activeCompany(): ?Company
    {
        $companyId = session('active_company_id');

        if ($companyId) {
            $company = $this->companies()->where('companies.id', $companyId)->first();
            if ($company) {
                return $company;
            }
        }

        return $this->companies()->count() === 1 ? $this->companies()->first() : null;
    }
}
