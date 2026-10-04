<?php

namespace App\Livewire\Admin;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class Users extends Component
{
    use WithPagination;

    public string $q = '';

    public string $role = '';

    public function updating(string $name): void
    {
        $this->resetPage();
    }

    public function setRole(int $id, string $role): void
    {
        if ($id === (int) auth()->id()) {
            session()->flash('err', 'You cannot change your own role.');

            return;
        }
        if (! in_array($role, array_map(fn ($r) => $r->value, UserRole::cases()), true)) {
            return;
        }
        User::findOrFail($id)->update(['role' => $role]);
        session()->flash('ok', 'Role updated.');
    }

    public function toggleSuspend(int $id): void
    {
        if ($id === (int) auth()->id()) {
            session()->flash('err', 'You cannot suspend yourself.');

            return;
        }
        $user = User::findOrFail($id);
        $user->update(['is_suspended' => ! $user->is_suspended]);
    }

    public function toggleVerified(int $id): void
    {
        $user = User::findOrFail($id);
        $user->update(['email_verified_at' => $user->email_verified_at ? null : now()]);
    }

    public function render(): View
    {
        $users = User::withCount(['orders', 'gangMemberships'])
            ->when($this->q !== '', fn ($q) => $q->where(fn ($w) => $w
                ->where('name', 'like', '%'.$this->q.'%')
                ->orWhere('email', 'like', '%'.$this->q.'%')))
            ->when($this->role !== '', fn ($q) => $q->where('role', $this->role))
            ->latest()
            ->paginate(15);

        return view('livewire.admin.users', [
            'users' => $users,
            'roles' => UserRole::cases(),
        ])->title('Users — Admin');
    }
}
