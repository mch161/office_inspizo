<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Namu\WireChat\Traits\Chatable;

class Karyawan extends Authenticatable
{
    protected $table = 'karyawan';

    use Chatable;
    use \Illuminate\Notifications\Notifiable;

    /**
     * Returns the URL for the user's cover image (avatar).
     * Adjust the 'avatar_url' field to your database setup.
     */
    public function getCoverUrlAttribute(): ?string
    {
        return $this->avatar_url ?? null;
    }

    public function getIdAttribute(): ?int
    {
        return $this->kd_karyawan;
    }

    /**
     * Returns the URL for the user's profile page.
     * Adjust the 'profile' route as needed for your setup.
     */
    public function getProfileUrlAttribute(): ?string
    {
        return route('profile', ['id' => $this->id]);
    }

    /**
     * Returns the display name for the user.
     * Modify this to use your preferred name field.
     */
    public function getDisplayNameAttribute(): ?string
    {
        return $this->nama ?? 'user';
    }

    /**
     * Search for users when creating a new chat or adding members to a group.
     * Customize the search logic to limit results, such as restricting to friends or eligible users only.
     */
    public function searchChatables(string $query): ?Collection
    {
        $searchableFields = ['nama', 'email', 'username', 'telp'];
        return Karyawan::where(function ($queryBuilder) use ($searchableFields, $query) {
            foreach ($searchableFields as $field) {
                $queryBuilder->orWhere($field, 'LIKE', '%' . $query . '%');
            }
        })
            ->limit(20)
            ->get();
    }

    public function canCreateChats(): bool
    {
        return true; // Adjust this logic as needed, e.g., based on user roles or permissions
    }

    protected $primaryKey = 'kd_karyawan';

    protected $fillable = [
        'nama',
        'telp',
        'alamat',
        'nip',
        'nik',
        'email',
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}