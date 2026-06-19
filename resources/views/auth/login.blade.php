<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - HK CRM</title>
    <meta name="description" content="Sign in to HK CRM Lead Management System">
    <link rel="icon" href="{{ asset('images/logo.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <img src="{{ asset('images/logo.png') }}" alt="HK CRM" class="login-logo">
            <h1 class="login-title">Welcome Back</h1>
            <p class="login-subtitle">Sign in to manage your leads</p>

            <button class="google-btn" id="googleSignInBtn">
                <svg width="20" height="20" viewBox="0 0 48 48">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                    <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                    <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                </svg>
                Sign in with Google
            </button>

            <div id="loginError" style="display: none; margin-top: 16px; padding: 10px; background: rgba(239,68,68,0.1); color: #EF4444; border-radius: 8px; font-size: 0.85rem;"></div>
            <div id="loginLoading" style="display: none; margin-top: 16px; color: #6B7280; font-size: 0.85rem;">
                <i class="fas fa-spinner fa-spin"></i> Signing you in...
            </div>

            <p class="login-footer">
                Secure login powered by Firebase & Google
            </p>
        </div>
    </div>

    <!-- Firebase SDK -->
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.0/firebase-auth-compat.js"></script>

    <script>
        // ============================================
        // FIREBASE CONFIGURATION
        // Replace these values with your Firebase project config
        // Get it from: Firebase Console > Project Settings > General > Your apps
        // ============================================
        const firebaseConfig = {
            apiKey: "{{ env('FIREBASE_API_KEY', 'YOUR_API_KEY') }}",
            authDomain: "{{ env('FIREBASE_AUTH_DOMAIN', 'YOUR_PROJECT.firebaseapp.com') }}",
            projectId: "{{ env('FIREBASE_PROJECT_ID', 'YOUR_PROJECT_ID') }}",
        };

        // Initialize Firebase
        firebase.initializeApp(firebaseConfig);

        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const signInBtn = document.getElementById('googleSignInBtn');
        const loginError = document.getElementById('loginError');
        const loginLoading = document.getElementById('loginLoading');

        signInBtn.addEventListener('click', async function() {
            signInBtn.disabled = true;
            loginError.style.display = 'none';
            loginLoading.style.display = 'block';

            try {
                const provider = new firebase.auth.GoogleAuthProvider();
                const result = await firebase.auth().signInWithPopup(provider);
                const user = result.user;

                // Send user info to our Laravel backend
                const response = await fetch('{{ route("auth.firebase") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        firebase_uid: user.uid,
                        email: user.email,
                        name: user.displayName || user.email.split('@')[0],
                        avatar: user.photoURL,
                    }),
                });

                const data = await response.json();

                if (data.success) {
                    window.location.href = data.redirect;
                } else {
                    throw new Error(data.message || 'Login failed');
                }
            } catch (error) {
                console.error('Login error:', error);
                loginError.textContent = error.message || 'Failed to sign in. Please try again.';
                loginError.style.display = 'block';
                loginLoading.style.display = 'none';
                signInBtn.disabled = false;
            }
        });
    </script>
</body>
</html>
