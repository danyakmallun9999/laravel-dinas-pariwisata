{{--
Editor.js Component for Admin Forms
Usage: <x-admin.editorjs name="content" :value="$post->content" label="Konten" />

Wraps Editor.js in an Alpine.js component for seamless Laravel form integration.
Stores output as JSON in a hidden input.
--}}

@props([
    'name' => 'content',
    'value' => null,
    'label' => 'Konten',
    'formatName' => null,
])

@php
    $formatFieldName = $formatName ?? $name . '_format';
    $holderId = 'editorjs-' . $name . '-' . uniqid();
@endphp

<div x-data="{
        editor: null,
        editorData: {},
        holderId: '{{ $holderId }}',
        
        async init() {
            const { initEditorJs } = await window.loadEditorJs();
            
            const initialData = @js($value);
            let parsedData = {};
            
            if (initialData) {
                try {
                    parsedData = typeof initialData === 'string' ? JSON.parse(initialData) : initialData;
                } catch (e) {
                    parsedData = {};
                }
            }
            
            this.editorData = parsedData;
            
            this.editor = await initEditorJs(this.holderId, {
                data: parsedData,
                uploadUrl: '{{ route('admin.editor.upload') }}',
                csrfToken: '{{ csrf_token() }}',
                onChange: (data) => {
                    this.editorData = data;
                },
            });
        },
        
        async getContent() {
            if (this.editor) {
                const data = await this.editor.save();
                this.editorData = data;
                return JSON.stringify(data);
            }
            return JSON.stringify(this.editorData);
        },
     }"
     x-on:submit.window="
        if ($el.closest('form')) {
            const data = await getContent();
            $refs.hiddenInput.value = data;
        }
     "
     class="space-y-2">
    
    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ $label }}</label>
    
    {{-- Editor Container --}}
    <div :id="holderId" 
         class="editorjs-container border border-gray-200 rounded-xl bg-white min-h-[400px] focus-within:ring-2 focus-within:ring-blue-500/20 focus-within:border-blue-400 transition-all overflow-hidden">
    </div>
    
    {{-- Hidden inputs for form submission --}}
    <input type="hidden" 
           name="{{ $name }}" 
           x-ref="hiddenInput"
           :value="JSON.stringify(editorData)">
    <input type="hidden" name="{{ $formatFieldName }}" value="editorjs">
    
    <x-input-error :messages="$errors->get($name)" class="mt-1" />
</div>

@pushOnce('styles')
<style>
    /* Editor.js Container Styles */
    .editorjs-container .codex-editor {
        padding: 1rem 1.5rem;
    }
    .editorjs-container .codex-editor__redactor {
        padding-bottom: 200px !important;
    }
    .editorjs-container .ce-block__content {
        max-width: 100%;
    }
    .editorjs-container .ce-toolbar__content {
        max-width: 100%;
    }
    /* Clean look for the editor toolbar */
    .editorjs-container .ce-toolbar__plus,
    .editorjs-container .ce-toolbar__settings-btn {
        background: #f3f4f6;
        border-radius: 8px;
        color: #6b7280;
    }
    .editorjs-container .ce-toolbar__plus:hover,
    .editorjs-container .ce-toolbar__settings-btn:hover {
        background: #e5e7eb;
        color: #374151;
    }
    .editorjs-container .ce-inline-toolbar {
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    .editorjs-container .ce-conversion-toolbar {
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }
    /* Image tool styles */
    .editorjs-container .image-tool__image {
        border-radius: 12px;
        overflow: hidden;
    }
    /* Code block */
    .editorjs-container .ce-code__textarea {
        font-family: 'JetBrains Mono', monospace;
        background: #1e293b;
        color: #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        border: none;
        min-height: 100px;
    }
    /* Header placeholder */
    .editorjs-container h2.ce-header[contenteditable=true][data-placeholder]:empty::before,
    .editorjs-container h3.ce-header[contenteditable=true][data-placeholder]:empty::before,
    .editorjs-container h4.ce-header[contenteditable=true][data-placeholder]:empty::before {
        color: #9ca3af;
    }
</style>
@endPushOnce
