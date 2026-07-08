<!DOCTYPE html>
<html lang="fr" data-theme="spotin">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Spot in — Réinventer l'information dans l'espace public</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

{{-- Navbar --}}
<div class="navbar bg-base-100 shadow-sm sticky top-0 z-50 px-4 lg:px-12">
    <div class="navbar-start">
        <a href="#" class="text-xl font-bold">Spot <span class="text-primary">in</span></a>
    </div>
    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal gap-1 text-sm font-medium">
            <li><a href="#concept">Concept</a></li>
            <li><a href="#avantages">Avantages</a></li>
            <li><a href="/prices">Prix</a></li>
            <li><a href="#suisse" class="flex items-center gap-1">100% Suisse</a></li>
        </ul>
    </div>
    <div class="navbar-end">
        <a href="#" class="btn btn-primary btn-sm rounded-full px-5">Démo</a>
    </div>
</div>

{{-- Hero --}}
<section class="hero min-h-[80vh] bg-base-100 px-4 lg:px-12 py-16">
    <div class="hero-content max-w-6xl w-full flex-col lg:flex-row justify-between gap-12 mx-auto">
        <div class="max-w-xl">
            <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight mb-6">
                Spot in réinvente<br>l'information dans<br>
                <span class="text-primary underline decoration-primary underline-offset-4">l'espace public</span>
            </h1>
            <p class="text-base-content/70 mb-8 text-lg leading-relaxed">
                Transformez vos lieux en expériences vivantes, utiles et accessibles à toutes et à tous.
                Simplifiez la diffusion d'informations fiables sur le terrain.
            </p>
            <a href="#" class="btn btn-primary rounded-full px-8 text-base">Commencer maintenant</a>
        </div>
        <div class="card bg-base-100 shadow-xl p-6 rounded-3xl">
            <img src="https://xry.ch/spot/newspot/qr.svg" alt="QR Code Spot in" class="w-48 lg:w-64" />
        </div>
    </div>
</section>

{{-- Donnez du sens à vos lieux --}}
<section id="concept" class="py-20 bg-base-100 px-4">
    <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-3xl font-bold mb-2">Donnez du sens à vos lieux</h2>
        <div class="w-12 h-1 bg-primary mx-auto mb-10 rounded-full"></div>
        <div class="card bg-base-200 p-8 text-left rounded-2xl">
            <p class="text-base-content/80 mb-6 leading-relaxed">
                <strong>Spot in</strong> est une plateforme suisse pour les communes, associations, organisations et événements.
                Elle permet de diffuser instantanément des informations fiables, directement sur le terrain,
                quand et là où la population en a besoin.
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="flex items-start gap-3">
                    <div class="text-primary mt-1">
                        <x-heroicon-o-device-phone-mobile class="w-6 h-6" />
                    </div>
                    <p class="text-sm text-base-content/70">Accès immédiat sans aucune application supplémentaire.</p>
                </div>
                <div class="flex items-start gap-3">
                    <div class="text-primary mt-1">
                        <x-heroicon-o-arrow-right-end-on-rectangle class="w-6 h-6" />
                    </div>
                    <p class="text-sm text-base-content/70">Une porte d'entrée intelligente vers des contenus et des données clairs.</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Pourquoi repenser l'information --}}
<section class="py-20 bg-neutral text-neutral-content px-4">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12">Pourquoi repenser l'information ?</h2>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white/5 p-6 rounded-2xl">
                <h3 class="font-bold text-primary mb-4 flex items-center gap-2">
                    <x-heroicon-o-user-group class="w-5 h-5" />
                    Problèmes pour le public
                </h3>
                <ul class="space-y-3 text-neutral-content/70 text-sm">
                    <li class="flex items-start gap-2"><span class="text-error">✕</span> On ne trouve pas (ou plus) ce qu'on cherche.</li>
                    <li class="flex items-start gap-2"><span class="text-error">✕</span> Liens cassés ou obsolètes.</li>
                    <li class="flex items-start gap-2"><span class="text-error">✕</span> Impossibilité de s'assurer de la fiabilité de l'information.</li>
                </ul>
            </div>
            <div class="bg-white/5 p-6 rounded-2xl">
                <h3 class="font-bold text-primary mb-4 flex items-center gap-2">
                    <x-heroicon-o-wrench-screwdriver class="w-5 h-5" />
                    Problèmes pour les gestionnaires
                </h3>
                <ul class="space-y-3 text-neutral-content/70 text-sm">
                    <li class="flex items-start gap-2"><span class="text-error">✕</span> Des informations en ligne "éparpillées".</li>
                    <li class="flex items-start gap-2"><span class="text-error">✕</span> Une dépendance à des plateformes et algorithmes pas maîtrisés.</li>
                    <li class="flex items-start gap-2"><span class="text-error">✕</span> Trop d'outils dispersés, chers et complexes.</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- Vos lieux deviennent informatifs --}}
<section id="avantages" class="py-20 bg-base-100 px-4">
    <div class="max-w-5xl mx-auto">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold mb-3">Vos lieux deviennent informatifs et toujours à jour</h2>
            <p class="text-base-content/60 max-w-2xl mx-auto">
                Spot in vous permet de garder vos lieux vivants, notamment quand les affiches ou les sites web ne suffisent plus.
            </p>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <div class="flex gap-5">
                <div class="text-5xl font-black text-base-300 leading-none select-none shrink-0">01</div>
                <div>
                    <h3 class="font-bold text-lg mb-3">Pour les visiteurs et citoyens</h3>
                    <ul class="space-y-2 text-base-content/70 text-sm">
                        <li class="flex items-center gap-2"><span class="text-primary font-bold">✓</span> Informations dynamiques, toujours à jour.</li>
                        <li class="flex items-center gap-2"><span class="text-primary font-bold">✓</span> Contenus clairs et faciles à comprendre.</li>
                        <li class="flex items-center gap-2"><span class="text-primary font-bold">✓</span> Action unique : scanner et accéder.</li>
                    </ul>
                </div>
            </div>
            <div class="flex gap-5">
                <div class="text-5xl font-black text-base-300 leading-none select-none shrink-0">02</div>
                <div>
                    <h3 class="font-bold text-lg mb-3">Pour les organisations</h3>
                    <ul class="space-y-2 text-base-content/70 text-sm">
                        <li class="flex items-center gap-2"><span class="text-primary font-bold">✓</span> Mise à jour à distance en quelques secondes.</li>
                        <li class="flex items-center gap-2"><span class="text-primary font-bold">✓</span> Plusieurs services avec un seul code.</li>
                        <li class="flex items-center gap-2"><span class="text-primary font-bold">✓</span> Outil simple, fiable et prêt à l'emploi.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- La simplification au quotidien --}}
<section class="py-20 bg-base-200 px-4">
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-12">La simplification au quotidien</h2>
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
            <div class="card bg-base-100 p-6 rounded-2xl text-center">
                <div class="text-primary mb-3 flex justify-center">
                    <x-heroicon-o-pencil-square class="w-8 h-8" />
                </div>
                <h4 class="font-bold italic mb-2">Mise à jour instantanée</h4>
                <p class="text-sm text-base-content/60">Modifiez vos contenus à tout moment, même après impression physique du QR code.</p>
            </div>
            <div class="card bg-base-100 p-6 rounded-2xl text-center">
                <div class="text-primary mb-3 flex justify-center">
                    <x-heroicon-o-square-3-stack-3d class="w-8 h-8" />
                </div>
                <h4 class="font-bold italic mb-2">Hub d'information</h4>
                <p class="text-sm text-base-content/60">Regroupez plusieurs liens et contenus dynamiques derrière une seule porte d'entrée.</p>
            </div>
            <div class="card bg-base-100 p-6 rounded-2xl text-center">
                <div class="text-primary mb-3 flex justify-center">
                    <x-heroicon-o-cursor-arrow-rays class="w-8 h-8" />
                </div>
                <h4 class="font-bold italic mb-2">Zéro Technique</h4>
                <p class="text-sm text-base-content/60">Aucune connaissance technique nécessaire. Simple, rapide et terriblement efficace.</p>
            </div>
        </div>
        <div class="text-center">
            <p class="text-xs font-bold tracking-widest text-base-content/40 uppercase mb-4">Applications concrètes</p>
            <div class="flex flex-wrap justify-center gap-2">
                <span class="badge badge-outline badge-lg">Transports</span>
                <span class="badge badge-outline badge-lg">Événements</span>
                <span class="badge badge-outline badge-lg">Patrimoine et culture</span>
                <span class="badge badge-outline badge-lg">Site d'urgence</span>
                <span class="badge badge-outline badge-lg">Vie locale</span>
            </div>
        </div>
    </div>
</section>

{{-- Plateforme suisse --}}
<section id="suisse" class="py-20 bg-base-100 px-4">
    <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
        <div>
            <h2 class="text-3xl font-bold mb-8">Une plateforme suisse, pensée et gouvernée localement</h2>
            <ul class="space-y-5">
                <li class="flex items-start gap-3">
                    <span class="text-xl shrink-0"></span>
                    <p class="text-sm text-base-content/70"><strong class="text-base-content">Hébergement 100% en Suisse</strong>, dans des centres de données souverains.</p>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-xl shrink-0"></span>
                    <p class="text-sm text-base-content/70"><strong class="text-base-content">Protection renforcée</strong> : vos données restent en Suisse et ne transitent pas via des acteurs externes.</p>
                </li>
                <li class="flex items-start gap-3">
                    <span class="text-xl shrink-0"></span>
                    <p class="text-sm text-base-content/70"><strong class="text-base-content">Gouvernance helvétique</strong> au plus proche des besoins des collectivités locales.</p>
                </li>
            </ul>
        </div>
        <div class="flex justify-center">
            <div class="card bg-base-200 border border-base-300 rounded-2xl px-16 py-10 text-center">
                <div class="flex justify-center gap-1 mb-3">
                    <div class="w-8 h-6 bg-red-600 rounded flex items-center justify-center">
                        <span class="text-white font-black text-sm">+</span>
                    </div>

                </div>
                <p class="font-black text-2xl tracking-widest text-base-content">SWISS MADE</p>
                <p class="text-xs tracking-[0.3em] text-base-content/50 uppercase mt-1">Gouvernance</p>
            </div>
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="bg-neutral text-neutral-content px-4 py-16">
    <div class="max-w-5xl mx-auto text-center">
        <p class="text-6xl font-black text-neutral-content/10 leading-none mb-2">+</p>
        <p class="text-lg font-semibold mb-2">Prêt à réinventer vos lieux ?</p>
        <a href="mailto:info@spotin.ch" class="text-primary text-xl font-bold hover:underline">info@spotin.ch</a>
        <div class="divider divider-neutral opacity-20 my-8"></div>
        <p class="text-neutral-content/40 text-sm mb-2">© 2026 Spot in. Plateforme suisse de diffusion d'information urbaine.</p>
        <div class="flex gap-4 justify-center text-sm text-neutral-content/40">
            <a href="#" class="hover:text-neutral-content transition-colors">Mentions Légales</a>
            <a href="#" class="hover:text-neutral-content transition-colors">Confidentialité</a>
        </div>
    </div>
</footer>

</body>
</html>
