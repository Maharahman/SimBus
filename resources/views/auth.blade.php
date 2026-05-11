<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CUKURUKUK | Auth</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/bulma@1.0.4/css/bulma.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/auth.css') }}">

    <style>
        /* Floating Notification Container (Top Right) */
        .notification-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 380px;
        }

        .notification.is-rounded {
            border-radius: 15px !important;
            border: 1px solid #ffb82b;
            background-color: rgba(26, 29, 32, 0.9) !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
        }

        /* Gold Scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #1a1d20;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #ffb82b;
            border-radius: 10px;
        }

        /* Independent Floating Close Button Fix */
        .custom-modal-frame {
            position: relative !important;
            overflow: visible !important;
        }

        .floating-close {
            position: absolute !important;
            right: 20px !important;
            top: 20px !important;
            z-index: 100 !important;
            background-color: rgba(255, 184, 43, 0.1) !important;
            transition: all 0.3s ease;
        }

        .floating-close:hover {
            background-color: #ffb82b !important;
        }

        .delete::before,
        .delete::after {
            background-color: #ffb82b !important;
        }

        .floating-close:hover::before,
        .floating-close:hover::after {
            background-color: #000 !important;
        }

        .bg-dark-custom {
            background-color: #1a1d20 !important;
        }

        .is-borderless {
            border: none !important;
        }
    </style>
</head>

<body>

    <div class="notification-container">
        @if (session('success'))
            <div
                class="notification is-warning is-light has-text-warning has-text-weight-bold is-rounded animate__animated animate__fadeInRight">
                <div class="is-flex is-align-items-center">
                    <i class="bi bi-check-circle-fill mr-3 is-size-4"></i>
                    <span>Your request has been sent. Waiting for developer approval.</span>
                    <button class="is-warning is-outlined" onclick="this.parentElement.style.display='none'">x</button>
                </div>
            </div>
        @endif
    </div>

    <section class="hero is-fullheight">
        <div class="hero-body has-text-centered">
            <div class="login">
                <figure class="image is-inline-block animate__animated animate__zoomIn" style="width: 200px;">
                    <img src="{{ asset('icons/cukurukuk.svg') }}" alt="Logo Cukurukuk">
                </figure>

                <form action="/login" method="POST">
                    @csrf
                    <div class="field">
                        <div class="control">
                            <input class="input is-medium is-rounded has-text-warning has-text-weight-bold"
                                name="num" type="number" placeholder="08*****" required />
                        </div>
                    </div>
                    <div class="field">
                        <div class="control">
                            <input class="input is-medium is-rounded has-text-warning has-text-weight-bold"
                                name="pass" type="password" placeholder="**********" required />
                        </div>
                    </div>
                    <br />
                    <button class="button is-block is-fullwidth has-text-weight-bold is-medium is-rounded is-login"
                        type="submit">
                        Login
                    </button>
                </form>

                <br>
                <nav class="level is-mobile">
                    <div class="level-item has-text-centered">
                        <a href="#" class="has-text-warning">Forgot Password?</a>
                    </div>
                    <div class="level-item has-text-centered">
                        <a href="javascript:void(0)" onclick="toggleModal()" class="has-text-warning">Create an
                            Account</a>
                    </div>
                </nav>
            </div>
        </div>
    </section>

    <div id="ticket-modal" class="modal">
        <div class="modal-background" onclick="toggleModal()"></div>
        <div class="modal-card custom-modal-frame animate__animated animate__fadeInUp animate__faster">

            <button class="delete is-large floating-close" aria-label="close" onclick="toggleModal()"></button>

            <section class="modal-card-body bg-dark-custom has-text-left custom-scrollbar is-borderless"
                style="border-radius: 15px 15px 0 0; padding-top: 3.5rem;">

                <form id="registration-ticket-form" action="{{ route('tickets.store') }}" method="POST">
                    @csrf
                    <p class="has-text-weight-extrabold has-text-warning is-size-3 has-text-centered mb-5">
                        Registration Request
                    </p>

                    <input type="hidden" name="category" value="registration">

                    <div class="field">
                        <label class="label has-text-grey-light">Full Name</label>
                        <div class="control">
                            <input class="input is-rounded has-text-warning bg-dark" type="text" name="name"
                                placeholder="Paidi Suparman" required>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label has-text-grey-light">Nickname (Sur Name)</label>
                        <div class="control">
                            <input class="input is-rounded has-text-warning bg-dark" type="text" name="sur_name"
                                placeholder="Michael" required>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label has-text-grey-light">Phone Number</label>
                        <div class="control">
                            <input class="input is-rounded has-text-warning bg-dark" type="number" name="num"
                                placeholder="0812345678" required>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label has-text-grey-light">Desired Password</label>
                        <div class="control">
                            <input class="input is-rounded has-text-warning bg-dark" type="password" name="password"
                                required>
                        </div>
                    </div>

                    <div class="field">
                        <label class="label has-text-grey-light">Note to Developer</label>
                        <div class="control">
                            <textarea class="textarea has-text-warning bg-dark" name="message" placeholder="Why do you need access?"
                                rows="2"></textarea>
                        </div>
                    </div>
                </form>
            </section>

            <footer class="modal-card-foot bg-dark-custom is-borderless pt-0"
                style="border-radius: 0 0 15px 15px; padding: 1.5rem;">
                <button type="submit" id="ticket-submit-btn" form="registration-ticket-form"
                    class="button is-block is-rounded is-warning is-fullwidth has-text-weight-bold is-medium is-login">
                    SUBMIT REGISTRATION TICKET
                </button>
            </footer>
        </div>
    </div>

    <script>
        function toggleModal() {
            document.getElementById('ticket-modal').classList.toggle('is-active');
        }

        document.getElementById('registration-ticket-form').onsubmit = function() {
            document.getElementById('ticket-submit-btn').classList.add('is-loading');
        };

        // Auto-dismiss floating notification after 7 seconds
        setTimeout(() => {
            const notif = document.querySelector('.notification');
            if (notif) {
                notif.classList.replace('animate__fadeInRight', 'animate__fadeOutRight');
                setTimeout(() => notif.style.display = 'none', 1000);
            }
        }, 7000);
    </script>
</body>

</html>
