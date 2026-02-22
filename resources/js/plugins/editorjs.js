/**
 * Editor.js Initialization Module
 * 
 * Import and configure Editor.js with all required tools.
 * Used by the <x-admin.editorjs> Blade component.
 */

import EditorJS from '@editorjs/editorjs';
import Header from '@editorjs/header';
import Paragraph from '@editorjs/paragraph';
import List from '@editorjs/list';
import ImageTool from '@editorjs/image';
import Embed from '@editorjs/embed';
import Table from '@editorjs/table';
import Quote from '@editorjs/quote';
import Delimiter from '@editorjs/delimiter';
import CodeTool from '@editorjs/code';
import Warning from '@editorjs/warning';
import Checklist from '@editorjs/checklist';
import RawTool from '@editorjs/raw';

// Inline tools
import Marker from '@editorjs/marker';
import InlineCode from '@editorjs/inline-code';
import Underline from '@editorjs/underline';
import Strikethrough from 'editorjs-strikethrough';
import ColorPlugin from 'editorjs-text-color-plugin';

// Block tunes
import AlignmentTune from 'editorjs-text-alignment-blocktune';

/**
 * Initialize an Editor.js instance.
 * 
 * @param {string} holderId - DOM element ID to mount the editor
 * @param {Object} options - Configuration options
 * @param {Object} options.data - Initial editor data (Editor.js JSON format)
 * @param {string} options.uploadUrl - Image upload endpoint URL
 * @param {string} options.csrfToken - CSRF token for upload requests
 * @param {Function} options.onChange - Callback fired on content change  
 * @returns {Promise<EditorJS>}
 */
export function initEditorJs(holderId, options = {}) {
    const { data, uploadUrl, csrfToken, onChange } = options;

    return new EditorJS({
        holder: holderId,
        placeholder: 'Tulis konten di sini...',
        data: data || {},
        minHeight: 300,

        // Enable inline toolbar with specific tools order
        inlineToolbar: ['bold', 'italic', 'underline', 'strikethrough', 'marker', 'Color', 'inlineCode', 'link'],

        onChange(api, event) {
            if (onChange) {
                api.saver.save().then(outputData => onChange(outputData));
            }
        },

        // Block tunes (applied to all blocks)
        tunes: ['textAlignment'],

        tools: {
            // ── Block Tools ──────────────────────────────────
            paragraph: {
                class: Paragraph,
                inlineToolbar: true,
                config: {
                    placeholder: 'Tulis konten...',
                    preserveBlank: true,
                },
            },
            header: {
                class: Header,
                config: {
                    levels: [2, 3, 4],
                    defaultLevel: 2,
                    placeholder: 'Judul...',
                },
                inlineToolbar: true,
                shortcut: 'CMD+SHIFT+H',
            },
            list: {
                class: List,
                inlineToolbar: true,
                config: {
                    defaultStyle: 'unordered',
                },
            },
            checklist: {
                class: Checklist,
                inlineToolbar: true,
            },
            image: {
                class: ImageTool,
                config: {
                    endpoints: {
                        byFile: uploadUrl,
                        byUrl: uploadUrl + '?type=url',
                    },
                    additionalRequestHeaders: {
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    field: 'image',
                    captionPlaceholder: 'Keterangan gambar...',
                    types: 'image/jpeg,image/png,image/webp,image/gif',
                },
            },
            embed: {
                class: Embed,
                config: {
                    services: {
                        youtube: true,
                        vimeo: true,
                        instagram: true,
                        twitter: true,
                    },
                },
            },
            table: {
                class: Table,
                inlineToolbar: true,
                config: {
                    rows: 2,
                    cols: 3,
                    withHeadings: true,
                },
            },
            quote: {
                class: Quote,
                inlineToolbar: true,
                config: {
                    quotePlaceholder: 'Tulis kutipan...',
                    captionPlaceholder: 'Sumber kutipan...',
                },
            },
            delimiter: Delimiter,
            code: {
                class: CodeTool,
                config: {
                    placeholder: 'Tulis kode...',
                },
            },
            warning: {
                class: Warning,
                config: {
                    titlePlaceholder: 'Judul peringatan',
                    messagePlaceholder: 'Isi peringatan...',
                },
            },
            raw: {
                class: RawTool,
                config: {
                    placeholder: 'Paste HTML di sini...',
                },
            },

            // ── Inline Tools ─────────────────────────────────
            marker: {
                class: Marker,
                shortcut: 'CMD+SHIFT+M',
            },
            inlineCode: {
                class: InlineCode,
                shortcut: 'CMD+SHIFT+C',
            },
            underline: Underline,
            strikethrough: Strikethrough,
            Color: {
                class: ColorPlugin,
                config: {
                    colorCollections: [
                        '#EC7878', '#9C27B0', '#673AB7', '#3F51B5',
                        '#0070FF', '#03A9F4', '#00BCD4', '#4CAF50',
                        '#8BC34A', '#CDDC39', '#FFF', '#FF9800',
                        '#FF5722', '#795548', '#607D8B', '#000000',
                    ],
                    defaultColor: '#FF1300',
                    type: 'text',
                    customPicker: true,
                },
            },

            // ── Block Tunes ──────────────────────────────────
            textAlignment: {
                class: AlignmentTune,
                config: {
                    default: 'left',
                    blocks: {
                        header: 'left',
                        paragraph: 'left',
                        list: 'left',
                    },
                },
            },
        },
    });
}
