<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>रिपोर्टर रजिस्टर - द पब्लिक एक्सप्रेस</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1a1a2e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }
        .register-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-header img {
            height: 50px;
            margin-bottom: 10px;
        }
        .register-header h3 {
            font-weight: 700;
            color: #1a1a2e;
        }
        .register-header p {
            color: #6c757d;
            font-size: 14px;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #c62828;
            box-shadow: 0 0 0 0.2rem rgba(198, 40, 40, 0.15);
        }
        .btn-register {
            background: #c62828;
            color: white;
            border-radius: 12px;
            padding: 14px;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
            width: 100%;
        }
        .btn-register:hover {
            background: #b71c1c;
            transform: translateY(-2px);
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }
        .login-link a {
            color: #c62828;
            font-weight: 600;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
        .alert {
            border-radius: 12px;
        }
        .form-label {
            font-weight: 600;
            font-size: 14px;
        }
        .required {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <!-- Header -->
        <div class="register-header">
            <img src="{{ asset('images/logo.png') }}" alt="द पब्लिक एक्सप्रेस" onerror="this.src='https://placehold.co/200x60/c62828/white?text=The+Public+Express'">
            <h3>रिपोर्टर रजिस्टर</h3>
            <p>एक नया अकाउंट बनाएं</p>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Registration Form -->
        <form method="POST" action="{{ route('reporter.register.post') }}">
            @csrf

            <div class="row">
                <div class="col-12 mb-3">
                    <label for="name" class="form-label">पूरा नाम <span class="required">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" placeholder="अपना पूरा नाम लिखें" 
                           value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 mb-3">
                    <label for="email" class="form-label">ईमेल <span class="required">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                           id="email" name="email" placeholder="admin@thepublicexpress.com" 
                           value="{{ old('email') }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 mb-3">
                    <label for="phone" class="form-label">मोबाइल नंबर <span class="required">*</span></label>
                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                           id="phone" name="phone" placeholder="9876543210" 
                           value="{{ old('phone') }}" required>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="state_id" class="form-label">राज्य</label>
                    <select class="form-select @error('state_id') is-invalid @enderror" id="state_id" name="state_id">
                        <option value="">राज्य चुनें</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('state_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="district_id" class="form-label">जिला</label>
                    <select class="form-select @error('district_id') is-invalid @enderror" id="district_id" name="district_id">
                        <option value="">जिला चुनें</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ old('district_id') == $district->id ? 'selected' : '' }}>
                                {{ $district->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('district_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="tehsil_id" class="form-label">तहसील</label>
                    <select class="form-select @error('tehsil_id') is-invalid @enderror" id="tehsil_id" name="tehsil_id">
                        <option value="">तहसील चुनें</option>
                        @foreach($tehsils as $tehsil)
                            <option value="{{ $tehsil->id }}" {{ old('tehsil_id') == $tehsil->id ? 'selected' : '' }}>
                                {{ $tehsil->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('tehsil_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label for="block_id" class="form-label">ब्लॉक</label>
                    <select class="form-select @error('block_id') is-invalid @enderror" id="block_id" name="block_id">
                        <option value="">ब्लॉक चुनें</option>
                        @foreach($blocks as $block)
                            <option value="{{ $block->id }}" {{ old('block_id') == $block->id ? 'selected' : '' }}>
                                {{ $block->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('block_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="alert alert-info mt-2" style="font-size: 13px;">
                <i class="fas fa-info-circle"></i> 
                रजिस्टर करने के बाद आपको एडमिन द्वारा approve किया जाएगा। 
                Approve होने के बाद आप खबरें लिख सकेंगे।
            </div>

            <button type="submit" class="btn-register">
                <i class="fas fa-user-plus"></i> रजिस्टर करें
            </button>
        </form>

        <div class="login-link">
            <p>पहले से अकाउंट है? <a href="{{ route('reporter.login') }}">लॉगिन करें</a></p>
            <a href="/" class="text-decoration-none" style="color: #6c757d; font-size: 13px;">
                <i class="fas fa-arrow-left"></i> होम पेज पर वापस जाएं
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>