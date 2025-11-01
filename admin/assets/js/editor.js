/**
 * Simple Rich Text Editor for Content Management
 */

class SimpleEditor {
    constructor(textarea) {
        this.textarea = textarea;
        this.toolbar = null;
        this.init();
    }

    init() {
        this.createToolbar();
        this.setupEventListeners();
        this.textarea.style.display = 'none';
        
        // Create editable div
        this.editor = document.createElement('div');
        this.editor.className = 'simple-editor';
        this.editor.contentEditable = true;
        this.editor.innerHTML = this.textarea.value || '<p>Start typing...</p>';
        
        this.textarea.parentNode.insertBefore(this.editor, this.textarea.nextSibling);
        
        // Sync content
        this.editor.addEventListener('input', () => {
            this.textarea.value = this.editor.innerHTML;
        });
    }

    createToolbar() {
        this.toolbar = document.createElement('div');
        this.toolbar.className = 'editor-toolbar';
        
        const buttons = [
            { command: 'bold', icon: 'B', title: 'Bold' },
            { command: 'italic', icon: 'I', title: 'Italic' },
            { command: 'underline', icon: 'U', title: 'Underline' },
            { command: 'separator' },
            { command: 'formatBlock', value: 'h2', icon: 'H2', title: 'Heading 2' },
            { command: 'formatBlock', value: 'h3', icon: 'H3', title: 'Heading 3' },
            { command: 'formatBlock', value: 'p', icon: 'P', title: 'Paragraph' },
            { command: 'separator' },
            { command: 'insertUnorderedList', icon: '•', title: 'Bullet List' },
            { command: 'insertOrderedList', icon: '1.', title: 'Numbered List' },
            { command: 'separator' },
            { command: 'createLink', icon: '🔗', title: 'Insert Link' },
            { command: 'unlink', icon: '🔗⃠', title: 'Remove Link' },
            { command: 'separator' },
            { command: 'justifyLeft', icon: '⬅', title: 'Align Left' },
            { command: 'justifyCenter', icon: '⬌', title: 'Align Center' },
            { command: 'justifyRight', icon: '➡', title: 'Align Right' }
        ];

        buttons.forEach(btn => {
            if (btn.command === 'separator') {
                const separator = document.createElement('div');
                separator.className = 'toolbar-separator';
                this.toolbar.appendChild(separator);
            } else {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'toolbar-btn';
                button.innerHTML = btn.icon;
                button.title = btn.title;
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.execCommand(btn.command, btn.value);
                });
                this.toolbar.appendChild(button);
            }
        });

        this.textarea.parentNode.insertBefore(this.toolbar, this.textarea);
    }

    setupEventListeners() {
        // Handle keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.target === this.editor) {
                if (e.ctrlKey || e.metaKey) {
                    switch (e.key) {
                        case 'b':
                            e.preventDefault();
                            this.execCommand('bold');
                            break;
                        case 'i':
                            e.preventDefault();
                            this.execCommand('italic');
                            break;
                        case 'u':
                            e.preventDefault();
                            this.execCommand('underline');
                            break;
                    }
                }
            }
        });
    }

    execCommand(command, value = null) {
        this.editor.focus();
        
        if (command === 'createLink') {
            const url = prompt('Enter URL:');
            if (url) {
                document.execCommand(command, false, url);
            }
        } else {
            document.execCommand(command, false, value);
        }
        
        this.textarea.value = this.editor.innerHTML;
    }
}

// Initialize editors on page load
document.addEventListener('DOMContentLoaded', function() {
    const contentEditors = document.querySelectorAll('.content-editor');
    contentEditors.forEach(textarea => {
        if (!textarea.classList.contains('no-editor')) {
            new SimpleEditor(textarea);
        }
    });
});