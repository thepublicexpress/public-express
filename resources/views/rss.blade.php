@php
    ob_clean();
    header('Content-Type: application/xml; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    error_reporting(0);
    echo '<?xml version="1.0" encoding="UTF-8"?>';
@endphp
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
    <channel>
        <title>द पब्लिक एक्सप्रेस - RSS Feed</title>
        <link>{{ url('/') }}</link>
        <description>हर कस्बे, गाँव और सिटी की खबरें - ताजा हिंदी समाचार</description>
        <language>hi</language>
        <lastBuildDate>{{ now()->format('D, d M Y H:i:s O') }}</lastBuildDate>
        <atom:link href="{{ url('/rss') }}" rel="self" type="application/rss+xml" />

        @foreach($news as $item)
        <item>
            <title>{{ htmlspecialchars(trim($item->title), ENT_XML1, 'UTF-8') }}</title>
            <link>{{ route('news.show', $item->slug) }}</link>
            <guid isPermaLink="true">{{ route('news.show', $item->slug) }}</guid>
            <description><![CDATA[
                @if($item->featured_image)
                    <img src="{{ asset($item->featured_image) }}" alt="{{ htmlspecialchars($item->alt_text ?? $item->title, ENT_XML1, 'UTF-8') }}">
                @endif
                {{ trim(strip_tags($item->summary ?? Str::limit($item->body, 200))) }}
            ]]></description>
            <pubDate>{{ \Carbon\Carbon::parse($item->published_at ?? $item->created_at)->format('D, d M Y H:i:s O') }}</pubDate>
            <category>{{ htmlspecialchars($item->category->name ?? 'General', ENT_XML1, 'UTF-8') }}</category>
        </item>
        @endforeach
    </channel>
</rss>