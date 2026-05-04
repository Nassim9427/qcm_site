<?php require __DIR__ . '/partials/header.php'; ?>

<?php
function quiz_option_value($question, $letter)
{
    if (empty($question['options'])) {
        return '';
    }

    foreach ($question['options'] as $option) {
        if (($option['key'] ?? '') === $letter) {
            return htmlspecialchars($option['text'] ?? '');
        }
    }

    return '';
}
?>

<style>
    .quiz-admin-page {
        padding: 40px 20px;
        background: #f5f5f5;
        min-height: calc(100vh - 120px);
    }

    .quiz-admin-shell {
        max-width: 1200px;
        margin: 0 auto;
    }

    .quiz-admin-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
        margin-bottom: 24px;
    }

    .quiz-admin-title {
        margin: 0 0 8px;
        font-size: 52px;
        color: #111827;
        font-weight: 800;
    }

    .quiz-admin-subtitle {
        margin: 0;
        color: #4b5563;
        font-size: 16px;
    }

    .quiz-admin-home-btn {
        display: inline-block;
        background: #f97316;
        color: white;
        padding: 14px 22px;
        border-radius: 999px;
        text-decoration: none;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(249, 115, 22, 0.2);
    }

    .quiz-admin-card {
        max-width: 1100px;
        margin: 0 auto 30px;
        background: white;
        color: black;
        padding: 28px;
        border-radius: 22px;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    }

    .quiz-admin-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }

    .quiz-admin-grid-4 {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 14px;
        margin-bottom: 16px;
    }

    .quiz-admin-label {
        display: block;
        font-weight: 700;
        margin-bottom: 8px;
        color: #111827;
    }

    .quiz-admin-input,
    .quiz-admin-select,
    .quiz-admin-textarea {
        width: 100%;
        padding: 14px 16px;
        border: 1px solid #d1d5db;
        border-radius: 14px;
        box-sizing: border-box;
        font: inherit;
        background: #fff;
    }

    .quiz-admin-textarea {
        resize: vertical;
    }

    .quiz-admin-btn {
        background: #f97316;
        color: white;
        border: none;
        padding: 14px 24px;
        border-radius: 999px;
        cursor: pointer;
        font-weight: 700;
        box-shadow: 0 8px 18px rgba(249, 115, 22, 0.2);
    }

    .quiz-delete-btn {
        background: #dc2626;
        color: white;
        border: none;
        padding: 12px 18px;
        border-radius: 999px;
        cursor: pointer;
        font-weight: 700;
    }

    .question-image-uploader {
        margin-bottom: 18px;
    }

    .question-image-dropzone {
        border: 2px dashed #f97316;
        background: #fff7ed;
        border-radius: 16px;
        padding: 18px;
        text-align: center;
        cursor: pointer;
        transition: 0.2s ease;
        outline: none;
    }

    .question-image-dropzone:hover,
    .question-image-dropzone.dragover,
    .question-image-dropzone:focus {
        background: #ffedd5;
        border-color: #ea580c;
    }

    .question-image-help {
        margin: 0;
        color: #9a3412;
        font-weight: 700;
        font-size: 16px;
    }

    .question-image-subhelp {
        margin-top: 8px;
        color: #7c2d12;
        font-size: 14px;
    }

    .question-image-actions {
        display: flex;
        gap: 10px;
        justify-content: center;
        flex-wrap: wrap;
        margin-top: 14px;
    }

    .question-image-small-btn {
        background: white;
        border: 1px solid #fdba74;
        color: #c2410c;
        border-radius: 999px;
        padding: 10px 16px;
        cursor: pointer;
        font-weight: 700;
    }

    .question-image-preview {
        margin-top: 16px;
        display: none;
    }

    .question-image-preview img {
        max-width: 100%;
        max-height: 260px;
        display: block;
        margin: 0 auto;
        border-radius: 14px;
        border: 1px solid #fed7aa;
        background: white;
    }

    .question-image-remove {
        margin-top: 12px;
        background: #dc2626;
        color: white;
        border: none;
        border-radius: 999px;
        padding: 10px 16px;
        cursor: pointer;
        font-weight: 700;
    }

    .quiz-section-title {
        margin: 0 0 16px;
        color: #f97316;
        font-size: 24px;
        font-weight: 800;
    }

    .quiz-existing-title {
        max-width: 1100px;
        margin: 0 auto 16px;
        color: #111827;
        font-size: 32px;
        font-weight: 800;
    }

    .quiz-chip-row {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .quiz-chip {
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 700;
    }
</style>

<main class="quiz-admin-page">
    <div class="quiz-admin-shell">
        <div class="quiz-admin-topbar">
            <div>
                <h1 class="quiz-admin-title">Gestion du quiz</h1>
                <p class="quiz-admin-subtitle">Ajoute, modifie ou supprime les questions directement depuis cette page.</p>
            </div>

            <a href="index.php?page=home" class="quiz-admin-home-btn">
                Retour à l'accueil
            </a>
        </div>

        <?php if (!empty($_SESSION['admin_quiz_success'])): ?>
            <div style="max-width:1100px; margin:0 auto 18px; background:#dcfce7; color:#166534; border:1px solid #86efac; padding:14px 18px; border-radius:12px; font-weight:700;">
                <?= htmlspecialchars($_SESSION['admin_quiz_success']) ?>
            </div>
            <?php unset($_SESSION['admin_quiz_success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['admin_quiz_error'])): ?>
            <div style="max-width:1100px; margin:0 auto 18px; background:#fee2e2; color:#991b1b; border:1px solid #fca5a5; padding:14px 18px; border-radius:12px; font-weight:700;">
                <?= htmlspecialchars($_SESSION['admin_quiz_error']) ?>
            </div>
            <?php unset($_SESSION['admin_quiz_error']); ?>
        <?php endif; ?>

        <section class="quiz-admin-card">
            <h2 class="quiz-section-title">Ajouter une question</h2>

            <form method="POST" action="index.php?page=admin_create_question">
                <div class="quiz-admin-grid">
                    <div>
                        <label class="quiz-admin-label">Type</label>
                        <select name="question_type" class="quiz-admin-select">
                            <option value="qcm">QCM</option>
                            <option value="text">Texte</option>
                        </select>
                    </div>

                    <div>
                        <label class="quiz-admin-label">Ordre d'affichage</label>
                        <input type="number" name="position" min="1" placeholder="Ex: 1" class="quiz-admin-input">
                    </div>

                    <div>
                        <label class="quiz-admin-label">Statut</label>
                        <select name="is_active" class="quiz-admin-select">
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <label class="quiz-admin-label">Texte de la question</label>
                    <textarea name="question_text" rows="3" class="quiz-admin-textarea"></textarea>
                </div>

                <div class="question-image-uploader js-image-uploader">
                    <label class="quiz-admin-label">Image dans la consigne</label>

                    <div class="question-image-dropzone js-dropzone" tabindex="0">
                        <p class="question-image-help">Glisse-dépose une image ici ou colle-la avec Ctrl+V</p>
                        <div class="question-image-subhelp">Formats acceptés : JPG, PNG, WEBP, GIF — max 5 Mo</div>

                        <div class="question-image-actions">
                            <button type="button" class="question-image-small-btn js-choose-btn">Choisir une image</button>
                        </div>

                        <input type="file" accept="image/*" class="js-file-input" style="display:none;">
                        <textarea name="image_data" class="js-image-data" style="display:none;"></textarea>
                        <input type="hidden" name="remove_image" class="js-remove-image" value="0">

                        <div class="question-image-preview js-preview">
                            <img src="" alt="Aperçu image question" class="js-preview-img" data-current-src="">
                            <button type="button" class="question-image-remove js-remove-btn">Retirer l'image</button>
                        </div>
                    </div>
                </div>

                <div class="quiz-admin-grid-4">
                    <?php foreach (['a', 'b', 'c', 'd'] as $letter): ?>
                        <div>
                            <label class="quiz-admin-label">Proposition <?= strtoupper($letter) ?></label>
                            <input type="text" name="options[<?= $letter ?>]" placeholder="Texte de la proposition <?= strtoupper($letter) ?>" class="quiz-admin-input">
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="quiz-admin-grid">
                    <div>
                        <label class="quiz-admin-label">Bonne réponse QCM</label>
                        <select name="correct_answer" class="quiz-admin-select">
                            <option value="a">A</option>
                            <option value="b">B</option>
                            <option value="c">C</option>
                            <option value="d">D</option>
                        </select>
                    </div>

                    <div>
                        <label class="quiz-admin-label">Réponses acceptées (question texte)</label>
                        <textarea name="accepted_answers" rows="4" placeholder="Une réponse par ligne&#10;Exemple :&#10;1&#10;un" class="quiz-admin-textarea"></textarea>
                    </div>
                </div>

                <button type="submit" class="quiz-admin-btn">Ajouter la question</button>
            </form>
        </section>

        <h2 class="quiz-existing-title">Questions existantes</h2>

        <?php if (empty($questions)): ?>
            <div class="quiz-admin-card">Aucune question trouvée.</div>
        <?php endif; ?>

        <?php foreach ($questions as $index => $question): ?>
            <div class="quiz-admin-card">
                <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; margin-bottom:18px;">
                    <div>
                        <h3 style="margin:0 0 6px; font-size:28px; color:#111827;">Question <?= $index + 1 ?></h3>
                        <div class="quiz-chip-row">
                            <span class="quiz-chip" style="background:#fff7ed; color:#c2410c;">
                                <?= strtoupper(htmlspecialchars($question['question_type'])) ?>
                            </span>
                            <span class="quiz-chip" style="background:<?= !empty($question['is_active']) ? '#dcfce7' : '#e5e7eb' ?>; color:<?= !empty($question['is_active']) ? '#166534' : '#374151' ?>;">
                                <?= !empty($question['is_active']) ? 'Active' : 'Inactive' ?>
                            </span>
                            <span class="quiz-chip" style="background:#f3f4f6; color:#374151;">
                                Ordre : <?= (int)$question['position'] ?>
                            </span>
                        </div>
                    </div>

                    <form method="POST" action="index.php?page=admin_delete_question" onsubmit="return confirm('Supprimer cette question ?');" style="margin:0;">
                        <input type="hidden" name="question_id" value="<?= (int)$question['id'] ?>">
                        <button type="submit" class="quiz-delete-btn">Supprimer</button>
                    </form>
                </div>

                <form method="POST" action="index.php?page=admin_update_question">
                    <input type="hidden" name="question_id" value="<?= (int)$question['id'] ?>">

                    <div class="quiz-admin-grid">
                        <div>
                            <label class="quiz-admin-label">Type</label>
                            <select name="question_type" class="quiz-admin-select">
                                <option value="qcm" <?= $question['question_type'] === 'qcm' ? 'selected' : '' ?>>QCM</option>
                                <option value="text" <?= $question['question_type'] === 'text' ? 'selected' : '' ?>>Texte</option>
                            </select>
                        </div>

                        <div>
                            <label class="quiz-admin-label">Ordre d'affichage</label>
                            <input type="number" name="position" min="1" value="<?= (int)$question['position'] ?>" class="quiz-admin-input">
                        </div>

                        <div>
                            <label class="quiz-admin-label">Statut</label>
                            <select name="is_active" class="quiz-admin-select">
                                <option value="1" <?= !empty($question['is_active']) ? 'selected' : '' ?>>Active</option>
                                <option value="0" <?= empty($question['is_active']) ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom:16px;">
                        <label class="quiz-admin-label">Texte de la question</label>
                        <textarea name="question_text" rows="3" class="quiz-admin-textarea"><?= htmlspecialchars($question['question_text']) ?></textarea>
                    </div>

                    <div class="question-image-uploader js-image-uploader">
                        <label class="quiz-admin-label">Image dans la consigne</label>

                        <div class="question-image-dropzone js-dropzone" tabindex="0">
                            <p class="question-image-help">Glisse-dépose une image ici ou colle-la avec Ctrl+V</p>
                            <div class="question-image-subhelp">Formats acceptés : JPG, PNG, WEBP, GIF — max 5 Mo</div>

                            <div class="question-image-actions">
                                <button type="button" class="question-image-small-btn js-choose-btn">Remplacer l'image</button>
                            </div>

                            <input type="file" accept="image/*" class="js-file-input" style="display:none;">
                            <textarea name="image_data" class="js-image-data" style="display:none;"></textarea>
                            <input type="hidden" name="remove_image" class="js-remove-image" value="0">

                            <div class="question-image-preview js-preview" style="<?= !empty($question['question_image']) ? 'display:block;' : 'display:none;' ?>">
                                <img
                                    src="<?= !empty($question['question_image']) ? htmlspecialchars($question['question_image']) : '' ?>"
                                    alt="Aperçu image question"
                                    class="js-preview-img"
                                    data-current-src="<?= !empty($question['question_image']) ? htmlspecialchars($question['question_image']) : '' ?>"
                                >
                                <button type="button" class="question-image-remove js-remove-btn">Retirer l'image</button>
                            </div>
                        </div>
                    </div>

                    <div class="quiz-admin-grid-4">
                        <?php foreach (['a', 'b', 'c', 'd'] as $letter): ?>
                            <div>
                                <label class="quiz-admin-label">Proposition <?= strtoupper($letter) ?></label>
                                <input type="text" name="options[<?= $letter ?>]" value="<?= quiz_option_value($question, $letter) ?>" placeholder="Texte de la proposition <?= strtoupper($letter) ?>" class="quiz-admin-input">
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="quiz-admin-grid">
                        <div>
                            <label class="quiz-admin-label">Bonne réponse QCM</label>
                            <select name="correct_answer" class="quiz-admin-select">
                                <?php foreach (['a', 'b', 'c', 'd'] as $letter): ?>
                                    <option value="<?= $letter ?>" <?= ($question['correct_answer'] ?? '') === $letter ? 'selected' : '' ?>><?= strtoupper($letter) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="quiz-admin-label">Réponses acceptées (question texte)</label>
                            <textarea name="accepted_answers" rows="4" placeholder="Une réponse par ligne" class="quiz-admin-textarea"><?= htmlspecialchars(implode("\n", $question['accepted_answers_list'] ?? [])) ?></textarea>
                        </div>
                    </div>

                    <div style="margin-bottom:16px; padding:16px; background:#fff7ed; border:1px solid #fdba74; border-radius:12px;">
                        <strong style="color:#c2410c;">Aperçu correction :</strong>
                        <?php if ($question['question_type'] === 'qcm'): ?>
                            <?php
                                $correctText = '';
                                foreach ($question['options'] as $option) {
                                    if (($option['key'] ?? '') === ($question['correct_answer'] ?? '')) {
                                        $correctText = $option['text'] ?? '';
                                        break;
                                    }
                                }
                            ?>
                            <span><?= strtoupper(htmlspecialchars($question['correct_answer'] ?? '')) ?><?= $correctText !== '' ? ' - ' . htmlspecialchars($correctText) : '' ?></span>
                        <?php else: ?>
                            <span><?= htmlspecialchars(implode(' / ', $question['accepted_answers_list'] ?? [])) ?></span>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="quiz-admin-btn">Enregistrer les modifications</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<script>
    function initQuestionImageUploaders() {
        const uploaders = document.querySelectorAll('.js-image-uploader');
        let activeUploader = null;

        uploaders.forEach(function (uploader) {
            const dropzone = uploader.querySelector('.js-dropzone');
            const chooseBtn = uploader.querySelector('.js-choose-btn');
            const fileInput = uploader.querySelector('.js-file-input');
            const imageDataField = uploader.querySelector('.js-image-data');
            const removeImageField = uploader.querySelector('.js-remove-image');
            const preview = uploader.querySelector('.js-preview');
            const previewImg = uploader.querySelector('.js-preview-img');
            const removeBtn = uploader.querySelector('.js-remove-btn');
            const currentSrc = previewImg ? (previewImg.dataset.currentSrc || '') : '';

            function showPreview(src) {
                if (!preview || !previewImg) {
                    return;
                }

                preview.style.display = 'block';
                previewImg.src = src;
            }

            function clearPreview(markAsRemoved) {
                if (!preview || !previewImg) {
                    return;
                }

                preview.style.display = 'none';
                previewImg.src = '';
                imageDataField.value = '';
                fileInput.value = '';
                removeImageField.value = markAsRemoved ? '1' : '0';
            }

            function loadFile(file) {
                if (!file) {
                    return;
                }

                if (!file.type || !file.type.startsWith('image/')) {
                    alert('Ce fichier n’est pas une image.');
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert('Image trop lourde. Taille max : 5 Mo.');
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (event) {
                    const result = event.target.result || '';
                    imageDataField.value = result;
                    removeImageField.value = '0';
                    showPreview(result);
                };

                reader.readAsDataURL(file);
            }

            function extractImageFromClipboardEvent(event) {
                if (!event.clipboardData || !event.clipboardData.items) {
                    return false;
                }

                const items = event.clipboardData.items;

                for (let i = 0; i < items.length; i++) {
                    const item = items[i];

                    if (item.type && item.type.indexOf('image/') === 0) {
                        const file = item.getAsFile();
                        loadFile(file);
                        event.preventDefault();
                        return true;
                    }
                }

                return false;
            }

            chooseBtn.addEventListener('click', function (event) {
                event.preventDefault();
                activeUploader = uploader;
                fileInput.click();
            });

            dropzone.addEventListener('click', function (event) {
                if (event.target.closest('.js-remove-btn')) {
                    return;
                }

                activeUploader = uploader;
                dropzone.focus();
            });

            dropzone.addEventListener('focus', function () {
                activeUploader = uploader;
            });

            dropzone.addEventListener('dragover', function (event) {
                event.preventDefault();
                activeUploader = uploader;
                dropzone.classList.add('dragover');
            });

            dropzone.addEventListener('dragleave', function () {
                dropzone.classList.remove('dragover');
            });

            dropzone.addEventListener('drop', function (event) {
                event.preventDefault();
                activeUploader = uploader;
                dropzone.classList.remove('dragover');

                const file = event.dataTransfer && event.dataTransfer.files ? event.dataTransfer.files[0] : null;
                loadFile(file);
            });

            dropzone.addEventListener('paste', function (event) {
                activeUploader = uploader;
                extractImageFromClipboardEvent(event);
            });

            fileInput.addEventListener('change', function () {
                activeUploader = uploader;
                const file = fileInput.files && fileInput.files[0] ? fileInput.files[0] : null;
                loadFile(file);
            });

            removeBtn.addEventListener('click', function (event) {
                event.preventDefault();
                activeUploader = uploader;
                clearPreview(currentSrc !== '');
            });

            if (currentSrc !== '' && previewImg.src === '') {
                showPreview(currentSrc);
            }
        });

        document.addEventListener('paste', function (event) {
            if (!activeUploader) {
                return;
            }

            const imageDataField = activeUploader.querySelector('.js-image-data');
            const removeImageField = activeUploader.querySelector('.js-remove-image');
            const preview = activeUploader.querySelector('.js-preview');
            const previewImg = activeUploader.querySelector('.js-preview-img');
            const fileInput = activeUploader.querySelector('.js-file-input');

            function showPreview(src) {
                if (!preview || !previewImg) {
                    return;
                }

                preview.style.display = 'block';
                previewImg.src = src;
            }

            function loadFile(file) {
                if (!file) {
                    return;
                }

                if (!file.type || !file.type.startsWith('image/')) {
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert('Image trop lourde. Taille max : 5 Mo.');
                    return;
                }

                const reader = new FileReader();

                reader.onload = function (e) {
                    const result = e.target.result || '';
                    imageDataField.value = result;
                    removeImageField.value = '0';
                    if (fileInput) {
                        fileInput.value = '';
                    }
                    showPreview(result);
                };

                reader.readAsDataURL(file);
            }

            if (!event.clipboardData || !event.clipboardData.items) {
                return;
            }

            const items = event.clipboardData.items;

            for (let i = 0; i < items.length; i++) {
                const item = items[i];

                if (item.type && item.type.indexOf('image/') === 0) {
                    const file = item.getAsFile();
                    loadFile(file);
                    event.preventDefault();
                    return;
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', initQuestionImageUploaders);
</script>

<?php require __DIR__ . '/partials/footer.php'; ?>