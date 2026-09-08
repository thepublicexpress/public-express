<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>नयी खबर लिखें - द पब्लिक एक्सप्रेस</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Devanagari', sans-serif; background-color: #f3f4f6; }
        .bg-brand { background-color: #c62828; }
        .text-brand { color: #c62828; }
        .form-card { background: white; border-radius: 16px; padding: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .form-label { font-weight: 600; font-size: 14px; color: #374151; margin-bottom: 6px; display: block; }
        .form-label .optional { color: #94a3b8; font-weight: 400; font-size: 12px; }
        .form-label .required { color: #c62828; }
        .form-input { width: 100%; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 12px; outline: none; transition: all 0.2s; font-size: 15px; background-color: #f9fafb; }
        .form-input:focus { border-color: #c62828; background-color: white; box-shadow: 0 0 0 4px rgba(198,40,40,0.1); }
        
        /* ===== EDITOR TOOLBAR ===== */
        .editor-toolbar {
            background: #f8fafc;
            border: 2px solid #e2e8f0;
            border-radius: 12px 12px 0 0;
            padding: 8px 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 4px;
            align-items: center;
            border-bottom: none;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .editor-toolbar .btn-tool {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 5px 12px;
            font-size: 13px;
            font-weight: 600;
            color: #334155;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .editor-toolbar .btn-tool:hover {
            background: #f1f5f9;
            border-color: #c62828;
            color: #c62828;
        }
        .editor-toolbar .btn-tool i {
            font-size: 13px;
        }
        .editor-toolbar .divider {
            width: 1px;
            height: 24px;
            background: #e2e8f0;
            margin: 0 4px;
        }
        .editor-toolbar .tool-label {
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-right: 4px;
        }
        
        /* ===== EDITOR BODY - FIXED HEIGHT WITH SCROLL ===== */
        .editor-body-wrapper {
            border: 2px solid #c62828;
            border-radius: 0 0 12px 12px;
            background: #ffffff;
            position: relative;
            height: 500px;
            overflow: hidden;
        }
        .editor-body-wrapper .editor-label {
            position: absolute;
            top: -10px;
            left: 16px;
            background: #ffffff;
            padding: 0 8px;
            font-size: 11px;
            font-weight: 600;
            color: #94a3b8;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            z-index: 5;
        }
        .editor-body-wrapper .editor-label .required {
            color: #c62828;
        }
        
        #bodyEditor {
            height: 100%;
            padding: 20px;
            font-family: 'Noto Sans Devanagari', sans-serif;
            font-size: 16px;
            line-height: 1.9;
            color: #0f172a;
            outline: none;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            white-space: pre-wrap;
            word-break: break-word;
            word-wrap: break-word;
            background: #ffffff;
            border-radius: 0 0 12px 12px;
            max-height: 100%;
            min-height: 100%;
        }
        #bodyEditor:empty:before {
            content: "यहाँ खबर लिखें...";
            color: #94a3b8;
            pointer-events: none;
            font-size: 16px;
        }
        #bodyEditor:not(:empty):before {
            content: "";
        }
        #bodyEditor img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 0.75rem 0;
            border-radius: 8px;
            border: 1px solid #eef2f6;
        }
        #bodyEditor figure {
            margin: 0.75rem 0;
        }
        #bodyEditor figure figcaption {
            font-size: 0.85rem;
            color: #64748b;
            text-align: center;
            margin-top: 4px;
        }
        #bodyEditor .pull-quote {
            padding: 16px 20px;
            background: #f1f5f9;
            border-left: 4px solid #c62828;
            border-radius: 0 8px 8px 0;
            font-style: italic;
            font-size: 1.05rem;
            color: #1e293b;
            margin: 0.75rem 0;
        }
        #bodyEditor .pull-quote .quote-author {
            display: block;
            font-style: normal;
            font-weight: 600;
            color: #64748b;
            font-size: 0.85rem;
            margin-top: 4px;
        }
        #bodyEditor ul.content-list {
            padding-left: 1.5rem;
            margin: 0.75rem 0;
        }
        #bodyEditor ul.content-list li {
            margin-bottom: 4px;
        }
        #bodyEditor ul.content-list li::marker {
            color: #c62828;
        }
        #bodyEditor h2 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin: 1rem 0 0.5rem 0;
            padding-left: 12px;
            border-left: 4px solid #c62828;
        }
        #bodyEditor h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0.75rem 0 0.5rem 0;
            padding-left: 12px;
            border-left: 4px solid #c62828;
        }
        #bodyEditor h4 {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0.5rem 0 0.5rem 0;
            padding-left: 12px;
            border-left: 4px solid #c62828;
        }
        
        /* ===== WORD COUNT ===== */
        .word-count {
            font-size: 13px;
            color: #94a3b8;
            text-align: right;
            margin-top: 6px;
            padding-right: 4px;
            font-weight: 600;
        }
        .word-count .count {
            color: #0f172a;
            font-weight: 700;
        }
        .word-count .target {
            color: #94a3b8;
        }
        .word-count.warning {
            color: #eab308;
        }
        .word-count.danger {
            color: #dc2626;
        }
        .word-count.success {
            color: #16a34a;
        }
        
        /* ===== WORD PROGRESS ===== */
        .word-progress {
            width: 100%;
            height: 4px;
            background: #e2e8f0;
            border-radius: 4px;
            margin-top: 4px;
            overflow: hidden;
        }
        .word-progress .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #c62828, #ef4444);
            border-radius: 4px;
            transition: width 0.3s ease;
            width: 0%;
        }
        .word-progress .progress-fill.success {
            background: linear-gradient(90deg, #16a34a, #22c55e);
        }
        .word-progress .progress-fill.warning {
            background: linear-gradient(90deg, #eab308, #f59e0b);
        }
        
        /* ===== IMAGE PREVIEW ===== */
        #imagePreview {
            display: none;
            margin-top: 8px;
        }
        #imagePreview img {
            max-height: 150px;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
        }
        
        /* ===== LOCATION GRID ===== */
        .location-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        
        /* ===== RESPONSIVE ===== */
        @media (max-width: 640px) {
            .form-card { padding: 16px; }
            .editor-toolbar { padding: 4px 8px; gap: 3px; }
            .editor-toolbar .btn-tool { padding: 3px 8px; font-size: 11px; }
            .editor-toolbar .btn-tool i { font-size: 10px; }
            .editor-toolbar .tool-label { font-size: 8px; }
            .editor-body-wrapper { height: 350px; }
            #bodyEditor { font-size: 15px; padding: 14px; min-height: 100%; }
            .location-grid { grid-template-columns: 1fr 1fr; gap: 8px; }
        }
        
        @media (max-width: 420px) {
            .editor-body-wrapper { height: 280px; }
            #bodyEditor { font-size: 14px; padding: 12px; }
        }
    </style>
</head>
<body class="pb-24">

    <header class="bg-brand text-white p-4 sticky top-0 z-50 shadow-md flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('reporter.news.index') }}" class="text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <h1 class="text-xl font-bold">नयी खबर लिखें</h1>
        </div>
    </header>

    <main class="max-w-2xl mx-auto p-4 mt-2">
        <div class="form-card">
            @if(session('error'))
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-red-700 text-sm font-semibold">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-red-700 text-sm font-semibold">{{ $errors->first() }}</div>
            @endif
            <form action="{{ route('reporter.news.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- ============================================================ -->
                <!-- ✅ TITLE - Required                                          -->
                <!-- ============================================================ -->
                <div>
                    <label class="form-label">खबर का शीर्षक (Title) <span class="required">*</span></label>
                    <input type="text" name="title" required class="form-input" placeholder="मुख्य हेडलाइन यहाँ लिखें..." value="{{ old('title') }}">
                </div>

                <!-- ============================================================ -->
                <!-- ✅ CATEGORY - Optional (No *)                                -->
                <!-- ============================================================ -->
                <div>
                    <label class="form-label">कैटेगरी चुनें <span class="optional">(वैकल्पिक)</span></label>
                    <select name="category_id" class="form-input">
                        <option value="">-- चुनें --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name_hindi ?? $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- ============================================================ -->
                <!-- ✅ LOCATION - Optional (No *)                                -->
                <!-- ============================================================ -->
                <div>
                    <label class="form-label">📍 स्थान <span class="optional">(वैकल्पिक)</span></label>
                    
                    <!-- Row 1: राज्य + जिला -->
                    <div class="location-grid">
                        <div>
                            <select name="state_id" id="stateSelect" class="form-input" onchange="loadDistricts(this.value)">
                                <option value="">राज्य</option>
                                @foreach($states as $state)
                                    <option value="{{ $state->id }}">{{ $state->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="district_id" id="districtSelect" class="form-input" onchange="loadTehsils(this.value)">
                                <option value="">जिला</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Row 2: तहसील + ब्लॉक -->
                    <div class="location-grid mt-2">
                        <div>
                            <select name="tehsil_id" id="tehsilSelect" class="form-input" onchange="loadBlocks(this.value)">
                                <option value="">तहसील</option>
                            </select>
                        </div>
                        <div>
                            <select name="block_id" id="blockSelect" class="form-input">
                                <option value="">ब्लॉक</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ============================================================ -->
                <!-- ✅ FEATURED IMAGE - Optional                                 -->
                <!-- ============================================================ -->
                <div>
                    <label class="form-label">मुख्य फोटो अपलोड करें <span class="optional">(वैकल्पिक)</span></label>
                    <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 bg-gray-50 text-center relative cursor-pointer min-h-[160px] flex flex-col items-center justify-center">
                        <input type="file" name="image" class="absolute inset-0 opacity-0 cursor-pointer w-full h-full" onchange="previewImage(this)">
                        <div id="uploadPlaceholder">
                            <span class="text-4xl mb-2 block">📸</span>
                            <p class="text-sm font-bold text-gray-500">यहाँ क्लिक करके फोटो चुनें</p>
                            <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP (Max 2MB)</p>
                        </div>
                        <img id="imgPreview" class="hidden max-h-40 rounded-xl object-cover" />
                    </div>
                </div>

                <!-- ============================================================ -->
                <!-- ✅ SUMMARY - Optional                                        -->
                <!-- ============================================================ -->
                <div>
                    <label class="form-label">खबर का सार (Summary) <span class="optional">(वैकल्पिक)</span></label>
                    <textarea name="summary" rows="2" class="form-input placeholder-gray-400" placeholder="खबर का संक्षिप्त विवरण यहाँ लिखें...">{{ old('summary') }}</textarea>
                </div>

                <!-- ============================================================ -->
                <!-- ✅ EDITOR TOOLBAR WITH FIXED HEIGHT BOX                     -->
                <!-- ============================================================ -->
                <div>
                    <label class="form-label">पूरी खबर का विवरण (Content) <span class="required">*</span></label>
                    
                    <div class="editor-toolbar">
                        <span class="tool-label">Format:</span>
                        <button type="button" class="btn-tool" onclick="formatText('h2')" title="Heading 2">H2</button>
                        <button type="button" class="btn-tool" onclick="formatText('h3')" title="Heading 3">H3</button>
                        <button type="button" class="btn-tool" onclick="formatText('h4')" title="Heading 4">H4</button>
                        <span class="divider"></span>
                        <button type="button" class="btn-tool" onclick="formatText('bold')" title="Bold"><b>B</b></button>
                        <button type="button" class="btn-tool" onclick="formatText('italic')" title="Italic"><i>I</i></button>
                        <span class="divider"></span>
                        <button type="button" class="btn-tool" onclick="formatText('quote')" title="Pull Quote">Quote</button>
                        <button type="button" class="btn-tool" onclick="formatText('list')" title="Bullet List">List</button>
                        <span class="divider"></span>
                        <button type="button" class="btn-tool" onclick="openInlineImageUpload()" title="Insert Image">Image</button>
                    </div>

                    <!-- === FIXED HEIGHT EDITOR BOX === -->
                    <div class="editor-body-wrapper">
                        <span class="editor-label">📝 सामग्री <span class="required">*</span></span>
                        <div id="bodyEditor" contenteditable="true" role="textbox" aria-multiline="true" data-placeholder="यहाँ खबर लिखें..."></div>
                        <!-- ✅ CRITICAL FIX: name="body" NOT "content" -->
                        <textarea name="body" id="body" rows="12" hidden>{{ old('body') }}</textarea>
                    </div>
                    
                    @error('body')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                    
                    <div class="word-count" id="wordCount">
                        <span class="count" id="wordCountNumber">0</span> 
                        <span class="target">शब्द</span>
                        <span id="wordStatus" style="margin-left:8px;font-size:11px;"></span>
                    </div>
                    <div class="word-progress">
                        <div class="progress-fill" id="wordProgressFill" style="width: 0%;"></div>
                    </div>
                </div>

                <!-- ============================================================ -->
                <!-- ✅ SUBMIT BUTTON                                             -->
                <!-- ============================================================ -->
                <button type="submit" class="w-full bg-brand text-white font-bold py-4 rounded-xl shadow-lg hover:bg-red-700 transition active:scale-95 text-lg">
                    🚀 खबर प्रकाशित करने के लिए भेजें
                </button>
            </form>
        </div>
    </main>

    <!-- ============================================================ -->
    <!-- ✅ INLINE IMAGE UPLOAD MODAL                                 -->
    <!-- ============================================================ -->
    <div class="modal fade" id="inlineImageModal" tabindex="-1" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.5); align-items:center; justify-content:center;">
        <div class="modal-content" style="background:white; max-width:400px; width:90%; border-radius:16px; padding:24px;">
            <div class="modal-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <h6 style="font-size:16px; font-weight:700;">📸 इमेज डालें</h6>
                <button type="button" onclick="closeInlineImageModal()" style="background:none; border:none; font-size:24px; cursor:pointer;">&times;</button>
            </div>
            <div class="modal-body">
                <div style="margin-bottom:12px;">
                    <label style="font-weight:600; font-size:13px;">इमेज अपलोड करें</label>
                    <input type="file" class="form-input" id="inlineImageInput" accept="image/*">
                    <small style="color:#94a3b8; font-size:12px;">JPG, PNG, WebP - Max 2MB</small>
                </div>
                <div style="margin-bottom:12px;">
                    <label style="font-weight:600; font-size:13px;">कैप्शन (वैकल्पिक)</label>
                    <input type="text" class="form-input" id="inlineImageCaption" placeholder="इमेज कैप्शन">
                </div>
                <div id="inlineImagePreview" style="display:none; margin-top:8px;">
                    <img id="inlinePreviewImg" src="#" alt="Preview" style="max-height:120px; border-radius:8px; border:2px solid #e2e8f0;">
                </div>
                <div id="inlineUploadProgress" style="display:none; margin-top:8px;">
                    <div class="progress" style="height:6px; background:#e2e8f0; border-radius:4px; overflow:hidden;">
                        <div id="inlineProgressBar" class="progress-bar" style="height:100%; background:linear-gradient(90deg,#c62828,#ef4444); border-radius:4px; width:0%;"></div>
                    </div>
                    <small style="font-size:12px; color:#64748b; display:block; margin-top:4px;" id="inlineUploadStatus">अपलोड हो रहा है...</small>
                </div>
            </div>
            <div class="modal-footer" style="display:flex; gap:8px; margin-top:16px; justify-content:flex-end;">
                <button type="button" onclick="closeInlineImageModal()" style="padding:8px 16px; border:1px solid #e2e8f0; border-radius:8px; background:white;">Cancel</button>
                <button type="button" onclick="insertInlineImage()" style="padding:8px 16px; border:none; border-radius:8px; background:#c62828; color:white; font-weight:600;">डालें</button>
            </div>
        </div>
    </div>

    <script>
        // ============================================================
        // ✅ LOCATION DROPDOWNS - राज्य → जिला → तहसील → ब्लॉक
        // ============================================================
        
        async function loadDistricts(stateId) {
            const districtSelect = document.getElementById('districtSelect');
            const tehsilSelect = document.getElementById('tehsilSelect');
            const blockSelect = document.getElementById('blockSelect');
            
            districtSelect.innerHTML = '<option value="">लोड हो रहा है...</option>';
            tehsilSelect.innerHTML = '<option value="">तहसील</option>';
            blockSelect.innerHTML = '<option value="">ब्लॉक</option>';

            if(!stateId) {
                districtSelect.innerHTML = '<option value="">जिला</option>';
                return;
            }
            try {
                const response = await fetch(`/api/get-districts/${stateId}`);
                const districts = await response.json();
                districtSelect.innerHTML = '<option value="">जिला</option>';
                districts.forEach(d => {
                    districtSelect.innerHTML += `<option value="${d.id}">${d.name}</option>`;
                });
            } catch (error) {
                console.error('Error loading districts:', error);
                districtSelect.innerHTML = '<option value="">त्रुटि</option>';
            }
        }

        async function loadTehsils(districtId) {
            const tehsilSelect = document.getElementById('tehsilSelect');
            const blockSelect = document.getElementById('blockSelect');
            
            tehsilSelect.innerHTML = '<option value="">लोड हो रहा है...</option>';
            blockSelect.innerHTML = '<option value="">ब्लॉक</option>';

            if(!districtId) {
                tehsilSelect.innerHTML = '<option value="">तहसील</option>';
                return;
            }
            try {
                const response = await fetch(`/api/get-tehsils/${districtId}`);
                const tehsils = await response.json();
                tehsilSelect.innerHTML = '<option value="">तहसील</option>';
                tehsils.forEach(t => {
                    tehsilSelect.innerHTML += `<option value="${t.id}">${t.name}</option>`;
                });
            } catch (error) {
                console.error('Error loading tehsils:', error);
                tehsilSelect.innerHTML = '<option value="">त्रुटि</option>';
            }
        }

        async function loadBlocks(tehsilId) {
            const blockSelect = document.getElementById('blockSelect');
            blockSelect.innerHTML = '<option value="">लोड हो रहा है...</option>';

            if(!tehsilId) {
                blockSelect.innerHTML = '<option value="">ब्लॉक</option>';
                return;
            }
            try {
                const response = await fetch(`/api/get-blocks/${tehsilId}`);
                const blocks = await response.json();
                blockSelect.innerHTML = '<option value="">ब्लॉक</option>';
                blocks.forEach(b => {
                    blockSelect.innerHTML += `<option value="${b.id}">${b.name}</option>`;
                });
            } catch (error) {
                console.error('Error loading blocks:', error);
                blockSelect.innerHTML = '<option value="">त्रुटि</option>';
            }
        }

        // ============================================================
        // ✅ IMAGE PREVIEW
        // ============================================================
        
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imgPreview').src = e.target.result;
                    document.getElementById('imgPreview').classList.remove('hidden');
                    document.getElementById('uploadPlaceholder').classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // ============================================================
        // ✅ EDITOR FUNCTIONS - SYNC TO "body" TEXTAREA
        // ============================================================
        
        const editor = document.getElementById('bodyEditor');
        const textarea = document.getElementById('body'); // ✅ Changed to "body"
        const wordCountEl = document.getElementById('wordCountNumber');
        const wordProgress = document.getElementById('wordProgressFill');
        const wordStatus = document.getElementById('wordStatus');

        function getWordCount() {
            const text = editor.innerText || '';
            const cleanText = text.replace(/\s+/g, ' ').trim();
            if (!cleanText) return 0;
            return cleanText.split(' ').length;
        }

        function updateWordCount() {
            const count = getWordCount();
            wordCountEl.textContent = count;
            
            const percentage = Math.min((count / 200) * 100, 100);
            wordProgress.style.width = percentage + '%';
            
            const wordCountDiv = document.getElementById('wordCount');
            wordCountDiv.className = 'word-count';
            
            if (count < 200) {
                const remaining = 200 - count;
                wordStatus.textContent = `⏳ ${remaining} और शब्द चाहिए`;
                wordStatus.style.color = '#eab308';
                wordProgress.className = 'progress-fill warning';
                wordCountDiv.classList.add('warning');
            } else if (count >= 200 && count < 250) {
                wordStatus.textContent = '✅ अच्छा!';
                wordStatus.style.color = '#16a34a';
                wordProgress.className = 'progress-fill success';
                wordCountDiv.classList.add('success');
            } else {
                wordStatus.textContent = '✅ बहुत अच्छा!';
                wordStatus.style.color = '#16a34a';
                wordProgress.className = 'progress-fill success';
                wordCountDiv.classList.add('success');
            }
            
            textarea.value = editor.innerHTML; // ✅ Sync to body textarea
        }

        function formatText(type) {
            const selection = window.getSelection();
            if (!selection.rangeCount) return;
            
            const range = selection.getRangeAt(0);
            const selectedText = range.extractContents();
            
            let wrapper = document.createElement('span');
            
            switch(type) {
                case 'h2':
                    wrapper = document.createElement('h2');
                    break;
                case 'h3':
                    wrapper = document.createElement('h3');
                    break;
                case 'h4':
                    wrapper = document.createElement('h4');
                    break;
                case 'bold':
                    wrapper = document.createElement('strong');
                    break;
                case 'italic':
                    wrapper = document.createElement('em');
                    break;
                case 'quote':
                    wrapper = document.createElement('div');
                    wrapper.className = 'pull-quote';
                    const author = document.createElement('span');
                    author.className = 'quote-author';
                    author.textContent = '- व्यक्ति का नाम';
                    wrapper.appendChild(selectedText);
                    wrapper.appendChild(author);
                    range.deleteContents();
                    range.insertNode(wrapper);
                    updateWordCount();
                    return;
                case 'list':
                    wrapper = document.createElement('ul');
                    wrapper.className = 'content-list';
                    const items = selectedText.textContent.split('\n').filter(item => item.trim());
                    items.forEach(item => {
                        const li = document.createElement('li');
                        li.textContent = item.trim();
                        wrapper.appendChild(li);
                    });
                    range.deleteContents();
                    range.insertNode(wrapper);
                    updateWordCount();
                    return;
                default:
                    return;
            }
            
            wrapper.appendChild(selectedText);
            range.deleteContents();
            range.insertNode(wrapper);
            updateWordCount();
        }

        function openInlineImageUpload() {
            document.getElementById('inlineImageModal').style.display = 'flex';
        }

        function closeInlineImageModal() {
            document.getElementById('inlineImageModal').style.display = 'none';
        }

        function insertInlineImage() {
            const fileInput = document.getElementById('inlineImageInput');
            const caption = document.getElementById('inlineImageCaption').value.trim();
            const file = fileInput.files[0];
            
            if (!file) {
                alert('कृपया एक इमेज चुनें!');
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('figure');
                const imgTag = document.createElement('img');
                imgTag.src = e.target.result;
                imgTag.alt = caption || 'Image';
                
                img.appendChild(imgTag);
                
                if (caption) {
                    const figcaption = document.createElement('figcaption');
                    figcaption.textContent = caption;
                    img.appendChild(figcaption);
                }
                
                const selection = window.getSelection();
                const range = selection.getRangeAt(0);
                range.deleteContents();
                range.insertNode(img);
                
                range.setStartAfter(img);
                range.collapse(true);
                selection.removeAllRanges();
                selection.addRange(range);
                
                updateWordCount();
                closeInlineImageModal();
            };
            reader.readAsDataURL(file);
        }

        // Inline image preview
        document.getElementById('inlineImageInput').addEventListener('change', function() {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('inlinePreviewImg').src = e.target.result;
                document.getElementById('inlineImagePreview').style.display = 'block';
            };
            reader.readAsDataURL(file);
            document.getElementById('inlineUploadProgress').style.display = 'block';
            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 15;
                if (progress >= 100) {
                    progress = 100;
                    clearInterval(interval);
                    document.getElementById('inlineUploadStatus').textContent = '✅ तैयार!';
                }
                document.getElementById('inlineProgressBar').style.width = progress + '%';
            }, 200);
        });

        // Editor events
        editor.addEventListener('input', function() {
            updateWordCount();
            // ✅ Ensure textarea is synced on every input
            textarea.value = editor.innerHTML;
        });
        editor.addEventListener('keyup', function() {
            updateWordCount();
            textarea.value = editor.innerHTML;
        });
        editor.addEventListener('paste', function(e) {
            setTimeout(function() {
                updateWordCount();
                textarea.value = editor.innerHTML;
            }, 100);
        });

        // Ensure content stays inside box
        editor.addEventListener('scroll', function(e) {
            e.stopPropagation();
        });

        // ✅ Sync editor to textarea on form submit
        document.querySelector('form').addEventListener('submit', function(e) {
            textarea.value = editor.innerHTML;
            const title = document.querySelector('input[name="title"]').value.trim();
            const body = textarea.value.trim();
            
            if (!title || !body) {
                e.preventDefault();
                alert('कृपया शीर्षक और खबर भरें।');
                return;
            }
        });

        // Initial word count
        updateWordCount();
    </script>

</body>
</html>