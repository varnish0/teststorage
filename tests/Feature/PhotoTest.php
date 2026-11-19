<?php

use App\Models\Photo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
});

test('index shows the photos and links to docs', function () {
    Photo::create(['name' => 'Playa', 'image_path' => 'photos/playa.jpg']);

    $this->get(route('photos.index'))
        ->assertOk()
        ->assertSee('Playa')
        ->assertSee('storage/photos/playa.jpg')
        ->assertSee(route('docs.index'));
});

test('store saves the image in the photos folder and creates the record', function () {
    $this->post(route('photos.store'), [
        'name' => 'Montaña',
        'image' => UploadedFile::fake()->image('montana.jpg'),
    ])->assertRedirect()->assertSessionHas('success');

    $photo = Photo::sole();
    expect($photo->image_path)->toStartWith('photos/');
    Storage::disk('public')->assertExists($photo->image_path);
});

test('store rejects files that are not images', function () {
    $this->post(route('photos.store'), [
        'name' => 'Pdf',
        'image' => UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf'),
    ])->assertSessionHasErrors('image');

    expect(Photo::count())->toBe(0);
});

test('update replaces the image and deletes the old file', function () {
    $oldPath = UploadedFile::fake()->image('vieja.jpg')->store('photos', 'public');
    $photo = Photo::create(['name' => 'Gato', 'image_path' => $oldPath]);

    $this->put(route('photos.update', $photo), [
        'image' => UploadedFile::fake()->image('nueva.png'),
    ])->assertRedirect()->assertSessionHas('success');

    $photo->refresh();
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($photo->image_path);
});

test('destroy deletes the record and the file', function () {
    $path = UploadedFile::fake()->image('borrar.jpg')->store('photos', 'public');
    $photo = Photo::create(['name' => 'Borrar', 'image_path' => $path]);

    $this->delete(route('photos.destroy', $photo))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Photo::count())->toBe(0);
    Storage::disk('public')->assertMissing($path);
});
