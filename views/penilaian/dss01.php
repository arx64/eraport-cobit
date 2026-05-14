<?php
/**
 * DSS01 Penilaian View
 * Form penilaian domain DSS01
 */
?>
<!-- Page Header -->
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h4 class="page-title">
                <i class="bi bi-clipboard-check me-2"></i>Penilaian DSS01
            </h4>
            <p class="text-muted mb-0">Manage Operations - Kelola penilaian domain operasional</p>
        </div>
        <div class="col-auto">
            <a href="<?= BASE_URL ?>/penilaian/responden" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>
</div>

<!-- Responden Selector -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="content-card">
            <div class="content-card-body">
                <form method="GET" action="<?= BASE_URL ?>/penilaian/dss01" class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Pilih Responden</label>
                        <select class="form-select" name="respondent_id" onchange="this.form.submit()">
                            <option value="">-- Pilih Responden --</option>
                            <?php foreach ($respondents as $r): ?>
                            <option value="<?= $r['id'] ?>" <?= $respondentId == $r['id'] ? 'selected' : '' ?>>
                                <?= sanitize($r['nama']) ?> - <?= sanitize($r['jabatan']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Penilaian Form -->
<?php if ($respondentId): ?>
<div class="row g-4">
    <div class="col-12">
        <form action="<?= BASE_URL ?>/penilaian/save-penilaian" method="POST">
            <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
            <input type="hidden" name="respondent_id" value="<?= $respondentId ?>">
            <input type="hidden" name="process_id" value="1">
            
            <!-- Progress Bar -->
            <div class="content-card mb-4">
                <div class="content-card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold">Progress Penilaian</span>
                        <span class="badge bg-primary" id="progressText">0/<?= count($questions) ?> diisi</span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar" id="progressBar" role="progressbar" 
                             style="width: 0%"></div>
                    </div>
                </div>
            </div>
            
            <!-- Questions -->
            <?php foreach ($questions as $i => $q): 
                $answer = $answers[$q['id']] ?? null;
                $nilai = $answer['nilai'] ?? -1;
            ?>
            <div class="question-card mb-4" id="question<?= $q['id'] ?>">
                <div class="question-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-primary me-2"><?= sanitize($q['kode_pertanyaan']) ?></span>
                            <span class="badge bg-info text-dark"><?= sanitize($q['komponen']) ?></span>
                        </div>
                        <span class="question-number"><?= $i + 1 ?>/<?= count($questions) ?></span>
                    </div>
                </div>
                <div class="question-body">
                    <p class="question-text"><?= sanitize($q['pertanyaan']) ?></p>
                    
                    <div class="rating-scale">
                        <div class="scale-labels">
                            <span>0 - Non-existent</span>
                            <span>1 - Initial</span>
                            <span>2 - Repeatable</span>
                            <span>3 - Defined</span>
                            <span>4 - Managed</span>
                            <span>5 - Optimized</span>
                        </div>
                        <div class="rating-options">
                            <?php for ($v = 0; $v <= 5; $v++): ?>
                            <label class="rating-option">
                                <input type="radio" name="nilai_<?= $q['id'] ?>" 
                                       value="<?= $v ?>" 
                                       <?= $nilai == $v ? 'checked' : '' ?>
                                       required>
                                <span class="rating-value"><?= $v ?></span>
                            </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <label class="form-label small text-muted">Keterangan (opsional)</label>
                        <textarea class="form-control" name="keterangan_<?= $q['id'] ?>" 
                                  rows="2" placeholder="Tambahkan keterangan jika diperlukan..."><?= sanitize($answer['keterangan'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <!-- Submit Button -->
            <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-save me-2"></i>Simpan Penilaian DSS01
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Update progress bar
document.addEventListener('DOMContentLoaded', function() {
    const totalQuestions = <?= count($questions) ?>;
    const radios = document.querySelectorAll('input[type="radio"]:checked');
    const answered = new Set();
    
    radios.forEach(radio => {
        const name = radio.getAttribute('name');
        if (name && name.startsWith('nilai_')) {
            answered.add(name);
        }
    });
    
    const count = answered.size;
    const percentage = (count / totalQuestions) * 100;
    
    document.getElementById('progressBar').style.width = percentage + '%';
    document.getElementById('progressText').textContent = count + '/' + totalQuestions + ' diisi';
    
    // Listen for changes
    document.querySelectorAll('input[type="radio"]').forEach(radio => {
        radio.addEventListener('change', updateProgress);
    });
});

function updateProgress() {
    const totalQuestions = <?= count($questions) ?>;
    const answered = new Set();
    
    document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
        const name = radio.getAttribute('name');
        if (name && name.startsWith('nilai_')) {
            answered.add(name);
        }
    });
    
    const count = answered.size;
    const percentage = (count / totalQuestions) * 100;
    
    document.getElementById('progressBar').style.width = percentage + '%';
    document.getElementById('progressText').textContent = count + '/' + totalQuestions + ' diisi';
}
</script>
<?php else: ?>
<!-- Empty State -->
<div class="row g-4">
    <div class="col-12">
        <div class="content-card text-center py-5">
            <i class="bi bi-person-check fs-1 text-muted mb-3"></i>
            <h5>Pilih Responden</h5>
            <p class="text-muted">Silakan pilih responden terlebih dahulu untuk mengisi penilaian.</p>
        </div>
    </div>
</div>
<?php endif; ?>
