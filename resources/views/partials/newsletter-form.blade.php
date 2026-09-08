<div class="newsletter-section">
    <h4>📧 हमारे न्यूज़लेटर की सदस्यता लें</h4>
    <p>हर नई खबर सीधे आपके ईमेल पर पाएं।</p>
    
    <form id="newsletterForm" class="newsletter-form">
        @csrf
        <div class="row g-2">
            <div class="col-md-5">
                <input type="text" class="form-control" id="newsletter_name" name="name" placeholder="आपका नाम (वैकल्पिक)">
            </div>
            <div class="col-md-5">
                <input type="email" class="form-control" id="newsletter_email" name="email" placeholder="ईमेल *" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100" id="newsletterBtn">सब्सक्राइब</button>
            </div>
        </div>
        <div id="newsletterMessage" class="mt-2"></div>
    </form>
</div>

<script>
document.getElementById('newsletterForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('newsletterBtn');
    const msg = document.getElementById('newsletterMessage');
    const email = document.getElementById('newsletter_email').value;
    const name = document.getElementById('newsletter_name').value;
    
    btn.disabled = true;
    btn.innerHTML = '⏳ हो रहा है...';
    msg.innerHTML = '';
    
    fetch('{{ route("newsletter.subscribe") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ email: email, name: name })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            msg.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
            document.getElementById('newsletterForm').reset();
        } else {
            let errors = '';
            if (data.errors) {
                for (let key in data.errors) {
                    errors += data.errors[key][0] + '<br>';
                }
            } else {
                errors = data.message || 'कुछ गड़बड़ हो गई।';
            }
            msg.innerHTML = '<div class="alert alert-danger">' + errors + '</div>';
        }
    })
    .catch(error => {
        msg.innerHTML = '<div class="alert alert-danger">कुछ गड़बड़ हो गई। कृपया पुनः प्रयास करें।</div>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = 'सब्सक्राइब';
    });
});
</script>