<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentController;

Route::prefix('v0.1/agents')->controller(AgentController::class)->group(function () {
  Route::get('/', 'index');
  Route::get('{agent}', 'show');
  Route::patch('{agent}/toggle-status', 'toggleStatus');
  Route::post('{agent}/assign-operation', 'assignToOperation');
  Route::post('generate', 'generate');
  Route::patch('deactivate-by-role', 'deactivateByRole');
  Route::delete('reset', 'resetAllAgents');
  Route::post('upsert', 'upsertAgents');
  Route::delete('soft-delete-by-role', 'softDeleteByRole');
  Route::patch('restore-by-role', 'restoreByRole');
  Route::delete('force-delete-by-ids', 'forceDeleteByIds');
  Route::get('trashed', 'trashed');
  Route::patch('restore-all', 'restoreAll');
  Route::delete('nuke', 'nukeAll');
});
