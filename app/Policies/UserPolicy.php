<?php

namespace App\Policies;

use App\Models\User;
use App\Enums\RoleType;
use App\Traits\HasBulkDeletePolicy;

class UserPolicy
{
  use HasBulkDeletePolicy;

  /**
   * List of allowerd roles for the user.
   */
  public array $allowed = [
    RoleType::ADMIN,
    RoleType::MANAGER,
  ];

  /**
   * Determine whether the user can view any models.
   */
  public function viewAny(User $user): bool
  {
    return in_array($user->role, $this->allowed);
  }

  /**
   * Determine whether the user can view the model.
   */
  public function view(User $user, User $model): bool
  {
    return in_array($user->role, $this->allowed);
  }

  /**
   * Determine whether the user can create models.
   */
  public function create(User $user): bool
  {
    return $user->role === RoleType::ADMIN;
  }

  /**
   * Determine whether the user can update the model.
   */
  public function update(User $user, User $model): bool
  {
    return $user->role === RoleType::ADMIN;
  }

  /**
   * Determine whether the user can delete the model.
   */
  public function delete(User $user, User $model): bool
  {
    return $user->role === RoleType::ADMIN;
  }

  /**
   * Determine whether the user can restore the model.
   */
  public function restore(User $user, User $model): bool
  {
    return false;
  }

  /**
   * Determine whether the user can permanently delete the model.
   */
  public function forceDelete(User $user, User $model): bool
  {
    return false;
  }
}
