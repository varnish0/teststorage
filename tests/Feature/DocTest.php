<?php

use App\Models\Doc;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('public');
});

test('index lists docs with a link that opens in a new tab', function () {
    $doc = Doc::create(['name' => 'Apuntes', 'doc_path' => 'docs/apuntes.pdf']);

    $this->get(route('docs.index'))
        ->assertOk()
        ->assertSee('Apuntes')
        ->assertSee('storage/docs/apuntes.pdf')
        ->assertSee('target="_blank"', false);
});

test('store saves the pdf in the docs folder and creates the record', function () {
    $response = $this->post(route('docs.store'), [
        'name' => 'Contrato',
        'doc' => UploadedFile::fake()->create('contrato.pdf', 100, 'application/pdf'),
    ]);

    $response->assertRedirect()->assertSessionHas('success');

    $doc = Doc::sole();
    expect($doc->name)->toBe('Contrato')
        ->and($doc->doc_path)->toStartWith('docs/');
    Storage::disk('public')->assertExists($doc->doc_path);
});

test('store rejects files that are not pdf', function () {
    $this->post(route('docs.store'), [
        'name' => 'Foto',
        'doc' => UploadedFile::fake()->image('foto.jpg'),
    ])->assertSessionHasErrors('doc');

    expect(Doc::count())->toBe(0);
});

test('store requires a name and rejects pdfs over 5MB', function () {
    $this->post(route('docs.store'), [
        'doc' => UploadedFile::fake()->create('grande.pdf', 6000, 'application/pdf'),
    ])->assertSessionHasErrors(['name', 'doc']);
});

test('update replaces the pdf and deletes the old file', function () {
    $oldPath = UploadedFile::fake()->create('viejo.pdf', 10, 'application/pdf')->store('docs', 'public');
    $doc = Doc::create(['name' => 'Informe', 'doc_path' => $oldPath]);

    $this->put(route('docs.update', $doc), [
        'doc' => UploadedFile::fake()->create('nuevo.pdf', 10, 'application/pdf'),
    ])->assertRedirect()->assertSessionHas('success');

    $doc->refresh();
    expect($doc->doc_path)->not->toBe($oldPath);
    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($doc->doc_path);
});

test('destroy deletes the record and the file', function () {
    $path = UploadedFile::fake()->create('borrar.pdf', 10, 'application/pdf')->store('docs', 'public');
    $doc = Doc::create(['name' => 'Borrar', 'doc_path' => $path]);

    $this->delete(route('docs.destroy', $doc))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Doc::count())->toBe(0);
    Storage::disk('public')->assertMissing($path);
});
