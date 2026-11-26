<!-- SEO Meta Content -->
@push('meta')
    <meta name="title" content="{{ $page->meta_title }}" />
    <meta name="description" content="{{ $page->meta_description }}" />
    <meta name="keywords" content="{{ $page->meta_keywords }}" />
@endPush

@push('styles')
<style>
    .cms-page-hero {
        background: #f8fafc;
        border-bottom: 1px solid #e5e7eb;
        padding: 40px 24px 36px;
        text-align: center;
        margin-bottom: 0;
    }
    .cms-page-hero h1 {
        font-size: 32px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 10px;
        line-height: 1.25;
        letter-spacing: -0.02em;
    }
    .cms-page-breadcrumb {
        font-size: 13px;
        color: #94a3b8;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .cms-page-breadcrumb a {
        color: #64748b;
        text-decoration: none;
    }
    .cms-page-breadcrumb a:hover {
        color: #0f172a;
    }
    .cms-page-wrapper {
        max-width: 860px;
        margin: 0 auto;
        padding: 48px 24px 72px;
    }
    .cms-page-wrapper h1,
    .cms-page-wrapper h2,
    .cms-page-wrapper h3,
    .cms-page-wrapper h4 {
        color: #0f172a;
        font-weight: 700;
        line-height: 1.3;
        margin: 32px 0 12px;
    }
    .cms-page-wrapper h2 { font-size: 22px; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; }
    .cms-page-wrapper h3 { font-size: 18px; }
    .cms-page-wrapper p {
        color: #374151;
        font-size: 15.5px;
        line-height: 1.8;
        margin: 0 0 16px;
    }
    .cms-page-wrapper ul,
    .cms-page-wrapper ol {
        color: #374151;
        font-size: 15.5px;
        line-height: 1.8;
        padding-left: 24px;
        margin: 0 0 16px;
    }
    .cms-page-wrapper li { margin-bottom: 6px; }
    .cms-page-wrapper a { color: #2563eb; text-decoration: underline; }
    .cms-page-wrapper a:hover { color: #1d4ed8; }
    .cms-page-wrapper strong { color: #0f172a; }
    .cms-page-wrapper table {
        width: 100%;
        border-collapse: collapse;
        margin: 24px 0;
        font-size: 14px;
    }
    .cms-page-wrapper table th {
        background: #f8fafc;
        padding: 10px 14px;
        text-align: left;
        font-weight: 600;
        border: 1px solid #e2e8f0;
        color: #0f172a;
    }
    .cms-page-wrapper table td {
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        color: #374151;
    }
    .cms-page-wrapper table tr:nth-child(even) td { background: #f8fafc; }
    .cms-page-wrapper blockquote {
        border-left: 4px solid #2563eb;
        background: #eff6ff;
        margin: 24px 0;
        padding: 16px 20px;
        border-radius: 0 8px 8px 0;
        font-style: italic;
        color: #1e40af;
    }
    .cms-page-wrapper hr {
        border: none;
        border-top: 1px solid #e2e8f0;
        margin: 32px 0;
    }
    @media (max-width: 768px) {
        .cms-page-hero h1 { font-size: 26px; }
        .cms-page-hero { padding: 40px 16px 36px; }
        .cms-page-wrapper { padding: 32px 16px 56px; }
        .cms-page-wrapper h2 { font-size: 19px; }
        .cms-page-wrapper p,
        .cms-page-wrapper ul,
        .cms-page-wrapper ol { font-size: 14.5px; }
    }
</style>
@endpush

<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title (browser tab) -->
    <x-slot:title>
        {{ $page->meta_title ?? $page->page_title }}
    </x-slot>

    <!-- Hero Banner with Page Title -->
    <div class="cms-page-hero">
        <h1>{{ $page->page_title }}</h1>
        <div class="cms-page-breadcrumb">
            <a href="{{ route('shop.home.index') }}">Home</a>
            <span>›</span>
            <span>{{ $page->page_title }}</span>
        </div>
    </div>

    <!-- Page Content -->
    <div class="cms-page-wrapper">
        {!! $page->html_content !!}
    </div>
</x-shop::layouts>