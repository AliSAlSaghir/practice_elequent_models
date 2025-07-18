<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AgentController extends Controller {
  public function index(Request $request) {
    $query = Agent::query();

    if ($request->has('active')) {
      $query->where('active', $request->boolean('active'));
    }

    if ($request->has('role')) {
      $query->where('role', $request->input('role'));
    }

    $agents = $query->orderBy('name')->paginate(10);

    return response()->json($agents);
  }

  // View details of a single agent
  public function show(Agent $agent) {
    return response()->json($agent);
  }

  public function assignToOperation(Request $request, Agent $agent) {
    $request->validate([
      'operation_id' => 'required|uuid',
      'role' => 'nullable|string|max:255'
    ]);

    $agent->operations()->attach($request->operation_id, [
      'assigned_role' => $request->role,
      'assigned_at' => now(),
    ]);

    return response()->json(['message' => 'Agent assigned to operation successfully.']);
  }

  // Toggle agent active/inactive
  public function toggleStatus(Agent $agent) {
    $agent->active = !$agent->active;
    $agent->save();

    return response()->json(['message' => 'Agent status updated.', 'active' => $agent->active]);
  }

  public function generate(Request $request) {
    $agent = Agent::create([
      'id' => Str::uuid(),
      'name' => fake()->name(),
      'role' => fake()->jobTitle(),
      'active' => true,
    ]);

    return response()->json($agent, 201);
  }

  // Deactivate all agents by role
  public function deactivateByRole(Request $request) {
    $request->validate([
      'role' => 'required|string',
    ]);

    $affected = Agent::where('role', $request->role)->update(['active' => false]);

    return response()->json(['message' => "$affected agent(s) deactivated."]);
  }

  public function resetAllAgents() {
    DB::table('agents')->truncate();

    return response()->json(['message' => 'All agents have been deleted.']);
  }

  public function upsertAgents(Request $request) {
    $request->validate([
      'agents' => 'required|array',
      'agents.*.name' => 'required|string',
      'agents.*.role' => 'required|string',
      'agents.*.active' => 'required|boolean',
    ]);

    $agents = collect($request->agents)->map(function ($agent) {
      return [
        'id' => $agent['id'] ?? (string) Str::uuid(),
        'name' => $agent['name'],
        'role' => $agent['role'],
        'active' => $agent['active'],
        'created_at' => now(),
        'updated_at' => now(),
      ];
    })->toArray();

    Agent::upsert(
      $agents,
      ['name', 'role'],
      ['active', 'updated_at']
    );

    return response()->json(['message' => 'Agents upserted successfully.']);
  }

  //soft delete by role
  public function softDeleteByRole(Request $request) {
    $request->validate([
      'role' => 'required|string',
    ]);

    $deleted = Agent::where('role', $request->role)->delete();

    return response()->json(['message' => "$deleted agent(s) soft deleted."]);
  }

  //restore based on role
  public function restoreByRole(Request $request) {
    $request->validate([
      'role' => 'required|string',
    ]);

    $restored = Agent::withTrashed()
      ->where('role', $request->role)
      ->restore();

    return response()->json(['message' => "$restored agent(s) restored."]);
  }

  //force delete by id's
  public function forceDeleteByIds(Request $request) {
    $request->validate([
      'ids' => 'required|array',
    ]);

    $forceDeleted = Agent::withTrashed()
      ->whereIn('id', $request->ids)
      ->forceDelete();

    return response()->json(['message' => "$forceDeleted agent(s) permanently deleted."]);
  }

  //get only softdeleted agents
  public function trashed() {
    $agents = Agent::onlyTrashed()->get();

    return response()->json($agents);
  }

  //restore soft deleted agents
  public function restoreAll() {
    $restored = Agent::onlyTrashed()->restore();

    return response()->json(['message' => "$restored agent(s) restored."]);
  }

  //force delete all agents
  public function nukeAll() {
    Agent::withTrashed()->forceDelete();

    return response()->json(['message' => 'All agents permanently deleted.']);
  }

  // you may use savequietly, deleteQuietly, updateQuietly, getQuietly to dont let events take action 
}
