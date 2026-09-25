import Quill from 'quill';
const BlockEmbed = Quill.import('blots/block/embed');
const Delta = Quill.import('delta');

function safeImage(src) {
    return /^\/storage\/uploads\/reviews\/[a-zA-Z0-9]+\.(png|jpe?g|webp)$/.test(src) || /^https?:\/\//i.test(src) ? src : '';
}
class Figure extends BlockEmbed {
    static blotName = 'figure';
    static tagName = 'FIGURE';
    static create(value) {
        const node = super.create();
        node.contentEditable = 'false';
        const img = document.createElement('img');
        img.src = safeImage(value.src || '');
        img.alt = value.alt || '';
        node.append(img);
        if (value.caption) { const caption = document.createElement('figcaption'); caption.textContent = value.caption; node.append(caption); }
        return node;
    }
    static value(node) { return { src: node.querySelector('img')?.getAttribute('src') || '', alt: node.querySelector('img')?.alt || '', caption: node.querySelector('figcaption')?.textContent || '' }; }
    html() { return this.domNode.outerHTML; }
}
class Comparison extends BlockEmbed {
    static blotName = 'comparison';
    static tagName = 'TABLE';
    static className = 'editor-comparison';
    static create(value) {
        const node = super.create();
        node.contentEditable = 'false';
        if (value.caption) { const caption = document.createElement('caption'); caption.textContent = value.caption; node.append(caption); }
        const head = document.createElement('thead'), body = document.createElement('tbody');
        (value.rows || []).slice(0, 15).forEach((row, r) => {
            const tr = document.createElement('tr');
            row.slice(0, 6).forEach((text) => { const cell = document.createElement(r ? 'td' : 'th'); cell.textContent = text; tr.append(cell); });
            (r ? body : head).append(tr);
        });
        node.append(head, body);
        return node;
    }
    static value(node) { return { caption: node.querySelector('caption')?.textContent || '', rows: [...node.rows].map(row => [...row.cells].map(cell => cell.textContent)) }; }
    html() { return this.domNode.outerHTML; }
}
Quill.register(Figure, true);
Quill.register(Comparison, true);

function dialog(title) {
    const el = document.createElement('dialog');
    el.className = 'editor-dialog';
    el.setAttribute('aria-label', title);
    el.innerHTML = '<form><h2></h2><div class="editor-dialog-fields"></div><p role="alert" class="editor-dialog-error"></p><div class="editor-dialog-actions"><button type="button" data-remove>Remove block</button><button type="button" data-cancel>Cancel</button><button type="submit" data-save>Insert</button></div></form>';
    el.querySelector('h2').textContent = title;
    el.querySelector('[data-cancel]').onclick = () => el.close();
    el.addEventListener('close', () => el.remove());
    document.body.append(el);
    return el;
}
function field(parent, label, value = '', type = 'text') {
    const wrapper = document.createElement('label');
    wrapper.textContent = label;
    const input = document.createElement('input');
    input.type = type;
    if (type !== 'file') input.value = value;
    wrapper.append(input); parent.append(wrapper);
    return input;
}
export function setupBlocks(editor, textarea) {
    editor.clipboard.addMatcher('FIGURE', node => new Delta().insert({ figure: Figure.value(node) }));
    editor.clipboard.addMatcher('IMG', node => new Delta().insert({ figure: { src: node.getAttribute('src'), alt: node.alt, caption: '' } }));
    editor.clipboard.addMatcher('TABLE', node => new Delta().insert({ comparison: Comparison.value(node) }));
    const controls = document.createElement('div');
    controls.className = 'editor-block-tools';
    controls.innerHTML = '<button type="button" data-image>＋ Image & caption</button><button type="button" data-table>＋ Comparison table</button><span>Double-click a block to edit</span>';
    editor.container.previousElementSibling.before(controls);

    const open = (kind, node = null) => {
        const blot = node ? Quill.find(node) : null;
        const index = blot ? editor.getIndex(blot) : (editor.getSelection()?.index ?? Math.max(0, editor.getLength() - 1));
        const value = node ? (kind === 'figure' ? Figure.value(node) : Comparison.value(node)) : {};
        const modal = dialog(kind === 'figure' ? 'Article image' : 'Comparison table');
        const parent = modal.querySelector('.editor-dialog-fields');
        const submit = modal.querySelector('[data-save]');
        submit.textContent = node ? 'Update block' : 'Insert block';
        const remove = modal.querySelector('[data-remove]');
        remove.hidden = !node;
        remove.onclick = () => { editor.deleteText(index, 1, 'user'); modal.close(); };
        let read;
        if (kind === 'figure') {
            const upload = field(parent, 'Upload image (JPG, PNG, WebP · max 5 MB)', '', 'file');
            upload.accept = 'image/jpeg,image/png,image/webp';
            const url = field(parent, 'Or image URL', value.src || '');
            const alt = field(parent, 'Image description (alt text)', value.alt || '');
            const caption = field(parent, 'Caption / photo credit', value.caption || '');
            const preview = document.createElement('img');
            preview.className = 'editor-upload-preview'; preview.alt = 'Image preview'; preview.hidden = !value.src;
            if (value.src) preview.src = safeImage(value.src);
            parent.append(preview);
            let temporary;
            upload.onchange = () => {
                if (temporary) URL.revokeObjectURL(temporary);
                if (upload.files[0]) { temporary = URL.createObjectURL(upload.files[0]); preview.src = temporary; preview.hidden = false; }
            };
            modal.addEventListener('close', () => { if (temporary) URL.revokeObjectURL(temporary); });
            read = async () => {
                let src = url.value.trim();
                const file = upload.files[0];
                if (file) {
                    if (file.size > 5 * 1024 * 1024) throw Error('Choose an image smaller than 5 MB.');
                    const form = new FormData(); form.append('image', file);
                    const response = await fetch(textarea.dataset.uploadUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': textarea.form.elements._token.value, Accept: 'application/json' }, body: form });
                    const result = await response.json();
                    if (!response.ok) throw Error(result.errors?.image?.[0] || result.message || 'Image upload failed.');
                    src = result.url;
                }
                if (!safeImage(src)) throw Error('Upload an image or enter a valid http/https image URL.');
                return { src, alt: alt.value.trim(), caption: caption.value.trim() };
            };
        } else {
            const caption = field(parent, 'Table title', value.caption || 'Product comparison');
            let rows = value.rows?.length ? value.rows : [['Feature', 'Product A', 'Product B'], ['Price', '', ''], ['Advantages', '', ''], ['Best for', '', '']];
            const grid = document.createElement('div'); grid.className = 'editor-table-grid'; parent.append(grid);
            const buttons = document.createElement('div'); buttons.className = 'editor-grid-actions'; parent.append(buttons);
            const collect = () => rows = [...grid.children].map(row => [...row.querySelectorAll('input')].map(input => input.value));
            const draw = () => {
                grid.replaceChildren();
                rows.forEach((row, r) => { const div = document.createElement('div'); div.style.gridTemplateColumns = `repeat(${row.length}, minmax(120px, 1fr))`; row.forEach((text, c) => { const input = document.createElement('input'); input.value = text; input.setAttribute('aria-label', `${r ? 'Row '+r : 'Header'}, column ${c+1}`); div.append(input); }); grid.append(div); });
            };
            for (const [label, action] of [
                ['Add row', () => { if (rows.length < 15) rows.push(rows[0].map(() => '')); }],
                ['Add column', () => { if (rows[0].length < 6) rows.forEach(row => row.push('')); }],
                ['Remove last row', () => { if (rows.length > 2) rows.pop(); }],
                ['Remove last column', () => { if (rows[0].length > 2) rows.forEach(row => row.pop()); }],
            ]) { const button = document.createElement('button'); button.type = 'button'; button.textContent = label; button.onclick = () => { collect(); action(); draw(); }; buttons.append(button); }
            draw();
            read = async () => { collect(); return { caption: caption.value, rows }; };
        }
        modal.querySelector('form').onsubmit = async event => {
            event.preventDefault(); submit.disabled = true;
            try {
                const block = await read();
                if (!modal.open) return;
                const change = new Delta().retain(index).delete(node ? 1 : 0).insert({ [kind]: block });
                if (!node) change.insert('\n');
                editor.updateContents(change, 'user');
                editor.setSelection(index + 2, 0, 'silent'); modal.close();
            } catch (error) { modal.querySelector('[role=alert]').textContent = error.message; }
            finally { submit.disabled = false; }
        };
        modal.showModal();
    };
    controls.querySelector('[data-image]').onclick = () => open('figure');
    controls.querySelector('[data-table]').onclick = () => open('comparison');
    editor.root.addEventListener('dblclick', event => {
        const node = event.target.closest('figure, table');
        if (node && editor.root.contains(node)) open(node.tagName === 'FIGURE' ? 'figure' : 'comparison', node);
    });
}
