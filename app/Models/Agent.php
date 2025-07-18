<?php

namespace App\Models;

use App\Models\Scopes\AncientScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// #[ScopedBy([AncientScope::class])] to apply global scope which is useful for filtering
// #[ObservedBy([AgentObserver::class])] to make it observable
class Agent extends Model {
  use HasFactory;
  // use SoftDeletes; to make the deletion soft
  // use HasUuids, HasUlids; //to create stronger id, uuid gives 36 chars unique string while ulid gives a 26 char unique string
  //use Prunable, MassPrunable // to delete records on a schedule and conditional basis


  // protected $table = 'agents_table'; to change the default  table name detected by laravel
  // protected $primaryKey = 'agent_id'; to change the default primary key detected by laravel
  // public $incrementing = false; to disable auto increment
  // protected $keyType = 'string'; set it if your primary key is not int
  // public $timestamps = false; to disable updated_at and created_at
  // protected $dateFormat = 'U'; to change the storage format of the model's date columns

  // const CREATED_AT = 'created_on'; change column name from created_at to created_on
  // const UPDATED_AT = 'updated_on'; change column name from updated_at to updated_on
  // Model::withoutTimestamps(fn () => $post->increment('reads')); to not change timestamps
  // protected $connection = 'sqlite'; to change the default db 

  // Default attribute
  protected $attributes = [
    'active' => true,
  ];

  // Mass assignment
  protected $fillable = ['name', 'role', 'active'];

  //   protected $dispatchesEvents = [
  //     'saved' => UserSaved::class,
  //     'deleted' => UserDeleted::class,
  // ]; to handle events  

  // protected $guarded = []; will let all columns be fillable

  // will remove agents older created more than 30 days ago 
  protected function prunable(): Builder {
    return static::where('created_at', '<=', now()->subDays(30));
  }

  // Schedule::command('model:prune')->daily(); can be putted in routes/console.php to auto detect classes with prune trait daily
  // php artisan model:prune --pretend to test pruning without actual deletion

  // replicating is like cloning by a field 

  // another way to apply global scope 
  protected static function booted(): void {
    static::addGlobalScope(new AncientScope);
  }

  // a local scope with you can call it and chain on it  
  protected function scopeActive(Builder $query): void {
    $query->where('active', true);
  }

  // a dynamic scope which is like the local scope but can accept params
  protected function scopeOfRole(Builder $query, string $role): void {
    $query->where('role', $role);
  }

  //   if ($agent->is($anotherAgent)) {

  // } is and isnot are for comaparing if the checking if two are of the same model
}
