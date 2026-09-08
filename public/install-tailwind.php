<?php
// public/install-tailwind.php
// Run this script once to download Tailwind CSS

$tailwindVersion = '3.4.1';
$tailwindCdn = "https://cdn.tailwindcss.com/3.4.1/tailwind.min.css";
$tailwindFile = __DIR__ . '/tailwind.min.css';

if (!file_exists($tailwindFile)) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $tailwindCdn);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $data = curl_exec($ch);
    curl_close($ch);

    if ($data) {
        file_put_contents($tailwindFile, $data);
        echo "✅ Tailwind CSS v{$tailwindVersion} downloaded successfully!\n";
        echo "📁 File saved at: public/tailwind.min.css\n";
    } else {
        echo "❌ Failed to download Tailwind CSS\n";
    }
} else {
    echo "✅ Tailwind CSS already exists at: public/tailwind.min.css\n";
}

// Create custom.css file
$customCss = __DIR__ . '/custom.css';
if (!file_exists($customCss)) {
    $cssContent = <<<'CSS'
/* ===== CUSTOM STYLES ===== */
:root {
    --brand-color: #e30613;
    --brand-light: #ff1e1e;
    --brand-dark: #b3000b;
}

/* Typography */
* {
    font-family: 'Noto Sans Devanagari', 'Inter', sans-serif;
    box-sizing: border-box;
}
html, body {
    overflow-x: hidden;
    background-color: #f1f5f9;
    color: #0f172a;
}

/* Container */
.container {
    width: 100%;
    max-width: 1280px;
    margin-left: auto;
    margin-right: auto;
    padding-left: 1rem;
    padding-right: 1rem;
}
@media (min-width: 768px) {
    .container {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
    }
}

/* Flex Utilities */
.flex { display: flex; }
.inline-flex { display: inline-flex; }
.flex-col { flex-direction: column; }
.flex-wrap { flex-wrap: wrap; }
.flex-1 { flex: 1 1 0%; }
.flex-shrink-0 { flex-shrink: 0; }
.items-center { align-items: center; }
.justify-center { justify-content: center; }
.justify-between { justify-content: space-between; }
.gap-1 { gap: 0.25rem; }
.gap-2 { gap: 0.5rem; }
.gap-3 { gap: 0.75rem; }
.gap-4 { gap: 1rem; }
.gap-5 { gap: 1.25rem; }
.gap-6 { gap: 1.5rem; }
.gap-8 { gap: 2rem; }

/* Grid */
.grid { display: grid; }
.grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
@media (min-width: 768px) {
    .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
}
@media (min-width: 1024px) {
    .lg\:grid-cols-4 { grid-template-columns: repeat(4, minmax(0, 1fr)); }
    .lg\:col-span-2 { grid-column: span 2 / span 2; }
}

/* Spacing */
.p-2 { padding: 0.5rem; }
.p-3 { padding: 0.75rem; }
.p-4 { padding: 1rem; }
.p-5 { padding: 1.25rem; }
.p-6 { padding: 1.5rem; }
.py-0\.5 { padding-top: 0.125rem; padding-bottom: 0.125rem; }
.py-1 { padding-top: 0.25rem; padding-bottom: 0.25rem; }
.py-2 { padding-top: 0.5rem; padding-bottom: 0.5rem; }
.py-2\.5 { padding-top: 0.625rem; padding-bottom: 0.625rem; }
.py-3 { padding-top: 0.75rem; padding-bottom: 0.75rem; }
.py-4 { padding-top: 1rem; padding-bottom: 1rem; }
.py-5 { padding-top: 1.25rem; padding-bottom: 1.25rem; }
.py-6 { padding-top: 1.5rem; padding-bottom: 1.5rem; }
.py-8 { padding-top: 2rem; padding-bottom: 2rem; }
.py-12 { padding-top: 3rem; padding-bottom: 3rem; }
.px-1\.5 { padding-left: 0.375rem; padding-right: 0.375rem; }
.px-2 { padding-left: 0.5rem; padding-right: 0.5rem; }
.px-2\.5 { padding-left: 0.625rem; padding-right: 0.625rem; }
.px-3 { padding-left: 0.75rem; padding-right: 0.75rem; }
.px-4 { padding-left: 1rem; padding-right: 1rem; }
.px-5 { padding-left: 1.25rem; padding-right: 1.25rem; }
.px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
.pt-4 { padding-top: 1rem; }
.pt-5 { padding-top: 1.25rem; }
.pt-6 { padding-top: 1.5rem; }
.pb-1 { padding-bottom: 0.25rem; }
.pb-8 { padding-bottom: 2rem; }
.pb-12 { padding-bottom: 3rem; }
.pb-24 { padding-bottom: 6rem; }
.m-0 { margin: 0; }
.mx-auto { margin-left: auto; margin-right: auto; }
.my-1 { margin-top: 0.25rem; margin-bottom: 0.25rem; }
.mt-1 { margin-top: 0.25rem; }
.mt-2 { margin-top: 0.5rem; }
.mt-4 { margin-top: 1rem; }
.mb-1 { margin-bottom: 0.25rem; }
.mb-2 { margin-bottom: 0.5rem; }
.mb-3 { margin-bottom: 0.75rem; }
.mb-4 { margin-bottom: 1rem; }
.mb-8 { margin-bottom: 2rem; }
.-ml-2 { margin-left: -0.5rem; }

/* Typography */
.text-xs { font-size: 0.75rem; line-height: 1rem; }
.text-sm { font-size: 0.875rem; line-height: 1.25rem; }
.text-base { font-size: 1rem; line-height: 1.5rem; }
.text-lg { font-size: 1.125rem; line-height: 1.75rem; }
.text-xl { font-size: 1.25rem; line-height: 1.75rem; }
.text-2xl { font-size: 1.5rem; line-height: 2rem; }
.text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
.text-4xl { font-size: 2.25rem; line-height: 2.5rem; }
.font-bold { font-weight: 700; }
.font-black { font-weight: 900; }
.font-semibold { font-weight: 600; }
.uppercase { text-transform: uppercase; }
.tracking-tight { letter-spacing: -0.025em; }
.tracking-wider { letter-spacing: 0.05em; }
.tracking-widest { letter-spacing: 0.1em; }
.leading-tight { line-height: 1.25; }
.leading-relaxed { line-height: 1.625; }
.text-white { color: #ffffff; }
.text-slate-200 { color: #e2e8f0; }
.text-slate-300 { color: #cbd5e1; }
.text-slate-400 { color: #94a3b8; }
.text-slate-500 { color: #64748b; }
.text-slate-600 { color: #475569; }
.text-slate-700 { color: #334155; }
.text-slate-800 { color: #1e293b; }
.text-slate-900 { color: #0f172a; }
.text-slate-950 { color: #020617; }
.text-brand { color: var(--brand-color); }
.text-amber-400 { color: #fbbf24; }
.text-green-400 { color: #4ade80; }
.text-red-400 { color: #f87171; }
.text-yellow-400 { color: #facc15; }
.text-muted { color: rgba(255,255,255,0.5); }

/* Backgrounds */
.bg-white { background-color: #ffffff; }
.bg-slate-50 { background-color: #f8fafc; }
.bg-slate-100 { background-color: #f1f5f9; }
.bg-slate-200 { background-color: #e2e8f0; }
.bg-slate-800 { background-color: #1e293b; }
.bg-slate-900 { background-color: #0f172a; }
.bg-slate-950 { background-color: #020617; }
.bg-brand { background-color: var(--brand-color); }
.bg-red-600 { background-color: #dc2626; }
.bg-green-600 { background-color: #16a34a; }
.bg-amber-500 { background-color: #f59e0b; }
.bg-red-50 { background-color: #fef2f2; }
.bg-white\/10 { background-color: rgba(255,255,255,0.1); }
.bg-white\/20 { background-color: rgba(255,255,255,0.2); }

/* Borders */
.border { border-width: 1px; }
.border-0 { border-width: 0; }
.border-b { border-bottom-width: 1px; }
.border-t { border-top-width: 1px; }
.border-2 { border-width: 2px; }
.border-4 { border-width: 4px; }
.border-brand { border-color: var(--brand-color); }
.border-slate-100 { border-color: #f1f5f9; }
.border-slate-200 { border-color: #e2e8f0; }
.border-slate-300 { border-color: #cbd5e1; }
.border-slate-700 { border-color: #334155; }
.border-slate-800 { border-color: #1e293b; }
.border-slate-800\/40 { border-color: rgba(30,41,59,0.4); }
.border-slate-800\/60 { border-color: rgba(30,41,59,0.6); }
.rounded { border-radius: 0.25rem; }
.rounded-lg { border-radius: 0.5rem; }
.rounded-xl { border-radius: 0.75rem; }
.rounded-full { border-radius: 9999px; }
.rounded-none { border-radius: 0; }

/* Shadows */
.shadow { box-shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1); }
.shadow-md { box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1); }
.shadow-lg { box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1); }
.shadow-xl { box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1); }
.shadow-inner { box-shadow: inset 0 2px 4px 0 rgb(0 0 0 / 0.05); }

/* Hover Effects */
.hover\:bg-brand:hover { background-color: var(--brand-color); }
.hover\:bg-slate-100:hover { background-color: #f1f5f9; }
.hover\:bg-red-700:hover { background-color: #b91c1c; }
.hover\:bg-amber-500:hover { background-color: #f59e0b; }
.hover\:bg-red-50:hover { background-color: #fef2f2; }
.hover\:bg-white\/20:hover { background-color: rgba(255,255,255,0.2); }
.hover\:text-white:hover { color: #ffffff; }
.hover\:text-brand:hover { color: var(--brand-color); }
.hover\:text-amber-500:hover { color: #f59e0b; }
.hover\:text-slate-950:hover { color: #020617; }

/* Transitions */
.transition { transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
.transition-all { transition-property: all; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 150ms; }
.duration-200 { transition-duration: 200ms; }

/* Positioning */
.relative { position: relative; }
.absolute { position: absolute; }
.sticky { position: sticky; }
.fixed { position: fixed; }
.top-0 { top: 0; }
.top-1\/2 { top: 50%; }
.left-0 { left: 0; }
.left-4 { left: 1rem; }
.left-full { left: 100%; }
.right-2 { right: 0.5rem; }
.inset-0 { top: 0; right: 0; bottom: 0; left: 0; }
.inset-x-0 { left: 0; right: 0; }
.-translate-y-1\/2 { transform: translateY(-50%); }
.z-10 { z-index: 10; }
.z-50 { z-index: 50; }
.z-\[100000\] { z-index: 100000; }

/* Width & Height */
.w-full { width: 100%; }
.w-6 { width: 1.5rem; }
.w-8 { width: 2rem; }
.w-44 { width: 11rem; }
.w-52 { width: 13rem; }
.w-auto { width: auto; }
.w-px { width: 1px; }
.h-6 { height: 1.5rem; }
.h-8 { height: 2rem; }
.h-10 { height: 2.5rem; }
.h-11 { height: 2.75rem; }
.h-16 { height: 4rem; }
.h-5 { height: 1.25rem; }
.h-auto { height: auto; }
.h-screen { height: 100vh; }
.min-h-\[80px\] { min-height: 80px; }
.max-w-7xl { max-width: 80rem; }
.max-w-md { max-width: 28rem; }
.max-w-\[360px\] { max-width: 360px; }
.max-w-full { max-width: 100%; }
.object-contain { object-fit: contain; }
.overflow-y-auto { overflow-y: auto; }
.overflow-x-hidden { overflow-x: hidden; }
.overflow-x-visible { overflow-x: visible; }

/* Display */
.block { display: block; }
.inline-block { display: inline-block; }
.hidden { display: none; }
@media (min-width: 768px) {
    .md\:block { display: block; }
    .md\:hidden { display: none; }
    .md\:static { position: static; }
    .md\:inset-auto { top: auto; right: auto; bottom: auto; left: auto; }
    .md\:mx-0 { margin-left: 0; margin-right: 0; }
    .md\:mt-0 { margin-top: 0; }
    .md\:inline-block { display: inline-block; }
    .md\:min-w-\[200px\] { min-width: 200px; }
    .md\:h-16 { height: 4rem; }
    .md\:text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
    .md\:text-\[10px\] { font-size: 10px; }
    .md\:pb-8 { padding-bottom: 2rem; }
    .md\:pb-12 { padding-bottom: 3rem; }
    .md\:px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
    .md\:justify-start { justify-content: flex-start; }
    .md\:pointer-events-auto { pointer-events: auto; }
}
@media (min-width: 1024px) {
    .lg\:col-span-2 { grid-column: span 2 / span 2; }
}

/* Animations */
.animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}

/* Misc */
.whitespace-nowrap { white-space: nowrap; }
.list-unstyled { list-style: none; padding-left: 0; }
.cursor-pointer { cursor: pointer; }
.pointer-events-none { pointer-events: none; }
.space-y-1 > * + * { margin-top: 0.25rem; }
.space-y-2 > * + * { margin-top: 0.5rem; }
.gap-\[10px\] { gap: 10px; }
CSS;

    file_put_contents($customCss, $cssContent);
    echo "✅ custom.css created successfully!\n";
    echo "📁 File saved at: public/custom.css\n";
} else {
    echo "✅ custom.css already exists\n";
}

// Create combined file
$tailwindContent = file_get_contents($tailwindFile);
$customContent = file_get_contents($customCss);
$combinedContent = "/* ===== TAILWIND CSS ===== */\n" . $tailwindContent . "\n\n/* ===== CUSTOM STYLES ===== */\n" . $customContent;
file_put_contents(__DIR__ . '/styles.css', $combinedContent);

echo "\n✅ Combined CSS file created: public/styles.css\n";
echo "✅ You can now use: <link rel=\"stylesheet\" href=\"/styles.css\">\n";
echo "\n🚀 Done! Tailwind CSS installed successfully!\n";