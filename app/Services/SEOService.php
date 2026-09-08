<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SEOService
{
    protected $client;
    protected $service;
    protected $isAvailable = false;

    // ✅ Default site info
    protected $siteName = 'द पब्लिक एक्सप्रेस';
    protected $siteUrl = 'https://thepublicexpress.com';
    protected $siteLogo = '/images/logo.png';

    public function __construct()
    {
        try {
            $this->initializeGoogleClient();
        } catch (Exception $e) {
            Log::error('SEOService initialization failed: ' . $e->getMessage());
            $this->client = null;
            $this->service = null;
            $this->isAvailable = false;
        }
    }

    protected function initializeGoogleClient()
    {
        try {
            $autoloadPath = base_path('vendor/google/apiclient/vendor/autoload.php');
            if (file_exists($autoloadPath)) {
                require_once $autoloadPath;
            } else {
                require_once base_path('vendor/autoload.php');
            }

            if (!env('GOOGLE_INDEXING_ENABLED', false)) {
                Log::info('Google Indexing is disabled');
                return;
            }

            if (!class_exists('Google\\Client')) {
                Log::warning('Google\\Client class not found');
                return;
            }

            $this->client = new \Google\Client();

            $jsonPath = storage_path('app/google-indexing-key.json');
            if (!file_exists($jsonPath)) {
                Log::warning('Google Indexing key not found');
                return;
            }

            $this->client->setAuthConfig($jsonPath);
            $this->client->addScope('https://www.googleapis.com/auth/indexing');

            if (class_exists('Google\\Service\\Indexing')) {
                $this->service = new \Google\Service\Indexing($this->client);
                $this->isAvailable = true;
                Log::info('Google Indexing API initialized successfully');
            }

        } catch (Exception $e) {
            Log::error('Google Indexing initialization failed: ' . $e->getMessage());
        }
    }

    // ============================================================
    // ✅ GOOGLE INDEXING
    // ============================================================

    public function submitToGoogleIndex($url)
    {
        if ($this->isAvailable && $this->service) {
            try {
                $postBody = new \Google\Service\Indexing\UrlNotification([
                    'url' => $url,
                    'type' => 'URL_UPDATED'
                ]);
                $this->service->urlNotifications->publish($postBody);
                Log::info('✅ Indexed via Google Client: ' . $url);
                return true;
            } catch (Exception $e) {
                Log::warning('Google Client indexing failed, falling back to cURL: ' . $e->getMessage());
            }
        }

        return $this->submitToGoogleIndexCurl($url);
    }

    public function submitToGoogleIndexCurl($url)
    {
        try {
            if (!env('GOOGLE_INDEXING_ENABLED', false)) {
                return false;
            }

            $jsonPath = storage_path('app/google-indexing-key.json');
            if (!file_exists($jsonPath)) {
                Log::error('Google Indexing key file not found');
                return false;
            }

            $jsonData = json_decode(file_get_contents($jsonPath), true);
            if (!$jsonData || !isset($jsonData['client_email'], $jsonData['private_key'])) {
                Log::error('Invalid Google service account JSON');
                return false;
            }

            $clientEmail = $jsonData['client_email'];
            $privateKey = $jsonData['private_key'];
            $now = time();

            $payload = [
                'iss' => $clientEmail,
                'scope' => 'https://www.googleapis.com/auth/indexing',
                'aud' => 'https://oauth2.googleapis.com/token',
                'iat' => $now,
                'exp' => $now + 3600,
            ];

            $jwtHeader = $this->base64UrlEncode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
            $jwtPayload = $this->base64UrlEncode(json_encode($payload));
            $unsignedToken = $jwtHeader . '.' . $jwtPayload;

            $keyResource = openssl_pkey_get_private($privateKey);
            if ($keyResource === false) {
                Log::error('Failed to load private key');
                return false;
            }

            if (!openssl_sign($unsignedToken, $jwtSignature, $keyResource, OPENSSL_ALGO_SHA256)) {
                Log::error('JWT signing failed');
                openssl_free_key($keyResource);
                return false;
            }
            openssl_free_key($keyResource);

            $jwt = $unsignedToken . '.' . $this->base64UrlEncode($jwtSignature);

            $ch = curl_init('https://oauth2.googleapis.com/token');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode != 200) {
                Log::error('Failed to get access token: ' . $response);
                return false;
            }

            $tokenData = json_decode($response, true);
            if (!isset($tokenData['access_token'])) {
                Log::error('No access token in response');
                return false;
            }

            $accessToken = $tokenData['access_token'];
            $apiUrl = 'https://indexing.googleapis.com/v3/urlNotifications:publish';
            $postBody = json_encode(['url' => $url, 'type' => 'URL_UPDATED']);

            $ch = curl_init($apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postBody);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                Log::info('✅ Google Indexing API Success (cURL)', ['url' => $url]);
                return true;
            }

            Log::error('Google Indexing API Error', ['url' => $url, 'code' => $httpCode]);
            return false;

        } catch (\Exception $e) {
            Log::error('Google Indexing Exception: ' . $e->getMessage());
            return false;
        }
    }

    public function submitToGoogle($url)
    {
        return $this->submitToGoogleIndex($url);
    }

    public function submitBatchToGoogleIndex($urls)
    {
        foreach ($urls as $url) {
            $this->submitToGoogleIndex($url);
            sleep(1);
        }
        return true;
    }

    // ============================================================
    // ✅ BING INDEXNOW
    // ============================================================

    public function submitToBing($url)
    {
        try {
            $bingKey = env('BING_INDEXNOW_KEY');
            $bingHost = env('BING_INDEXNOW_HOST', 'thepublicexpress.com');

            if (empty($bingKey) || $bingKey == 'your_bing_indexnow_key_here') {
                Log::warning('Bing IndexNow key not configured.');
                return false;
            }

            $bingUrl = "https://www.bing.com/indexnow?url=" . urlencode($url) . "&key=" . $bingKey;

            $ch = curl_init($bingUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode >= 200 && $httpCode < 300) {
                Log::info('✅ Bing IndexNow Success', ['url' => $url]);
                return true;
            } else {
                Log::warning('⚠️ Bing IndexNow Failed', ['url' => $url, 'http_code' => $httpCode]);
                return false;
            }

        } catch (Exception $e) {
            Log::error('Bing IndexNow Error: ' . $e->getMessage());
            return false;
        }
    }

    // ============================================================
    // ✅ SEO META GENERATION (Unique Titles & Descriptions)
    // ============================================================

    /**
     * Generate SEO Meta for any page
     */
    public function generateSEOMeta($data = [])
    {
        try {
            // If news object is passed
            if (isset($data['news']) || isset($data['id'])) {
                return $this->generateNewsMeta($data);
            }

            // Default meta
            return $this->getDefaultSEOMeta();

        } catch (Exception $e) {
            Log::error('generateSEOMeta Error: ' . $e->getMessage());
            return $this->getDefaultSEOMeta();
        }
    }

    /**
     * Generate SEO Meta for News Article
     */
    public function generateNewsMeta($news)
    {
        try {
            // Extract news object
            if (is_array($news) && isset($news['news'])) {
                $news = $news['news'];
            }
            
            $title = $news->title ?? $this->siteName;
            $description = strip_tags($news->summary ?? $news->body ?? $this->getDefaultDescription());
            $description = Str::limit($description, 160, '...');
            
            // ✅ Generate unique title with site name
            $seoTitle = $title;
            if (!str_contains($seoTitle, $this->siteName)) {
                $seoTitle = $seoTitle . ' - ' . $this->siteName;
            }
            if (strlen($seoTitle) > 70) {
                $seoTitle = Str::limit($title, 60) . ' - ' . $this->siteName;
            }

            // ✅ Generate unique description
            $seoDescription = $description;
            if (empty($seoDescription) || $seoDescription === $this->getDefaultDescription()) {
                $seoDescription = $this->getDefaultDescription();
            }

            // ✅ Generate keywords
            $keywords = $this->generateKeywords($title, $description);

            return [
                'title' => $seoTitle,
                'description' => $seoDescription,
                'keywords' => $keywords,
                'og_title' => $seoTitle,
                'og_description' => $seoDescription,
                'og_image' => $news->featured_image ? asset($news->featured_image) : asset($this->siteLogo),
                'og_url' => route('news.show', $news->slug ?? ''),
                'og_type' => 'article',
                'twitter_card' => 'summary_large_image',
                'twitter_title' => $seoTitle,
                'twitter_description' => $seoDescription,
                'twitter_image' => $news->featured_image ? asset($news->featured_image) : asset($this->siteLogo),
                'canonical' => route('news.show', $news->slug ?? ''),
                'robots' => 'index, follow',
            ];
        } catch (Exception $e) {
            Log::error('generateNewsMeta Error: ' . $e->getMessage());
            return $this->getDefaultSEOMeta();
        }
    }

    /**
     * Generate SEO Meta for Category Page
     */
    public function generateCategoryMeta($category)
    {
        try {
            $categoryName = $category->display_name ?? $category->name ?? 'श्रेणी';
            $title = $categoryName . ' समाचार - ' . $this->siteName;
            $description = 'द पब्लिक एक्सप्रेस पर ' . $categoryName . ' की ताजा खबरें, अपडेट और विश्लेषण पढ़ें।';

            return [
                'title' => $title,
                'description' => Str::limit($description, 160),
                'og_title' => $title,
                'og_description' => Str::limit($description, 160),
                'og_image' => asset($this->siteLogo),
                'og_url' => route('category.show', $category->slug ?? ''),
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
                'twitter_title' => $title,
                'twitter_description' => Str::limit($description, 160),
                'twitter_image' => asset($this->siteLogo),
                'canonical' => route('category.show', $category->slug ?? ''),
                'robots' => 'index, follow',
            ];
        } catch (Exception $e) {
            Log::error('generateCategoryMeta Error: ' . $e->getMessage());
            return $this->getDefaultSEOMeta();
        }
    }

    /**
     * Generate SEO Meta for Static Pages
     */
    public function generatePageMeta($page, $title = null, $description = null)
    {
        try {
            $pageTitle = $title ?? $page . ' - ' . $this->siteName;
            $pageDescription = $description ?? $this->getDefaultDescription();

            return [
                'title' => $pageTitle,
                'description' => Str::limit($pageDescription, 160),
                'og_title' => $pageTitle,
                'og_description' => Str::limit($pageDescription, 160),
                'og_image' => asset($this->siteLogo),
                'og_url' => url()->current(),
                'og_type' => 'website',
                'twitter_card' => 'summary_large_image',
                'twitter_title' => $pageTitle,
                'twitter_description' => Str::limit($pageDescription, 160),
                'twitter_image' => asset($this->siteLogo),
                'canonical' => url()->current(),
                'robots' => 'index, follow',
            ];
        } catch (Exception $e) {
            Log::error('generatePageMeta Error: ' . $e->getMessage());
            return $this->getDefaultSEOMeta();
        }
    }

    /**
     * Generate Keywords from title and description
     */
    public function generateKeywords($title, $description, $limit = 10)
    {
        $keywords = [];
        
        // Extract from title
        $titleWords = preg_split('/\s+/', strip_tags($title), -1, PREG_SPLIT_NO_EMPTY);
        $keywords = array_merge($keywords, $titleWords);
        
        // Extract from description
        $descWords = preg_split('/\s+/', strip_tags($description), -1, PREG_SPLIT_NO_EMPTY);
        $keywords = array_merge($keywords, $descWords);
        
        // Remove duplicates and limit
        $keywords = array_unique($keywords);
        $keywords = array_slice($keywords, 0, $limit);
        
        // Add site name
        $keywords[] = $this->siteName;
        
        return implode(', ', $keywords);
    }

    /**
     * Get default SEO Meta
     */
    protected function getDefaultSEOMeta()
    {
        return [
            'title' => $this->siteName,
            'description' => $this->getDefaultDescription(),
            'keywords' => $this->siteName . ', समाचार, हिंदी समाचार, ताजा खबरें',
            'og_title' => $this->siteName,
            'og_description' => $this->getDefaultDescription(),
            'og_image' => asset($this->siteLogo),
            'og_url' => url('/'),
            'og_type' => 'website',
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $this->siteName,
            'twitter_description' => $this->getDefaultDescription(),
            'twitter_image' => asset($this->siteLogo),
            'canonical' => url('/'),
            'robots' => 'index, follow',
        ];
    }

    /**
     * Get default description
     */
    protected function getDefaultDescription()
    {
        return 'द पब्लिक एक्सप्रेस – हर कस्बे, गाँव और सिटी की खबरें। ताजा हिंदी समाचार, राजनीति, शिक्षा, खेल और मनोरंजन।';
    }

    // ============================================================
    // ✅ STRUCTURED DATA (Schema.org)
    // ============================================================

    /**
     * Generate News Article Schema
     */
    public function generateNewsSchema($news)
    {
        try {
            $imageUrl = $news->featured_image ? asset($news->featured_image) : asset($this->siteLogo);
            $description = strip_tags($news->summary ?? $news->body ?? '');
            $description = trim(preg_replace('/\s+/', ' ', $description));
            $description = Str::limit($description, 200);

            return [
                '@context' => 'https://schema.org',
                '@type' => 'NewsArticle',
                'headline' => $news->title ?? '',
                'description' => $description,
                'image' => [$imageUrl],
                'datePublished' => optional($news->published_at ?? $news->created_at)->toIso8601String(),
                'dateModified' => optional($news->updated_at ?? $news->published_at ?? $news->created_at)->toIso8601String(),
                'author' => [
                    '@type' => 'Person',
                    'name' => $news->user->name ?? 'द पब्लिक एक्सप्रेस',
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => $this->siteName,
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => asset($this->siteLogo),
                    ],
                ],
                'mainEntityOfPage' => [
                    '@type' => 'WebPage',
                    '@id' => route('news.show', $news->slug ?? ''),
                ],
                'thumbnailUrl' => $imageUrl,
                'about' => $news->category->display_name ?? $news->category->name ?? null,
                'articleSection' => $news->category->display_name ?? $news->category->name ?? null,
                'isAccessibleForFree' => true,
            ];
        } catch (Exception $e) {
            Log::error('generateNewsSchema Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate Breadcrumb Schema
     */
    public function generateBreadcrumbSchema($items = [])
    {
        try {
            $itemList = [];
            $position = 1;
            foreach ($items as $item) {
                $itemList[] = [
                    '@type' => 'ListItem',
                    'position' => $position,
                    'name' => $item['name'],
                    'item' => $item['url'] ?? '',
                ];
                $position++;
            }

            return [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => $itemList,
            ];
        } catch (Exception $e) {
            Log::error('generateBreadcrumbSchema Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate Organization Schema
     */
    public function generateOrganizationSchema()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'NewsMediaOrganization',
            'name' => $this->siteName,
            'url' => $this->siteUrl,
            'logo' => asset($this->siteLogo),
            'description' => $this->getDefaultDescription(),
            'sameAs' => [
                'https://www.facebook.com/thepublicexpress',
                'https://twitter.com/thepublicexpress',
                'https://www.instagram.com/thepublicexpress',
                'https://www.youtube.com/thepublicexpress'
            ],
            'foundingDate' => '2024',
            'publishingPrinciples' => $this->siteUrl . '/privacy',
            'actionableFeedbackPolicy' => $this->siteUrl . '/contact',
            'correctionsPolicy' => $this->siteUrl . '/privacy',
            'ethicsPolicy' => $this->siteUrl . '/terms',
        ];
    }

    // ============================================================
    // ✅ HELPER METHODS
    // ============================================================

    public function isAvailable()
    {
        return $this->isAvailable;
    }

    protected function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}