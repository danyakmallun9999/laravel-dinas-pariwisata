
/**
 * Global TinyMCE Handler for SPA (Livewire v4)
 * 
 * Handles initialization and cleanup of TinyMCE editors
 * to prevent memory leaks and ensure correct re-rendering
 * during Livewire navigation.
 */

const initTinyMCE = () => {
    if (!window.tinymce) return;

    // Remove any existing instances to prevent duplicates/zombies
    tinymce.remove('.settings-tiny');

    // Initialize with standard configuration
    tinymce.init({
        selector: '.settings-tiny',
        height: 500,
        menubar: false,
        plugins: [
            'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
            'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
            'insertdatetime', 'media', 'table', 'help', 'wordcount'
        ],
        toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
        content_style: 'body { font-family:Inter,sans-serif; font-size:14px }',
        branding: false,
        promotion: false,

        // Sync content to textarea on change for Livewire/Form submission
        setup: (editor) => {
            editor.on('change', () => {
                editor.save();
                // Trigger input event for Alpine/Livewire binding if needed
                const textarea = document.getElementById(editor.id);
                if (textarea) {
                    textarea.dispatchEvent(new Event('input', { bubbles: true }));
                }
            });
        }
    });
};

// Initialize on load
document.addEventListener('DOMContentLoaded', initTinyMCE);

// Livewire SPA Hooks
document.addEventListener('livewire:navigated', initTinyMCE);

document.addEventListener('livewire:navigating', () => {
    if (window.tinymce) {
        tinymce.remove('.settings-tiny');
    }
});
