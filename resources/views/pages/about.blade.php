@extends('layouts.app')

@section('title', 'हमारे बारे में - द पब्लिक एक्सप्रेस')

@section('meta_tags')
    <meta name="description" content="द पब्लिक एक्सप्रेस - उत्तर प्रदेश का सबसे तेज़ हाइपरलोकल न्यूज़ प्लेटफॉर्म। हमारे बारे में जानें।">
    <meta property="og:title" content="हमारे बारे में - द पब्लिक एक्सप्रेस">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
@endsection

@section('content')

    <!-- ============================================================ -->
    <!--  PAGE HEADER (Clean & Professional)                           -->
    <!-- ============================================================ -->
    <div class="relative mb-8 rounded-2xl overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 shadow-xl border-b-4 border-brand">
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-0 right-0 w-64 h-64 bg-brand rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-500 rounded-full blur-3xl"></div>
        </div>
        <div class="relative z-10 px-6 md:px-8 py-6 md:py-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight flex items-center gap-3">
                        <span class="text-4xl md:text-6xl">📰</span>
                        <span class="text-brand-light drop-shadow-lg">हमारे बारे में</span>
                    </h1>
                    <p class="text-slate-400 text-sm font-semibold mt-1 flex items-center gap-2">
                        <i class="fas fa-info-circle text-brand"></i>
                        जानें हमारे बारे में, हमारा उद्देश्य और टीम
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!--  ABOUT CONTENT                                                -->
    <!-- ============================================================ -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-slate-200 p-6 md:p-8 max-w-4xl mx-auto">
        <div class="prose prose-slate max-w-none text-slate-700 leading-relaxed">

            <h2 class="text-xl font-black text-slate-900">🙏 नमस्ते, हम हैं <strong>द पब्लिक एक्सप्रेस</strong></h2>

            <p>
                <strong>द पब्लिक एक्सप्रेस</strong> उत्तर प्रदेश का सबसे तेज़ और विश्वसनीय हाइपरलोकल न्यूज़ प्लेटफॉर्म है। 
                हमारा मिशन हर कस्बे, गाँव और शहर की खबरों को आप तक पहुँचाना है – बिना किसी पूर्वाग्रह के, पूरी निष्पक्षता के साथ।
            </p>

            <h2 class="text-xl font-black text-slate-900 mt-6">🎯 हमारा उद्देश्य</h2>
            <ul>
                <li><strong>हाइपरलोकल फोकस:</strong> हम उन खबरों को प्राथमिकता देते हैं जो आपके आस-पास की होती हैं – जिला, तहसील, ब्लॉक स्तर तक।</li>
                <li><strong>निष्पक्षता:</strong> हम किसी भी राजनीतिक दल या व्यक्तिगत हित से प्रभावित नहीं होते।</li>
                <li><strong>सत्यता:</strong> हम पुष्टि किए गए तथ्यों को ही प्रकाशित करते हैं।</li>
                <li><strong>समुदाय की आवाज:</strong> हम आम लोगों की आवाज को मंच देते हैं – उनकी समस्याएँ, उनकी उपलब्धियाँ।</li>
            </ul>

            <h2 class="text-xl font-black text-slate-900 mt-6">👥 हमारी टीम</h2>
            <p>
                हमारी टीम में अनुभवी पत्रकार, डिजिटल मीडिया विशेषज्ञ और 
                स्थानीय संवाददाता शामिल
                है
                हर सदस्य हमारे मूल्यों – सत्य, निष्पक्षता और सेवा – के प्रति प्रतिबद्ध है।
            </p>

            

            <h2 class="text-xl font-black text-slate-900 mt-6">📬 संपर्क करें</h2>
            <p>
                यदि आपको कोई जानकारी देनी है, कोई शिकायत है, या आप हमारी टीम का हिस्सा बनना चाहते हैं, तो हमें लिखें:
            </p>
            <ul>
                <li><strong>ईमेल:</strong> <a href="mailto:admin@thepublicexpress.com" class="text-brand hover:underline">admin@thepublicexpress.com</a></li>
             
            </ul>
        </div>
    </div>

@endsection