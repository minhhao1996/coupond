import Quill from 'quill';
import 'quill/dist/quill.snow.css';
import { setupBlocks } from './editor-blocks';

export function initEditors() {
    document.querySelectorAll('[data-review-editor]').forEach((textarea) => {
        if (textarea.dataset.ready) return;
        const container = document.createElement('div');
        textarea.after(container);
        const editor = new Quill(container, {
            theme: 'snow',
            placeholder: 'Write your review…',
            formats: ['header', 'bold', 'italic', 'underline', 'strike', 'blockquote', 'code-block', 'list', 'link', 'figure', 'comparison'],
            modules: { toolbar: [[{ header: [2, 3, false] }], ['bold', 'italic', 'underline', 'strike'], [{ list: 'ordered' }, { list: 'bullet' }], ['blockquote', 'code-block', 'link'], ['clean']] },
        });
        setupBlocks(editor, textarea);
        if (textarea.dataset.format === 'html') {
            editor.setContents(editor.clipboard.convert({ html: textarea.value }));
        } else {
            editor.setText(textarea.value);
        }
        editor.root.setAttribute('role', 'textbox');
        editor.root.setAttribute('aria-label', 'Article content');
        editor.root.setAttribute('aria-multiline', 'true');
        const labels = { bold: 'Bold', italic: 'Italic', underline: 'Underline', strike: 'Strikethrough', blockquote: 'Quote', 'code-block': 'Code block', link: 'Insert link', clean: 'Clear formatting' };
        container.previousElementSibling.querySelectorAll('button').forEach((button) => {
            const format = [...button.classList].find((name) => name.startsWith('ql-'))?.slice(3);
            const label = format === 'list' ? (button.value === 'ordered' ? 'Numbered list' : 'Bullet list') : labels[format];
            if (label) { button.setAttribute('aria-label', label); button.title = label; }
        });
        const hasContent = () => editor.getText().trim() || editor.root.querySelector('figure img, table');
        const sync = () => {
            textarea.value = editor.getSemanticHTML();
            textarea.form.elements.content_format.value = 'html';
            if (hasContent()) {
                editor.root.removeAttribute('aria-invalid');
                error.hidden = true;
            }
        };
        editor.on('text-change', sync);
        textarea.form.addEventListener('submit', (event) => {
            sync();
            if (!hasContent()) {
                event.preventDefault();
                editor.focus();
                editor.root.setAttribute('aria-invalid', 'true');
                error.hidden = false;
            }
        });
        const error = document.createElement('p');
        error.textContent = 'Please enter article content.';
        error.className = 'mt-2 text-sm text-red-700';
        error.setAttribute('role', 'alert');
        error.hidden = true;
        container.after(error);
        textarea.hidden = true;
        textarea.required = false;
        textarea.dataset.ready = 'true';
    });
}
