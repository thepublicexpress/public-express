@extends('layouts.app')

@section('content')

    <!-- 🛠️ लाइव लोकल सर्च पट्टी -->
    <div class="mb-4" style="background: linear-gradient(135deg, #0f172a, #1e293b); border-top: 4px solid #b91c1c; padding: 24px; border-radius: 6px; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
        <form action="{{ route('news.local-search') }}" method="GET" id="localSearchForm">
            <div class="row align-items-center g-3">
                <!-- लेफ्ट टेक्स्ट साइड -->
                <div class="col-12 col-lg-4">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span style="display: inline-block; width: 8px; height: 8px; border-radius: 50%; background-color: #b91c1c;"></span>
                        <p style="color: #f87171; font-weight: 800; font-size: 12px; margin: 0; letter-spacing: 0.05em;">📍 लाइव लोकल सर्च</p>
                    </div>
                    <h2 style="color: #ffffff; font-size: 22px; font-weight: 800; margin: 0; line-height: 1.3;">हर कस्बे गाँव और सिटी की खबरें</h2>
                </div>
                
                <!-- राइट ड्रॉपडाउन साइड -->
                <div class="col-12 col-lg-8">
                    <div class="row g-2">
                        <!-- राज्य ड्रॉपडाउन -->
                        <div class="col-12 col-sm-3">
                            <select name="state" id="stateSelect" class="form-select" style="font-weight: bold; height: 42px;">
                                <option value="">राज्य चुनें</option>
                            </select>
                        </div>
                        <!-- जिला ड्रॉपडाउन -->
                        <div class="col-12 col-sm-3">
                            <select name="district" id="districtSelect" class="form-select" style="font-weight: bold; height: 42px;" disabled>
                                <option value="">जिला चुनें</option>
                            </select>
                        </div>
                        <!-- तहसील ड्रॉपडाउन -->
                        <div class="col-12 col-sm-3">
                            <select name="tehsil" id="tehsilSelect" class="form-select" style="font-weight: bold; height: 42px;" disabled>
                                <option value="">तहसील चुनें</option>
                            </select>
                        </div>
                        <!-- ब्लॉक ड्रॉपडाउन -->
                        <div class="col-12 col-sm-3">
                            <select name="block" id="blockSelect" class="form-select" style="font-weight: bold; height: 42px;" disabled>
                                <option value="">ब्लॉक चुनें</option>
                            </select>
                        </div>
                    </div>
                    <div class="text-end mt-2">
                        <button type="submit" class="btn btn-danger px-4 fw-bold" style="background-color: #b91c1c; height: 42px;">
                            खबरें देखें ➔
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- 📰 मुख्य ग्रिड सेक्शन: मुख्य खबर (बाएं) + लाइव आंकड़े और रैंकिंग (दाएं) -->
    <div class="row g-4">
        
        <!-- 🔥 बड़ी मुख्य खबर -->
        <div class="col-12 col-lg-8">
            @if(isset($latestNews) && $latestNews->first())
            <div class="position-relative overflow-hidden bg-dark rounded-1" style="border-bottom: 4px solid #b91c1c; box-shadow: 0 4px 10px rgba(0,0,0,0.15);">
                <div class="main-banner-container">
                    <img src="{{ url('/serve-image/' . urlencode($latestNews->first()->featured_image)) }}" 
                         alt="{{ $latestNews->first()->title }}" 
                         class="w-100 h-100 object-fit-cover"
                         onerror="this.src='{{ asset('images/default-news.jpg') }}'">
                </div>
                
                <div class="position-absolute bottom-0 start-0 end-0 p-3" style="background: linear-gradient(to top, rgba(0,0,0,1) 0%, rgba(0,0,0,0.6) 80%, transparent 100%);">
                    <div class="d-flex gap-2 mb-2">
                        <span class="badge bg-danger text-uppercase fw-bold rounded-0" style="font-size: 11px; padding: 4px 8px;">🔥 मुख्य समाचार</span>
                        <span class="badge bg-white text-dark fw-bold rounded-0" style="font-size: 11px; padding: 4px 8px;">{{ $latestNews->first()->category->name ?? 'ताजा खबर' }}</span>
                    </div>
                    <h2 class="text-white fw-bold m-0" style="font-size: 20px; line-height: 1.4;">
                        <a href="{{ route('news.show', $latestNews->first()->slug) }}" class="text-white text-decoration-none">{{ $latestNews->first()->title }}</a>
                    </h2>
                </div>
            </div>
            @else
            <!-- अगर डेटाबेस में कोई खबर न हो तो बैकअप बैनर -->
            <div class="position-relative overflow-hidden bg-dark rounded-1" style="border-bottom: 4px solid #b91c1c; height: 430px;">
                <div class="d-flex align-items-center justify-content-center h-100 bg-secondary text-white">
                    <h4>द पब्लिक एक्सप्रेस पर आपका स्वागत है</h4>
                </div>
            </div>
            @endif
        </div>

        <!-- 📊 राइट साइडबार -->
        <div class="col-12 col-lg-4">
            <div class="d-flex flex-column gap-4">
                
                <!-- लाइव आंकड़े कार्ड -->
                <div class="bg-white p-3 border-top border-4 border-dark rounded-1 shadow-sm">
                    <h3 class="text-dark fw-bold border-bottom pb-2 mb-3" style="font-size: 14px;"><i class="fas fa-chart-bar me-1 text-primary"></i> आज के लाइव आंकड़े</h3>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="bg-light p-3 border-start border-4 border-danger">
                                <p class="fs-2 fw-black m-0 text-dark" style="font-weight: 900;">275</p>
                                <p class="text-muted small fw-bold m-0">कुल खबरें</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light p-3 border-start border-4 border-dark">
                                <p class="fs-2 fw-black m-0 text-dark" style="font-weight: 900;">0</p>
                                <p class="text-muted small fw-bold m-0">आज की खबरें</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- सर्वश्रेष्ठ रिपोर्टर रैंकिंग कार्ड -->
                <div class="bg-white p-3 border-top border-4 border-danger rounded-1 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
                        <h3 class="text-dark fw-bold m-0" style="font-size: 14px;">🏆 हमारे सर्वश्रेष्ठ रिपोर्टर</h3>
                        <a href="{{ route('leaderboard') }}" class="text-danger fw-bold text-decoration-none small">सूची देखें ➔</a>
                    </div>
                    <div class="d-flex flex-column gap-2">
                        <div class="text-muted small py-2 text-center fw-semibold">आजमगढ़ मंडल के सक्रिय रिपोर्टर यहाँ दिखाई देंगे।</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- एक्स्ट्रा रेस्पॉन्सिव CSS फिक्स -->
    <style>
        .main-banner-container { height: 320px; }
        @media (min-width: 768px) {
            .main-banner-container { height: 430px; }
        }
        .fw-black { font-weight: 900 !important; }
    </style>
@endsection

<!-- 🌐 लाइव सर्च अनुवादक स्क्रिप्ट -->
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const stateSelect = document.getElementById('stateSelect');
            const districtSelect = document.getElementById('districtSelect');
            const tehsilSelect = document.getElementById('tehsilSelect');
            const blockSelect = document.getElementById('blockSelect');

            // 🎯 अचूक हिंदी शब्दकोश (सारे इंग्लिश नाम छोटे अक्षरों/lowercase में रखें)
            const hindiNames = {
                // राज्य
                'uttar pradesh': 'उत्तर प्रदेश',
                'up': 'उत्तर प्रदेश',
                
                // जिले
                'azamgarh': 'आजमगढ़',
                'mau': 'मऊ',
                'ballia': 'बलिया',
                
                // तहसील और ब्लॉक (आजमगढ़ मंडल के मुख्य क्षेत्र)
                'lalganj': 'लालगंज',
                'sagri': 'सगरी',
                'sadar': 'सदर',
                'nizamabad': 'निजामाबाद',
                'phulpur': 'फूलपुर',
                'burhanpur': 'बूढ़नपुर',
                'mehnagar': 'मेहनागर',
                'menhnagar': 'मेहनागर',
                'gopalpur': 'गोपालपुर',
                'bilariaganj': 'बिलरियागंज',
                'azmatgarh': 'अजमतगढ़',
                'martinganj': 'मार्टीनगंज',
                'tarwa': 'तरवां',
                'jahanganj': 'जहानगंज',
                'ahiraula': 'अहिरौला',
                'koilsa': 'कोयल्सा',
                'atraulia': 'अतरौलिया',
                'mirzapur': 'मिर्ज़ापुर',
                'mohammadpur': 'मोहम्मदपुर',
                'palkhani': 'पल्हनी',
                'ranipur': 'रानीपुर',
                'kopaganj': 'कोपागंज',
                'ghosi': 'घोसी',
                'rasra': 'रसड़ा'
            };

            // नाम को साफ़ करके हिंदी में कन्वर्ट करने वाला जादुई फंक्शन
            function translateToHindi(englishName) {
                if (!englishName) return '';
                // स्पेस हटाकर छोटे अक्षरों में बदलें ताकि मैचिंग में कोई गलती न हो
                const formattedKey = englishName.toString().trim().toLowerCase();
                return hindiNames[formattedKey] || englishName;
            }

            // 1. राज्यों की लिस्ट लोड करें
            fetch('/api/states')
                .then(res => res.json())
                .then(data => {
                    stateSelect.innerHTML = '<option value="">राज्य चुनें</option>';
                    data.forEach(state => {
                        let opt = document.createElement('option');
                        opt.value = state.id;
                        // यहाँ चेक हो रहा है कि API डेटा 'name' में भेज रहा है या 'text' में
                        opt.textContent = translateToHindi(state.name || state.text);
                        stateSelect.appendChild(opt);
                    });
                })
                .catch(err => console.error('Error fetching states:', err));

            // 2. राज्य बदलने पर जिला लोड करें
            stateSelect.addEventListener('change', function () {
                districtSelect.innerHTML = '<option value="">जिला चुनें</option>';
                tehsilSelect.innerHTML = '<option value="">तहसील चुनें</option>';
                blockSelect.innerHTML = '<option value="">ब्लॉक चुनें</option>';
                districtSelect.disabled = true;
                tehsilSelect.disabled = true;
                blockSelect.disabled = true;

                if (this.value) {
                    fetch(`/api/districts/${this.value}`)
                        .then(res => res.json())
                        .then(data => {
                            if(data.length > 0) {
                                districtSelect.disabled = false;
                                data.forEach(dist => {
                                    let opt = document.createElement('option');
                                    opt.value = dist.id;
                                    opt.textContent = translateToHindi(dist.name || dist.text);
                                    districtSelect.appendChild(opt);
                                });
                            }
                        });
                }
            });

            // 3. जिला बदलने पर तहसील लोड करें
            districtSelect.addEventListener('change', function () {
                tehsilSelect.innerHTML = '<option value="">तहसील चुनें</option>';
                blockSelect.innerHTML = '<option value="">ब्लॉक चुनें</option>';
                tehsilSelect.disabled = true;
                blockSelect.disabled = true;

                if (this.value) {
                    fetch(`/api/tehsils/${this.value}`)
                        .then(res => res.json())
                        .then(data => {
                            if(data.length > 0) {
                                tehsilSelect.disabled = false;
                                data.forEach(teh => {
                                    let opt = document.createElement('option');
                                    opt.value = teh.id;
                                    opt.textContent = translateToHindi(teh.name || teh.text);
                                    tehsilSelect.appendChild(opt);
                                });
                            }
                        });
                }
            });

            // 4. तहसील बदलने पर ब्लॉक लोड करें
            tehsilSelect.addEventListener('change', function () {
                blockSelect.innerHTML = '<option value="">ब्लॉक चुनें</option>';
                blockSelect.disabled = true;

                if (this.value) {
                    fetch(`/api/blocks/${this.value}`)
                        .then(res => res.json())
                        .then(data => {
                            if(data.length > 0) {
                                blockSelect.disabled = false;
                                data.forEach(blk => {
                                    let opt = document.createElement('option');
                                    opt.value = blk.id;
                                    opt.textContent = translateToHindi(blk.name || blk.text);
                                    blockSelect.appendChild(opt);
                                });
                            }
                        });
                }
            });
        });
    </script>
@endpush