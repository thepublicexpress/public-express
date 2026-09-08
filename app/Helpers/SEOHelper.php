<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;

class SEOHelper
{
    /**
     * Default site name
     */
    protected static $siteName = 'द पब्लिक एक्सप्रेस';

    /**
     * Default site description
     */
    protected static $siteDescription = 'द पब्लिक एक्सप्रेस – हर कस्बे, गाँव और सिटी की खबरें। ताजा हिंदी समाचार, राजनीति, शिक्षा, खेल और मनोरंजन।';

    /**
     * Generate unique page title
     *
     * @param string|null $title
     * @param string|null $siteName
     * @param bool $appendSiteName
     * @return string
     */
    public static function generateTitle($title = null, $siteName = null, $appendSiteName = true)
    {
        $siteName = $siteName ?? self::getSiteName();
        
        // If no title provided, return site name
        if (empty($title)) {
            return $siteName;
        }

        // If title already contains site name, return as is
        if (str_contains($title, $siteName)) {
            return $title;
        }

        // For long titles, truncate and add site name
        if (strlen($title) > 60) {
            $title = Str::limit($title, 55);
        }

        return $appendSiteName ? $title . ' - ' . $siteName : $title;
    }

    /**
     * Generate unique meta description
     *
     * @param string|null $description
     * @param string|null $defaultDescription
     * @param int $limit
     * @return string
     */
    public static function generateDescription($description = null, $defaultDescription = null, $limit = 160)
    {
        $defaultDescription = $defaultDescription ?? self::getSiteDescription();

        // If description provided, clean and limit
        if (!empty($description)) {
            // Strip HTML tags
            $cleanDescription = strip_tags($description);
            // Remove extra whitespace
            $cleanDescription = preg_replace('/\s+/', ' ', $cleanDescription);
            // Limit length
            return Str::limit($cleanDescription, $limit);
        }

        return Str::limit($defaultDescription, $limit);
    }

    /**
     * Generate keywords from title and description
     *
     * @param string|null $title
     * @param string|null $description
     * @param int $limit
     * @return string
     */
    public static function generateKeywords($title = null, $description = null, $limit = 10)
    {
        $keywords = [];

        // Extract words from title
        if ($title) {
            $titleWords = preg_split('/\s+/', strip_tags($title), -1, PREG_SPLIT_NO_EMPTY);
            $keywords = array_merge($keywords, $titleWords);
        }

        // Extract words from description
        if ($description) {
            $descWords = preg_split('/\s+/', strip_tags($description), -1, PREG_SPLIT_NO_EMPTY);
            $keywords = array_merge($keywords, $descWords);
        }

        // Remove duplicates
        $keywords = array_unique($keywords);
        
        // Limit keywords
        $keywords = array_slice($keywords, 0, $limit);
        
        // Add site name if not present
        $siteName = self::getSiteName();
        if (!in_array($siteName, $keywords)) {
            $keywords[] = $siteName;
        }

        return implode(', ', $keywords);
    }

    /**
     * Generate Open Graph meta tags
     *
     * @param array $data
     * @return string
     */
    public static function generateOpenGraphTags($data = [])
    {
        $defaults = [
            'title' => self::generateTitle(),
            'description' => self::generateDescription(),
            'url' => url()->current(),
            'image' => asset('images/logo.png'),
            'type' => 'website',
            'site_name' => self::getSiteName(),
            'locale' => 'hi_IN',
        ];

        $merged = array_merge($defaults, $data);

        $html = "\n<!-- Open Graph Tags -->\n";
        $html .= '<meta property="og:title" content="' . e($merged['title']) . '">' . "\n";
        $html .= '<meta property="og:description" content="' . e($merged['description']) . '">' . "\n";
        $html .= '<meta property="og:url" content="' . e($merged['url']) . '">' . "\n";
        $html .= '<meta property="og:type" content="' . e($merged['type']) . '">' . "\n";
        $html .= '<meta property="og:image" content="' . e($merged['image']) . '">' . "\n";
        
        // Add image width/height if available
        if (isset($merged['image_width']) && isset($merged['image_height'])) {
            $html .= '<meta property="og:image:width" content="' . e($merged['image_width']) . '">' . "\n";
            $html .= '<meta property="og:image:height" content="' . e($merged['image_height']) . '">' . "\n";
        }
        
        $html .= '<meta property="og:site_name" content="' . e($merged['site_name']) . '">' . "\n";
        $html .= '<meta property="og:locale" content="' . e($merged['locale']) . '">' . "\n";

        // Article specific tags
        if (isset($merged['type']) && $merged['type'] === 'article') {
            if (isset($merged['published_time'])) {
                $html .= '<meta property="article:published_time" content="' . e($merged['published_time']) . '">' . "\n";
            }
            if (isset($merged['modified_time'])) {
                $html .= '<meta property="article:modified_time" content="' . e($merged['modified_time']) . '">' . "\n";
            }
            if (isset($merged['author'])) {
                $html .= '<meta property="article:author" content="' . e($merged['author']) . '">' . "\n";
            }
            if (isset($merged['category'])) {
                $html .= '<meta property="article:section" content="' . e($merged['category']) . '">' . "\n";
            }
            if (isset($merged['tags']) && is_array($merged['tags'])) {
                foreach ($merged['tags'] as $tag) {
                    $html .= '<meta property="article:tag" content="' . e($tag) . '">' . "\n";
                }
            }
        }

        return $html;
    }

    /**
     * Generate Twitter Card meta tags
     *
     * @param array $data
     * @return string
     */
    public static function generateTwitterCardTags($data = [])
    {
        $defaults = [
            'card' => 'summary_large_image',
            'title' => self::generateTitle(),
            'description' => self::generateDescription(),
            'image' => asset('images/logo.png'),
            'site' => '@thepublicexpress',
            'creator' => '@thepublicexpress',
        ];

        $merged = array_merge($defaults, $data);

        $html = "\n<!-- Twitter Card Tags -->\n";
        $html .= '<meta name="twitter:card" content="' . e($merged['card']) . '">' . "\n";
        $html .= '<meta name="twitter:title" content="' . e($merged['title']) . '">' . "\n";
        $html .= '<meta name="twitter:description" content="' . e($merged['description']) . '">' . "\n";
        $html .= '<meta name="twitter:image" content="' . e($merged['image']) . '">' . "\n";
        $html .= '<meta name="twitter:site" content="' . e($merged['site']) . '">' . "\n";
        
        if (isset($merged['creator']) && !empty($merged['creator'])) {
            $html .= '<meta name="twitter:creator" content="' . e($merged['creator']) . '">' . "\n";
        }

        return $html;
    }

    /**
     * Generate complete SEO meta tags
     *
     * @param array $data
     * @return string
     */
    public static function generateAllMetaTags($data = [])
    {
        $defaults = [
            'title' => null,
            'description' => null,
            'keywords' => null,
            'url' => url()->current(),
            'image' => asset('images/logo.png'),
            'type' => 'website',
            'site_name' => self::getSiteName(),
            'twitter_card' => 'summary_large_image',
            'twitter_site' => '@thepublicexpress',
            'twitter_creator' => '@thepublicexpress',
            'robots' => 'index, follow',
            'canonical' => url()->current(),
        ];

        $merged = array_merge($defaults, $data);

        // Generate title
        $title = self::generateTitle($merged['title'], $merged['site_name']);
        
        // Generate description
        $description = self::generateDescription($merged['description']);
        
        // Generate keywords if not provided
        $keywords = $merged['keywords'] ?? self::generateKeywords($title, $description);

        $html = "\n<!-- SEO Meta Tags -->\n";
        $html .= '<title>' . e($title) . '</title>' . "\n";
        $html .= '<meta name="description" content="' . e($description) . '">' . "\n";
        
        if (!empty($keywords)) {
            $html .= '<meta name="keywords" content="' . e($keywords) . '">' . "\n";
        }
        
        $html .= '<meta name="robots" content="' . e($merged['robots']) . '">' . "\n";
        
        if ($merged['canonical']) {
            $html .= '<link rel="canonical" href="' . e($merged['canonical']) . '">' . "\n";
        }

        // Add Open Graph tags
        $ogData = [
            'title' => $title,
            'description' => $description,
            'url' => $merged['url'],
            'image' => $merged['image'],
            'type' => $merged['type'],
            'site_name' => $merged['site_name'],
        ];
        
        if (isset($merged['published_time'])) {
            $ogData['published_time'] = $merged['published_time'];
        }
        if (isset($merged['modified_time'])) {
            $ogData['modified_time'] = $merged['modified_time'];
        }
        if (isset($merged['author'])) {
            $ogData['author'] = $merged['author'];
        }
        if (isset($merged['category'])) {
            $ogData['category'] = $merged['category'];
        }
        if (isset($merged['tags'])) {
            $ogData['tags'] = $merged['tags'];
        }
        
        $html .= self::generateOpenGraphTags($ogData);

        // Add Twitter Card tags
        $twitterData = [
            'card' => $merged['twitter_card'],
            'title' => $title,
            'description' => $description,
            'image' => $merged['image'],
            'site' => $merged['twitter_site'],
            'creator' => $merged['twitter_creator'],
        ];
        
        $html .= self::generateTwitterCardTags($twitterData);

        return $html;
    }

    /**
     * Generate JSON-LD structured data
     *
     * @param array $data
     * @return string
     */
    public static function generateStructuredData($data = [])
    {
        $defaults = [
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => self::generateTitle(),
            'description' => self::generateDescription(),
            'url' => url()->current(),
        ];

        $merged = array_merge($defaults, $data);

        // If it's an article
        if (isset($merged['@type']) && $merged['@type'] === 'NewsArticle') {
            if (!isset($merged['headline'])) {
                $merged['headline'] = $merged['name'] ?? self::generateTitle();
            }
            if (!isset($merged['datePublished'])) {
                $merged['datePublished'] = now()->toISOString();
            }
            if (!isset($merged['dateModified'])) {
                $merged['dateModified'] = now()->toISOString();
            }
            if (!isset($merged['author'])) {
                $merged['author'] = [
                    '@type' => 'Person',
                    'name' => 'द पब्लिक एक्सप्रेस'
                ];
            }
            if (!isset($merged['publisher'])) {
                $merged['publisher'] = [
                    '@type' => 'Organization',
                    'name' => self::getSiteName(),
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => asset('images/logo.png')
                    ]
                ];
            }
        }

        $json = json_encode($merged, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        return "\n<script type=\"application/ld+json\">\n" . $json . "\n</script>\n";
    }

    /**
     * Generate Article structured data
     *
     * @param array $articleData
     * @return string
     */
    public static function generateArticleStructuredData($articleData = [])
    {
        $defaults = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => self::generateTitle(),
            'description' => self::generateDescription(),
            'url' => url()->current(),
            'mainEntityOfPage' => url()->current(),
            'datePublished' => now()->toISOString(),
            'dateModified' => now()->toISOString(),
            'author' => [
                '@type' => 'Person',
                'name' => 'द पब्लिक एक्सप्रेस'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => self::getSiteName(),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png')
                ]
            ]
        ];

        $merged = array_merge($defaults, $articleData);

        // Add image if available
        if (isset($merged['image'])) {
            if (is_string($merged['image'])) {
                $merged['image'] = [
                    '@type' => 'ImageObject',
                    'url' => $merged['image']
                ];
            }
        }

        return self::generateStructuredData($merged);
    }

    /**
     * Get site name
     */
    public static function getSiteName()
    {
        try {
            $siteName = \App\Models\SiteSetting::get('site_name', 'द पब्लिक एक्सप्रेस');
            return $siteName;
        } catch (\Exception $e) {
            return self::$siteName;
        }
    }

    /**
     * Get site description
     */
    public static function getSiteDescription()
    {
        try {
            $description = \App\Models\SiteSetting::get('meta_description', self::$siteDescription);
            return $description;
        } catch (\Exception $e) {
            return self::$siteDescription;
        }
    }

    /**
     * Generate breadcrumb structured data
     *
     * @param array $breadcrumbs
     * @return string
     */
    public static function generateBreadcrumbStructuredData($breadcrumbs = [])
    {
        if (empty($breadcrumbs)) {
            return '';
        }

        $items = [];
        $position = 1;
        foreach ($breadcrumbs as $breadcrumb) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => $position,
                'name' => $breadcrumb['name'],
                'item' => $breadcrumb['url'] ?? ''
            ];
            $position++;
        }

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items
        ];

        return self::generateStructuredData($data);
    }

    /**
     * Generate FAQ structured data
     *
     * @param array $faqs
     * @return string
     */
    public static function generateFAQStructuredData($faqs = [])
    {
        if (empty($faqs)) {
            return '';
        }

        $items = [];
        foreach ($faqs as $faq) {
            $items[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer']
                ]
            ];
        }

        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $items
        ];

        return self::generateStructuredData($data);
    }

    /**
     * Generate Local Business structured data
     *
     * @param array $businessData
     * @return string
     */
    public static function generateLocalBusinessStructuredData($businessData = [])
    {
        $defaults = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsMediaOrganization',
            'name' => self::getSiteName(),
            'url' => url('/'),
            'logo' => asset('images/logo.png'),
            'description' => self::getSiteDescription(),
            'sameAs' => [
                'https://www.facebook.com/thepublicexpress',
                'https://twitter.com/thepublicexpress',
                'https://www.instagram.com/thepublicexpress',
                'https://www.youtube.com/thepublicexpress'
            ]
        ];

        $merged = array_merge($defaults, $businessData);
        return self::generateStructuredData($merged);
    }
}