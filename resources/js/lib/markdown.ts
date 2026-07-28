/**
 * A lightweight, custom regex-based parser that handles standard Markdown and basic MDX/JSX constructs.
 */
export function parseMarkdown(markdown: string | null | undefined): string {
    if (!markdown) {
        return '';
    }

    // 1. Strip import statements (common in MDX)
    let html = markdown.replace(/^import\s+.*\s+from\s+['"].*['"];?\s*$/gm, '');

    // 1b. Predefined MDX Component Toggles
    // Card with title: <Card title="My Title">content</Card>
    html = html.replace(/<Card\s+title="([^"]+)">([\s\S]*?)<\/Card>/g, '<div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-[#161615] my-6"><h4 class="text-lg font-bold text-neutral-900 dark:text-white mb-2">$1</h4><div class="text-neutral-700 dark:text-neutral-300 text-sm">$2</div></div>');
    // Card without title: <Card>content</Card>
    html = html.replace(/<Card>([\s\S]*?)<\/Card>/g, '<div class="rounded-2xl border border-black/5 bg-white p-6 shadow-sm dark:border-white/5 dark:bg-[#161615] my-6"><div class="text-neutral-700 dark:text-neutral-350 text-sm">$1</div></div>');

    // Center: <Center>content</Center>
    html = html.replace(/<Center>([\s\S]*?)<\/Center>/g, '<div class="flex flex-col items-center justify-center text-center my-6">$1</div>');

    // Accordion: <Accordion title="My Title">content</Accordion>
    html = html.replace(/<Accordion\s+title="([^"]+)">([\s\S]*?)<\/Accordion>/g, '<details class="group rounded-xl border border-neutral-200 bg-neutral-50/50 p-4 dark:border-neutral-800 dark:bg-neutral-900/30 my-4 cursor-pointer"><summary class="flex items-center justify-between font-semibold text-neutral-900 dark:text-white list-none"><span>$1</span><span class="transition-transform group-open:rotate-180"><svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg></span></summary><div class="mt-3 text-sm text-neutral-600 dark:text-neutral-350 cursor-default">$2</div></details>');

    // Badges: <Badges>tag1, tag2</Badges> or <Badges tags="tag1, tag2" />
    html = html.replace(/<Badges>([\s\S]*?)<\/Badges>/g, (_, content) => {
        const list = content.split(',').map((item: any) => String(item).trim()).filter(Boolean);
        return `<div class="flex flex-wrap gap-1.5 my-2">` + 
            list.map((item: any) => `<span class="inline-flex items-center rounded-full bg-amber-600/10 px-2.5 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">${item}</span>`).join('') +
            `</div>`;
    });
    html = html.replace(/<Badges\s+tags="([^"]+)"\s*\/>/g, (_, tags) => {
        const list = tags.split(',').map((item: any) => String(item).trim()).filter(Boolean);
        return `<div class="flex flex-wrap gap-1.5 my-2">` + 
            list.map((item: any) => `<span class="inline-flex items-center rounded-full bg-amber-600/10 px-2.5 py-0.5 text-xs font-semibold text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">${item}</span>`).join('') +
            `</div>`;
    });

    // Flex: <Flex>content</Flex>
    html = html.replace(/<Flex>([\s\S]*?)<\/Flex>/g, '<div class="flex flex-col md:flex-row items-center gap-6 my-6">$1</div>');

    // 2. Escape HTML tags, but preserve basic ones or JSX components we might support later
    // For now, let's treat it as safe HTML since it is admin-controlled.

    // 3. Blockquotes
    html = html.replace(/^\>\s+(.*)$/gm, '<blockquote class="border-l-4 border-amber-600 pl-4 italic my-4 text-neutral-600 dark:text-neutral-300">$1</blockquote>');

    // 4. Code blocks
    html = html.replace(/```(?:[a-zA-Z0-9]+)?\n([\s\S]*?)\n```/g, '<pre class="bg-neutral-100 dark:bg-neutral-900 p-4 rounded-lg overflow-x-auto my-4 text-sm font-mono">$1</pre>');

    // 5. Headings
    html = html.replace(/^#\s+(.*)$/gm, '<h1 class="text-3xl font-extrabold text-neutral-900 dark:text-white mt-6 mb-4">$1</h1>');
    html = html.replace(/^##\s+(.*)$/gm, '<h2 class="text-2xl font-bold text-neutral-900 dark:text-white mt-5 mb-3">$1</h2>');
    html = html.replace(/^###\s+(.*)$/gm, '<h3 class="text-xl font-bold text-neutral-900 dark:text-white mt-4 mb-2">$1</h3>');
    html = html.replace(/^####\s+(.*)$/gm, '<h4 class="text-lg font-semibold text-neutral-900 dark:text-white mt-3 mb-2">$1</h4>');

    // 6. Unordered lists
    // First wrap list items
    html = html.replace(/^\s*[-*+]\s+(.*)$/gm, '<li class="ml-6 list-disc text-neutral-700 dark:text-neutral-300 my-1">$1</li>');

    // 7. Ordered lists
    html = html.replace(/^\s*(\d+)\.\s+(.*)$/gm, '<li class="ml-6 list-decimal text-neutral-700 dark:text-neutral-300 my-1">$2</li>');

    // 8. Inline bold and italics
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong class="font-bold text-neutral-900 dark:text-white">$1</strong>');
    html = html.replace(/\*(.*?)\*/g, '<em class="italic">$1</em>');

    // 9. Links
    html = html.replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" class="text-amber-600 hover:underline font-medium dark:text-amber-500">$1</a>');

    // 10. Paragraphs
    // Split by double newline, filter empty blocks, wrap with <p> if not already inside block element
    const blocks = html.split(/\n{2,}/);
    html = blocks.map(block => {
        const trimmed = block.trim();
        if (!trimmed) return '';
        if (
            trimmed.startsWith('<h') || 
            trimmed.startsWith('<blockquote') || 
            trimmed.startsWith('<pre') || 
            trimmed.startsWith('<li') ||
            trimmed.startsWith('<div') ||
            trimmed.startsWith('<details')
        ) {
            return trimmed;
        }
        return `<p class="leading-relaxed text-neutral-700 dark:text-neutral-300 my-3">${trimmed}</p>`;
    }).join('\n');

    return html;
}
