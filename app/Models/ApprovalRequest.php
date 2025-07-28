<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalRequest extends Model
{
    protected $fillable = [
        'requested_by',
        'siswa_id',
        'mata_pelajaran_id', 
        'jenis_nilai_id',
        'old_value',
        'new_value',
        'reason',
        'status',
        'processed_by',
        'processed_at',
        'rejection_reason'
    ];

    protected $casts = [
        'old_value' => 'decimal:2',
        'new_value' => 'decimal:2',
        'processed_at' => 'datetime'
    ];

    /**
     * Get the user who requested the approval
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the user who processed the request
     */
    public function processedByUser()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Get the siswa (student) for this request.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Get the mata pelajaran (subject) for this request.
     */
    public function mataPelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    /**
     * Get the mata pelajaran (subject) for this request.
     */
    public function mata_pelajaran(): BelongsTo
    {
        return $this->belongsTo(MataPelajaran::class, 'mata_pelajaran_id');
    }

    /**
     * Get the jenis nilai (grade type) for this request.
     */
    public function jenisNilai(): BelongsTo
    {
        return $this->belongsTo(JenisNilai::class, 'jenis_nilai_id');
    }

    /**
     * Get the jenis nilai (grade type) for this request.
     */
    public function jenis_nilai(): BelongsTo
    {
        return $this->belongsTo(JenisNilai::class, 'jenis_nilai_id');
    }

    /**
     * Check if the request is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the request is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the request is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Get the status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'pending' => 'yellow',
            'approved' => 'green',
            'rejected' => 'red',
            default => 'gray'
        };
    }

    /**
     * Get the formatted status text.
     */
    public function getStatusTextAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => ucfirst($this->status)
        };
    }
}
