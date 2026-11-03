<?php

namespace Tests\Feature;

use App\Models\Doctor;
use App\Models\DoctorDiagnosticOrder;
use App\Models\DoctorPrescription;
use App\Models\DoctorReferral;
use App\Models\DoctorStamp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class DoctorStampSelectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_keeps_multiple_named_private_stamps_in_settings(): void
    {
        Storage::fake('local');
        $doctor = Doctor::factory()->create();
        $other = DoctorStamp::factory()->create(['name' => 'ختم طبيب آخر']);
        $this->actingAs($doctor, 'doctor');
        foreach (['ختم العيادة', '<script>stamp()</script>'] as $name) {
            $this->postJson(route('doctor.account.stamp.store'), ['name' => $name, 'stamp' => UploadedFile::fake()->image('stamp.png', 180, 100)])
                ->assertOk()->assertJsonPath('url', route('doctor.account.settings', ['tab' => 'stamps']));
        }

        $this->assertSame(2, $doctor->stamps()->count());
        $this->get(route('doctor.account.settings', ['tab' => 'stamps']))->assertOk()->assertSee('ختم العيادة')
            ->assertSee('<script>stamp()</script>')->assertDontSee('<script>stamp()</script>', false)->assertDontSee($other->name);
        foreach ($doctor->stamps as $stamp) {
            Storage::disk('local')->assertExists($stamp->path);
            $this->get(route('doctor.account.stamp.show', ['stamp' => $stamp->id]))->assertOk()->assertHeader('Content-Type', 'image/png');
            $this->assertArrayNotHasKey('path', $stamp->toArray());
        }
        $this->get(route('doctor.account.stamp.show', ['stamp' => $other->id]))->assertNotFound();
    }

    #[TestWith([''])]
    #[TestWith([null])]
    #[TestWith([['invalid']])]
    public function test_invalid_name_does_not_create_a_stamp(mixed $name): void
    {
        Storage::fake('local');
        $this->actingAs(Doctor::factory()->create(), 'doctor')->postJson(route('doctor.account.stamp.store'), ['name' => $name, 'stamp' => UploadedFile::fake()->image('stamp.png', 100, 100)])
            ->assertUnprocessable()->assertJsonValidationErrors('name');
        $this->assertDatabaseCount('doctor_stamps', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    #[TestWith(['prescription'])]
    #[TestWith(['lab'])]
    #[TestWith(['radiology'])]
    #[TestWith(['referral'])]
    #[TestWith(['followup'])]
    public function test_all_documents_can_print_with_a_selected_stamp_or_without_and_keep_original_unchanged(string $kind): void
    {
        Storage::fake('local');
        $document = $this->document($kind);
        $this->actingAs($document->doctor, 'doctor');
        $image = UploadedFile::fake()->image('stamp.png', 180, 100);
        $path = $image->store('stamps', 'local');
        $stamp = DoctorStamp::factory()->for($document->doctor)->create(['path' => $path, 'name' => 'ختم العيادة']);
        $foreign = DoctorStamp::factory()->create(['name' => 'ختم لا يخص الطبيب']);
        $before = $document->fresh()->getAttributes();

        $this->get($this->url($document))->assertOk()->assertSee('اختيار ختم الطباعة')->assertSee($stamp->name)->assertDontSee($foreign->name);
        $response = $this->postJson($this->url($document, true), ['stamp_choice' => (string) $stamp->id])->assertOk();

        $uri = 'data:image/png;base64,'.base64_encode(Storage::disk('local')->get($path));
        $response->assertJsonPath('stamp_data_uri', $uri);
        $audit = $document->revisions()->where('action', 'print_requested')->sole();
        $this->assertSame($stamp->id, $audit->snapshot['print_stamp']['stamp_id']);
        $this->assertSame(hash('sha256', $uri), $audit->snapshot['print_stamp']['sha256']);
        $this->assertSame($uri, $audit->snapshot['print_stamp']['stamp_data_uri']);
        $this->postJson($this->url($document, true), ['stamp_choice' => 'none'])->assertOk()->assertJsonPath('stamp_data_uri', null);
        $this->assertSame($before, $document->fresh()->getAttributes());
        $this->assertDatabaseCount('doctor_payments', 0);
    }

    #[TestWith(['prescription'])]
    #[TestWith(['lab'])]
    #[TestWith(['referral'])]
    public function test_foreign_missing_and_invalid_stamps_cannot_be_printed_or_audited(string $kind): void
    {
        Storage::fake('local');
        $document = $this->document($kind);
        $foreign = DoctorStamp::factory()->create();
        $missing = DoctorStamp::factory()->for($document->doctor)->create();
        $invalid = DoctorStamp::factory()->for($document->doctor)->create(['path' => 'invalid.png']);
        Storage::disk('local')->put('invalid.png', '<script>not an image</script>');
        $this->actingAs($document->doctor, 'doctor');
        $url = $this->url($document, true);

        $this->postJson($url, ['stamp_choice' => (string) $foreign->id])->assertNotFound();
        $this->postJson($url, ['stamp_choice' => (string) $missing->id])->assertUnprocessable()->assertJsonValidationErrors('stamp_choice');
        $this->postJson($url, ['stamp_choice' => (string) $invalid->id])->assertUnprocessable()->assertJsonValidationErrors('stamp_choice');
        $this->postJson($url, ['stamp_choice' => '../../secret'])->assertUnprocessable()->assertJsonValidationErrors('stamp_choice');
        $this->assertSame(0, $document->revisions()->count());
    }

    private function document(string $kind): DoctorPrescription|DoctorDiagnosticOrder|DoctorReferral
    {
        $attributes = ['status' => 'approved', 'approved_at' => now(), 'issued_snapshot' => ['doctor_name' => 'طبيب', 'stamp_data_uri' => null]];

        return match ($kind) {
            'prescription' => DoctorPrescription::factory()->create($attributes),
            'lab', 'radiology' => DoctorDiagnosticOrder::factory()->create($attributes + ['kind' => $kind]),
            default => DoctorReferral::factory()->create($attributes + ['kind' => $kind]),
        };
    }

    private function url(DoctorPrescription|DoctorDiagnosticOrder|DoctorReferral $document, bool $store = false): string
    {
        $prefix = match (true) {
            $document instanceof DoctorPrescription => 'doctor.clinical.prescriptions',
            $document instanceof DoctorDiagnosticOrder => 'doctor.orders',
            default => 'doctor.clinical.referrals',
        };

        return route($prefix.'.print'.($store ? '.store' : ''), $document);
    }
}
