<?php

namespace App\Models;

use Database\Factories\DoctorExternalReportFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use LogicException;

#[Fillable(['doctor_id', 'doctor_diagnostic_order_id', 'title', 'source_name', 'source_reference', 'report_date', 'file_path', 'file_mime', 'file_size', 'file_sha256', 'request_key', 'reviewed_at', 'review_note'])]
class DoctorExternalReport extends Model
{
    /** @use HasFactory<DoctorExternalReportFactory> */
    use HasFactory;

    protected $hidden = ['file_path', 'file_sha256'];

    protected static function booted(): void
    {
        static::creating(function (self $report): void {
            $order = $report->order;
            if (! $order || $order->doctor_id !== $report->doctor_id || ! $order->approved_at || $order->status !== 'approved') {
                throw new LogicException('التقرير يرتبط بطلب معتمد لنفس الطبيب.');
            }
        });
        static::updating(function (self $report): void {
            if ($report->getOriginal('reviewed_at') || array_diff(array_keys($report->getDirty()), ['reviewed_at', 'review_note', 'updated_at'])) {
                throw new LogicException('التقرير الخارجي وبيانات مصدره ومراجعته لا تستبدل أو تعدل بصمت.');
            }
        });
        static::deleting(fn () => throw new LogicException('التقارير الطبية المحفوظة لا تحذف.'));
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(DoctorDiagnosticOrder::class, 'doctor_diagnostic_order_id');
    }

    protected function casts(): array
    {
        return ['report_date' => 'immutable_date', 'reviewed_at' => 'immutable_datetime', 'file_size' => 'integer'];
    }
}
