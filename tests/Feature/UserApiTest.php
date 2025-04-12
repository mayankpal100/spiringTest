<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{get, post, delete, patch};

uses(RefreshDatabase::class);

it('can fetch the list of users', function () {
    User::factory()->count(5)->create();
    get('/api/users')
        ->assertOk()
        ->assertJsonCount(5);
});

it('can search users by name', function () {
    User::factory()->create(['name' => 'Alice']);
    User::factory()->create(['name' => 'Bob']);

    get('/api/users?search=Ali')
        ->assertOk()
        ->assertJsonFragment(['name' => 'Alice'])
        ->assertJsonMissing(['name' => 'Bob']);
});

it('can sort users by points', function () {
    User::factory()->create(['name' => 'Low', 'points' => 10]);
    User::factory()->create(['name' => 'High', 'points' => 100]);

    get('/api/users?sortBy=points&sortOrder=desc')
        ->assertOk()
        ->assertSeeInOrder(['High', 'Low']);
});

it('can create a user successfully', function () {
    post('/api/users', [
        'name' => 'Jane',
        'age' => 25,
        'email' => 'jane@example.com',
        'address' => '123 Test Street',
    ])
        ->assertCreated()
        ->assertJsonFragment(['email' => 'jane@example.com']);
});

//it('shows validation errors when creating user with invalid data', function () {
//    post('/api/users', [
//        'name' => '',
//        'age' => 'not-an-integer',
//        'email' => 'invalid-email',
//        'address' => '',
//    ])
//        ->assertStatus(302)
//        ->assertJsonValidationErrors(['name', 'age', 'email', 'address']);
//});

it('can delete a user', function () {
    $user = User::factory()->create();
    delete("/api/users/{$user->id}")
        ->assertNoContent();
    expect(User::find($user->id))->toBeNull();
});

it('can increment user points', function () {
    $user = User::factory()->create(['points' => 3]);

    patch("/api/users/{$user->id}/increment")
        ->assertOk()
        ->assertJsonFragment(['points' => 4]);
});

it('can decrement user points', function () {
    $user = User::factory()->create(['points' => 3]);

    patch("/api/users/{$user->id}/decrement")
        ->assertOk()
        ->assertJsonFragment(['points' => 2]);
});

it('can fetch a single user', function () {
    $user = User::factory()->create();
    get("/api/users/{$user->id}")
        ->assertOk()
        ->assertJsonFragment(['id' => $user->id]);
});

it('returns users grouped by score with average age', function () {
    User::factory()->create(['name' => 'User1', 'age' => 20, 'email' => "mayanktest@gmail.com", 'points' => 10]);
    User::factory()->create(['name' => 'User2', 'age' => 30, 'email' => "testEmail@gmail.com", 'points' => 10]);

    get('/api/grouped-by-score')
        ->assertOk()
        ->assertJsonStructure(['10' => ['names', 'average_age']])
        ->assertJsonFragment(['average_age' => 25]);
});
