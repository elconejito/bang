<?php

namespace Tests\Feature\API;

use App\Enums\PictureProcessingStatus;
use App\Jobs\ProcessPictureDerivatives;
use App\Models\Firearm;
use App\Models\Picture;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use PDOException;
use Tests\TestCase;

class PictureTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->actingAs($this->user, 'api');
        config()->set('filesystems.disks.pictures.key', 'test-key');
        config()->set('filesystems.disks.pictures.secret', 'test-secret');
        config()->set('filesystems.disks.pictures.region', 'us-east-1');
        config()->set('filesystems.disks.pictures.bucket', 'test-bucket');
    }

    public function test_picture_reads_return_placeholders_when_aws_is_not_configured(): void
    {
        config()->set('filesystems.disks.pictures.key', null);
        $picture = Picture::factory()->create(['user_id' => $this->user->id]);

        $this->getJson('/pictures')
            ->assertOk()
            ->assertJsonPath('data.0.id', $picture->id)
            ->assertJsonPath('data.0.thumbnail_url', null)
            ->assertJsonPath('data.0.card_url', null)
            ->assertJsonPath('data.0.large_url', null);
    }

    public function test_upload_returns_a_specific_error_when_aws_is_not_configured(): void
    {
        config()->set('filesystems.disks.pictures.key', null);

        $this->postJson('/pictures', [
            'image' => UploadedFile::fake()->image('firearm.jpg'),
        ])
            ->assertServiceUnavailable()
            ->assertJsonPath('message', 'AWS photo storage is not configured. Photo uploads are unavailable.');
    }

    public function test_permanent_delete_returns_a_specific_error_when_aws_is_not_configured(): void
    {
        config()->set('filesystems.disks.pictures.key', null);
        $picture = Picture::factory()->create(['user_id' => $this->user->id]);

        $this->deleteJson("/pictures/{$picture->id}")
            ->assertServiceUnavailable()
            ->assertJsonPath('message', 'AWS photo storage is not configured. Permanent photo deletion is unavailable.');

        $this->assertModelExists($picture);
    }

    public function test_user_can_upload_a_private_picture_for_processing(): void
    {
        Storage::fake('pictures');
        Queue::fake();

        $response = $this->postJson('/pictures', [
            'image' => UploadedFile::fake()->image('firearm.jpg', 1200, 800),
            'name' => 'Firearm side',
        ]);

        $response->assertCreated()->assertJsonPath('data.processing_status', 'pending');
        $picture = Picture::firstOrFail();
        $this->assertSame(PictureProcessingStatus::Pending, $picture->processing_status);
        Storage::disk('pictures')->assertExists($picture->stagingKey());
        Queue::assertPushed(ProcessPictureDerivatives::class);
    }

    public function test_upload_returns_a_specific_error_and_removes_staging_when_schema_is_out_of_date(): void
    {
        Storage::fake('pictures');
        $databaseException = new PDOException('Undefined column');
        $databaseException->errorInfo = ['42703', null, 'column disk does not exist'];
        DB::shouldReceive('transaction')
            ->once()
            ->andThrow(new QueryException('pgsql', 'insert into pictures', [], $databaseException));

        $this->postJson('/pictures', [
            'image' => UploadedFile::fake()->image('firearm.jpg'),
        ])
            ->assertInternalServerError()
            ->assertJsonPath(
                'message',
                'Photo uploads are unavailable because the application database is out of date.'
            );

        $this->assertSame([], Storage::disk('pictures')->allFiles());
    }

    public function test_first_attachment_is_primary_and_multi_photo_primary_cannot_be_detached(): void
    {
        $firearm = Firearm::factory()->create(['user_id' => $this->user->id]);
        $primary = Picture::factory()->create(['user_id' => $this->user->id]);
        $secondary = Picture::factory()->create(['user_id' => $this->user->id]);

        $this->postJson("/firearms/{$firearm->id}/pictures/{$primary->id}/attach")->assertOk();
        $this->postJson("/firearms/{$firearm->id}/pictures/{$secondary->id}/attach")->assertOk();

        $this->assertTrue((bool) $firearm->pictures()->findOrFail($primary->id)->pivot->is_primary);
        $this->deleteJson("/firearms/{$firearm->id}/pictures/{$primary->id}")->assertConflict();
    }

    public function test_primary_can_be_replaced_then_detached(): void
    {
        $firearm = Firearm::factory()->create(['user_id' => $this->user->id]);
        $first = Picture::factory()->create(['user_id' => $this->user->id]);
        $second = Picture::factory()->create(['user_id' => $this->user->id]);
        $this->postJson("/firearms/{$firearm->id}/pictures/{$first->id}/attach");
        $this->postJson("/firearms/{$firearm->id}/pictures/{$second->id}/attach");

        $this->patchJson("/firearms/{$firearm->id}/pictures/{$second->id}/primary")->assertNoContent();
        $this->deleteJson("/firearms/{$firearm->id}/pictures/{$first->id}")->assertNoContent();
        $this->assertTrue((bool) $firearm->pictures()->findOrFail($second->id)->pivot->is_primary);
    }

    public function test_reorder_requires_exact_attachment_set(): void
    {
        $firearm = Firearm::factory()->create(['user_id' => $this->user->id]);
        $picture = Picture::factory()->create(['user_id' => $this->user->id]);
        $this->postJson("/firearms/{$firearm->id}/pictures/{$picture->id}/attach");

        $this->patchJson("/firearms/{$firearm->id}/pictures/reorder", ['ids' => []])->assertUnprocessable();
    }

    public function test_user_cannot_attach_another_users_picture(): void
    {
        $firearm = Firearm::factory()->create(['user_id' => $this->user->id]);
        $picture = Picture::factory()->create();

        $this->postJson("/firearms/{$firearm->id}/pictures/{$picture->id}/attach")->assertNotFound();
    }

    public function test_library_delete_removes_database_row_and_private_objects(): void
    {
        Storage::fake('pictures');
        $picture = Picture::factory()->create(['user_id' => $this->user->id]);
        foreach (array_keys(Picture::VARIANTS) as $variant) {
            Storage::disk('pictures')->put($picture->variantKey($variant), 'image');
        }

        $this->deleteJson("/pictures/{$picture->id}")->assertNoContent();

        $this->assertModelMissing($picture);
        foreach (array_keys(Picture::VARIANTS) as $variant) {
            Storage::disk('pictures')->assertMissing($picture->variantKey($variant));
        }
    }

    public function test_library_delete_is_blocked_while_picture_is_attached(): void
    {
        $firearm = Firearm::factory()->create(['user_id' => $this->user->id]);
        $picture = Picture::factory()->create(['user_id' => $this->user->id]);
        $this->postJson("/firearms/{$firearm->id}/pictures/{$picture->id}/attach");

        $this->deleteJson("/pictures/{$picture->id}")
            ->assertConflict()
            ->assertJsonPath('message', 'Detach this picture from every item before deleting it.');
        $this->assertModelExists($picture);
    }

    public function test_processing_job_creates_aspect_preserving_variants_and_removes_staging(): void
    {
        Storage::fake('pictures');
        $picture = Picture::factory()->create([
            'user_id' => $this->user->id,
            'processing_status' => PictureProcessingStatus::Pending,
            'processed_at' => null,
        ]);
        $source = UploadedFile::fake()->image('portrait.jpg', 600, 1200);
        Storage::disk('pictures')->put($picture->stagingKey(), $source->getContent());

        (new ProcessPictureDerivatives($picture->id, $picture->processing_version))->handle();

        $picture->refresh();
        $this->assertSame(PictureProcessingStatus::Ready, $picture->processing_status);
        Storage::disk('pictures')->assertMissing($picture->stagingKey());
        $manager = new ImageManager(new Driver);
        foreach (Picture::VARIANTS as $variant => $maximumEdge) {
            Storage::disk('pictures')->assertExists($picture->variantKey($variant));
            $image = $manager->read(Storage::disk('pictures')->get($picture->variantKey($variant)));
            $this->assertLessThanOrEqual($maximumEdge, max($image->width(), $image->height()));
            $this->assertSame(0.5, $image->width() / $image->height());
        }
    }
}
