<script lang="ts">
    import { untrack } from 'svelte';
    import { parseMarkdown } from '@/lib/markdown';
    import { themeState } from '@/lib/theme.svelte';
    import { EditorView, basicSetup } from 'codemirror';
    import { EditorState, Compartment } from '@codemirror/state';
    import { markdown } from '@codemirror/lang-markdown';
    import { oneDark } from '@codemirror/theme-one-dark';
    import { Bold, Italic, Heading1, Heading2, Heading3, Quote, Code, Link as LinkIcon, Image as ImageIcon, Eye, Edit3 } from 'lucide-svelte';

    let {
        value = $bindable(''),
        label = '',
        id = 'markdown-editor',
        rows = 12,
        error = '',
        placeholder = 'Supports markdown and MDX...',
    }: {
        value: string;
        label?: string;
        id?: string;
        rows?: number;
        error?: string;
        placeholder?: string;
    } = $props();

    const { appearance } = themeState();
    let activeTab = $state<'write' | 'preview'>('write');
    let containerRef = $state<HTMLDivElement | null>(null);
    let isUploading = $state(false);

    let editorView: EditorView | null = null;
    let lastDoc = untrack(() => value || '');
    const themeCompartment = new Compartment();

    // Initialize CodeMirror editor
    $effect(() => {
        if (!containerRef) return;

        const isDark = appearance.value === 'dark' || document.documentElement.classList.contains('dark');
        const initialDoc = untrack(() => value || '');
        lastDoc = initialDoc;

        const state = EditorState.create({
            doc: initialDoc,
            extensions: [
                basicSetup,
                markdown(),
                EditorView.lineWrapping,
                themeCompartment.of(isDark ? oneDark : []),
                EditorView.theme({
                    '&': {
                        height: `${rows * 24}px`,
                        maxHeight: '600px',
                        minHeight: '200px',
                        fontSize: '14px',
                        fontFamily: 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace',
                        borderRadius: '0 0 0.75rem 0.75rem',
                        backgroundColor: 'transparent',
                    },
                    '.cm-content': {
                        padding: '12px 16px',
                    },
                    '.cm-scroller': {
                        overflow: 'auto',
                    },
                    '&.cm-focused': {
                        outline: 'none',
                    },
                    '.cm-gutters': {
                        backgroundColor: 'transparent',
                        borderRight: '1px solid rgba(120, 120, 120, 0.15)',
                    },
                }),
                EditorView.updateListener.of((update) => {
                    if (update.docChanged) {
                        const newDoc = update.state.doc.toString();
                        lastDoc = newDoc;
                        value = newDoc;
                    }
                }),
            ],
        });

        editorView = new EditorView({
            state,
            parent: containerRef,
        });

        return () => {
            if (editorView) {
                editorView.destroy();
                editorView = null;
            }
        };
    });

    // Sync external value changes to CodeMirror
    $effect(() => {
        const val = value || '';
        if (editorView && val !== lastDoc) {
            lastDoc = val;
            untrack(() => {
                const currentDoc = editorView!.state.doc.toString();
                if (currentDoc !== val) {
                    editorView!.dispatch({
                        changes: { from: 0, to: currentDoc.length, insert: val },
                    });
                }
            });
        }
    });

    // Sync theme mode to CodeMirror
    $effect(() => {
        if (editorView) {
            const isDark = appearance.value === 'dark' || document.documentElement.classList.contains('dark');
            editorView.dispatch({
                effects: themeCompartment.reconfigure(isDark ? oneDark : []),
            });
        }
    });

    function insertAtCursor(before: string, after: string = '') {
        if (!editorView) return;

        const { main } = editorView.state.selection;
        const selected = editorView.state.sliceDoc(main.from, main.to);
        const replacement = before + selected + after;

        editorView.dispatch({
            changes: { from: main.from, to: main.to, insert: replacement },
            selection: {
                anchor: main.from + before.length,
                head: main.from + before.length + selected.length,
            },
        });
        editorView.focus();
    }

    function handleHeading(level: number) {
        insertAtCursor('#'.repeat(level) + ' ', '');
    }

    async function handleImageUpload(e: Event) {
        const target = e.target as HTMLInputElement;
        const files = target.files;
        if (!files || files.length === 0) return;

        const file = files[0];
        const formData = new FormData();
        formData.append('image', file);

        isUploading = true;

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const response = await fetch('/admin/posts/upload-image', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: formData,
            });

            if (response.ok) {
                const data = await response.json();
                insertAtCursor(`![${file.name}](${data.url})`, '');
            } else {
                alert('Failed to upload image. Max size 5MB.');
            }
        } catch (error) {
            console.error(error);
            alert('An error occurred during image upload.');
        } finally {
            isUploading = false;
            target.value = '';
        }
    }

    const previewHtml = $derived(parseMarkdown(value));
</script>

<div class="space-y-1.5 w-full">
    {#if label}
        <label for={id} class="block text-sm font-semibold text-neutral-900 dark:text-white">{label}</label>
    {/if}

    <div class="rounded-xl border border-neutral-200 overflow-hidden bg-white shadow-sm dark:border-neutral-800 dark:bg-[#161615]">
        <!-- Toolbar & Tabs Header -->
        <div class="flex flex-wrap items-center justify-between border-b border-neutral-200/80 bg-neutral-50/80 p-2 dark:border-neutral-800 dark:bg-[#0a0a0a]">
            <!-- Markdown Formatting Buttons -->
            <div class="flex flex-wrap items-center gap-1 {activeTab !== 'write' ? 'opacity-40 pointer-events-none' : ''}">
                <button type="button" onclick={() => insertAtCursor('**', '**')} class="p-1.5 rounded hover:bg-neutral-200 dark:hover:bg-neutral-800 text-neutral-600 dark:text-neutral-400 transition-colors" title="Bold">
                    <Bold class="h-4 w-4" />
                </button>
                <button type="button" onclick={() => insertAtCursor('*', '*')} class="p-1.5 rounded hover:bg-neutral-200 dark:hover:bg-neutral-800 text-neutral-600 dark:text-neutral-400 transition-colors" title="Italic">
                    <Italic class="h-4 w-4" />
                </button>
                <div class="h-4 w-px bg-neutral-200 dark:bg-neutral-800 mx-1"></div>
                <button type="button" onclick={() => handleHeading(1)} class="p-1.5 rounded hover:bg-neutral-200 dark:hover:bg-neutral-800 text-neutral-600 dark:text-neutral-400 transition-colors" title="Heading 1">
                    <Heading1 class="h-4 w-4" />
                </button>
                <button type="button" onclick={() => handleHeading(2)} class="p-1.5 rounded hover:bg-neutral-200 dark:hover:bg-neutral-800 text-neutral-600 dark:text-neutral-400 transition-colors" title="Heading 2">
                    <Heading2 class="h-4 w-4" />
                </button>
                <button type="button" onclick={() => handleHeading(3)} class="p-1.5 rounded hover:bg-neutral-200 dark:hover:bg-neutral-800 text-neutral-600 dark:text-neutral-400 transition-colors" title="Heading 3">
                    <Heading3 class="h-4 w-4" />
                </button>
                <div class="h-4 w-px bg-neutral-200 dark:bg-neutral-800 mx-1"></div>
                <button type="button" onclick={() => insertAtCursor('> ', '')} class="p-1.5 rounded hover:bg-neutral-200 dark:hover:bg-neutral-800 text-neutral-600 dark:text-neutral-400 transition-colors" title="Blockquote">
                    <Quote class="h-4 w-4" />
                </button>
                <button type="button" onclick={() => insertAtCursor('```\n', '\n```')} class="p-1.5 rounded hover:bg-neutral-200 dark:hover:bg-neutral-800 text-neutral-600 dark:text-neutral-400 transition-colors" title="Code Block">
                    <Code class="h-4 w-4" />
                </button>
                <button type="button" onclick={() => insertAtCursor('[', '](url)')} class="p-1.5 rounded hover:bg-neutral-200 dark:hover:bg-neutral-800 text-neutral-600 dark:text-neutral-400 transition-colors" title="Add Link">
                    <LinkIcon class="h-4 w-4" />
                </button>

                <!-- Image upload button -->
                <div class="relative">
                    <label class="p-1.5 rounded hover:bg-neutral-200 dark:hover:bg-neutral-800 text-neutral-600 dark:text-neutral-400 cursor-pointer flex items-center justify-center transition-colors" title="Insert Image File">
                        <ImageIcon class="h-4 w-4" />
                        <input type="file" accept="image/*" onchange={handleImageUpload} class="hidden" disabled={isUploading} />
                    </label>
                </div>
                {#if isUploading}
                    <span class="text-[10px] text-amber-600 dark:text-amber-500 font-semibold animate-pulse">Uploading Image...</span>
                {/if}
            </div>

            <!-- Tabs buttons -->
            <div class="flex gap-1 border border-neutral-200 rounded-lg p-0.5 bg-white dark:border-neutral-800 dark:bg-[#0a0a0a] ml-auto">
                <button 
                    type="button" 
                    onclick={() => activeTab = 'write'}
                    class="flex items-center gap-1 px-3 py-1 rounded-md text-xs font-semibold transition-colors {activeTab === 'write' ? 'bg-amber-600 text-white shadow-sm' : 'text-neutral-500 hover:text-neutral-800 dark:text-neutral-400 dark:hover:text-white'}"
                >
                    <Edit3 class="h-3 w-3" /> Edit
                </button>
                <button 
                    type="button" 
                    onclick={() => activeTab = 'preview'}
                    class="flex items-center gap-1 px-3 py-1 rounded-md text-xs font-semibold transition-colors {activeTab === 'preview' ? 'bg-amber-600 text-white shadow-sm' : 'text-neutral-500 hover:text-neutral-800 dark:text-neutral-400 dark:hover:text-white'}"
                >
                    <Eye class="h-3 w-3" /> Preview
                </button>
            </div>
        </div>

        <!-- CodeMirror Editor Container and Preview display -->
        <div class="w-full relative">
            <div 
                bind:this={containerRef} 
                class="w-full text-sm font-mono leading-relaxed {activeTab === 'write' ? 'block' : 'hidden'}"
            ></div>

            {#if activeTab === 'preview'}
                <div class="p-5 overflow-y-auto dark:bg-[#161615] max-h-[600px] min-h-[250px] leading-relaxed">
                    <div class="prose prose-neutral dark:prose-invert max-w-none text-sm">
                        {#if value}
                            {@html previewHtml}
                        {:else}
                            <p class="text-neutral-400 text-xs italic">Nothing to preview yet. Start writing some markdown content.</p>
                        {/if}
                    </div>
                </div>
            {/if}
        </div>
    </div>

    {#if error}
        <p class="text-xs text-red-600">{error}</p>
    {/if}
</div>
