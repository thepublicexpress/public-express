function getEditorElement() {
    return document.getElementById('bodyEditor');
}

function getHiddenTextarea() {
    return document.getElementById('body');
}

function syncEditorToTextarea() {
    const editor = getEditorElement();
    const textarea = getHiddenTextarea();
    if (editor && textarea) {
        textarea.value = editor.innerHTML.trim();
        updateCharCount();
    }
}

function setEditorContent() {
    const editor = getEditorElement();
    const textarea = getHiddenTextarea();
    if (editor && textarea) {
        editor.innerHTML = textarea.value.trim() || '';
    }
}

function updatePreview() {
    const textarea = getHiddenTextarea();
    const preview = document.getElementById('previewContent');
    if (preview && textarea) {
        preview.innerHTML = textarea.value || '<p class="text-muted text-center">कोई सामग्री नहीं</p>';
    }
}

function updateCharCount() {
    const textarea = getHiddenTextarea();
    const charCount = document.getElementById('charCount');
    if (!textarea || !charCount) return;
    const length = textarea.value.length;
    charCount.textContent = `${length} characters`;
    charCount.className = 'char-count';
    if (length > 5000) charCount.classList.add('warning');
    if (length > 10000) charCount.classList.add('danger');
}

function formatSelection(command, value = null) {
    document.execCommand(command, false, value);
    syncEditorToTextarea();
    updatePreview();
}

function applyHeading(level) {
    if (['h2', 'h3', 'h4'].includes(level)) {
        formatSelection('formatBlock', `<${level}>`);
    }
}

function insertHeading(level) {
    applyHeading(level);
}

function toggleBold() {
    formatSelection('bold');
}

function toggleItalic() {
    formatSelection('italic');
}

function applyList() {
    formatSelection('insertUnorderedList');
}

function clearFormatting() {
    formatSelection('removeFormat');
}

function insertQuote() {
    const selection = window.getSelection().toString().trim() || 'यहां अपना उद्धरण लिखें';
    const html = `<div class="pull-quote"><p>"${selection}"</p><span class="quote-author">- लेखक</span></div><p></p>`;
    insertHtmlAtCursor(html);
}

function insertHtmlAtCursor(html) {
    const editor = getEditorElement();
    if (!editor) return;
    editor.focus();
    document.execCommand('insertHTML', false, html);
    syncEditorToTextarea();
    updatePreview();
}

function insertImageFromFile(file, caption) {
    if (!file) return;

    const reader = new FileReader();
    reader.onload = function(event) {
        const img = new Image();
        img.onload = function() {
            const maxWidth = 760;
            const maxHeight = 420;
            let width = img.width;
            let height = img.height;
            let ratio = Math.min(maxWidth / width, maxHeight / height, 1);
            width = Math.round(width * ratio);
            height = Math.round(height * ratio);

            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            function generateDataUrl(type, quality) {
                canvas.width = width;
                canvas.height = height;
                ctx.clearRect(0, 0, width, height);
                ctx.drawImage(img, 0, 0, width, height);
                return canvas.toDataURL(type, quality);
            }

            function getValidDataUrl(type, quality) {
                try {
                    const url = generateDataUrl(type, quality);
                    if (!url || !url.startsWith(`data:${type}`)) {
                        return null;
                    }
                    return url;
                } catch (err) {
                    return null;
                }
            }

            let quality = 0.92;
            let typeCandidates = ['image/webp'];
            if (file.type && file.type.startsWith('image/')) {
                typeCandidates.push(file.type);
            }
            typeCandidates = typeCandidates.concat(['image/jpeg', 'image/png']);
            typeCandidates = [...new Set(typeCandidates)];

            let dataUrl = null;
            let type = 'image/webp';
            for (const candidate of typeCandidates) {
                const url = getValidDataUrl(candidate, quality);
                if (url) {
                    type = candidate;
                    dataUrl = url;
                    break;
                }
            }

            if (!dataUrl) {
                alert('Your browser does not support this image conversion. Please try a different browser or choose a smaller image.');
                return;
            }

            const maxSize = 120000;
            while (dataUrl.length > maxSize && quality > 0.35) {
                quality -= 0.08;
                dataUrl = getValidDataUrl(type, quality) || getValidDataUrl('image/jpeg', quality) || getValidDataUrl('image/png', quality);
                if (!dataUrl) {
                    break;
                }
            }

            while (dataUrl && dataUrl.length > maxSize && width > 320) {
                width = Math.round(width * 0.8);
                height = Math.round(height * 0.8);
                dataUrl = getValidDataUrl(type, quality) || getValidDataUrl('image/jpeg', quality) || getValidDataUrl('image/png', quality);
                if (!dataUrl) {
                    break;
                }
            }

            if (!dataUrl || dataUrl.length > maxSize) {
                alert('Image too large. Please choose a smaller image.');
                return;
            }

            const captionHtml = caption ? `<figcaption>${caption}</figcaption>` : '';
            const figureHtml = `<figure><img src="${dataUrl}" alt="${caption || 'Image'}" style="width:100%;max-width:760px;height:auto;border-radius:8px;" width="${width}" height="${height}">${captionHtml}</figure><p></p>`;
            insertHtmlAtCursor(figureHtml);
        };
        img.src = event.target.result;
    };
    reader.readAsDataURL(file);
}

function loadImagePreview(inputId, previewImgId, previewContainerId) {
    const input = document.getElementById(inputId);
    const preview = document.getElementById(previewImgId);
    const container = document.getElementById(previewContainerId);
    if (!input || !preview || !container) return;
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        preview.src = e.target.result;
        container.style.display = 'block';
    };
    reader.readAsDataURL(file);
}

function openInlineImageModal(modalId) {
    const modalEl = document.getElementById(modalId);
    if (!modalEl) return;
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}

function openInlineImageUpload() {
    if (document.getElementById('inlineImageModal')) {
        openInlineImageModal('inlineImageModal');
        return;
    }

    if (document.getElementById('imageUploadModal')) {
        openInlineImageModal('imageUploadModal');
        return;
    }
}

function openImageUpload() {
    // Legacy button alias used by admin and reporter views
    openInlineImageUpload();
}

function insertImageTag() {
    openInlineImageUpload();
}

function insertInlineImage(modalId = 'inlineImageModal', inputId = 'inlineImageInput', captionId = 'inlineImageCaption') {
    let input = document.getElementById(inputId);
    if (!input) {
        input = document.getElementById('inContentImage');
    }
    const captionElement = document.getElementById(captionId);
    const caption = captionElement ? captionElement.value.trim() : '';
    if (!input || !input.files || !input.files[0]) {
        alert('कृपया पहले एक इमेज चुनें!');
        return;
    }
    insertImageFromFile(input.files[0], caption);
    closeModal(modalId);
}

function insertImageFromUpload() {
    insertInlineImage('imageUploadModal', 'inContentImage', 'imageCaption');
}

function closeModal(modalId) {
    const modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
    if (modal) modal.hide();
}

function wrapText(type) {
    const editor = getEditorElement();
    if (!editor) return;
    editor.focus();
    const selection = window.getSelection().toString() || 'text';
    const tag = type === 'b' ? 'strong' : type === 'i' ? 'em' : null;
    if (!tag) return;
    document.execCommand('insertHTML', false, `<${tag}>${selection}</${tag}>`);
    syncEditorToTextarea();
    updatePreview();
}

function initEditor() {
    const editor = getEditorElement();
    const textarea = getHiddenTextarea();
    if (!editor || !textarea) return;

    if (textarea.value.trim()) {
        editor.innerHTML = textarea.value;
    }
    editor.addEventListener('input', function() {
        textarea.value = editor.innerHTML;
        updateCharCount();
        updatePreview();
    });

    editor.addEventListener('paste', function(event) {
        event.preventDefault();
        const clipboardData = (event.clipboardData || window.clipboardData);
        const text = clipboardData.getData('text/plain');
        document.execCommand('insertText', false, text);
        textarea.value = editor.innerHTML;
        updateCharCount();
        updatePreview();
    });

    editor.addEventListener('keydown', function(event) {
        if (!event.ctrlKey) return;
        switch (event.key.toLowerCase()) {
            case '2':
                event.preventDefault();
                applyHeading('h2');
                break;
            case '3':
                event.preventDefault();
                applyHeading('h3');
                break;
            case '4':
                event.preventDefault();
                applyHeading('h4');
                break;
            case 'b':
                event.preventDefault();
                toggleBold();
                break;
            case 'i':
                event.preventDefault();
                toggleItalic();
                break;
            case 'q':
                event.preventDefault();
                insertQuote();
                break;
            case 'l':
                event.preventDefault();
                applyList();
                break;
            default:
                break;
        }
    });

    updateCharCount();
    updatePreview();
}

function submitEditorForm(formId) {
    syncEditorToTextarea();
    const form = document.getElementById(formId);
    if (!form) return;
    const textarea = getHiddenTextarea();
    if (textarea && !textarea.value.trim()) {
        alert('कृपया खबर की सामग्री भरें।');
        return;
    }
    form.submit();
}

function formatText(type) {
    switch (type) {
        case 'h2':
        case 'h3':
        case 'h4':
            applyHeading(type);
            break;
        case 'bold':
            toggleBold();
            break;
        case 'italic':
            toggleItalic();
            break;
        case 'list':
            applyList();
            break;
        case 'quote':
            insertQuote();
            break;
        default:
            break;
    }
}

function togglePreview() {
    updatePreview();
    const modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}

window.addEventListener('DOMContentLoaded', function() {
    initEditor();
});
