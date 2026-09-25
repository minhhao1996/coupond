import './bootstrap';

if (document.querySelector('[data-review-editor]')) {
    import('./review-editor').then(({ initEditors }) => initEditors()).catch(() => {
        // Keep the textarea usable if the editor bundle cannot load.
    });
}

document.querySelectorAll('[data-image-picker]').forEach((picker) => {
    const input = picker.querySelector('[data-image-input]');
    const preview = picker.querySelector('[data-image-preview]');
    const original = preview.getAttribute('src');
    let temporary;
    input.addEventListener('change', () => {
        if (temporary) URL.revokeObjectURL(temporary);
        const file = input.files[0];
        temporary = file ? URL.createObjectURL(file) : null;
        preview.src = temporary || original || '';
        preview.hidden = !preview.getAttribute('src');
    });
});
