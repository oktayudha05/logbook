<?php

namespace App\Models;

use Illuminate\Support\Uri;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipment extends Model
{
  /**
   * The attributes that are mass assignable.
   *
   * @var array<string, mixed>
   */
  protected $fillable = [
    'name',
    'description',
    'image',
    'price',
    'vendor_id',
  ];

  /**
   * Get the vendor that owns the equipment.
   */
  public function vendor(): BelongsTo
  {
    return $this->belongsTo(Vendor::class);
  }

  /**
   * Getter attribute for image.
   */
  public function image(): Attribute
  {
    $default = Uri::of('https://ui-avatars.com')
      ->withPath('api')
      ->withQuery([
        'name' => urlencode($this->name),
        'background' => '09090b',
        'color' => 'FFFFFF',
      ]);

    return Attribute::make(
      get: fn() => $this->attributes['image'] ?? (string) $default,
    );
  }

  /**
   * Get the label of the equipment.
   *
   * @return string
   */
  public function label(): Attribute
  {
    return Attribute::make(
      get: fn() => $this->name . ' - ' . $this->code,
    );
  }

  /**
   * Get the logbooks that belong to the equipment.
   *
   * @return Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function logbooks(): HasMany
  {
    return $this->hasMany(Logbook::class);
  }

  /**
   * Get the efficiency plannings that belong to the equipment.
   *
   * @return Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function plannings(): HasMany
  {
    return $this->hasMany(EfficiencyPlanning::class);
  }
}
