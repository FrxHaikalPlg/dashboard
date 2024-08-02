<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
            /* ! tailwindcss v3.4.1 | MIT License | https://tailwindcss.com */
            *, ::after, ::before {
                box-sizing: border-box;
                border-width: 0;
                border-style: solid;
                border-color: #e5e7eb;
            }
            ::after, ::before {
                --tw-content: '';
            }
            :host, html {
                line-height: 1.5;
                -webkit-text-size-adjust: 100%;
                -moz-tab-size: 4;
                tab-size: 4;
                font-family: Figtree, ui-sans-serif, system-ui, sans-serif, Apple Color Emoji, Segoe UI Emoji, Segoe UI Symbol, Noto Color Emoji;
                font-feature-settings: normal;
                font-variation-settings: normal;
                -webkit-tap-highlight-color: transparent;
            }
            body {
                margin: 0;
                line-height: inherit;
            }
            hr {
                height: 0;
                color: inherit;
                border-top-width: 1px;
            }
            abbr:where([title]) {
                -webkit-text-decoration: underline dotted;
                text-decoration: underline dotted;
            }
            h1, h2, h3, h4, h5, h6 {
                font-size: inherit;
                font-weight: inherit;
            }
            a {
                color: inherit;
                text-decoration: inherit;
            }
            b, strong {
                font-weight: bolder;
            }
            code, kbd, pre, samp {
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
                font-feature-settings: normal;
                font-variation-settings: normal;
                font-size: 1em;
            }
            small {
                font-size: 80%;
            }
            sub, sup {
                font-size: 75%;
                line-height: 0;
                position: relative;
                vertical-align: baseline;
            }
            sub {
                bottom: -.25em;
            }
            sup {
                top: -.5em;
            }
            table {
                text-indent: 0;
                border-color: inherit;
                border-collapse: collapse;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        <div class="bg-gray-50 text-black">
            <div class="relative min-h-screen flex flex-col items-center justify-center">
                <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                    <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                        <div class="flex lg:justify-center lg:col-start-2">
                            <!-- Logo or Image can be placed here -->
                            <img src="https://laravel.com/assets/img/welcome/logo.svg" alt="Laravel Logo" class="h-12 w-auto">
                        </div>
                    </header>
                    <main>
                        <h1>Welcome to Laravel!</h1>
                        <p>This is a simple example of a Laravel application without database interaction.</p>
                    </main>
                    <footer class="py-16 text-center text-sm text-black">
                        Laravel v{{ Illuminate\Foundation\Application::VERSION }}
                    </footer>
                </div>
            </div>
        </div>
    </body>
</html>